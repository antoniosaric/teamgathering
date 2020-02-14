<?php
ob_start();
header("Access-Control-Allow-Headers: content-type");	
include_once('../_database/confi.php');
include_once('../_authorization/assignVerifyJWT.php');
include_once '../_general/status_returns.php';
include_once '../_general/functions.php';
include '../_crud/create.php';
include '../_crud/update.php';
include '../_crud/read.php';

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);

if( !isset($request->tag_name) || !isset($request->token) ){
    // include '../_general/cors.php';
    die();
}
$tag_name = trim($request->tag_name);
$token = $request->token;
$project_id = intval($request->project_id);

$data = new stdClass();

try {
    mysqli_check();
    global $conn;

    $pro_info = returnTokenProfileId($token);
    $profile_id = intval($pro_info->profile_id);

    $clauseArray = [ strtolower($tag_name) ];

    $row_tag = get_tabel_info_single_row( 'tags', 'WHERE LOWER( tag_name ) = ?', 's', $clauseArray );

    if( !!isset( $row_tag["tag_id"]) ){

        $clauseArray = [ $row_tag["tag_id"], $project_id ];
        $row_project_tag = get_tabel_info_single_row( 'projects_tag', 'WHERE tag_id=? AND project_id=?', 'ii', $clauseArray );

        if(!isset($row_project_tag['projects_tag_id'])){

            $return_project_tag_id = create_projects_tag( $project_id , $row_tag["tag_id"] );

            if(!!$return_project_tag_id){
                $data->message = "tag added to project";
                $data->tag_id = $row_tag["tag_id"];
                $data->token = exchangeToken($token);
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
        }else{
            $data->message = "tag already on project";
            status_return(400); 
            echo json_encode($data);
            $conn->close();
            return;
        }
    }else{

        $return_tag_id = create_tag( $tag_name );
        $return_project_tag_id = create_projects_tag( $project_id , $return_tag_id );

        if(!!$return_project_tag_id){
            $data->tag_id = $return_tag_id;
            $data->token = exchangeToken($token);
            $data->message = "tag added to project";
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
    }
}catch (Exception $e ){
    status_return(500);
    echo($e->message);
    return;
}
?>