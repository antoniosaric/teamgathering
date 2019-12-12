<?php
include_once('../_database/confi.php');
include_once('../_authorization/assignVerifyJWT.php');
include_once '../_general/status_returns.php';
include_once '../_general/functions.php';
include '../_authorization/do_passwordHash.php';
include '../_crud_functions/delete.php';
include '../_crud_functions/read.php';

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$pass = trim($request->password);
$token = $request->token;
$data = new stdClass();

try {
    mysqli_check();
    global $conn;

    $pro_info = returnTokenProfileId($token);
    $profile_id = $pro_info->profile_id;

    $clauseArray = [$email];
    $row_profile = get_tabel_info_single_row( 'profiles', 'WHERE email=?', 's', $clauseArray );

    if( !!$row_profile['profile_id'] && validate_pw($pass, $row_profile["password"]) ){
        $return_profile = delete_function( 'profiles', 'profile_id', $period_id );
        $data->message = "profile deleted";
        status_return(200);
    }else{
        $data->message = "unauthorized";
        status_return(401); 
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