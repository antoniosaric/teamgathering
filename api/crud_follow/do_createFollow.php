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

if( !isset($request->project_id) || !isset($request->token) ){
    // include '../_general/cors.php';
    die();
}
$project_id = trim($request->project_id);
$token = $request->token;
$data = new stdClass();

try {
    mysqli_check();
    global $conn;

    $pro_info = returnTokenProfileId($token);
    $profile_id = intval($pro_info->profile_id);

    $clauseArray = [ $project_id, $profile_id ];

    $row_follow = get_tabel_info_single_row( 'follows', 'WHERE project_id = ? AND profile_id = ? ' , 'ii', $clauseArray );

    if( !isset( $row_follow["follow_id"]) ){

        $return_follow_id = create_follow( $profile_id , $project_id );

        if(!!$return_follow_id){
            $data->message = "now following project";
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
        $data->message = "already following project";
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