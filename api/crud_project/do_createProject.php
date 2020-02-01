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

if( !isset($request->project_name) || !isset($request->token) ){
    // include '../_general/cors.php';
    die();
}

$project_name = isset($request->project_name) && strlen($request->project_name) > 0 ? trim($request->project_name) : false;
$description = isset($request->description) && strlen($request->description) > 0 ? trim($request->description) : false;
$short_description = isset($request->short_description) && strlen($request->short_description) > 0 ? trim($request->short_description) : false;
$project_status = isset($request->project_status) && strlen($request->project_status) > 0 ? trim($request->project_status) : false;

if( !$project_name || !$description || !$short_description || !$project_status ){
    $data->message = "form specifications not met";
    status_return(401);
    echo json_encode($data);
    return; 
}

$token = $request->token;
$data = new stdClass();
$image = "https://res.cloudinary.com/dqd4ouqyf/image/upload/v1579289397/default_project.jpg";

try {
    mysqli_check();
    global $conn;

    $pro_info = returnTokenProfileId($token);
    $profile_id = intval($pro_info->profile_id);

    $clauseArray = [ $project_name, $profile_id ];
    $row_project = get_tabel_info_single_row( 'projects', 'WHERE projects.project_name=? AND projects.owner_id=?', 'si', $clauseArray );

    if( !$row_project && !$row_project["project_id"] ){
        $return_profile_id = create_project( $project_name, $description, $short_description, $project_status, $image, $profile_id );
        $data->new_project_id = $return_profile_id;
        $data->token = exchangeToken($token);
        $data->message = "project updated";
        status_return(200);
        echo json_encode($data);
        $conn->close();
        return;
    }else{
        $data->message = "project already exists";
        status_return(401); 
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