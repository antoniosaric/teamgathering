<?php
ob_start();
header("Access-Control-Allow-Headers: content-type");	
include_once('../_database/confi.php');
include_once('../_authorization/assignVerifyJWT.php');
include_once '../_general/status_returns.php';
include_once '../_general/functions.php';
include '../_crud/create.php';
include '../_crud/read.php';

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);

if( !isset($request->project_id) || !isset($request->token) ){
    // include '../_general/cors.php';
    die();
}

$project_id = isset($request->project_id) && strlen($request->project_id) > 0 ? intval($request->project_id) : false;
if( !$project_id ){
    $data->message = "form specifications not met";
    status_return(401);
    echo json_encode($data);
    return; 
}

$token = $request->token;
$data = new stdClass();

try {
    mysqli_check();
    global $conn;

    $pro_info = returnTokenProfileId($token);
    $profile_id = intval($pro_info->profile_id);

    $pending = "pending";
    $sql_request = "SELECT DISTINCT * FROM `requests`
    WHERE requests.requester_id=? AND requests.project_id=? AND requests.request_status = ?";     
      
    $stmt_request = $conn->prepare($sql_request);
    $stmt_request->bind_param("iis", $profile_id, $project_id, $pending);
    $stmt_request->execute();
    $result_request = $stmt_request->get_result();
    $stmt_request->close();

    if( !!$result_request && $result_request->num_rows == 0){

        $clauseArray = [ $project_id ];
        $row_request = get_tabel_info_single_row( 'projects', 'WHERE projects.project_id=?', 'i', $clauseArray );    

        if(!!$row_request && !!isset($row_request['owner_id']) ){
            $message = '';
            $return_request_id = create_request( $profile_id, $row_request['owner_id'], $project_id, $pending, $message );
            if(!!$return_request_id){
                $data->token = exchangeToken($token);
                $data->message = "project updated";
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
            $data->message = "project not found";
            status_return(400); 
            echo json_encode($data);
            $conn->close();
            return;   
        }
    }else{
        $data->message = "request already exists";
        status_return(401); 
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


