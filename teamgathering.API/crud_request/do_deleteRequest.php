<?php
ob_start();
header('Access-Control-Allow-Origin: http://localhost:4200', false);
header("Access-Control-Allow-Headers: content-type");	
include_once('../_database/confi.php');
include_once('../_authorization/assignVerifyJWT.php');
include_once '../_general/status_returns.php';
include_once '../_general/functions.php';
include '../_crud/read.php';
include '../_crud/delete.php';

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);

if( !isset($request->request_id) || !isset($request->token) ){
    // include '../_general/cors.php';
    die();
}

$request_id = isset($request->request_id) && $request->request_id > 0 ? (int)$request->request_id : false;

if( !$request_id ){
    $data->message = "form specifications not met";
    status_return(401);
    echo json_encode($data);
    return; 
}

$token = $request->token;
$data = new stdClass();

try {
    mysqli_check();
    global $conn;

    $pro_info = returnTokenProfileId($token);
    $profile_id = intval($pro_info->profile_id);

    $clauseArray = [ $request_id, $profile_id, $profile_id ];
    $row_request = get_tabel_info_single_row( 'requests', 'WHERE requests.request_id=? AND ( requests.requestee_id=? OR requests.requester_id=? )', 'iii', $clauseArray );

    if( !!$row_request && !!$row_request["request_id"] ){

        $delete_request_return = delete_function( 'requests', 'request_id', $request_id );

        if(!!$delete_request_return){
            $data->message = 'request deleted';
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
    }else{
        $data->message = "request not found";
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
