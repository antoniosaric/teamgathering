<?php
ob_start();
// header('Access-Control-Allow-Origin: http://localhost:4200', false);
include_once('../_database/confi.php');
include_once('../_authorization/assignVerifyJWT.php');
include '../_general/status_returns.php';
include '../_general/functions.php';
include '../_crud/read.php';

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);

if( !isset($request->profile_id) ){
    include '../_general/cors.php';
    die();
}

$profile_id = (int)$request->profile_id;
$profile = new stdClass(); 
$projects = []; 
$teams = []; 
$data = new stdClass();

try {
    mysqli_check();
    global $conn;

    $clauseArray = [ $profile_id ];
    $row_profile = get_tabel_info_single_row( 'profiles', 'WHERE profile_id=?', 'i', $clauseArray );


    $sql2 = "SELECT DISTINCT *
    FROM projects 
    LEFT JOIN teams ON teams.project_id = projects.project_id
    LEFT JOIN profiles_team ON profiles_team.team_id = teams.team_id
    WHERE profiles_team.profile_id = ".$profile_id." ORDER BY projects.project_id, teams.team_id";

    $stmt2 = $conn->prepare($sql2);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    $stmt2->close();

    if(!!$result2 && $result2->num_rows > 0){  
        while( $row2 = $result2->fetch_assoc() ){
            $project_object = new stdClass(); 
            $team_object = new stdClass(); 
            if( !!$row2["project_id"] ){ 
                $project_object->project_id = $row2['project_id'];
                $project_object->project_name = $row2['project_name'];
                $project_object->project_status = $row2['project_status']; 
                $project_object->created_date = $row2['created_date']; 
                $project_object->project_roll = ( $row2['owner_id'] == $profile_id ) ? 'Owner': 'Member';
                
                if( !in_array( $project_object, $projects ) ){                   
                    array_push($projects, $project_object);
                }
            }
            if( !!$row2["team_id"] ){ 
                    $team_object->team_id = $row2['team_id'];
                    $team_object->team_name = $row2['team_name'];
                    $team_object->role = $row2['role'];
                    $team_object->profile_team_status = $row2['profile_team_status'];
                    $team_object->joined_date = $row2['joined_date'];

                if( !in_array( $team_object, $teams ) ){
                    array_push($teams, $team_object);
                }
            }
        }
    }
    
    if( !!$row_profile['profile_id'] ){
        $profile->profile_id = $row_profile['profile_id'];
        $profile->email = $row_profile['email'];
        $profile->first_name = $row_profile['first_name'];
        $profile->last_name = $row_profile['last_name'];
        $profile->image = $row_profile['image'];
        $profile->city = $row_profile['city'];
        $profile->zip_code = $row_profile['zip_code'];
        $profile->state = $row_profile['state'];
        $profile->created_date = $row_profile['created_date'];
        $profile->looking_for = $row_profile['looking_for'];
        $profile->description = $row_profile['description'];
        $profile->teams = $teams;
        $profile->projects = $projects;
        $data->profile = $profile;
        $data->message = "profile retrieved";
        status_return(200);
        echo json_encode($data);
        $conn->close();
        return;
    }else{
        $data->message = "page not found";
        status_return(404); 
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