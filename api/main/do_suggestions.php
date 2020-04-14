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

try{

    $headers = array(
    "From: LevelPlay Sports <share@levelplaysports.com>",
    "MIME-Version: 1.0",
    "Content-Type: text/html;charset=utf-8; charset=iso-8859-1",
    "Reply-To: info@levelplaysports.com",
	"X-Mailer: PHP/" . phpversion()
    );

    $subject = "A friend is sharing a LevelPlay Sports ".$pathArray[3]." profile with you...";
    $txt = " Hello! Check out this LevelPlay Sports ".$pathArray[3]." profile I've shared with you.  Here is the link:"."<br>"." <a href='".$path."'>".$profileName."  </a>"."<br><br><br>"." <span style='font-size:17px;'><strong>LevelPlay</strong></span>"."<br>"." <span style='font-size:13px;'>Watch. Share. Elevate</span>"."<br><br>"." <span style='font-size:11px;'>If you have any questions, email us at info@levelplaysports.com</span>";

    mail($email,$subject,$txt,implode("\r\n", $headers));

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