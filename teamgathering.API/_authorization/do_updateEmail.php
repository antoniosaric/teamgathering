<?php
ob_start();
header('Access-Control-Allow-Origin: http://localhost:4200', false);
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

if( !isset($request->email) && !isset($request->password)  ){
    // include '../_general/cors.php';
    die();
}

$email = isset($request->email) && strlen($request->email) > 0 ? trim(strtolower($request->email)) : false;
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

        $clauseArray = [$email];
        $row_profile = get_tabel_info_single_row( 'profiles', 'WHERE email=?', 's', $clauseArray );

        if( !isset( $row_profile["email"]) ){
            $set = 'email=?';
            $clauseArray = [ $email, $profile_id ];
            $return_update_profiles = update_table( 'profiles', $set, 'profile_id', 'si', $clauseArray );
            $data->token = exchangeToken($token);
            $data->message = "email successfully changed";
            
            status_return(200);
            echo json_encode($data);
            return;
        }else{
            $data->message = "email already registered";
            echo json_encode($data);
            status_return(400);
            return;
        }
    }else{
        $data->message = "could not change email";
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