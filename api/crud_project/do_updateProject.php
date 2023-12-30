<?php
ob_start();
header("Access-Control-Allow-Headers: content-type");	

include_once('../_database/confi.php');
include_once('../_authorization/assignVerifyJWT.php');
include_once '../_general/status_returns.php';
include_once '../_general/functions.php';
include '../_crud/update.php';
include '../_crud/read.php';

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);

if( !isset($request->project_id) || !isset($request->token) ){
    // include '../_general/cors.php';
    die();
}

$project_name = isset($request->project_name) && strlen($request->project_name) > 0 ? trim($request->project_name) : false;
$description = isset($request->description) && strlen($request->description) > 0 ? trim($request->description) : false;
$short_description = isset($request->short_description) && strlen($request->short_description) > 0 ? trim($request->short_description) : false;
$project_status = isset($request->project_status) && strlen($request->project_status) > 0 ? trim($request->project_status) : false;
$looking_for = isset($request->looking_for) && strlen($request->looking_for) > 0 ? trim($request->looking_for) : false;
$image = isset($request->image) && strlen($request->image) > 0 ? trim($request->image) : false;

if( !$project_name || !$description || !$short_description || !$project_status || !$image ){
    $data->message = "form specifications not met";
    status_return(401);
    echo json_encode($data);
    return; 
}

$project_id = (int)$request->project_id;
$token = $request->token;
$data = new stdClass();

try {
    mysqli_check();
    global $conn;

    $pro_info = returnTokenProfileId($token);
    $profile_id = intval($pro_info->profile_id);

    $clauseArray = [ $project_id, $profile_id ];
    $row_project = get_tabel_info_single_row( 'projects', 'WHERE project_id=? AND owner_id=?', 'ii', $clauseArray );

    if( !!$row_project && !!$row_project["project_id"] ){
        $set = 'project_name=?, description=?, short_description=?, looking_for=?, project_status=?, image=? ';
        $clauseArray = [ $project_name, $description, $short_description, $looking_for, $project_status, $image, $project_id ];
        $return_update_projects = update_table( 'projects', $set, 'project_id', 'ssssssi', $clauseArray );

        $data->token = exchangeToken($token);
        $data->message = "project updated";
        status_return(200);
        echo json_encode($data);
        $conn->close();
        return;
    }else{
        $data->message = "page not found";
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