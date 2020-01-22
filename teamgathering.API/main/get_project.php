<?php
ob_start();
include_once('../_database/confi.php');
include_once('../_authorization/assignVerifyJWT.php');
include '../_general/status_returns.php';
include '../_general/functions.php';
include '../_crud/read.php';

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);

if( !isset($request->project_id) ){
  include '../_general/cors.php';
  die();
}

$token = ( $request->token != null ) ? $request->token : false;
$project_id = $request->project_id;

$data = new stdClass();
$project = new stdClass();
$teams = [];
$profile = [];
$saved_team_array = [];
$profile_access_status = false;

try {
    global $conn;
    mysqli_check();

    $pro_info = !!$token ? returnTokenProfileId($token) : false;
    $profile_id = !!$pro_info ? (int)$pro_info->profile_id : false;

    $clauseArray = [ $project_id ];
    $row_project = get_tabel_info_single_row( 'projects', 'WHERE project_id=?', 'i', $clauseArray );

    if(!!$row_project['project_id']){

        if( $profile_id != false &&  $row_project['owner_id'] == $profile_id   ){
            $profile_access_status = true;
        }

        $sql_team = "SELECT DISTINCT teams.team_id, teams.team_name, teams.team_description FROM teams 
        WHERE teams.project_id=".$row_project['project_id']." ORDER BY teams.team_id";     

        $stmt_team = $conn->prepare( $sql_team );
        $stmt_team->execute( );
        $result_team = $stmt_team->get_result();
        $stmt_team->close();

        if(!!$result_team && $result_team->num_rows > 0){  
            while( $row_team = $result_team->fetch_assoc() ){
                if( !in_array( $saved_team_array, $row_team ) ){                   
                    array_push($saved_team_array, $row_team);
                    $team_object = new stdClass();
                    $team_object->profiles = [];
                    $team_object = $row_team;
                    $sql_project_team = "SELECT DISTINCT
                    profiles_team.profile_team_status, 
                    profiles_team.role, 
                    profiles_team.profile_id AS owner_id, 
                    profiles.profile_id, 
                    profiles.first_name, 
                    profiles.last_name,
                    profiles_team.joined_date,
                    profiles_team.profile_team_status
                    FROM profiles 
                    LEFT JOIN profiles_team ON profiles_team.profile_id = profiles.profile_id
                    WHERE profiles_team.team_id = ".$row_team['team_id']." ORDER BY profiles.profile_id";     
                    
                    $stmt_project_team = $conn->prepare($sql_project_team);
                    $stmt_project_team->execute();
                    $result_project_team = $stmt_project_team->get_result();
                    $stmt_project_team->close();

                    $profiles = [];

                    if(!!$result_project_team && $result_project_team->num_rows > 0){  
                        while( $row_project_team = $result_project_team->fetch_assoc() ){
                            if( $profile_id != false && ( $row_project_team['owner_id'] == $profile_id || $row_project_team['profile_id'] == $profile_id ) || $profile_access_status != true ){
                                $profile_access_status = true;
                            }
                            array_push($profiles, $row_project_team);
                        }
                    }
                    $team_object['profiles'] = $profiles;
                    array_push($teams, $team_object);
                }
            }
        }
        $project = $row_project;
        $project['teams'] = $teams;
        if( $row_project['project_status'] == 'public' || $profile_access_status == true ){

            $sql_count = "SELECT DISTINCT count(profiles.profile_id)
            FROM projects 
            LEFT JOIN profiles ON profiles.profile_id = projects.owner_id 
            LEFT JOIN teams ON teams.project_id = projects.project_id
            LEFT JOIN profiles_team ON profiles_team.team_id = teams.team_id
            WHERE projects.project_id=".$row_project['project_id']." AND profiles_team.role != 'Owner'";     

            $stmt_count = $conn->prepare($sql_count);
            $stmt_count->execute();
            $result_count = $stmt_count->get_result();
            $row_count = $result_count->fetch_assoc();
            $stmt_count->close();
            $project['count'] = $row_count['count(profiles.profile_id)'] > 0 ? $row_count['count(profiles.profile_id)'] + 1 : 1;
            
            $project['view_status'] = 'authorized';
            $data->project = $project;
        }else{
            $project['view_status'] = 'unathorized';
            $project['description'] = '';

            $project['teams'] = [];
            $data->project = $project;
        }
        
        $data->message = "page found";
        status_return(200); 
        echo json_encode($data);
        $conn->close();
        return; 
    }else{
        $data->message = "page not found";
        status_return(404); 
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


