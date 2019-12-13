<?php
ob_start();
header('Access-Control-Allow-Origin: http://localhost:4200', false);
include_once('../_database/confi.php');
include_once('../_authorization/assignVerifyJWT.php');
include '../_general/status_returns.php';
include '../_general/functions.php';
include '../_authorization/do_passwordHash.php';
include '../_crud/create.php';
include '../_crud/read.php';

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$email = isset($request->email) && strlen($request->email) > 0 ? trim(strtolower($request->email)) : status_return(401);
$pass = isset($request->password) && strlen($request->password) > 0 ? trim($request->password) : status_return(401);

$data = new stdClass();
$image = 'default.jpg';

try {
    mysqli_check();
    global $conn;

    $clauseArray = [$email];
    $row_profile = get_tabel_info_single_row( 'profiles', 'WHERE email=?', 's', $clauseArray );

    if( !isset( $row_profile["email"]) ){

        // generate the salt
        $salt=substr(base64_encode(openssl_random_pseudo_bytes(17)),0,22);
        $hash_password = generate_hash($password, 11, $salt);

        $return_profile_id = create_profile( $email , $hash_password, $salt, $image );
        $data->message = "profile created";
        status_return(200);
    }else{
        $data->message = "email already registered";
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