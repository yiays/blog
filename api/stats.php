<?php
require_once(dirname(__DIR__)."/api/api.php");
require_once(dirname(__DIR__)."/api/error.php");

class stats extends Handler {
	function __construct($conn, $start=0, $postId=null)
	{
		$this->conn = $conn;
		$this->start = $start;
		$this->postId = $postId;
	}
	
	function resolve($ctx){
		switch($ctx->params[$this->start+1]){
			case "views":
				return json_encode($this->show_views(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
			break;
			default:
				return generic_error(UNKNOWN_REQUEST);
		}
	}
	
	function show_views(){
		$result = $this->conn->query(
			"SELECT CONCAT(YEAR(ViewTime), '/', WEEK(ViewTime)), COUNT(*)
			FROM viewers
			".($this->postId?" WHERE PostId = $this->postId":'')."
			GROUP BY CONCAT(YEAR(ViewTime), '/', WEEK(ViewTime))
			ORDER BY ViewTime DESC
			LIMIT 52"
		);
		if(!$result){
			return specific_error(SERVER_ERROR, $this->conn->error);
		}
		$resultobject = [];
		while($row = $result->fetch_row()){
			$resultobject[$row[0]] = $row[1];
		}
		return $resultobject;
	}
}
?>