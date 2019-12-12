<?php
include_once('../databaseApi/confi.php');

function assignToken( $profile_id, $first_name = NULL, $last_name = NULL, $email = NULL ){

    $configTG = parse_ini_file("../../config.ini");
    $tokenId    = base64_encode(mcrypt_create_iv(32));
    $issuedAt   = time();
    $notBefore  = $issuedAt;             //Not set as it has to have instant access
    $expire     = $notBefore  + ( 4 * 7 * 24 * 60 * 60 ); // 4 weeks; 7 days; 24 hours; 60 mins; 60 secs

    $token = array(
        'iat'  => $issuedAt,         // Issued at: time when the token was generated
        'jti'  => $tokenId,          // Json Token Id: an unique identifier for the token
        'iss'  => 'https://www.teamgathering.com',       // Issuer
        'iaudss'  => 'https://teamgathering.com',       // Issuer
        'nbf'  => $notBefore, 
        'exp'  => $expire,           // Expire
        'data' => [                  // Data related to the signer user
            'profile_id'   => $profile_id, // userid from the users table
            'first_name'   => $first_name, // userid from the users table
            'last_name' => $last_name, // User name
            'email' => $email // User name
        ]
    );

    $jwt = JWT::encode($token, $configTG['secret_JWT']);
    
    if( isset($jwt) ){
        return $jwt;
    }else{
        status_return(401);
        return;
        exit;
    }
}

function returnTokenProfileId($token){
    $configTG = parse_ini_file("../../config.ini");
    $decoded = JWT::decode($token, $configTG['secret_JWT'], array('HS256'));

    $return = new stdClass();

    $return->profile_id = isset($decoded->data->profile_id) ? $decoded->data->profile_id : NULL;

    if(isset($return->profile_id)){
        return $return;
    }else{
        status_return(401);
        return;
        exit;
    }
}

function varifyToken($token){
    $configTG = parse_ini_file("../../config.ini");
    $decoded = JWT::decode($token, $configTG['secret_JWT'], array('HS256'));
    $newTime = time();
    $timeCheck = $decoded->iat + 3600; //One hour renew token

    if( isset($decoded->data) ){

        $set_first_name = isset($decoded->data->first_name) ? $decoded->data->first_name : NULL;
        $set_last_name = isset($decoded->data->last_name) ? $decoded->data->last_name : NULL;
        $set_email = isset($decoded->data->email) ? $decoded->data->email : NULL;
        $set_profile_id = isset($decoded->data->profile_id) ? $decoded->data->profile_id : NULL;

        if( $newTime < $decoded->exp ){
            if( $newTime < $timeCheck ){
                return $token;
            }else{
                $new_token = assignToken( $set_profile_id, $set_first_name, $set_last_name, $set_email );
                return $new_token;
            }
        }else{
            status_return(401);
            return;
            exit;
        }
    }else{
        status_return(401);
        return;
        exit;
    }
}

?>