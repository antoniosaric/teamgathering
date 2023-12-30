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

if( !isset($request->profile_id) || !isset($request->token) ){
    // include '../_general/cors.php';
    die();
}

$delete_profile_id = intval($request->profile_id);
$profiles_team_id = intval($request->profiles_team_id);
$token = $request->token;
$data = new stdClass();
$deleted = 'deleted';

try {
    mysqli_check();
    global $conn;

    $pro_info = returnTokenProfileId($token);
    $profile_id = intval($pro_info->profile_id);

    if($profile_id != $delete_profile_id ){

        $clauseArray = [ $profiles_team_id, $delete_profile_id ];
        $row_request = get_tabel_info_single_row( 'profiles_team', 'WHERE profiles_team_id=? AND profile_id=?', 'ii', $clauseArray ); 

        if( !!$row_request['profiles_team_id'] ){

            $set = 'profile_team_status=? ';
            $clauseArray = [ $deleted, $profiles_team_id ];
            $return_delete_profiles_team = update_table( 'profiles_team', $set, 'profiles_team_id', 'si', $clauseArray );

            if(!!$return_delete_profiles_team){
                // $return_delete_profiles_team = delete_function('profiles_team', 'profiles_team_id', $profiles_team_id );
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
        }else{
            $data->message = "team profile association not found";
            status_return(400); 
            echo json_encode($data);
            $conn->close();
            return;         
        }
    }else{
        $data->message = "cannot delete owner";
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