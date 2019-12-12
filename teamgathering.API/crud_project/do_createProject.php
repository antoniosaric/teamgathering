<?php
include_once('../_database/confi.php');
include_once('../_authorization/assignVerifyJWT.php');
include_once '../_general/status_returns.php';
include_once '../_general/functions.php';
include '../_authorization/do_passwordHash.php';
include '../_crud/create.php';
include '../_crud/read.php';

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$project_name = trim($request->project_name);
$description = trim($request->description);
$short_description = trim($request->short_description);
$project_status = trim($request->project_status);
$token = $request->token;
$image = 'default.jpg';
$data = new stdClass();

try {
    mysqli_check();
    global $conn;

    $pro_info = returnTokenProfileId($token);
    $profile_id = $pro_info->profile_id;

    $clauseArray = [ $team_id, $profile_id ];
    $row_project = get_tabel_info_single_row( 'projects', 'WHERE projects.project_name=? AND project.owner_id=?', 'si', $clauseArray );

    if( !isset( $row_project["email"]) ){

        $return_profile_id = create_project( $project_name, $description, $short_description, $project_status, $image, $profile_id );
        $data->message = "profile created";
        status_return(200);
    }else{
        $data->message = "email already registered";
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