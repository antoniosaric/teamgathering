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
  // include '../_general/cors.php';
  die();
}

$token = $request->token;

$data = new stdClass();
$projects = [];
$deleted = 'deleted';

try {
  global $conn;
  mysqli_check();

  $pro_info = returnTokenProfileId($token);
  $profile_id = intval($pro_info->profile_id);

    $sql = "SELECT DISTINCT 
    projects.project_id AS project_id,
    projects.project_name AS project_name,
    projects.project_status AS project_status,
    projects.short_description AS short_description,
    projects.image AS image,
    projects.owner_id AS owner_id,
    profiles.first_name AS first_name,
    profiles.last_name AS last_name
    FROM projects 
    LEFT JOIN profiles ON profiles.profile_id = projects.owner_id 
    LEFT JOIN teams ON teams.project_id = projects.project_id
    LEFT JOIN profiles_team ON profiles_team.team_id = teams.team_id
    WHERE ( projects.project_status != 'deleted' ) 
    AND ( projects.project_status = 'public' OR projects.project_status = 'private' ) 
    AND profiles_team.profile_id = ".$profile_id;     
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if(!!$result && $result->num_rows > 0){  
        while( $row = $result->fetch_assoc() ){
            if( !in_array( $row, $projects ) ){   
                array_push( $projects, $row );
            }
        }
    }
    $data->projects = $projects;
    $data->message = "projects found";
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