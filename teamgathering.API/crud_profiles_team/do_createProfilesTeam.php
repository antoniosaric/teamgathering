<?php
include_once('../_database/confi.php');
include_once('../_authorization/assignVerifyJWT.php');
include_once '../_general/status_returns.php';
include_once '../_general/functions.php';
include '../_crud/create.php';
include '../_crud/read.php';

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$requested_profile_id = (int)$request->requested_profile_id;
$team_id = (int)$request->team_id;
$role = trim($request->role);
$profile_team_status = trim($request->profile_team_status);
$token = $request->token;
$data = new stdClass();

try {
    mysqli_check();
    global $conn;

    $pro_info = returnTokenProfileId($token);
    $profile_id = intval($pro_info->profile_id);

    $sql = "SELECT DISTINCT projects.owner_id AS owner_id FROM profiles_team LEFT JOIN teams ON teams.team_id = profiles_team.team_id LEFT JOIN projects ON projects.id = teams.project_id WHERE team.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $team_id);
    $result = $stmt->execute();
    $get_result = $stmt->get_result();
    $row = $get_result->fetch_assoc();
    $stmt->close();

    if( !!$result ){
        if ( !isset( $row["owner_id"]) == $profile_id  ){
            $return_profile_team_id = create_ProfileTeam( $requested_profile_id , $team_id, $role, $profile_team_status );
            $data->message = "profile added to team";
            status_return(200);
        }else{
            $data->message = "project owner error";
            status_return(401); 
        }
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