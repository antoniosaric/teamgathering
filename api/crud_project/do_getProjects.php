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

$data = new stdClass();
$projects = [];

try {
    global $conn;
    mysqli_check();

    $pro_info = returnTokenProfileId($token);
    $profile_id = intval($pro_info->profile_id);

    $sql = "SELECT DISTINCT project_id, project_name FROM projects WHERE owner_id = ".$profile_id;
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if(!!$result && $result->num_rows > 0){  
        while( $row = $result->fetch_assoc() ){
            if( $row['project_id'] && $row['project_name'] ){
                if( !in_array( $projects, $row ) ){                   
                    array_push( $projects, $row );
                }
            }
        }
        $data->projects = $projects;
        $data->message = "projects found";
        status_return(200); 
        echo json_encode($data);
        $conn->close();
        return; 
    }else{
        $data->message = "you need to add a project";
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