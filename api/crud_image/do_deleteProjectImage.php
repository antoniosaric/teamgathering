<?php
ob_start();
include_once('../_database/confi.php');
include_once('../_authorization/assignVerifyJWT.php');
include_once '../_general/status_returns.php';
include_once '../_general/functions.php';
include '../_crud/read.php';
include '../_crud/update.php';
require '../_general/autoload.php';

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);

if( !isset($request->token) || !isset($request->project_id)){
    // include '../_general/cors.php';
    die();
}

$project_id = $request->project_id;
$token = $request->token;
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

    $files['deleted_image'] = \Cloudinary\Uploader::destroy('project_'.$project_id );
    
  	if ( $files['deleted_image']['result'] == "ok" ){
        $return_update_projects = false;
        $set = 'image=?';
        $image_url = "https://res.cloudinary.com/dqd4ouqyf/image/upload/v1579289397/default_project.jpg";
        $clauseArray = [ $image_url, $project_id ];
        $return_update_projects = update_table( 'projects', $set, 'project_id', 'si', $clauseArray );

        if(!!$return_update_projects){
            $data->message = "project image deleted";
            $data->image = $image_url;
            $data->token = exchangeToken($request->token);
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