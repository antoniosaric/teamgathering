<?php
ob_start();
header('Access-Control-Allow-Origin: http://localhost:4200', false);
header("Access-Control-Allow-Headers: content-type");	
include_once('../_database/confi.php');
include_once('../_authorization/assignVerifyJWT.php');
include_once '../_general/status_returns.php';
include_once '../_general/functions.php';
include '../_authorization/do_passwordHash.php';
include '../_crud/create.php';
include '../_crud/read.php';

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);

if( !isset($request->team_name) || !isset($request->token) ){
    // include '../_general/cors.php';
    die();
}

$team_name = trim($request->team_name);	
$team_description = trim($request->team_description);
$project_id = (int)$request->project_id;
$token = $request->token;
$data = new stdClass();
$role = 'Owner';
$profile_team_status = 'active';
$team_already_created = false;

try {
    mysqli_check();
    global $conn;

    $pro_info = returnTokenProfileId($token);
    $profile_id = intval($pro_info->profile_id);

    $clauseArray = [ $team_name, $project_id ];
    $row_team = get_tabel_info_single_row( 'projects', 'WHERE projects.project_id=? AND projects.owner_id=?', 'ii', $clauseArray );

    if( !isset( $row_team["team_id"]) ){

        $sql = "SELECT DISTINCT teams.team_id FROM teams LEFT JOIN projects ON projects.project_id = teams.project_id WHERE teams.team_name = ? AND projects.project_id = ?";
    
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $team_name, $project_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
    
        if(!!$result && $result->num_rows > 0){  
            while( $row = $result->fetch_assoc() ){
                if( !!$row["team_id"] ){    
                    $team_already_created = true;
                    break;
                }
            }
        }

        if(!!$team_already_created){
            $data->message = "team alerady exists";
            status_return(400); 
            echo json_encode($data);
            $conn->close();
            return;   
        }else{
            $return_team_id = create_team( $team_name, $team_description, $project_id, $profile_id );
            $return_profile_team_id = create_profiles_team( $profile_id , $return_team_id, $role, $profile_team_status );
    
            $data->newTeamId=$return_team_id;
            $data->message = "team created";
            $data->token = exchangeToken($token);
            status_return(200); 
            echo json_encode($data);
            $conn->close();
            return;   
        }
    }else{
        $data->message = "you are not the owner";
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