<?php
ob_start();
header('Access-Control-Allow-Origin: http://localhost:4200', false);
include_once('../_database/confi.php');
include_once('assignVerifyJWT.php');
include '../_general/status_returns.php';
include '../_general/functions.php';
include 'do_passwordHash.php';
include '../_crud/read.php';

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$data = new stdClass();

if( isset( $request->email ) && ( strlen($request->email) > 4 && strlen( $request->email ) < 255 ) ){
  $email = trim(strtolower($request->email));
}else{
  $data->message = "email specifications not met";
  status_return(401);
  echo json_encode($data);
  return;
}
if( isset( $request->password ) && ( strlen( $request->password ) > 0 && strlen( $request->password ) < 24 ) ){
  $pass = trim($request->password);
}else{
  $data->message = "password specifications not met";
  status_return(401);
  echo json_encode($data);
  return;
}

// $pass = 'asdf';
// $email = 'a@a.com';
$authenticationVerified = false;

try {
  global $conn;
  mysqli_check();
 
  $clauseArray = [ $email ];
  $row_profile = get_tabel_info_single_row( 'profiles', 'WHERE email=?', 's', $clauseArray );

  if( !!$row_profile['profile_id'] && validate_pw($pass, $row_profile["password"]) ){
    $set_first_name = isset($row_profile['first_name']) ? (string)$row_profile['first_name'] : NULL;
    $set_last_name = isset($row_profile['last_name']) ? (string)$row_profile['last_name'] : NULL;
    $set_email = (string)$row_profile['email'];
    $set_profile_id = (string)$row_profile['profile_id'];

    $data->JWT = assignToken( $set_profile_id, $set_first_name, $set_last_name, $set_email );
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