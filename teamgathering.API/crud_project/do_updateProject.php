<?php
include_once('../_database/confi.php');
include_once('../_authorization/assignVerifyJWT.php');
include_once '../_general/status_returns.php';
include_once '../_general/functions.php';
include '../_crud/update.php';
include '../_crud/read.php';

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$project_id = (int)$request->project_id;
$project_name = trim($request->project_name);
$description = trim($request->description);
$short_description = trim($request->short_description);
$project_status = trim($request->project_status);
$image = trim($request->image);
$token = $request->token;
$data = new stdClass();

try {
    mysqli_check();
    global $conn;

    $pro_info = returnTokenProfileId($token);
    $profile_id = $pro_info->profile_id;

    $clauseArray = [ $project_id, $profile_id ];
    $row_project = get_tabel_info_single_row( 'projects', 'WHERE project_id=? AND owner_id=?', 'ii', $clauseArray );

    if( !!$row_project && !!$row_project["project_id"] ){
        $set = '$project_name=?, $description=?, $short_description=?, $project_status=?, $image=? ';
        $clauseArray = [ $project_name, $description, $short_description, $project_status, $image, $row_project["project_id"] ];
        $return_update_projects = update_table( 'projects', $set, 'project_id', 'sssssi', $clauseArray );
        $data->message = "project updated";
        status_return(200);
    }else{
        $data->message = "project not found";
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