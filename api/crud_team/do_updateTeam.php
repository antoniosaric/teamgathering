<?php
ob_start();
header("Access-Control-Allow-Headers: content-type");	
include_once('../_database/confi.php');
include_once('../_authorization/assignVerifyJWT.php');
include_once '../_general/status_returns.php';
include_once '../_general/functions.php';
include '../_crud/update.php';
include '../_crud/read.php';

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);

if( !isset($request->team_id) || !isset($request->token) ){
    // include '../_general/cors.php';
    die();
}

$team_name = trim($request->team_name);	
$team_description = trim($request->team_description);
$team_id = intval($request->team_id);
$token = $request->token;
$data = new stdClass();

try {
    mysqli_check();
    global $conn;

    $pro_info = returnTokenProfileId($token);
    $profile_id = intval($pro_info->profile_id);

    $sql = "SELECT DISTINCT teams.team_id FROM teams LEFT JOIN projects ON projects.project_id = teams.project_id WHERE teams.team_id = ? AND projects.owner_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $team_id, $profile_id);
    $result = $stmt->execute();
    $get_result = $stmt->get_result();
    $row = $get_result->fetch_assoc();
    $stmt->close();

    if( !!$row['team_id'] ){
        $set = 'team_name=?, team_description=? ';
        $clauseArray = [ $team_name, $team_description, $team_id ];
        $return_update_teams = update_table( 'teams', $set, 'team_id', 'ssi', $clauseArray );
        $data->token = exchangeToken($token);
        $data->message = "team info updated";
        status_return(200);
        echo json_encode($data);
        $conn->close();
        return;
    }else{
        $data->message = "team not found";
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