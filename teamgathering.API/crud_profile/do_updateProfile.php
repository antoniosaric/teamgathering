<?php
include_once('../_database/confi.php');
include_once('../_authorization/assignVerifyJWT.php');
include_once '../_general/status_returns.php';
include_once '../_general/functions.php';
include '../_crud_functions/read.php';
include '../_crud_functions/update.php';

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$first_name = trim($request->first_name);
$last_name = trim($request->last_name);
$token = $request->token;
$data = new stdClass();

try {
    mysqli_check();
    global $conn;

    $pro_info = returnTokenProfileId($token);
    $profile_id = $pro_info->profile_id;

    if( !!$profile_id ){
        $set = 'first_name=?, last_name=?';
        $clauseArray = [ $first_name, $last_name, $profile_id ];
        $return_update_profiles = update_table( 'profiles', $set, 'profile_id', 'ssi', $clauseArray );
        $data->message = "profile update";
        status_return(200);
    }else{
        $data->message = "profile not found";
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