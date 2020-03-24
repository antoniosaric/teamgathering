<?php
ob_start();
include_once('../_database/confi.php');
include_once('../_authorization/assignVerifyJWT.php');
include '../_general/status_returns.php';
include '../_general/functions.php';
include '../_crud/read.php';

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);

if( !isset($request->search) ){
//   include '../_general/cors.php';
  die();
}

// $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1ODQzMTU2ODcsImp0aSI6IlFUaTY2eXV2VGR2dVlpa0J1SUNCNEZoc002T2p0K3pSckxEclA0OXNQZWs9IiwiaXNzIjoiaHR0cHM6XC9cL3d3dy50ZWFtZ2F0aGVyaW5nLmNvbSIsImlhdWRzcyI6Imh0dHBzOlwvXC90ZWFtZ2F0aGVyaW5nLmNvbSIsIm5iZiI6MTU4NDMxNTY4NywiZXhwIjoxNTg2NzM0ODg3LCJkYXRhIjp7InByb2ZpbGVfaWQiOjEsImZpcnN0X25hbWUiOiJ4eHhqamoiLCJsYXN0X25hbWUiOiJTYXJpY2NkZGRkIiwiZW1haWwiOiJzYXJpYy50b255QGdtYWlsLmNvbSJ9fQ.im8hGakJQEAHvp4k2ifooF_lLDuj_VaRUwtnCGVXUF8';
$token = ( !!isset($request->token) ) ? ( $request->token != null ) ? $request->token : false : false;
$search = trim( $request->search );
// $search = 'LoreM';

$data = new stdClass();
$projects = [];
$deleted = 'deleted';
$private = 'private';
$public = 'public';
$search_array = [ '%'.$search.'%' ];
// $searchTerms = explode(" ",$search);
// $searchTerms = array_map('strtolower', $searchTerms);
$searchTerms = array_map('strtolower', $search_array );
// $searchTerms = depth_picker($searchTerms);
// $bindClause = implode(',', array_fill(0, count($searchTerms), '?'));
$bindString = str_repeat('s', count($searchTerms));

try {
    global $conn;
    mysqli_check();

    $pro_info = $token != false ? returnTokenProfileId($token) : false;
    $profile_id = $pro_info != false ? intval($pro_info->profile_id) : false;

    if( $token != false ){
        $set_string = "siss";
        $clauseArrayTemp = array($deleted, $profile_id, $private , $public);
        $sql_string = " ( projects.project_status != ? AND projects.owner_id != ? AND ( projects.project_status = ? OR projects.project_status = ? ) ) ";
    }else{
        $set_string = "ss";
        $clauseArrayTemp = array( $deleted, $public);
        $sql_string = " ( projects.project_status != ? AND projects.project_status = ? ) ";
    }

    // if( count( $searchTerms ) == 1 ){

    //     $sql = "SELECT DISTINCT 
    //     projects.project_id AS project_id,
    //     projects.project_name AS project_name,
    //     projects.project_status AS project_status,
    //     projects.short_description AS short_description,
    //     projects.image AS image,
    //     projects.owner_id AS owner_id,
    //     profiles.first_name AS first_name,
    //     profiles.last_name AS last_name
    //     FROM projects
    //     LEFT JOIN profiles ON profiles.profile_id = projects.owner_id 
    //     LEFT JOIN projects_tag ON projects_tag.project_id = projects.project_id
    //     LEFT JOIN tags ON tags.tag_id = projects_tag.tag_id 
    //     WHERE {$sql_string} 
    //     AND ( LOWER( tags.tag_name ) LIKE ( {$bindClause} )
    //     OR LOWER( projects.description ) LIKE ( {$bindClause} ) 
    //     OR LOWER( projects.short_description ) LIKE ( {$bindClause} ) 
    //     OR LOWER( projects.looking_for ) LIKE ( {$bindClause} ) ) "; 

    //     $searchTerms[0] = '%'.$searchTerms[0].'%';
    // }else{

        $sql = "SELECT DISTINCT 
        projects.project_id AS project_id,
        projects.project_name AS project_name,
        projects.project_status AS project_status,
        projects.short_description AS short_description,
        projects.image AS image,
        projects.owner_id AS owner_id,
        profiles.first_name AS first_name,
        profiles.last_name AS last_name
        FROM projects
        LEFT JOIN profiles ON profiles.profile_id = projects.owner_id 
        LEFT JOIN projects_tag ON projects_tag.project_id = projects.project_id
        LEFT JOIN tags ON tags.tag_id = projects_tag.tag_id 
        WHERE {$sql_string} 
        AND ( LOWER( tags.tag_name ) LIKE ?
        OR LOWER( projects.description ) LIKE ? 
        OR LOWER( projects.short_description ) LIKE ? 
        OR LOWER( projects.looking_for ) LIKE ? ) ";  

    // }

    $bindString = $set_string.str_repeat(str_repeat('s', count($searchTerms)), 4);
    $clauseArray = array_merge($clauseArrayTemp, clauseFiller($searchTerms, 4));
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($bindString, ...$clauseArray);

    if($stmt->execute()){
        $result = $stmt->get_result();
        if(!!$result && $result->num_rows > 0){  
            while( $row = $result->fetch_assoc() ){
                if( !in_array( $row, $projects ) ){   
                    array_push( $projects, $row );
                }
            }
        }
    }
    $stmt->close();

    $data->projects = $projects;
    $data->profiles = [];
    $data->message = "search found";
    status_return(200); 
    echo json_encode($data);
    $conn->close();
    return;
}catch (Exception $e ){
    status_return(500);
    echo($e->message);
    return;
}
?>