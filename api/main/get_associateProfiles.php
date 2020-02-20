<?php
ob_start();
include_once('../_database/confi.php');
include_once('../_authorization/assignVerifyJWT.php');
include '../_general/status_returns.php';
include '../_general/functions.php';
include '../_crud/read.php';

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);

if( !isset($request->token) ){
    include '../_general/cors.php';
    die();
}

$token = $request->token;
$data = new stdClass();
$profiles = [];

try {
    mysqli_check();
    global $conn;

    $pro_info = returnTokenProfileId($token);
    $profile_id = intval($pro_info->profile_id);
  
    $sql = "SELECT DISTINCT 
    profiles.profile_id AS profile_id,
    profiles.email AS email,
    profiles.image AS image,
    profiles.first_name AS first_name,
    profiles.last_name AS last_name, 
    profiles.created_date AS created_date, 
    teams.team_name AS team_name, 
    teams.team_id AS team_id, 
    projects.project_id AS project_id, 
    projects.project_name AS project_name 
    FROM profiles 
    LEFT JOIN profiles_team ON profiles_team.profile_id = profiles.profile_id
    LEFT JOIN teams ON teams.team_id = profiles_team.team_id
    LEFT JOIN projects ON projects.project_id = teams.project_id
    WHERE profiles_team.team_id IN ( 
        SELECT DISTINCT profiles_team.team_id 
        FROM profiles_team 
        WHERE profiles_team.profile_id = ".$profile_id." ) AND profiles.profile_id != ".$profile_id." AND profiles_team.profile_team_status != 'deleted' ORDER BY projects.project_id, teams.team_id";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
  
    if(!!$result && $result->num_rows > 0){  
        while( $row = $result->fetch_assoc() ){
            if( !!$row["profile_id"] ){ 
                if( !in_array( $row, $profiles ) ){   
                    array_push($profiles, $row);
                }
            }
        }
    }
    $data->profiles = $profiles;
    $data->message = "associates pulled";
    status_return(200);
    echo json_encode($data);
    $conn->close();
    return;
}catch (Exception $e ){
    status_return(500);
    echo($e->message);
    return;
}


?>