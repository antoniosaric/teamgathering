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
$project_requests = [];
$profile_requests = [];

try {
  global $conn;
  mysqli_check();

  $pro_info = returnTokenProfileId($token);
  $profile_id = intval($pro_info->profile_id);

  $sql = "SELECT DISTINCT 
  requests.request_id, 
  requests.request_status, 
  requests.created_date, 
  requests.updated_date, 
  requests.requester_id, 
  requests.requestee_id, 
  projects.project_name,
  projects.project_id,  
  profiles.profile_id,
  profiles.first_name,
  profiles.last_name
  FROM requests LEFT JOIN projects ON projects.project_id = requests.project_id LEFT JOIN profiles ON profiles.profile_id = requests.requester_id
  WHERE requests.requestee_id = ".$profile_id." ORDER BY projects.project_id, requests.request_status, requests.request_id";     
    
  $stmt = $conn->prepare($sql);
  $stmt->execute();
  $result = $stmt->get_result();
  $stmt->close();

  if(!!$result && $result->num_rows > 0){  
    while( $row = $result->fetch_assoc() ){
      array_push($project_requests, $row);
    }
  }

  $sql2 = "SELECT DISTINCT 
  requests.request_id, 
  requests.request_status, 
  requests.created_date, 
  requests.updated_date, 
  requests.requester_id, 
  requests.requestee_id, 
  projects.project_name, 
  projects.project_id, 
  profiles.profile_id,
  profiles.first_name,
  profiles.last_name
  FROM requests LEFT JOIN projects ON projects.project_id = requests.project_id LEFT JOIN profiles ON profiles.profile_id = requests.requestee_id
  WHERE requests.requester_id = ".$profile_id." ORDER BY projects.project_id, requests.request_status, requests.request_id";     
    
  $stmt2 = $conn->prepare($sql2);
  $stmt2->execute();
  $result2 = $stmt2->get_result();
  $stmt2->close();

  if(!!$result2 && $result2->num_rows > 0){  
    while( $row2 = $result2->fetch_assoc() ){
      array_push($profile_requests, $row2);
    }
  }

  $data->message = "requests found";
  $data->project_requests = array_reverse($project_requests);
  $data->profile_requests = array_reverse($profile_requests);
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


