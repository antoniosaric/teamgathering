<?php
ob_start();
header("Access-Control-Allow-Headers: content-type");	
include_once('../_database/confi.php');
include_once('assignVerifyJWT.php');
include '../_general/status_returns.php';
include '../_general/functions.php';
include 'do_passwordHash.php';
include '../_crud/update.php';
include '../_crud/read.php';

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);

if( !isset($request->newPassword) && !isset($request->token) && !isset($request->password) ){
    // include '../_general/cors.php';
    die();
}

$newPassword = isset($request->newPassword) && strlen($request->newPassword) > 0 ? trim($request->newPassword) : false;
$password = isset($request->password) && strlen($request->password) > 0 ? trim($request->password) : false;
$token = isset($request->token) && strlen($request->token) > 0 ? trim($request->token) : false;

$data = new stdClass();

try {
    global $conn;
    mysqli_check();

    $pro_info = returnTokenProfileId($token);
    $profile_id = $pro_info->profile_id;
 
    $clauseArray = [ $profile_id ];
    $row_profile = get_tabel_info_single_row( 'profiles', 'WHERE profile_id=?', 'i', $clauseArray );

    if( !!$row_profile['profile_id'] && validate_pw($password, $row_profile["password"]) ){

        $hash_password_object = generate_hash( $newPassword );

        $set = 'password=?, salt=?';
        $clauseArray = [ $hash_password_object->hash_pass, $hash_password_object->salt, $profile_id ];
        $return_update_profiles = update_table( 'profiles', $set, 'profile_id', 'ssi', $clauseArray );
        $data->token = exchangeToken($token);
        $data->message = "password successfully changed";
        
        status_return(200);
        echo json_encode($data);
        return;
    }else{
        $data->message = "could not change password";
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