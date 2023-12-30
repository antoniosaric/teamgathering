<?php
ob_start();
header("Access-Control-Allow-Headers: content-type");			

include_once('../_database/confi.php');
include_once('../_authorization/assignVerifyJWT.php');
include_once '../_general/status_returns.php';
include_once '../_general/functions.php';
include '../_crud/read.php';
include '../_crud/update.php';
require '../_general/autoload.php';

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);

if( !isset($request->token) ){
    // include '../_general/cors.php';
    die();
}

$token = $request->token;
$image = $request->image;
$data = new stdClass();

try {
    mysqli_check();
    global $conn;

    $pro_info = returnTokenProfileId($token);
    $profile_id = intval($pro_info->profile_id);

    \Cloudinary::config(array(
        'cloud_name' => $cloud_name,
        'api_key' => $api_key,
        'api_secret' => $api_secret, 
        "secure" => true
    ));

    $files['uploaded_image'] = \Cloudinary\Uploader::upload($image, array('public_id' => 'profile_'.$profile_id ));
    // https://res.cloudinary.com/dqd4ouqyf/image/upload/v1578563539/profile_1.png
  	if ( !!$files['uploaded_image'] ){
        $return_update_profiles = false;
        $set = 'image=?';
        $image_url = "https://res.cloudinary.com/dqd4ouqyf/image/upload/v".$files['uploaded_image']['version']."/profile_".$profile_id;
        $clauseArray = [ $image_url, $profile_id ];
        $return_update_profiles = update_table( 'profiles', $set, 'profile_id', 'si', $clauseArray );

        if(!!$return_update_profiles){
            $data->message = "profile image saved";
            $data->image = $image_url;
            $data->token = exchangeToken($token);
            status_return(200);
            echo json_encode($data);
            return;
        }else{
            $data->message = "something went wrong";
            status_return(400);
            echo json_encode($data);
            return;
        }
	}else{
        $data->message = "something went wrong";
        status_return(400);
        echo json_encode($data);
	  	return;
	}
	$conn->close();
}catch (Exception $e ){
    status_return(500);
    echo($e->message);
    return;
}
?>