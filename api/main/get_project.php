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
//   include '../_general/cors.php';
  die();
}

$token = ( $request->token != null ) ? $request->token : false;
$project_id = $request->project_id;

$data = new stdClass();
$project = new stdClass();
$teams = [];
$profile = [];
$saved_team_array = [];
$tags = [];
$profile_access_status = false;
$deleted = 'deleted';

try {
    global $conn;
    mysqli_check();

    $pro_info = !!$token ? returnTokenProfileId($token) : false;
    $profile_id = !!$pro_info ? intval($pro_info->profile_id) : false;

    $clauseArray = [ $project_id, $deleted ];
    $row_project = get_tabel_info_single_row( 'projects', 'WHERE project_id=? AND project_status !=?', 'is', $clauseArray );

    if(!!$row_project['project_id']){

        if( $profile_id != false &&  $row_project['owner_id'] == $profile_id   ){
            $profile_access_status = true;
        }

        $clauseArray = [ $row_project['owner_id'] ];
        $row_owner = get_tabel_info_single_row( 'profiles', 'WHERE profile_id=? ', 'i', $clauseArray );

        $sql_team = "SELECT DISTINCT teams.team_id, teams.team_name, teams.team_description FROM teams 
        WHERE teams.project_id=".$row_project['project_id']." AND teams.team_status != 'deleted' ORDER BY teams.team_id";     

        $stmt_team = $conn->prepare( $sql_team );
        $stmt_team->execute();
        $result_team = $stmt_team->get_result();
        $stmt_team->close();

        if(!!$result_team && $result_team->num_rows > 0){  
            while( $row_team = $result_team->fetch_assoc() ){
                if( !in_array( $row_team, $saved_team_array ) ){                   
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
                    profiles.image, 
                    profiles.last_name,
                    profiles_team.joined_date,
                    profiles_team.profile_team_status
                    FROM profiles 
                    LEFT JOIN profiles_team ON profiles_team.profile_id = profiles.profile_id
                    WHERE profiles_team.team_id = ".$row_team['team_id']." AND profiles_team.profile_team_status != 'deleted' ORDER BY profiles.profile_id";     
                    
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

        $sql4 = "SELECT DISTINCT tags.tag_id, tags.tag_name 
        FROM tags 
        LEFT JOIN teams_tag ON teams_tag.tag_id = tags.tag_id 
        LEFT JOIN teams ON teams.team_id = teams_tag.team_id 
        LEFT JOIN projects ON projects.project_id = teams.project_id
        WHERE projects.project_id = ".$project_id." ORDER BY tags.tag_name ";

        $stmt4 = $conn->prepare($sql4);
        $stmt4->execute();
        $result4 = $stmt4->get_result();
        $stmt4->close();
    
        if(!!$result4 && $result4->num_rows > 0){  
            while( $row4 = $result4->fetch_assoc() ){
                if( !!$row4["tag_id"] ){ 
                    if( !in_array( $row4, $tags) ){                   
                        array_push( $tags, $row4 );
                    }
                }
            }
        }
        $project['tags'] = $tags;

        $project['first_name'] = $row_owner['first_name'];
        $project['last_name'] = $row_owner['last_name'];

        if( $row_project['project_status'] == 'public' || $profile_access_status == true ){

            $sql_count = "SELECT DISTINCT count(profiles.profile_id)
            FROM projects 
            LEFT JOIN profiles ON profiles.profile_id = projects.owner_id 
            LEFT JOIN teams ON teams.project_id = projects.project_id
            LEFT JOIN profiles_team ON profiles_team.team_id = teams.team_id
            WHERE projects.project_id=".$row_project['project_id']." AND profiles_team.role != 'Owner' AND teams.team_status !='deleted' AND profiles_team.profile_team_status != 'deleted'";     

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


