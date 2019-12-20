<?php
ob_start();
include '../_general/cors.php';
include_once('../_database/confi.php');
include_once('assignVerifyJWT.php');
include '../_general/status_returns.php';
include '../_general/functions.php';
include 'do_passwordHash.php';
include '../_crud/read.php';

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$email = isset( $request->email ) && ( strlen($request->email) > 4 && strlen( $request->email ) < 255 ) ? trim(strtolower($request->email)) : false;
$pass = isset( $request->password ) && ( strlen( $request->password ) > 0 && strlen( $request->password ) < 24 ) ? trim($request->password) : false;
$data = new stdClass();

if( !$email ){
  $data->message = "email specifications not met";
  status_return(401);
  echo json_encode($data);
  return;
}
if( !$pass ){
  $data->message = "password specifications not met";
  status_return(401);
  echo json_encode($data);
  return;
}

// $pass = 'asdf';
// $email = 'a@a.com';
$authenticationVerified = false;

try {
  global $conn;
  mysqli_check();
 
  $clauseArray = [ $email ];
  $row_profile = get_tabel_info_single_row( 'profiles', 'WHERE email=?', 's', $clauseArray );

  if( !!$row_profile['profile_id'] && validate_pw($pass, $row_profile["password"]) ){
    $set_first_name = isset($row_profile['first_name']) ? (string)$row_profile['first_name'] : NULL;
    $set_last_name = isset($row_profile['last_name']) ? (string)$row_profile['last_name'] : NULL;
    $set_email = (string)$row_profile['email'];
    $set_profile_id = (string)$row_profile['profile_id'];

    $data->message = "logged in";
    $data->JWT = assignToken( $set_profile_id, $set_first_name, $set_last_name, $set_email );
    status_return(200);
    echo json_encode($data);
    return;
  }else{
    $data->message = "incorrect login info";
    status_return(401);
    echo json_encode($data);
    return;
  }
  $conn->close();
}catch (Exception $e ){
  status_return(500);
  echo($e->message);
  return;
}
?>


<!-- 
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8
Accept-Charset: utf-8;q=0.7,*;q=0.3
Accept-Encoding: gzip, deflate, br
Accept-Language: en-US,en;q=0.9
Access-Control-Allow-Headers: content-type
Access-Control-Allow-Origin: http://localhost:4200
Cache-Control: no-transform,public,max-age=300,s-maxage=900
Connection: keep-alive, close
Content-Length: 26
Content-Type: text/html; charset=UTF-8
Date: Thu, 19 Dec 2019 05:03:47 GMT
Server: Apache/2.4.37 (Unix) OpenSSL/1.0.2q PHP/5.6.40 mod_perl/2.0.8-dev Perl/v5.16.3

Accept: */*
Accept-Encoding: gzip, deflate, br
Accept-Language: en-US,en;q=0.9
Access-Control-Request-Headers: content-type
Access-Control-Request-Method: POST
Connection: keep-alive
Host: localhost:5001
Origin: http://localhost:4200
Referer: http://localhost:4200/
Sec-Fetch-Mode: cors
Sec-Fetch-Site: same-site
User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.79 Safari/537.36
-->
