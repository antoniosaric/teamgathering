<?php
ob_clean();
include 'set_env.php';
$conn = new mysqli($db_host, $db_username, $db_pass, $db_name);

if (!$conn) {
    die('Could not connect');
}

mysqli_set_charset($conn, "UTF8");
$GLOBALS['connection'] = $conn;

?>