<?php
ob_start();
include_once('../_database/confi.php');
include_once('../_authorization/assignVerifyJWT.php');
include '../_general/status_returns.php';
include '../_general/functions.php';
include '../_crud/read.php';
include '../_crud/update.php';

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);

if( !isset($request->token) ){
//   include '../_general/cors.php';
  die();
}

$token = $request->token;
$data = new stdClass();
$messages = [];
$temp_messages = [];

try {
    global $conn;
    mysqli_check();

    $pro_info = returnTokenProfileId($token);
    $profile_id = intval($pro_info->profile_id);

    $sql = "SELECT DISTINCT profiles.image, profiles.first_name, profiles.last_name, messages.sender_id, messages.recipient_id, messages.message_id
    FROM messages LEFT JOIN profiles ON profiles.profile_id = messages.recipient_id WHERE messages.sender_id = ".$profile_id." ORDER BY messages.message_id ASC, profiles.last_name";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if(!!$result && $result->num_rows > 0){  
        while( $row = $result->fetch_assoc() ){
            if( !in_array( $row, $messages ) ){
                array_push($messages, $row);
            }
        }
    }

    $sql2 = "SELECT DISTINCT profiles.image, profiles.first_name, profiles.last_name, messages.sender_id, messages.recipient_id, messages.message_id 
    FROM messages LEFT JOIN profiles ON profiles.profile_id = messages.sender_id WHERE messages.recipient_id = ".$profile_id." ORDER BY messages.message_id ASC, profiles.last_name";
        
    $stmt2 = $conn->prepare($sql2);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    $stmt2->close();

    if(!!$result2 && $result2->num_rows > 0){  
        while( $row2 = $result2->fetch_assoc() ){
            $temp_rec = $row2['recipient_id'];
            $temp_sen = $row2['sender_id'];
            $row2['recipient_id'] = $temp_sen;
            $row2['sender_id'] = $temp_rec;
            if( !in_array( $row2, $messages ) ){
                $row2['recipient_id'] = $temp_rec;
                $row2['sender_id'] = $temp_sen;
                array_push($messages, $row2);
            }
        }
    }

    $set = 'IsRead=? ';
    $clauseArray = [ 1, $profile_id ];
    $return_update_teams = update_table( 'messages', $set, 'recipient_id', 'ii', $clauseArray );

    $data->message = "messages found";
    $data->messages = $messages;
    status_return(200); 
    echo json_encode($data);
    $conn->close();
    return;
}catch (Exception $e ){
    status_return(500);
    echo($e->message);
    return;
}

//   SELECT DISTINCT 
//   profiles.image, 
//   profiles.first_name, 
//   profiles.last_name,
//   messages.message_id, 
//   messages.sender_id, 
//   messages.recipient_id 
//   FROM 
//   (SELECT DISTINCT profiles.image, 
//   profiles.first_name, 
//   profiles.last_name,
//   messages.message_id, 
//   messages.sender_id, 
//   messages.recipient_id 
//   FROM messages LEFT JOIN profiles ON profiles.profile_id = messages.sender_id
//   WHERE messages.sender_id = 1 ORDER BY messages.message_id ASC )
//  UNION
//  ( SELECT DISTINCT profiles.image, 
//   profiles.first_name, 
//   profiles.last_name,
//   messages.message_id, 
//   messages.sender_id, 
//   messages.recipient_id   
//   FROM messages LEFT JOIN profiles ON profiles.profile_id = messages.recipient_id
//   WHERE messages.recipient_id = 1 ORDER BY messages.message_id ASC )
?>