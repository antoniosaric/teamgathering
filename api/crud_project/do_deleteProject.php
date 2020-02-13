<?php
ob_start();
include_once('../_database/confi.php');
include_once('../_authorization/assignVerifyJWT.php');
include_once '../_general/status_returns.php';
include_once '../_general/functions.php';
include '../_authorization/do_passwordHash.php';
include '../_crud/delete.php';
include '../_crud/read.php';

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$project_id = (int)$request->project_id;
$pass = trim($request->password);
$token = $request->token;
$data = new stdClass();

try {
    mysqli_check();
    global $conn;

    $pro_info = returnTokenProfileId($token);
    $profile_id = intval($pro_info->profile_id);

    $clauseArray = [ $project_id, $profile_id ];
    $row_project = get_tabel_info_single_row( 'projects', 'WHERE project_id=? AND owner_id=?', 'ii', $clauseArray );

    if( !!$row_project['project_id'] && validate_pw($pass, $row_project["password"]) ){
        $return_delete_project = delete_function( 'projects', 'project_id', $row_project['project_id']  );
        $data->token = exchangeToken($token);
        $data->message = "project deleted";
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