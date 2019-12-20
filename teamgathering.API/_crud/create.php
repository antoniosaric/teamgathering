<?php

// ======================================================================
//  MAIN TABLES
// ======================================================================

function create_profile( $email, $password, $salt, $image ){

    global $conn;

    $sql = "INSERT INTO `profiles` ( `email`, `password`, `salt`, `image` ) VALUES (?,?,?,?)";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param("ssss", $email, $password, $salt, $image );

    if ($stmt->execute()) {
    	$return_id = $conn->insert_id;
    	return $return_id;
    } else {
    	return false;
    }
    $stmt->close();
}

function create_project( $project_name, $description, $short_description, $project_status, $image, $owner_id ){

    global $conn;

    $sql = "INSERT INTO `projects` ( 'project_name', 'description', 'short_description', 'project_status', 'image', 'owner_id' ) VALUES ( ?,?,?,?,? )";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param("sssssi", $project_name, $description, $short_description, $project_status, $image, $owner_id );

    if ($stmt->execute()) {
    	$return_id = $conn->insert_id;
    	return $return_id;
    } else {
    	return false;
    }
    $stmt->close();
}

function create_team( ){

    // global $conn;

    // $sql = "INSERT INTO `teams` (  ) VALUES ()";
	// $stmt = $conn->prepare($sql);
	// $stmt->bind_param("",  );

    // if ($stmt->execute()) {
    // 	$return_id = $conn->insert_id;
    // 	return $return_id;
    // } else {
    // 	return false;
    // }
    // $stmt->close();
}


function create_request( ){

    // global $conn;

    // $sql = "INSERT INTO `requests` (  ) VALUES ()";
	// $stmt = $conn->prepare($sql);
	// $stmt->bind_param("",  );

    // if ($stmt->execute()) {
    // 	$return_id = $conn->insert_id;
    // 	return $return_id;
    // } else {
    // 	return false;
    // }
    // $stmt->close();
}

// ======================================================================
//  JOIN TABLES
// ======================================================================

function create_profile_join_team( $requested_profile_id , $team_id, $role, $profile_team_status ){

    global $conn;

    $sql = "INSERT INTO `profiles_team_sm` ( 'profile_id' , 'team_id', 'role', 'profile_team_status' ) VALUES (?,?,?,?)";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param("iiss", $requested_profile_id , $team_id, $role, $profile_team_status );

    if ($stmt->execute()) {
    	$return_id = $conn->insert_id;
    	return $return_id;
    } else {
    	return false;
    }
    $stmt->close();
}

?>