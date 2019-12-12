<?php
include_once('../_database/confi.php');
include_once('../_authorization/assignVerifyJWT.php');
include_once '../_general/status_returns.php';
include_once '../_general/functions.php';
include '../_authorization/do_passwordHash.php';
include '../_crud/create.php';
include '../_crud/read.php';

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$param = isset($request->param) ? trim($request->param) : "ORDER BY project_id ASC Limit 5";

$team_name = trim($request->team_name);	
$team_description = trim($request->team_description);
$project_id = (int)$request->project_id;
$token = $request->token;
$data = new stdClass();
$role = 'owner';
$profile_team_status = 'approved';

try {
    mysqli_check();
    global $conn;

    $pro_info = returnTokenProfileId($token);
    $profile_id = $pro_info->profile_id;

    $clauseArray = [ $team_name, $project_id ];
    $row_team = get_tabel_info_single_row( 'teams', 'WHERE teams.project_name=? AND teams.project_id=?', 'si', $clauseArray );

    if( !isset( $row_team["team_id"]) ){

        $return_team_id = create_team( $team_name, $team_description, $project_id, $profile_id );
        $return_profile_team_id = create_ProfileTeam( $profile_id , $return_team_id, $role, $profile_team_status );

        $data->message = "team created";
        status_return(200);
    }else{
        $data->message = "team already exists";
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