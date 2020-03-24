<?php
ob_start();
include_once('../_database/confi.php');
include_once('../_authorization/assignVerifyJWT.php');
include '../_general/status_returns.php';
include '../_general/functions.php';
include '../_authorization/do_passwordHash.php';
include '../_crud/create.php';
include '../_crud/read.php';


$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$email = isset($request->email) && strlen($request->email) > 0 ? trim(strtolower($request->email)) : false;
$password = isset($request->password) && strlen($request->password) > 0 ? trim($request->password) : false;
$zip_code = isset($request->zip_code) && strlen($request->zip_code) > 0 ? trim($request->zip_code) : false;
$first_name = isset($request->first_name) && strlen($request->first_name) > 0 ? trim($request->first_name) : false;
$last_name = isset($request->last_name) && strlen($request->last_name) > 0 ? trim($request->last_name) : false;

if( !$email && !$password && !$zip_code && !$first_name && !$last_name ){
    // include '../_general/cors.php';
    die();
}

$zip_url = "http://api.zippopotam.us/us/" . $zip_code;

$address_info = file_get_contents($zip_url);
$json = json_decode($address_info);

if(isset($json->places[0]->{'place name'})){
    $city = $json->places[0]->{'place name'};
    $state = $json->places[0]->{'state abbreviation'};
}else{
    $city = "";
    $state = "";
}

$data = new stdClass();
$image = 'https://res.cloudinary.com/dqd4ouqyf/image/upload/v1578601905/default_user.png';

if( !$email ){
    $data->message = "email specifications not met";
    status_return(401);
    echo json_encode($data);
    return;
}
if( !$password ){
    $data->message = "password specifications not met";
    status_return(401);
    echo json_encode($data);
    return;
}

if( !$zip_code || !$first_name || !$last_name ){
    $data->message = "form specifications not met";
    status_return(401);
    echo json_encode($data);
    return; 
}

try {
    mysqli_check();
    global $conn;

    $clauseArray = [$email];
    $row_profile = get_tabel_info_single_row( 'profiles', 'WHERE email=?', 's', $clauseArray );

    if( !isset( $row_profile["email"]) ){

        $hash_password_object = generate_hash( $password );

        $return_profile_id = create_profile( $email, $hash_password_object->hash_pass, $hash_password_object->salt, $image, $zip_code, $city, $state, $first_name, $last_name );

        if(!!$return_profile_id){
            $data->message = "profile created";
            echo json_encode($data);
            status_return(200);
        }else{
            $data->message = "something went wrong";
            echo json_encode($data);
            status_return(400);
            return;
        }
    }else{
        $data->message = "email already registered";
        echo json_encode($data);
        status_return(400);
        return;
    }
    $conn->close();
}catch (Exception $e ){
    status_return(500);
    echo($e->message);
    return;
}
?>