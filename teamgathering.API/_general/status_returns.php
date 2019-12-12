<?php


function status_return($status){

    switch ($status) {
        case 200;
			header_remove();
			http_response_code(200);
			header("Cache-Control: no-transform,public,max-age=300,s-maxage=900");
			header('Content-Type: application/json');
			header('Status: 200 OK');
            break;

        case 204;
			header_remove();
			http_response_code(204);
			header("Cache-Control: no-transform,public,max-age=300,s-maxage=900");
			header('Content-Type: application/json');
			header('HTTP/1.0 204 No Content');
            break;   

        case 400;
			header_remove();
			http_response_code(400);
			header("Cache-Control: no-transform,public,max-age=300,s-maxage=900");
			header('Content-Type: application/json');
			header('HTTP/1.0 400 Bad Request');
            break;

        case 401;
			header_remove();
			http_response_code(401);
			header("Cache-Control: no-transform,public,max-age=300,s-maxage=900");
			header('Content-Type: application/json');
			header('HTTP/1.0 401 Unauthorized');
            break;   

        case 403;
			header_remove();
			http_response_code(403);
			header("Cache-Control: no-transform,public,max-age=300,s-maxage=900");
			header('Content-Type: application/json');
			header('HTTP/1.0 403 Forbidden');
            break;
            
        case 404;
			header_remove();
			http_response_code(404);
			header("Cache-Control: no-transform,public,max-age=300,s-maxage=900");
			header('Content-Type: application/json');
			header('HTTP/1.0 404 Page Not Found');
            break;

        case 500;
			header_remove();
			http_response_code(500);
			header("Cache-Control: no-transform,public,max-age=300,s-maxage=900");
			header('Content-Type: application/json');
			header('HTTP/1.0 500 Internal Server Error');
            break;   

        default:
            header_remove();
			http_response_code(501);
			header("Cache-Control: no-transform,public,max-age=300,s-maxage=900");
			header('Content-Type: application/json');
			header('HTTP/1.0 501 Not implemented');
    }

    header('Access-Control-Allow-Origin: http://localhost:4200', false);
}



?>