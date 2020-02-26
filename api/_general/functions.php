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
        echo mysqli_connect_error();
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

  function depth_picker($arr) {
    $collect = array();   
    for ($i=0; $i<sizeof($arr);$i++) {
        $collect [] = $arr[$i];
    }   
      
    for ($n=1; $n<sizeof($arr);$n++) {
        $arr[0] = $arr[0].' '.$arr[$n];
        $collect [] = $arr[0];    
    }      
    return array_reverse($collect);
  }
  
  function clauseFiller($arr, $count){
    $dataReturn = array();
    for ($i=0; $i<$count;$i++) {
      for ($j=0; $j<sizeof($arr);$j++) {
        array_push($dataReturn, $arr[$j]);
      } 
    } 
    return $dataReturn;
  }

  function unique_multidim_array($array, $key) {
    $arrayCount = count($array);
    $temp_array = array();
    $i = 0;
    $key_array = array();
    for($i=0; $i<$arrayCount;$i++){
      if (!in_array($array[$i][$key], $key_array)) {
          $key_array[$i] = $array[$i][$key];
          $temp_array[$i] = $array[$i];
      }
    }
    return $temp_array;
  }

?>