<?php
ob_start();
include_once('../_database/confi.php');
include_once('../_authorization/assignVerifyJWT.php');
include '../_general/status_returns.php';
include '../_general/functions.php';
include '../_crud/read.php';

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);

if( !isset($request->profile_id) ){
    // include '../_general/cors.php';
    die();
}

$profile_id = (int)$request->profile_id;
$profile = new stdClass(); 
$projects = []; 
$teams = []; 
$tags = [];
$follows = [];
$data = new stdClass();

try {
    mysqli_check();
    global $conn;

    $clauseArray = [ $profile_id ];
    $row_profile = get_tabel_info_single_row( 'profiles', 'WHERE profile_id=?', 'i', $clauseArray );

    $sql2 = "SELECT DISTINCT *
    FROM projects 
    LEFT JOIN teams ON teams.project_id = projects.project_id
    LEFT JOIN profiles_team ON profiles_team.team_id = teams.team_id
    WHERE profiles_team.profile_id = ".$profile_id." AND profiles_team.profile_team_status !='deleted' ORDER BY projects.project_id, teams.team_id";

    $stmt2 = $conn->prepare($sql2);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    $stmt2->close();

    if(!!$result2 && $result2->num_rows > 0){  
        while( $row2 = $result2->fetch_assoc() ){
            $team_object = new stdClass(); 
            if( !!$row2["team_id"] && ( $row2["profile_team_status"] != 'deleted' || $row2["team_status"] != 'deleted' ) ){ 
                    $team_object->project_id = $row2['project_id'];
                    $team_object->project_name = $row2['project_name'];
                    $team_object->owner_id = $row2['owner_id'];
                    $team_object->team_id = $row2['team_id'];
                    $team_object->team_name = $row2['team_name'];
                    $team_object->role = $row2['role'];
                    $team_object->profile_team_status = $row2['profile_team_status'];
                    $team_object->joined_date = $row2['joined_date'];

                if( !in_array( $team_object, $teams ) ){
                    array_push($teams, $team_object);
                }
            }
            $project_object = new stdClass(); 
            if( !!$row2["project_id"] ){ 
                $project_object->project_id = $row2['project_id'];
                $project_object->project_name = $row2['project_name'];
                $project_object->project_status = $row2['project_status']; 
                $project_object->created_date = $row2['created_date']; 
                $project_object->image = $row2['image']; 
                $project_object->owner_id = $row2['owner_id']; 
                $project_object->project_role = $row2['role'];
                
                if( !in_array( $project_object, $projects ) ){                   
                    array_push($projects, $project_object);
                }
            }
        }
    }

    // $sql3 = "SELECT DISTINCT * FROM projects 
    // LEFT JOIN teams ON teams.project_id = projects.project_id
    // LEFT JOIN profiles_team ON profiles_team.team_id = teams.team_id
    // WHERE profiles_team.profile_id = ".$profile_id." AND projects.project_status != 'deleted' ORDER BY projects.project_id";

    // $stmt3 = $conn->prepare($sql3);
    // $stmt3->execute();
    // $result3 = $stmt3->get_result();
    // $stmt3->close();

    // if(!!$result3 && $result3->num_rows > 0){  
    //     while( $row3 = $result3->fetch_assoc() ){
    //         $project_object = new stdClass(); 
    //         if( !!$row3["project_id"] ){ 
    //             $project_object->project_id = $row3['project_id'];
    //             $project_object->project_name = $row3['project_name'];
    //             $project_object->project_status = $row3['project_status']; 
    //             $project_object->created_date = $row3['created_date']; 
    //             $project_object->image = $row3['image']; 
    //             $project_object->owner_id = $row3['owner_id']; 
    //             $project_object->project_role = ( $row3['owner_id'] == $profile_id ) ? 'Owner': 'Member';
                
    //             if( !in_array( $project_object, $projects ) ){                   
    //                 array_push($projects, $project_object);
    //             }
    //         }
    //     }
    // }

    $sql4 = "SELECT DISTINCT tags.tag_id, tags.tag_name FROM tags LEFT JOIN profiles_tag ON profiles_tag.tag_id = tags.tag_id WHERE profiles_tag.profile_id = ".$profile_id." ORDER BY tags.tag_id";

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

    $sql5 = "SELECT DISTINCT projects.project_id, projects.project_name, follows.follow_id FROM follows LEFT JOIN projects ON projects.project_id = follows.project_id WHERE follows.profile_id = ".$profile_id." ORDER BY follows.created_date DESC";

    $stmt5 = $conn->prepare($sql5);
    $stmt5->execute();
    $result5 = $stmt5->get_result();
    $stmt5->close();

    if(!!$result5 && $result5->num_rows > 0){  
        while( $row5 = $result5->fetch_assoc() ){
            if( !!$row5["follow_id"] ){ 
                if( !in_array( $row5, $follows) ){                   
                    array_push( $follows, $row5 );
                }
            }
        }
    }

    if( !!$row_profile['profile_id'] ){
        $profile->profile_id = $row_profile['profile_id'];
        $profile->email = $row_profile['email'];
        $profile->first_name = $row_profile['first_name'];
        $profile->last_name = $row_profile['last_name'];
        $profile->image = $row_profile['image'];
        $profile->city = $row_profile['city'];
        $profile->zip_code = $row_profile['zip_code'];
        $profile->state = $row_profile['state'];
        $profile->created_date = $row_profile['created_date'];
        $profile->looking_for = $row_profile['looking_for'];
        $profile->description = $row_profile['description'];
        $profile->teams = $teams;
        $profile->projects = $projects;
        $profile->tags = $tags;
        $profile->follows = $follows;
        $data->profile = $profile;
        $data->message = "profile retrieved";
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



