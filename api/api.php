<?php
// Session contains account data
session_start();

// Require the API error handler
require_once(dirname(__DIR__)."/api/error.php");

// Conn contains a mysqli connection object
require_once(dirname(__DIR__)."/../blog.conn.php");

// Each php file includes a Handler object
require_once(dirname(__DIR__)."/api/posts.php");
require_once(dirname(__DIR__)."/api/stats.php");

// Setup account object
/*if(!isset($_SESSION['account'])) $_SESSION['account'] = serialize(new account($conn));
$account = unserialize($_SESSION['account']);
$account->conn = $conn;*/

// Requests provide context to Handlers
class Request {
	function __construct($url, $method)
	{
		// URL must start with '/api/'
		if(substr($url, 0, 5) == "/api/"){
			$this->url = $url;
			$this->method = $method;
			$this->params = explode('/', substr(strtolower($url), 5));
			if(end($this->params) == '') array_pop($this->params);
		}else{
			throw new Exception('Invalid api path.');
		}
	}
}

// Handlers are extended by modules in files in this directory
abstract class Handler {
	function __construct(mysqli $conn, int $start=0){
		$this->conn = $conn;
		$this->start = $start;
	}

	function resolve(Request $ctx){
		return generic_error(UNKNOWN_REQUEST);
	}
}

// All api requests are sent here (thanks to .htaccess)
// Only send a response if this file is master (not included by another file)
if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
	// Universal headers for any API response
	header("Content-Type: application/json");
	header("Access-Control-Allow-Origin: *"); // This is for debug, on release this should be *.yiays.com

	$ctx = new Request($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);

	if(count($ctx->params)>0){
		switch($ctx->params[0]){
			case "test": //api/test/
				http_response_code(VALID_RESPONSE);
				print(json_encode(['desc' => $ctx->method], JSON_PRETTY_PRINT));
			break;
			case "posts": //api/posts/
			case "post": //api/post/{id}/
				$posts = new posts($conn);
				print($posts->resolve($ctx));
			break;
			case "stats":
				$stats = new stats($conn);
				print($stats->resolve($ctx));
			break;
			/*case "account": //api/account/
				print($account->resolve($ctx));
				$_SESSION['account'] = serialize($account);
			break;*/
			default:
				print(generic_error(UNKNOWN_REQUEST));
		}
	}else{
		print(json_encode(['desc'=>"Welcome to the blog.yiays.com API!",
		'queries'=>[
			'GET /posts/',
			'GET /post/{id}/',
			'GET /post/{id}/stats/views/',
			'GET /stats/views/'
		]], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
	}
	$conn->close();
}
?>