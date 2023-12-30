<?php
ob_start();
include_once('../_database/confi.php');
include_once('../_authorization/assignVerifyJWT.php');
include_once '../_general/status_returns.php';
include_once '../_general/functions.php';

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$param = isset($request->param) ? trim($request->param) : "ORDER BY profile_id ASC Limit 5";
$token = $request->token;

$data = new stdClass();

try {
  global $conn;
  mysqli_check();

  $pro_info = returnTokenProfileId($token);
  $profile_id = intval($pro_info->profile_id);

  $sql = "SELECT * FROM profiles ".$param;
  $stmt = $conn->prepare($sql);
  isset($request->param) ? $stmt->bind_param("i", $param) : null ;
  $stmt->execute();
  $result = $stmt->get_result();
  $stmt->close();

  if(!!$result && $result->num_rows > 0){  
    while( $row = $result->fetch_assoc() ){
        status_return(200);
        echo json_encode($data);
        return;
    }
  }else{
    status_return(401);
    return;
  }
  $conn->close();
}catch (Exception $e ){
  status_return(500);
  echo($e->message);
  return;
}
?>