<?php
ob_start();
header("Access-Control-Allow-Headers: content-type");	
include_once('../_database/confi.php');
include_once('../_authorization/assignVerifyJWT.php');
include_once '../_general/status_returns.php';
include_once '../_general/functions.php';
include '../_crud/create.php';
include '../_crud/read.php';

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);

if( !isset($request->token) ){
    // include '../_general/cors.php';
    die();
}

$token = $request->token;

$data = new stdClass();
$tag_array = [];

try {
    mysqli_check();
    global $conn;

    $pro_info = returnTokenProfileId($token);
    $profile_id = intval($pro_info->profile_id);

    $sql = "SELECT DISTINCT tag_name FROM tags "; 
	$stmt = $conn->prepare($sql);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        while( $row = $result->fetch_assoc() ){
            if( !in_array( $row, $tag_array ) ){                   
                array_push( $tag_array, $row );
            }
        }
        $data->tags = $tag_array;
        $data->message = "tags found";
        status_return(200);
        echo json_encode($data);
        $stmt->close();
        return;
    } else {
        $data->message = "tags not found";
        status_return(400); 
        echo json_encode($data);
        $stmt->close();
        return;
    }
    $conn->close();
}catch (Exception $e ){
    status_return(500);
    echo($e->message);
    return;
}
?>