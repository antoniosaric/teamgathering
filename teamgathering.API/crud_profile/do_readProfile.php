<?php
ob_start();
header('Access-Control-Allow-Origin: http://localhost:4200', false);
include_once('../_database/confi.php');
include_once('../_authorization/assignVerifyJWT.php');
include '../_general/status_returns.php';
include '../_general/functions.php';
include '../_crud/read.php';

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$profile_id = (int)$request->profile_id;
$token = $request->token;

// $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NzYyMDMyNzksImp0aSI6IkxQTDM3RkN3d1IzOUp3ZnpBc3dPTEswQ1FpXC9FSzV2WU10NXdteDlvSEdVPSIsImlzcyI6Imh0dHBzOlwvXC93d3cudGVhbWdhdGhlcmluZy5jb20iLCJpYXVkc3MiOiJodHRwczpcL1wvdGVhbWdhdGhlcmluZy5jb20iLCJuYmYiOjE1NzYyMDMyNzksImV4cCI6MTU3ODYyMjQ3OSwiZGF0YSI6eyJwcm9maWxlX2lkIjoiMSIsImZpcnN0X25hbWUiOiJUb255IiwibGFzdF9uYW1lIjoiU2FyaWMiLCJlbWFpbCI6ImFAYS5jb20ifX0.FhrsKJ5FznokE0EXK3XM5JP3wzFLmBQxYjfUoIi7VWg";
$data = new stdClass();

try {
    mysqli_check();
    global $conn;

    $pro_info = returnTokenProfileId($token);
    $profile_id = $pro_info->profile_id;

    $clauseArray = [ $profile_id ];
    $row_profile = get_tabel_info_single_row( 'profiles', 'WHERE profile_id=?', 'i', $clauseArray );
    
    if( !!$row_profile['profile_id'] ){
        $profile = new stdClass(); 
        $profile->profile_id = $row_profile['profile_id'];
        $profile->email = $row_profile['email'];
        $profile->first_name = $row_profile['first_name'];
        $profile->last_name = $row_profile['last_name'];
        $profile->image = $row_profile['image'];
        $profile->created_date = $row_profile['created_date'];
        $data->profile = $profile;
        $data->message = "profile retrieved";
        status_return(200);
    }else{
        $data->message = "page not found";
        status_return(404); 
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