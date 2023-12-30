<?php
ob_start();
include_once('../_database/confi.php');
include_once('../_authorization/assignVerifyJWT.php');
include '../_general/status_returns.php';
include '../_general/functions.php';
include '../_crud/read.php';

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);

$token = ( !!isset($request->token) ) ? ( $request->token != null ) ? $request->token : false : false;
if( $token == false ){
    //   include '../_general/cors.php';
    die();
}

$data = new stdClass();
$profiles = [];
$projects = [];

try {
    global $conn;
    mysqli_check();

    $pro_info = !!$token ? returnTokenProfileId($token) : false;
    $profile_id = !!$pro_info ? intval($pro_info->profile_id) : false;

    if( $profile_id != false ){

        $sql = "SELECT DISTINCT 
        projects.project_id AS project_id,
        projects.project_name AS project_name,
        projects.project_status AS project_status,
        projects.short_description AS short_description,
        projects.image AS image,
        projects.owner_id AS owner_id,
        profiles.first_name AS first_name,
        profiles.last_name AS last_name
        FROM projects 
        LEFT JOIN profiles ON profiles.profile_id = projects.owner_id 
        LEFT JOIN teams ON teams.project_id = projects.project_id
        LEFT JOIN profiles_team ON profiles_team.team_id = teams.team_id
        LEFT JOIN teams_tag ON teams_tag.team_id = teams.team_id 
        WHERE teams_tag.tag_id 
        IN ( SELECT DISTINCT tag_id FROM profiles_tag WHERE profile_id = {$profile_id}) 
        AND projects.project_status !='deleted' AND projects.owner_id != {$profile_id}";     
        
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
    
        if(!!$result && $result->num_rows > 0){  
            while( $row = $result->fetch_assoc() ){
                if( !in_array( $row, $projects ) ){   
                    array_push( $projects, $row );
                }
            }
        }

        $sql2 = "SELECT DISTINCT 
        profiles.profile_id AS profile_id,
        profiles.email AS email,
        profiles.image AS image,
        profiles.first_name AS first_name,
        profiles.last_name AS last_name, 
        profiles.created_date AS created_date
        FROM profiles LEFT JOIN profiles_tag ON profiles_tag.profile_id = profiles.profile_id 
        WHERE profiles_tag.tag_id 
        IN ( SELECT DISTINCT tag_id 
            FROM teams_tag 
            LEFT JOIN teams ON teams.team_id =  teams_tag.team_id 
            LEFT JOIN projects ON projects.project_id = teams.project_id 
            WHERE projects.owner_id = {$profile_id}) 
        AND profiles.profile_id != {$profile_id}";     
        
        $stmt2 = $conn->prepare($sql2);
        $stmt2->execute();
        $result2 = $stmt2->get_result();
        $stmt2->close();
    
        if(!!$result2 && $result2->num_rows > 0){  
            while( $row2 = $result2->fetch_assoc() ){
                if( !in_array( $row2, $profiles ) ){   
                    array_push( $profiles, $row2 );
                }
            }
        }
    }

    $data->profiles = $profiles;
    $data->projects = $projects;
    $data->message = "suggestions found";
    status_return(200); 
    echo json_encode($data);
    $conn->close();
    return;
}catch (Exception $e ){
    status_return(500);
    echo($e->message);
    return;
}
?>