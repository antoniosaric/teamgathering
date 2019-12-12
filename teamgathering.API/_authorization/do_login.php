<?php
ob_start();
include_once('../_database/confi.php');
include 'do_passwordHash.php';
include_once('assignVerifyJWT.php');
include_once '../_general/status_returns.php';
include_once '../_general/functions.php';

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$pass = isset($request->password) ? trim($request->password) : status_return(401);
$email = isset($request->email) ? trim($request->email) : status_return(401);
$authenticationVerified = false;
$data = new stdClass();

try {
  global $conn;
  mysqli_check();

  $row_profile = get_tabel_info_single_row( 'profile', "email=?" , $email, 's' );

  if( !!$row_profile['profile_id'] && validate_pw($pass, $row_profile["password"]) ){
    $set_first_name = isset($row_profile['first_name']) ? (string)$row_profile['first_name'] : NULL;
    $set_last_name = isset($row_profile['last_name']) ? (string)$row_profile['last_name'] : NULL;
    $set_email = (string)$row_profile['email'];
    $set_profile_id = (string)$row_profile['profile_id'];

    $data->JWT = assignToken( $decoded->data->set_profile_id, $set_first_name, $set_last_name, $set_email );
    status_return(200);
    echo json_encode($data);
    return;
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