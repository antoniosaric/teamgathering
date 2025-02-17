<?php
// Blowfish algorithm.
function generate_hash($password, $cost=11){
        // generate the salt
        $salt=substr(base64_encode(openssl_random_pseudo_bytes(17)),0,22);
        /* Blowfish takes a salt with the alphabet ./A-Za-z0-9 we have to
         * replace any '+' in the base64 string with '.'. We don't have to do
         * anything about the '=', as this only occurs when the b64 string is
         * padded, which is always after the first 22 characters.
         */
        $salt=str_replace("+",".",$salt);
        // string that will be passed to crypt, containing all of the settings
        $param='$'.implode('$',array(
                "2y", //select the most secure version of blowfish (>=PHP 5.3.7)
                str_pad($cost,2,"0",STR_PAD_LEFT), //add the cost in two digits
                $salt //add the salt
        ));
       
        //returns actual hashed password
        $return = new stdClass();
        $return->salt = $salt;
        $return->hash_pass = crypt($password,$param);
        return $return;
}

function validate_pw($password, $hash){
    //returns a 1 if its a match
        return crypt($password, $hash)==$hash;
}

?>