<?php
ob_start();
include_once('../_database/confi.php');
include_once('../_authorization/assignVerifyJWT.php');
include '../_general/status_returns.php';
include '../_general/functions.php';
include '../_crud/update.php';

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);

if( !isset($request->token) ){
    // include '../_general/cors.php';
    die();
}

$token = $request->token;
$data = new stdClass();
$status = new stdClass();

try {
    mysqli_check();
    global $conn;

    $pro_info = returnTokenProfileId($token);
    $profile_id = intval($pro_info->profile_id);
  
    $sql = "SELECT DISTINCT * FROM messages WHERE IsRead = false AND recipient_id = ".$profile_id;

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
  
    if(!!$result && $result->num_rows > 0){  
        $status->message = true;
    }else{
        $status->message = false;
    }

    $sql2 = "SELECT DISTINCT * FROM requests WHERE request_status = 'pending' AND requestee_id = ".$profile_id;

    $stmt2 = $conn->prepare($sql2);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    $stmt2->close();
  
    if(!!$result2 && $result2->num_rows > 0){  
        $status->requests = true;
    }else{
        $status->requests = false;
    }


    $data->status = $status;
    $data->message = "status pulled";
    status_return(200);
    echo json_encode($data);
    $conn->close();
    return;
}catch (Exception $e ){
    status_return(500);
    echo($e->message);
    return;
}


?>