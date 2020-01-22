<?php
ob_start();
include_once('../_database/confi.php');
include_once('../_authorization/assignVerifyJWT.php');
include_once '../_general/status_returns.php';
include_once '../_general/functions.php';
include '../_crud/update.php';
include '../_crud/read.php';

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$team_name = trim($request->team_name);	
$team_description = trim($request->team_description);
$team_id = (int)$request->team_id;
$token = $request->token;
$data = new stdClass();

try {
    mysqli_check();
    global $conn;

    $pro_info = returnTokenProfileId($token);
    $profile_id = intval($pro_info->profile_id);

    $sql = "SELECT DISTINCT * FROM profiles_team LEFT JOIN teams ON teams.team_id = profiles_team.team_id LEFT JOIN projects ON projects.id = teams.project_id WHERE team.id = ? AND projects.owner_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $team_id, $profile_id);
    $result = $stmt->execute();
    $get_result = $stmt->get_result();
    $row = $get_result->fetch_assoc();
    $stmt->close();

    if( !!$row['profiles_team_id'] ){
        $set = '$team_name=?, $team_description=? ';
        $clauseArray = [ $team_name, $team_description, $team_id ];
        $return_update_teams = update_table( 'teams', $set, 'team_id', 'ssi', $clauseArray );
        $data->message = "team updated";
        status_return(200);
    }else{
        $data->message = "team not found";
        status_return(400); 
    }
    echo json_encode($data);
    $conn->close();
    return;
}catch (Exception $e ){
    status_return(500);
    echo($e->message);
    return;
}
?>