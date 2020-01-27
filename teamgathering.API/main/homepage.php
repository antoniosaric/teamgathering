<?php
ob_start();
include_once('../_database/confi.php');
include '../_general/status_returns.php';
include '../_general/functions.php';

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
// $param = isset($request->param) ? trim($request->param) : " ORDER BY project_id ASC Limit 5";

$data = [];

try {
  global $conn;
  mysqli_check();

  $private = 'private';
  $sql = "SELECT DISTINCT 
          projects.project_id AS project_id,
          projects.project_name AS project_name,
          projects.short_description AS short_description,
          projects.image AS image,
          projects.owner_id AS owner_id,
          profiles.first_name AS first_name,
          profiles.last_name AS last_name
          FROM projects 
          LEFT JOIN profiles ON profiles.profile_id = projects.owner_id 
          LEFT JOIN teams ON teams.project_id = projects.project_id
          LEFT JOIN profiles_team ON profiles_team.team_id = teams.team_id
          WHERE project_status != 'private' AND profiles_team.role != 'Owner'";
  $stmt = $conn->prepare($sql);
  $stmt->execute();
  $result = $stmt->get_result();
  $stmt->close();

  if( !!$result && $result->num_rows > 0 ){  
    while( $row = $result->fetch_assoc() ){
      if( !!$row["project_id"] ){ 
        array_push($data, $row);
      }
    }
    status_return(200);
    echo json_encode($data);
    return;
  }else{
    status_return(204);
    return;
  }
  $conn->close();
}catch (Exception $e ){
  status_return(500);
  echo($e->message);
  return;
}
?>