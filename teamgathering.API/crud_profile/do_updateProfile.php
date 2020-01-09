<?php
// header('Access-Control-Allow-Origin: http://localhost:4200', false);
include_once('../_database/confi.php');
include_once('../_authorization/assignVerifyJWT.php');
include_once '../_general/status_returns.php';
include_once '../_general/functions.php';
include '../_crud/read.php';
include '../_crud/update.php';

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$first_name = trim($request->first_name);
$last_name = trim($request->last_name);
$looking_for = trim($request->looking_for);
$description = trim($request->description);

// $first_name = 'bob';
// $last_name = 'charlie';
// $looking_for = 'stuff';
// $description = 'default description';

// $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NzgyNTY2NjIsImp0aSI6ImtOWFhmREZcL1B2UFJvQW9jcFZLWkRyaVRjRE1QQUQyZzNZYVlNVU12NTNZPSIsImlzcyI6Imh0dHBzOlwvXC93d3cudGVhbWdhdGhlcmluZy5jb20iLCJpYXVkc3MiOiJodHRwczpcL1wvdGVhbWdhdGhlcmluZy5jb20iLCJuYmYiOjE1NzgyNTY2NjIsImV4cCI6MTU4MDY3NTg2MiwiZGF0YSI6eyJwcm9maWxlX2lkIjoiMSIsImZpcnN0X25hbWUiOiJUb255IiwibGFzdF9uYW1lIjoiU2FyaWMiLCJlbWFpbCI6ImFAYS5jb20ifX0.siptu0-B_EYr02nh7ACumXdtTlxrCTQvLTZsHQHXg3g';

if( !isset($request->token) ){
    include '../_general/cors.php';
    die();
}

$token = $request->token;
$data = new stdClass();

try {
    mysqli_check();
    global $conn;

    $pro_info = returnTokenProfileId($token);
    $profile_id = $pro_info->profile_id;

    if( !!$profile_id ){
        $set = 'first_name=?, last_name=?, description=?, looking_for=?';
        $clauseArray = [ $first_name, $last_name, $description, $looking_for, $profile_id ];
        $return_update_profiles = update_table( 'profiles', $set, 'profile_id', 'ssssi', $clauseArray );
        $data->message = "profile update";
        status_return(200);
    }else{
        $data->message = "profile not found";
        status_return(400); 
    }
    echo json_encode($data);
    $conn->close();
    return;
}catch (Exception $e ){
    status_return(500);
    echo($e->message);
    return;
}
?>