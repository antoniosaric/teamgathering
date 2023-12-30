<?php
ob_start();
include_once('../_database/confi.php');
include_once('assignVerifyJWT.php');
include '../_general/status_returns.php';
include '../_general/functions.php';
include 'do_passwordHash.php';
include '../_crud/update.php';
include '../_crud/read.php';

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$secret_key = isset($request->key) && strlen($request->key) > 0 ? trim(strtolower($request->key)) : false;
$password = isset($request->password) && strlen($request->password) > 0 ? trim($request->password) : false;

$data = new stdClass();
$empty = '';

if(!$secret_key && !$password ){
  die();
}

try {
  global $conn;
  mysqli_check();

  $hash_password_object = generate_hash( $password );
  $set = 'secret_key=?, password=?, salt=?';
  $clauseArray = [ $empty, $hash_password_object->hash_pass, $hash_password_object->salt, $secret_key ];
  $return_password = update_table( 'profiles', $set, 'secret_key', 'ssss', $clauseArray );

  if( !!$return_password ){
    $data->message = "password reset";
    status_return(200);
    echo json_encode($data);
    $conn->close();
    return;
  }else{
    $data->message = "incorrect info";
    status_return(401);
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