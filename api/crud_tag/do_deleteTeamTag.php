<?php
ob_start();
header("Access-Control-Allow-Headers: content-type");	
include_once('../_database/confi.php');
include_once('../_authorization/assignVerifyJWT.php');
include_once '../_general/status_returns.php';
include_once '../_general/functions.php';
include '../_crud/delete.php';
include '../_crud/read.php';

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);

if( !isset($request->tag_id) || !isset($request->token) ){
    // include '../_general/cors.php';
    die();
}
$tag_id = intval($request->tag_id);
$token = $request->token;
$team_id = intval($request->team_id);


$data = new stdClass();

try {
    mysqli_check();
    global $conn;

    $pro_info = returnTokenProfileId($token);
    $profile_id = intval($pro_info->profile_id);

    $sql = "SELECT DISTINCT count( teams_tag_id ) AS count_team FROM `teams_tag` WHERE tag_id=?"; 
	$stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $tag_id );
    if( $query = $stmt->execute() ){
        $result = $stmt->get_result();
    }
    $stmt->close();

    $sql2 = "SELECT DISTINCT count( profiles_tag_id ) AS count_profile FROM `profiles_tag` WHERE tag_id=?"; 
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("i", $tag_id );
    if( $query2 = $stmt2->execute() ){
        $result2 = $stmt2->get_result();
    }
    $stmt2->close();

    if ( !!$query && !!$query2 ) {
        $row = $result->fetch_assoc();
        $row2 = $result2->fetch_assoc();
        
        $sql3 = "SELECT DISTINCT teams_tag_id FROM `teams_tag` WHERE tag_id=? AND team_id=?"; 
        $stmt3 = $conn->prepare($sql3);
        $stmt3->bind_param("ii", $tag_id, $team_id );
        if($query3 = $stmt3->execute()){
            $result3 = $stmt3->get_result();
        }
        $stmt3->close();

        if( $query3 ){
            $row3 = $result3->fetch_assoc();
            if( !!isset($row3['teams_tag_id']) ){
                if( intval($row2['count_profile']) > 1 ){
                    // delete assocation

                    $return_delete_team_tag = delete_function( 'teams_tag', 'teams_tag_id', $row3['teams_tag_id']  );
        
                    if(!!$return_delete_team_tag){
                        $data->token = exchangeToken($token);
                        $data->message = "tag removed from team";
                        status_return(200);
                        echo json_encode($data);
                        return;
                    }else{
                        $data->message = "something went wrong";
                        status_return(400); 
                        echo json_encode($data);
                        return;        
                    }
                }else if( intval($row['count_team']) > 1 ){
                    // delete assocation

                    $return_delete_team_tag = delete_function( 'teams_tag', 'teams_tag_id', $row3['teams_tag_id']  );
        
                    if(!!$return_delete_team_tag){
                        $data->token = exchangeToken($token);
                        $data->message = "tag removed from team";
                        status_return(200);
                        echo json_encode($data);
                        return;
                    }else{
                        $data->message = "something went wrong1";
                        status_return(400); 
                        echo json_encode($data);
                        return;        
                    }
                }else if( intval($row['count_team']) == 1 ){
                    // delete assocation and tag
                    $return_delete_team_tag = delete_function( 'teams_tag', 'teams_tag_id', $row3['teams_tag_id']  );
        
                    if(!!$return_delete_team_tag){

                        $return_delete_tag = delete_function( 'tags', 'tag_id', $tag_id  );

                        if(!!$return_delete_tag){
                            $data->token = exchangeToken($token);
                            $data->message = "skill removed from team";
                            status_return(200);
                            echo json_encode($data);
                            return;

                        }else{
                            $data->token = exchangeToken($token);
                            $data->message = "skill removed from team, tag not removed";
                            status_return(200); 
                            echo json_encode($data);
                            return;        
                        }
                    }else{
                        $data->message = "something went wrong2";
                        status_return(400); 
                        echo json_encode($data);
                        return;        
                    }
                }
            }else{

                $data->message = "could not find tag";
                status_return(400); 
                echo json_encode($data);
                return; 
            }
        }else{
            $data->message = "something went wrong3";
            status_return(400); 
            echo json_encode($data);
            return;        
        }
    } else {
        $data->message = "something went wrong4";
        status_return(400); 
        echo json_encode($data);
        return;
    }
}catch (Exception $e ){
    status_return(500);
    echo($e->message);
    return;
}
?>