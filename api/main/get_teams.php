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
$teams = [];
$deleted = 'deleted';

try {
  global $conn;
  mysqli_check();

  $pro_info = returnTokenProfileId($token);
  $profile_id = intval($pro_info->profile_id);

  $sql = "SELECT DISTINCT teams.team_id, 
  teams.team_name, 
  teams.team_status, 
  projects.project_name 
  FROM teams LEFT JOIN projects ON projects.project_id = teams.project_id LEFT JOIN profiles ON profiles.profile_id = projects.owner_id
  WHERE profiles.profile_id = ".$profile_id." AND projects.project_status !='deleted' ORDER BY projects.project_id, teams.team_id";     
    
  $stmt = $conn->prepare($sql);
  $stmt->execute();
  $result = $stmt->get_result();
  $stmt->close();

  if(!!$result && $result->num_rows > 0){  
    while( $row = $result->fetch_assoc() ){
      if( $row['team_status'] != $deleted ){      
        if( !in_array( $teams, $row ) ){   
          array_push($teams, $row);
        }
      }
    }
  }else{
    $data->message = "project not found";
    status_return(400); 
    echo json_encode($data);
    $conn->close();
    return;   
  }

  $data->message = "teams list found";
  $data->teams = $teams;
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