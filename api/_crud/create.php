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

function create_team( $team_name, $team_description, $project_id ){

    global $conn;

    $sql = "INSERT INTO `teams` ( `team_name`, `team_description`, `project_id` ) VALUES (?,?,?)";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param("ssi", $team_name, $team_description, $project_id );

    if ($stmt->execute()) {
    	$return_id = $conn->insert_id;
    	return $return_id;
    } else {
    	return false;
    }
    $stmt->close();
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

function create_tag( $tag_name ) {

    global $conn;

    $sql = "INSERT INTO `tags` ( `tag_name` ) VALUES ( ? )";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param("s", $tag_name );

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

function create_profiles_team( $profile_id , $team_id, $role, $profile_team_status ){

    global $conn;

    $sql = "INSERT INTO `profiles_team` ( `profile_id` , `team_id`, `role`, `profile_team_status` ) VALUES (?,?,?,?)";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param("iiss", $profile_id , $team_id, $role, $profile_team_status );

    if ($stmt->execute()) {
    	$return_id = $conn->insert_id;
    	return $return_id;
    } else {
    	return false;
    }
    $stmt->close();
}


function create_profiles_tag( $profile_id , $tag_id  ){

    global $conn;

    $sql = "INSERT INTO `profiles_tag` ( `profile_id` , `tag_id` ) VALUES (?,?)";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param("ii", $profile_id , $tag_id  );

    if ($stmt->execute()) {
    	$return_id = $conn->insert_id;
    	return $return_id;
    } else {
    	return false;
    }
    $stmt->close();
}

?>



