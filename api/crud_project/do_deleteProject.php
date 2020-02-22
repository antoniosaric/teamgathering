<?php
ob_start();
header("Access-Control-Allow-Headers: content-type");	
include_once('../_database/confi.php');
include_once('../_authorization/assignVerifyJWT.php');
include_once '../_general/status_returns.php';
include_once '../_general/functions.php';
include '../_authorization/do_passwordHash.php';
include '../_crud/delete.php';
include '../_crud/update.php';
include '../_crud/read.php';

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);

if( !isset($request->project_id) || !isset($request->token) ){
    // include '../_general/cors.php';
    die();
}

$project_id = intval($request->project_id);
$password = trim($request->password);
$token = $request->token;
$data = new stdClass();
$deleted = 'deleted';

try {
    mysqli_check();
    global $conn;

    $pro_info = returnTokenProfileId($token);
    $profile_id = intval($pro_info->profile_id);

    $clauseArray = [ $profile_id ];
    $row_profile_auth = get_tabel_info_single_row( 'profiles', 'WHERE profile_id=? ', 'i', $clauseArray );

    if( !!$row_profile_auth['profile_id'] && validate_pw($password, $row_profile_auth["password"]) ){

        $sql = "UPDATE projects SET project_status=? WHERE project_id = ( SELECT DISTINCT projects.project_id FROM projects WHERE projects.project_id = ? AND projects.owner_id = ? )";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sii', $deleted, $project_id, $profile_id);

        if($stmt->execute()){
            if( intval($stmt->affected_rows) > 0 ){

                $set = 'team_status=? ';
                $clauseArray = [ $deleted, $project_id ];
                $return_delete_team = update_table( 'teams', $set, 'project_id', 'si', $clauseArray );

                if(!!$return_delete_team){

                    $sql2 = "SELECT DISTINCT team_id FROM teams WHERE project_id = ? ";
                    $stmt2 = $conn->prepare($sql2);
                    $stmt2->bind_param('i', $project_id);
    
                    if($stmt2->execute()){
                        $result2 = $stmt2->get_result();
                        if(!!$result2 && $result2->num_rows > 0){  
                            while( $row2 = $result2->fetch_assoc() ){
                                $set = 'profile_team_status=? ';
                                $clauseArray = [ $deleted, $row2['team_id'] ];
                                $return_delete_profiles_team = update_table( 'profiles_team', $set, 'team_id', 'si', $clauseArray );
                            }
                        }
                        $data->token = exchangeToken($token);
                        $data->message = "project deleted successfully";
                        status_return(200); 
                        echo json_encode($data);
                        $conn->close();
                        return;  
                    }else{
                        $data->message = "something went wrong";
                        status_return(500); 
                        echo json_encode($data);
                        $conn->close();
                        return;         
                    }
                }else{
                    $data->message = "something went wrong";
                    status_return(500); 
                    echo json_encode($data);
                    $conn->close();
                    return;         
                }
            }else{
                $data->message = "something went wrong";
                status_return(400); 
                echo json_encode($data);
                $conn->close();
                return;         
            }
        }else{
            $data->message = "something went wrong";
            status_return(400); 
            echo json_encode($data);
            $conn->close();
            return;      
        }
    }else{
        $data->message = "unathorized";
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