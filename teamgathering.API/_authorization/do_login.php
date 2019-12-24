<?php
ob_start();
include_once('../_database/confi.php');
include_once('assignVerifyJWT.php');
include '../_general/status_returns.php';
include '../_general/functions.php';
include 'do_passwordHash.php';
include '../_crud/create.php';
include '../_crud/read.php';

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$email = trim(strtolower($request->email));
$pass = trim($request->password);
$data = new stdClass();

if(!$email && !$pass ){
  include '../_general/cors.php';
  die();
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

    $data->message = "logged in";
    $data->JWT = assignToken( $set_profile_id, $set_first_name, $set_last_name, $set_email );
    status_return(200);
    echo json_encode($data);
    return;
  }else{
    $data->message = "incorrect login info";
    status_return(401);
    echo json_encode($data);
    return;
  }
  $conn->close();
}catch (Exception $e ){
    status_return(500);
    echo($e->message);
    return;
}
?>