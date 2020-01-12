<?php
// header('Access-Control-Allow-Origin: http://localhost:4200', false);
include_once('../_database/confi.php');
include_once('../_authorization/assignVerifyJWT.php');
include_once '../_general/status_returns.php';
include_once '../_general/functions.php';
include '../_crud/read.php';
include '../_crud/update.php';

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);

if( !isset($request->token) ){
    include '../_general/cors.php';
    die();
}
$zip_code = isset($request->zip_code) && strlen($request->zip_code) > 0 ? trim($request->zip_code) : false;
$first_name = isset($request->first_name) && strlen($request->first_name) > 0 ? trim($request->first_name) : false;
$last_name = isset($request->last_name) && strlen($request->last_name) > 0 ? trim($request->last_name) : false;

if( !$zip_code || !$first_name || !$last_name ){
    $data->message = "form specifications not met";
    status_return(401);
    echo json_encode($data);
    return; 
}

$looking_for = trim($request->looking_for);
$description = trim($request->description);

$zip_url = "http://api.zippopotam.us/us/" . (int)$zip_code;

$address_info = file_get_contents($zip_url);
$json = json_decode($address_info);

$city = $json->places[0]->{'place name'};
$state = $json->places[0]->{'state abbreviation'};
$token = $request->token;

$data = new stdClass();

try {
    mysqli_check();
    global $conn;

    $pro_info = returnTokenProfileId($token);
    $profile_id = $pro_info->profile_id;

    if( !!$profile_id ){
        $set = 'first_name=?, last_name=?, description=?, looking_for=?, zip_code=?, city=?, state=?';
        $clauseArray = [ $first_name, $last_name, $description, $looking_for, $zip_code, $city, $state, $profile_id ];
        $return_update_profiles = update_table( 'profiles', $set, 'profile_id', 'sssssssi', $clauseArray );
        $data->token = exchangeToken($token);
        $data->message = "profile update";
        status_return(200);
        echo json_encode($data);
        $conn->close();
        return;
    }else{
        $data->message = "profile not found";
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