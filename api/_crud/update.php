<?php

function update_table( $table, $set, $target, $param_string, $clauseArray){

    global $conn;

    $sql = "UPDATE ".$table." SET ".$set." WHERE ".$target."=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param( $param_string, ...$clauseArray );

    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
    $stmt->close();
}


// $set = 'email=?, password=?';
// $clauseArray = [ $email, $password, $profile_id ];
// $return_update_account = update_table( 'profile', $set, 'id', 'ssi', $clauseArray );

?>