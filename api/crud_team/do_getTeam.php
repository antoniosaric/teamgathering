<?php
ob_start();
header("Access-Control-Allow-Headers: content-type");	
include_once('../_database/confi.php');
include_once('../_authorization/assignVerifyJWT.php');
include_once '../_general/status_returns.php';
include_once '../_general/functions.php';
include '../_crud/read.php';

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);

if( !isset($request->team_id) || !isset($request->token) ){
    // include '../_general/cors.php';
    die();
}

$team_id = isset($request->team_id) && intval($request->team_id) > 0 ? intval($request->team_id) : false;
$token = $request->token;
$data = new stdClass();
$team = new stdClass();
$team_profile = [];

try {
    mysqli_check();
    global $conn;

    $pro_info = returnTokenProfileId($token);
    $profile_id = intval($pro_info->profile_id);

    $sql = "SELECT DISTINCT 
            teams.team_id,
            projects.project_id,
            teams.team_name,
            teams.team_description,
            profiles_team.profiles_team_id,
            profiles_team.role,
            profiles_team.profile_team_status,
            profiles_team.joined_date,
            profiles_team.ended_date,
            profiles.first_name,
            profiles.last_name,
            profiles.profile_id
            FROM `profiles_team`
            LEFT JOIN `teams` ON teams.team_id = profiles_team.team_id 
            LEFT JOIN `projects` ON projects.project_id = teams.project_id 
            LEFT JOIN `profiles` ON profiles.profile_id = profiles_team.profile_id 
            WHERE teams.team_id = ? AND projects.owner_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $team_id, $profile_id );
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if(!!$result && $result->num_rows > 0){  
        while( $row = $result->fetch_assoc() ){
            $team_profile_object = new stdClass(); 

            $team_info_name = $row['team_name'];
            $team_info_description = $row['team_description'];
            $team_info_id = $row['team_id'];
            $team_info_project_id = $row['project_id'];

            if( !!$row["profiles_team_id"] ){ 
                    $team_profile_object->profiles_team_id = $row['profiles_team_id'];
                    $team_profile_object->profile_id = $row['profile_id'];
                    $team_profile_object->role = $row['role'];
                    $team_profile_object->profile_team_status = $row['profile_team_status'];
                    $team_profile_object->joined_date = $row['joined_date'];
                    $team_profile_object->ended_date = $row['ended_date'];
                    $team_profile_object->first_name = $row['first_name'];
                    $team_profile_object->last_name = $row['last_name'];

                if( !in_array( $team_profile_object, $team_profile ) ){
                    array_push($team_profile, $team_profile_object);
                }
            }
        }

        $team->team_name = $team_info_name;
        $team->team_description = $team_info_description;
        $team->team_id = $team_info_id;
        $team->project_id = $team_info_project_id;
        $team->profiles = $team_profile;
        $data->team = $team;
        $data->message = 'team info pulled';
        $data->token = exchangeToken($token);
        status_return(200);
        echo json_encode($data);
        $conn->close();
        return;
    }else{
        $data->message = "team not found";
        status_return(400); 
        echo json_encode($data);
        $conn->close();
        return;    
    }
}catch (Exception $e ){
    status_return(500);
    echo($e->message);
    return;
}
?>