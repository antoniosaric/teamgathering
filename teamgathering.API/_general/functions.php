<?php

function decodeReturn($value){
  return htmlspecialchars_decode( html_entity_decode($value, ENT_QUOTES, "UTF-8"), ENT_QUOTES );
}

function get_date(){
  $now = new DateTime();
  $date = $now->format('Y-m-d H:i:s');

  return $date;
}

function get_random_string(){
  $randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);

  return $randomString;
}

function mysqli_check(){
    if ( mysqli_connect_error() ){
        status_return(500);
        die();
    }   
  } 

  function check_post_data($postdata){
    if(isset($postdata) && !empty($postdata)){
      return true;
    }else{
      $data = new stdClass();
      $data->message = "data not set";
      status_return(400);
      echo json_encode($data);
      die();
    }
  }

?>