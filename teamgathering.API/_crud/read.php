<?php

    function get_tabel_info_single_row( $table, $target = " ", $clauseArray, $param_string ){
        //e.g. 'account', 'email', 'a@a.com', 's'
        global $conn;

        $sql = "SELECT * FROM ".$table.$target;

        $stmt = $conn->prepare($sql);
        $stmt->bind_param( $param_string, ...$clauseArray);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = mysqli_fetch_assoc($result);
        $stmt->close();    

        if(!!isset($row['id'])){
            return $row;
        }else{
            return false;
        }

    }

// $clauseArray = [ $profile_id ];
// $return = get_tabel_info_single_row( 'profiles', 'WHERE id = ?', 'i', $clauseArray );    

?>