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
$team_id = (int)$request->team_id;
$pass = trim($request->password);
$token = $request->token;
$data = new stdClass();

try {
    mysqli_check();
    global $conn;

    $pro_info = returnTokenProfileId($token);
    $profile_id = intval($pro_info->profile_id);

    $clauseArray = [ $team_id, 'owner', $profile_id ];
    $row_team = get_tabel_info_single_row( 'teams', 'WHERE team_id=? AND ( role=? AND profile_id=? )', 'isi', $clauseArray );

    if( !!$row_team['team_id'] && validate_pw($pass, $row_team["password"]) ){
        $return_profile = delete_function( 'teams', 'team_id', $row_team['team_id']  );
        $data->message = "team deleted";
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