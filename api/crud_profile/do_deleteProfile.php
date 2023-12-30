<?php
ob_start();
header("Access-Control-Allow-Headers: content-type");	
include_once('../_database/confi.php');
include_once('../_authorization/assignVerifyJWT.php');
include_once '../_general/status_returns.php';
include_once '../_general/functions.php';
include '../_authorization/do_passwordHash.php';
include '../_crud/delete.php';
include '../_crud/update.php';
include '../_crud/read.php';
require '../_general/autoload.php';

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);

if( !isset($request->password) || !isset($request->token) ){
    // include '../_general/cors.php';
    die();
}

$password = trim($request->password);
$token = $request->token;
$data = new stdClass();
$deleted = 'deleted';
$string = '';

try {
    mysqli_check();
    global $conn;

    $pro_info = returnTokenProfileId($token);
    $profile_id = intval($pro_info->profile_id);

    $clauseArray = [ $profile_id ];
    $row_profile_auth = get_tabel_info_single_row( 'profiles', 'WHERE profile_id=? ', 'i', $clauseArray );

    if( !!$row_profile_auth['profile_id'] && validate_pw($password, $row_profile_auth["password"]) ){
// var_dump('%%%%%%%');
// var_dump($profile_id);
        // delete profile
        $delete_profile_return = delete_function( 'profiles', 'profile_id', $profile_id );

        if(!!$delete_profile_return){

            try {
                // delete image
                \Cloudinary::config(array(
                    'cloud_name' => $cloud_name,
                    'api_key' => $api_key,
                    'api_secret' => $api_secret, 
                    "secure" => true
                ));
            
                $files['deleted_image'] = \Cloudinary\Uploader::destroy('profile_'.$profile_id );
                
            }catch (Exception $e ){}    

            // updates delete profile to deleted status
            $sql = "UPDATE projects SET project_status=? WHERE project_id = ( SELECT DISTINCT projects.project_id FROM projects WHERE projects.project_id = ? AND projects.owner_id = ? )";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('sii', $deleted, $project_id, $profile_id);
    
            if($stmt->execute()){
                if( intval($stmt->affected_rows) > 0 ){
    
                    // selects all project_id's to target teams
                    $sql2 = "SELECT DISTINCT project_id FROM projects WHERE owner_id = ? ";
                    $stmt2 = $conn->prepare($sql2);
                    $stmt2->bind_param('i', $profile_id);
    
                    if($stmt2->execute()){
                        $result2 = $stmt2->get_result();
                        if(!!$result2 && $result2->num_rows > 0){  
                            while( $row2 = $result2->fetch_assoc() ){

                                // builds string for email
                                $string .= "<li>id: {$row2['project_id']}, name: {$row2['project_name']}</li>";

                                // updates teams to deleted
                                $set = 'team_status=? ';
                                $clauseArray = [ $deleted, $project_id ];
                                $return_delete_team = update_table( 'teams', $set, 'project_id', 'si', $clauseArray );
                
                                if(!!$return_delete_team){
                
                                    // targets all team_is's for profiles_team
                                    $sql3 = "SELECT DISTINCT team_id FROM teams WHERE project_id = ? ";
                                    $stmt3 = $conn->prepare($sql3);
                                    $stmt3->bind_param('i', $project_id);
                    
                                    if($stmt3->execute()){
                                        $result3 = $stmt3->get_result();
                                        if(!!$result3 && $result3->num_rows > 0){  
                                            while( $row3 = $result3->fetch_assoc() ){
                                                // updates profile_team status to deleted
                                                $set = 'profile_team_status=? ';
                                                $clauseArray = [ $deleted, $row3['team_id'] ];
                                                $return_delete_profiles_team = update_table( 'profiles_team', $set, 'team_id', 'si', $clauseArray );
                                            }
                                        }
                                    }else{
                                        $data->message = "something went wrong";
                                        status_return(500); 
                                        echo json_encode($data);
                                        $conn->close();
                                        return;         
                                    }
                                }else{
                                    $data->message = "something went wrong";
                                    status_return(500); 
                                    echo json_encode($data);
                                    $conn->close();
                                    return;         
                                }
                            }
                        }
                    }
                }
            }

            $subject = "Deleting Profile";
            $message = "
            <html>
            <head>
            <title>Thank You!</title>
            </head>
            <body>
            <p>We are sorry you want to cancel your profiles</p>
            <p>Please keep this email for your records</p>
            <p>Deleted the following projects:</p>
            <div><ul>{$string}</ul></div>
            <p>Sincerely,</p>
            <p>The IdeifyHub Team</p>
            </body>
            </html>";
            
            $header = "From:help@ideifyhub.com \r\n";
            $header .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $header .= "MIME-Version: 1.0\r\n";
            
            $retval = mail ($row_profile_auth['email'],$subject,$message,$header);

            $data->message = "profile deleted successfully";
            status_return(200); 
            echo json_encode($data);
            $conn->close();
            return;  

        }else{
            $data->message = "something went wrong";
            status_return(500); 
            echo json_encode($data);
            $conn->close();
            return; 
        }
    }else{
        $data->message = "unathorized";
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