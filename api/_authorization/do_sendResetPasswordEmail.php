<?php
ob_start();
header("Access-Control-Allow-Headers: content-type");	
include_once('../_database/confi.php');
include_once('assignVerifyJWT.php');
include '../_general/status_returns.php';
include '../_general/functions.php';
include 'do_passwordHash.php';
include '../_crud/update.php';
include '../_crud/read.php';

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);

if( !isset($request->email) ){
    // include '../_general/cors.php';
    die();
}

$email = trim($request->email);

$data = new stdClass();
$random_key = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 16);

try {
    global $conn;
    mysqli_check();

    $subject = "Reset Key";
    $message = "
    <html>
    <head>
    <title>Reset Password Key</title>
    </head>
    <body>
    <p>Here is your Reset Password Key!</p>
    <p>{$random_key}</p>
    <p>Please copy and paste this into the browser</p>
    </body>
    </html>";
    
    $header = "From:help@teamgathering.com \r\n";
    $header .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $header .= "MIME-Version: 1.0\r\n";
    
    $retval = mail ($email,$subject,$message,$header);
    
    if( $retval == true ) {

        $set = 'secret_key=?';
        $clauseArray = [ $random_key, $email ];
        $return_password = update_table( 'profiles', $set, 'email', 'ss', $clauseArray );

        if(!!$return_password){
            $data->message = "email sent";
            status_return(200);
            echo json_encode($data);
            $conn->close();
            return;
        }else{
            $data->message = "something went wrong";
            status_return(400);
            echo json_encode($data);
            $conn->close();
            return;
        }
    }else {
        $data->message = "something went wrong";
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