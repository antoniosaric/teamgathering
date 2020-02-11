<?php


function status_return($status){

    switch ($status) {
        case 200;
			header_remove();
			http_response_code(200);
			header("Set-Cookie", "HttpOnly;Secure;SameSite=Strict");
			header('Content-Type: application/json');
			header('Access-Control-Max-Age: 86400');  
			header("Cache-Control: no-transform,public,max-age=300,s-maxage=900");
			header("Access-Control-Allow-Methods: POST, OPTIONS");
			header("Access-Control-Allow-Headers: content-type");			
			header("Accept: */*");
			header("Accept-Encoding: gzip, deflate, br");
			header("Accept-Charset: utf-8;q=0.7,*;q=0.3");
			header("Accept-Language:en-US,en;q=0.9");
			header('HTTP/1.1 200 OK');
            break;

        case 204;
			header_remove();
			http_response_code(204);
			header("Set-Cookie", "HttpOnly;Secure;SameSite=Strict");
			header('Content-Type: application/json');
			header('Access-Control-Max-Age: 86400');  
			header("Cache-Control: no-transform,public,max-age=300,s-maxage=900");
			header("Access-Control-Allow-Methods: POST, OPTIONS");
			header("Access-Control-Allow-Headers: content-type");			
			header("Accept: */*");
			header("Accept-Encoding: gzip, deflate, br");
			header("Accept-Charset: utf-8;q=0.7,*;q=0.3");
			header("Accept-Language:en-US,en;q=0.9");
			header('HTTP/1.1 204 No Content');
            break;   

        case 400;
			header_remove();
			http_response_code(400);
			header("Set-Cookie", "HttpOnly;Secure;SameSite=Strict");
			header('Content-Type: application/json');
			header('Access-Control-Max-Age: 86400');  
			header("Cache-Control: no-transform,public,max-age=300,s-maxage=900");
			header("Access-Control-Allow-Methods: POST, OPTIONS");
			header("Access-Control-Allow-Headers: content-type");			
			header("Accept: */*");
			header("Accept-Encoding: gzip, deflate, br");
			header("Accept-Charset: utf-8;q=0.7,*;q=0.3");
			header("Accept-Language:en-US,en;q=0.9");
			header('HTTP/1.1 400 Bad Request');
            break;

        case 401;
			header_remove();
			http_response_code(401);
			header("Set-Cookie", "HttpOnly;Secure;SameSite=Strict");
			header('Content-Type: application/json');
			header('Access-Control-Max-Age: 86400');  
			header("Cache-Control: no-transform,public,max-age=300,s-maxage=900");
			header("Access-Control-Allow-Methods: POST, OPTIONS");
			header("Access-Control-Allow-Headers: content-type");			
			header("Accept: */*");
			header("Accept-Encoding: gzip, deflate, br");
			header("Accept-Charset: utf-8;q=0.7,*;q=0.3");
			header("Accept-Language:en-US,en;q=0.9");
			header('HTTP/1.1 401 Unauthorized');
            break;   

        case 403;
			header_remove();
			http_response_code(403);
			header("Set-Cookie", "HttpOnly;Secure;SameSite=Strict");
			header('Content-Type: application/json');
			header('Access-Control-Max-Age: 86400');  
			header("Cache-Control: no-transform,public,max-age=300,s-maxage=900");
			header("Access-Control-Allow-Methods: POST, OPTIONS");
			header("Access-Control-Allow-Headers: content-type");			
			header("Accept: */*");
			header("Accept-Encoding: gzip, deflate, br");
			header("Accept-Charset: utf-8;q=0.7,*;q=0.3");
			header("Accept-Language:en-US,en;q=0.9");
			header('HTTP/1.1 403 Forbidden');
            break;
            
        case 404;
			header_remove();
			http_response_code(404);
			header("Set-Cookie", "HttpOnly;Secure;SameSite=Strict");
			header('Content-Type: application/json');
			header('Access-Control-Max-Age: 86400');  
			header("Cache-Control: no-transform,public,max-age=300,s-maxage=900");
			header("Access-Control-Allow-Methods: POST, OPTIONS");
			header("Access-Control-Allow-Headers: content-type");			
			header("Accept: */*");
			header("Accept-Encoding: gzip, deflate, br");
			header("Accept-Charset: utf-8;q=0.7,*;q=0.3");
			header("Accept-Language:en-US,en;q=0.9");
			header('HTTP/1.1 404 Page Not Found');
            break;

        case 500;
			header_remove();
			http_response_code(500);
			header("Set-Cookie", "HttpOnly;Secure;SameSite=Strict");
			header('Content-Type: application/json');
			header('Access-Control-Max-Age: 86400');  
			header("Cache-Control: no-transform,public,max-age=300,s-maxage=900");
			header("Access-Control-Allow-Methods: POST, OPTIONS");
			header("Access-Control-Allow-Headers: content-type");			
			header("Accept: */*");
			header("Accept-Encoding: gzip, deflate, br");
			header("Accept-Charset: utf-8;q=0.7,*;q=0.3");
			header("Accept-Language:en-US,en;q=0.9");
			header('HTTP/1.1 500 Internal Server Error');
			break;   
			
		case 501;
			header_remove();
			http_response_code(500);
			header("Set-Cookie", "HttpOnly;Secure;SameSite=Strict");
			header('Content-Type: application/json');
			header('Access-Control-Max-Age: 86400');  
			header("Cache-Control: no-transform,public,max-age=300,s-maxage=900");
			header("Access-Control-Allow-Methods: POST, OPTIONS");
			header("Access-Control-Allow-Headers: content-type");			
			header("Accept: */*");
			header("Accept-Encoding: gzip, deflate, br");
			header("Accept-Charset: utf-8;q=0.7,*;q=0.3");
			header("Accept-Language:en-US,en;q=0.9");
			header('HTTP/1.1 501 Not implemented');
            break;   


        default:
            header_remove();
			http_response_code(200);
			header("Set-Cookie", "HttpOnly;Secure;SameSite=Strict");
			header('Content-Type: application/json');
			header('Access-Control-Max-Age: 86400');  
			header("Cache-Control: no-transform,public,max-age=300,s-maxage=900");
			header("Access-Control-Allow-Methods: POST, OPTIONS");
			header("Access-Control-Allow-Headers: content-type");			
			header("Accept: */*");
			header("Accept-Encoding: gzip, deflate, br");
			header("Accept-Charset: utf-8;q=0.7,*;q=0.3");
			header("Accept-Language:en-US,en;q=0.9");
			header('HTTP/1.1 200 OK');
    }
}



?>