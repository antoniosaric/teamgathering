<?php
ob_start();
include_once('../_database/confi.php');
include_once('../_authorization/assignVerifyJWT.php');
include '../_general/status_returns.php';
include '../_general/functions.php';
include '../_crud/read.php';

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);

if( !isset($request->token) ){
//   include '../_general/cors.php';
  die();
}

$token = $request->token;
$thread_profile_id = $request->profile_id;
$data = new stdClass();
$threads = [];

try {
    global $conn;
    mysqli_check();

    $pro_info = returnTokenProfileId($token);
    $profile_id = intval($pro_info->profile_id);

    $sql = "SELECT DISTINCT profiles.first_name, profiles.last_name, messages.sender_id, messages.recipient_id, messages.message, messages.message_id, messages.created_date 
    FROM messages LEFT JOIN profiles ON profiles.profile_id = messages.recipient_id 
    WHERE ( messages.sender_id = ? AND messages.recipient_id = ? ) 
    OR ( messages.sender_id = ? AND messages.recipient_id = ? ) 
    ORDER BY messages.message_id ASC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param( 'iiii', $profile_id, $thread_profile_id, $thread_profile_id, $profile_id );
    $stmt->execute();
    $result = $stmt->get_result();

    if(!!$result && $result->num_rows > 0){  
        while( $row = $result->fetch_assoc() ){
            if( !in_array( $row, $threads ) ){
                $row['state'] = intval($row['sender_id']) == intval($profile_id) ? 'outgoing' : 'incoming';
                array_push($threads, $row);
            }
        }
    }
    $stmt->close();

    $data->message = "messages found";
    $data->threads = $threads;
    status_return(200); 
    echo json_encode($data);
    $conn->close();
    return;
}catch (Exception $e ){
    status_return(500);
    echo($e->message);
    return;
}