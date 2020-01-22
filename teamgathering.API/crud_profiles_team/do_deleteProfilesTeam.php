<?php 
include_once('../_database/confi.php');
include_once('../_authorization/assignVerifyJWT.php');
include_once('../_general/status_returns.php');
include_once('../_general/functions.php');
include '../_crud/delete.php';

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$requested_profile_id = (int)$request->requested_profile_id;
$team_id = (int)$request->team_id;
$token = $request->token;
$data = new stdClass();

try {
    mysqli_check();
    global $conn;

    $pro_info = returnTokenProfileId($token);
    $profile_id = intval($pro_info->profile_id);

    //not sure if deleteing profiles_team should be allowed.

    echo json_encode($data);
    $conn->close();
    return;
}catch (Exception $e ){
    status_return(500);
    echo($e->message);
    return;
}
?>