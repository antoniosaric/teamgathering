<?php
  header("Cache-Control: no-transform,public,max-age=300,s-maxage=900");
  // header('Content-Type: application/json');
  header('Access-Control-Max-Age: 86400');  
  header("Cache-Control: no-transform,public,max-age=300,s-maxage=900");
  header("Access-Control-Allow-Methods: POST, OPTIONS");
  header("Access-Control-Allow-Headers: content-type");			
  header("Accept: */*");
  header("Accept-Encoding: gzip, deflate, br");
  header("Accept-Charset: utf-8;q=0.7,*;q=0.3");
  header("Accept-Language:en-US,en;q=0.9");
  header('HTTP/1.1 200 OK');
?>