<?php
require_once(dirname(__DIR__)."/api/api.php");
require_once(dirname(__DIR__)."/api/error.php");

class posts extends Handler {
	function __construct($conn)
	{
		$this->conn = $conn;
		$this->list = array();
	}
	
	function resolve($ctx){
		switch($ctx->params[0]){
			case "posts":
				return json_encode($this->show_posts(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
			break;
			case "post":
				if(count($ctx->params)>1){
					return json_encode(($this->show_post($ctx->params[1])), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
				}
				return generic_error(UNKNOWN_REQUEST);
			default:
				return generic_error(UNKNOWN_REQUEST);
		}
	}
	
	function show_posts(){
		$result = $this->conn->query("SELECT * FROM blog WHERE Hidden = 0 ORDER BY PostID DESC");
		if(!$result){
			specific_error(SERVER_ERROR, $result->error);
		}
		$resultobject = [];
		while($row = $result->fetch_assoc()){
			$resultobject[] = $row;
		}
		return $resultobject;
	}
	
	function show_post($id){
		$result = $this->conn->query("SELECT * FROM blog WHERE Hidden = 0 AND ".(ctype_digit($id)?"PostId = $id":"Url = \"".$this->conn->escape_string($id)."\""));
		if(!$result){
			specific_error(SERVER_ERROR, $result->error);
		}
		return $result->fetch_assoc();
	}
}
?>