<?php
include_once('../_database/confi.php');
include_once('../_authorization/assignVerifyJWT.php');
include_once '../_general/status_returns.php';
include_once '../_general/functions.php';
include '../_crud/read.php';

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$team_id = (int)$request->team_id;
$token = $request->token;
$data = new stdClass();

try {
    mysqli_check();
    global $conn;

    $pro_info = returnTokenProfileId($token);
    $profile_id = $pro_info->profile_id;

    $sql = "SELECT DISTINCT * FROM profiles_team LEFT JOIN teams ON teams.team_id = profiles_team.team_id LEFT JOIN projects ON projects.id = teams.project_id WHERE team.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $team_id);
    $result = $stmt->execute();
    $get_result = $stmt->get_result();
    // $row = $get_result->fetch_assoc();
    $stmt->close();

    if( !!$result ){
        var_dump($get_result);
        status_return(200); 
    }else{
        $data->message = "page not found";
        status_return(404); 
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