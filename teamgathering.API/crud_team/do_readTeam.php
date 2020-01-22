<?php
ob_start();
header('Access-Control-Allow-Origin: http://localhost:4200', false);
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

$team_id = isset($request->team_id) && strlen($request->team_id) > 0 ? trim($request->team_id) : false;
$token = $request->token;
$data = new stdClass();
$teams = [];

try {
    mysqli_check();
    global $conn;

    $pro_info = returnTokenProfileId($token);
    $profile_id = intval($pro_info->profile_id);

    $sql = "SELECT DISTINCT * 
            FROM profiles_team 
            LEFT JOIN teams ON teams.team_id = profiles_team.team_id 
            LEFT JOIN projects ON projects.project_id = teams.project_id 
            LEFT JOIN profiles ON profiles.profile_id=profiles_team.profile_id 
            WHERE teams.team_id = ? AND projects.owner_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute('ii', $team_id, $profile_id);
    $result = $stmt->get_result();
    $stmt->close();

    if(!!$result && $result->num_rows > 0){  
        while( $row = $result->fetch_assoc() ){
            $team_object = new stdClass(); 

            $team_object->team_name = $row['team_name'];
            $team_object->team_description = $row['team_description'];

            if( !!$row["profiles_team_id"] ){ 
                    $team_object->profiles_team_id = $row['profiles_team_id'];
                    $team_object->profile_id = $row['profile_id'];
                    $team_object->role = $row['role'];
                    $team_object->profile_team_status = $row['profile_team_status'];
                    $team_object->joined_date = $row['joined_date'];
                    $team_object->ended_date = $row['ended_date'];
                    $team_object->first_name = $row['first_name'];
                    $team_object->last_name = $row['ended_date'];

                if( !in_array( $team_object, $teams ) ){
                    array_push($teams, $team_object);
                }
            }
        }

        $data->teams = $teams;
        $data->message = 'team member added';
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