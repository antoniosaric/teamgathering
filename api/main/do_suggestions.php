<?php
ob_start();
include_once('../_database/confi.php');
include_once('../_authorization/assignVerifyJWT.php');
include '../_general/status_returns.php';
include '../_general/functions.php';
include '../_authorization/do_passwordHash.php';
include '../_crud/create.php';
include '../_crud/read.php';


$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$full_name = trim(strtolower($request->full_name));
$email = trim(strtolower($request->email));
$message = trim($request->message);
$data = new stdClass;

try{
    $saric = "saric.tony@gmail.com";

    $headers = array(
    "From: suggestions@teamgathering.com",
    "MIME-Version: 1.0",
    "Content-Type: text/html;charset=utf-8; charset=iso-8859-1",
    "Reply-To: ".$email,
	"X-Mailer: PHP/" . phpversion()
    );

    $subject = "Team Gathering Suggestion";
    $txt = $message;

    mail( $saric, $subject,$txt,implode("\r\n", $headers));

    $data->message = "suggestion sent";
    echo json_encode($data);
    status_return(200);
    return;

}catch (Exception $e ){
    status_return(500);
    echo json_encode($e->message);
    return;
}

?>