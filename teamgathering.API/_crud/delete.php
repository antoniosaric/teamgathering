<?php

    function delete_function( $table, $target, $id ){

        global $connection;

        $sql = "DELETE FROM `".$table."` WHERE ".$target."=?";  
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
        $stmt->close();
    }

    // $return_period = delete_function( 'profile', 'id', $periodId );

?>