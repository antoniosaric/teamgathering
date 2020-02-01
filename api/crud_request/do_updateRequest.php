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

if( !isset($request->request_id) || !isset($request->token) ){
    // include '../_general/cors.php';
    die();
}

$team_id = isset($request->team_id) && $request->team_id > 0 ? (int)$request->team_id : false;
$request_id = isset($request->request_id) && $request->request_id > 0 ? (int)$request->request_id : false;
$requester_id = isset($request->requester_id) && $request->requester_id > 0 ? (int)$request->requester_id : false;
$requestee_id = isset($request->requestee_id) && $request->requestee_id > 0 ? (int)$request->requestee_id : false;
$role = isset($request->role) && strlen($request->role) > 0 ? trim($request->role) : false;
$status = isset($request->status) && strlen($request->status) > 0 ? trim($request->status) : false;

if( !$request_id && !$role && !$status && !$team_id){
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

    $clauseArray = [ $request_id, $profile_id ];
    $row_request = get_tabel_info_single_row( 'requests', 'WHERE requests.request_id=? AND requests.requestee_id=?', 'ii', $clauseArray );

    if( !!$row_request && !!$row_request["request_id"] ){
        if($status == 'approved'){
            $status = 'active';
            $return_profiles_team_id = create_profiles_team( $requester_id, $team_id, $role, $status );

            if(!!$return_profiles_team_id){
                $status = 'approved';
                $set = 'request_status=?';
                $clauseArray = [ $status, $request_id ];
                $return_update_request = update_table( 'requests', $set, 'request_id', 'si', $clauseArray );

                $data->message = 'team member added';
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
            $set = 'status=?';
            $clauseArray = [ $status, $request_id ];
            $return_update_profiles = update_table( 'requests', $set, 'request_id', 'si', $clauseArray );

            $data->message = 'request updated';
            $data->token = exchangeToken($token);
            status_return(200);
            echo json_encode($data);
            $conn->close();
            return;
        }
    }else{
        $data->message = "request not found";
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


