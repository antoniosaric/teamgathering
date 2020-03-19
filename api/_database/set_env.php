<?php
ob_start();
$configTG = parse_ini_file("../config.ini");
$config['HTTP_HOST'] = $_SERVER['HTTP_HOST'];

$config['HTTPS'] = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';     
$is_local = $_SERVER['SERVER_NAME'] === 'localhost';
$is_prod = $_SERVER['SERVER_NAME'] === 'teamgathering.com' || $_SERVER['SERVER_NAME'] === 'www.teamgathering.com';

if ( $is_local ){
	// local database credentials
	$db_host = $configTG['db_hostlocal']; 
	// Place the username for the MySQL database here 
	$db_username = $configTG['db_usernamelocal']; 
	// Place the password for the MySQL database here 
	$db_pass = "";  
	// Place the name for the MySQL database here 
	$db_name = $configTG['db_namelocal'];
}else if ( $is_prod ){
	// prod database credentials
	$db_host = $configTG['db_hostprod']; 
	// Place the username for the MySQL database here 
	$db_username = $configTG['db_usernameprod']; 
	// Place the password for the MySQL database here 
	$db_pass = $configTG['db_passprod'];  
	// Place the name for the MySQL database here 
	$db_name = $configTG['db_nameprod'];
}

$cloud_name = $configTG['cloud_name'];
$api_key = $configTG['api_key'];
$api_secret = $configTG['api_secret'];


?>