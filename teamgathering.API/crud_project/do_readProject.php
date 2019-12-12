<?php
include_once('../_database/confi.php');
include_once('../_authorization/assignVerifyJWT.php');
include_once '../_general/status_returns.php';
include_once '../_general/functions.php';
include '../_crud_functions/read.php';

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$project_id = (int)$request->project_id;
$token = $request->token;
$data = new stdClass();

try {
    mysqli_check();
    global $conn;

    $pro_info = returnTokenProfileId($token);
    $profile_id = $pro_info->profile_id;

    $clauseArray = [ $project_id ];
    $row_project = get_tabel_info_single_row( 'projects', 'WHERE project_id=?', 'i', $clauseArray );

    if( !!$row_project ){
        var_dump($row_project);
        status_return(200); 
    }else{
        $data->message = "page not found";
        status_return(404); 
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