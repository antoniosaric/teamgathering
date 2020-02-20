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

if( !isset($request->team_id) || !isset($request->token) ){
    // include '../_general/cors.php';
    die();
}

$team_id = intval($request->team_id);
$pass = trim($request->password);
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

    if( validate_pw($pass, $row_profile_auth["password"]) ){

        $sql = "SELECT DISTINCT teams.team_id FROM teams LEFT JOIN projects ON projects.project_id = teams.project_id WHERE teams.team_id = ? AND projects.owner_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ii', $team_id, $profile_id);
        $result = $stmt->execute();
        $get_result = $stmt->get_result();
        $row = $get_result->fetch_assoc();
        $stmt->close();
    
        if( !!$row['team_id'] ){

            $set = 'profile_team_status=? ';
            $clauseArray = [ $deleted, $team_id ];
            $return_delete_profiles_team = update_table( 'profiles_team', $set, 'team_id', 'si', $clauseArray );

            if(!!$return_delete_profiles_team){

                $set = 'team_status=? ';
                $clauseArray = [ $deleted, $team_id ];
                $return_update_teams = update_table( 'teams', $set, 'team_id', 'si', $clauseArray );

                if( !!$return_delete_team ){
                    $data->token = exchangeToken($token);
                    $data->message = "team successfully deleted";
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
                $data->message = "something went wrong";
                status_return(400); 
                echo json_encode($data);
                $conn->close();
                return;  
            }
        }else{
            $data->message = "team not found";
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