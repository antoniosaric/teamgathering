<?php
ob_start();
header("Access-Control-Allow-Headers: content-type");	
include_once('../_database/confi.php');
include_once('../_authorization/assignVerifyJWT.php');
include_once '../_general/status_returns.php';
include_once '../_general/functions.php';
include '../_crud/delete.php';
include '../_crud/update.php';
include '../_crud/read.php';

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);

if( !isset($request->team_id) || !isset($request->token) ){
    // include '../_general/cors.php';
    die();
}

$team_id = intval($request->team_id);
$token = $request->token;
$data = new stdClass();
$deleted = 'deleted';
$owner = 'owner';

try {
    mysqli_check();
    global $conn;

    $pro_info = returnTokenProfileId($token);
    $profile_id = intval($pro_info->profile_id);

    $set = 'profile_team_status=? ';
    $clauseArray = [ $deleted, $profile_id, $team_id, $owner ];
    $return_delete_profiles_team = update_table_multiple_targets( 'profiles_team', $set, 'profile_id = ? AND team_id = ? AND role != ? ', 'siis', $clauseArray );

    if(!!$return_delete_profiles_team){
        $data->token = exchangeToken($token);
        $data->message = "profile removed from team";
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
}catch (Exception $e ){
    status_return(500);
    echo($e->message);
    return;
}
?>