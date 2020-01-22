<?php
ob_start();
include_once('../_database/confi.php');
include_once('../_authorization/assignVerifyJWT.php');
include_once '../_general/status_returns.php';
include_once '../_general/functions.php';
include '../_crud/update.php';
include '../_crud/read.php';

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$requested_profile_id = (int)$request->requested_profile_id;
$team_id = (int)$request->team_id;
$role = trim($request->role);
$profile_team_status = trim($request->profile_team_status);
$token = $request->token;
$data = new stdClass();

try {
    mysqli_check();
    global $conn;

    $pro_info = returnTokenProfileId($token);
    $profile_id = intval($pro_info->profile_id);

    $sql = "SELECT DISTINCT profiles_team.profile_id AS profile_id, profiles_team.profiles_team_id AS profile_team_id FROM profiles_team WHERE profiles_team.team_id = ? AND ( profiles_team.profile_id = ? OR profiles_team.profile_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param( 'iii', $team_id, $requested_profile_id, $profile_id );
    $result = $stmt->execute();
    $get_result = $stmt->get_result();
    $row = $get_result->fetch_assoc();
    $stmt->close();

    if( !!$result && !!$row["profiles_team_id"] ){
        $set = 'profile_id=?, team_id=?, role=?, profile_team_status=? ';
        $clauseArray = [ $requested_profile_id , $team_id, $role, $profile_team_status, $row["profiles_team_id"] ];
        $return_update_profiles_team = update_table( 'profiles_team', $set, 'profiles_team_id', 'iissi', $clauseArray );
        $data->message = "profiles team association updated";
        status_return(200);
    }else{
        $data->message = "association not found";
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