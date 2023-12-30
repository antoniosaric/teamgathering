<?php
ob_start();
header("Access-Control-Allow-Headers: content-type");	
include_once('../_database/confi.php');
include_once('../_authorization/assignVerifyJWT.php');
include_once '../_general/status_returns.php';
include_once '../_general/functions.php';
include '../_crud/create.php';
include '../_crud/update.php';
include '../_crud/read.php';

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);

if( !isset($request->token) ){
    // include '../_general/cors.php';
    die();
}
$recipient_id = intval($request->recipient_id);
$message = trim($request->message);
$token = $request->token;
$data = new stdClass();

try {
    mysqli_check();
    global $conn;

    $pro_info = returnTokenProfileId($token);
    $profile_id = intval($pro_info->profile_id);

    $return_message_id = create_message( $profile_id, $recipient_id, $message, 0 );

    if(!!$return_message_id){
        $data->message = "message created";
        $data->token = exchangeToken($token);
        status_return(200); 
        echo json_encode($data);
        $conn->close();
        return; 
    }else{
        $data->message = "something went wrong";
        status_return(400); 
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