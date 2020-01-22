<?php

// ======================================================================
//  MAIN TABLES
// ======================================================================

function create_profile( $email, $password, $salt, $image, $zip_code, $city, $state, $first_name, $last_name ){

    global $conn;

    $sql = "INSERT INTO `profiles` ( `email`, `password`, `salt`, `image`, `zip_code`, `city`, `state`, `first_name`, `last_name` ) VALUES (?,?,?,?,?,?,?,?,?)";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param("sssssssss", $email, $password, $salt, $image, $zip_code, $city, $state, $first_name, $last_name );

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

    $sql = "INSERT INTO `projects` ( `project_name`, `description`, `short_description`, `project_status`, `image`, `owner_id` ) VALUES ( ?,?,?,?,?,? )";
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


function create_request( $profile_id, $owner_id, $project_id, $status ) {

    global $conn;

    $sql = "INSERT INTO `requests` ( `requester_id`, `requestee_id`, `project_id`, `request_status` ) VALUES (?,?,?,?)";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param("iiis", $profile_id, $owner_id, $project_id, $status );

    if ($stmt->execute()) {
    	$return_id = $conn->insert_id;
    	return $return_id;
    } else {
    	return false;
    }
    $stmt->close();
}

// ======================================================================
//  JOIN TABLES
// ======================================================================

function create_profiles_team( $requested_profile_id , $team_id, $role, $profile_team_status ){

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