<?php
require_once(dirname(__DIR__)."/api/api.php");
require_once(dirname(__DIR__)."/api/error.php");
require_once(dirname(__DIR__)."/api/stats.php");

class posts extends Handler {
	function resolve($ctx){
		switch($ctx->params[$this->start]){
			case "posts":
				return json_encode($this->show_posts(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
			break;
			case "post":
				if(count($ctx->params)>$this->start+2){
					switch($ctx->params[$this->start+2]){
						case "stats":
							$stats = new stats($this->conn, $this->start+2, $this->get_post($ctx->params[$this->start+1])['PostId']);
							return $stats->resolve($ctx);
						break;
						default:
							return generic_error(UNKNOWN_REQUEST);
					}
				}
				if(count($ctx->params)==$this->start+2){
					return json_encode(($this->show_post($ctx->params[$this->start+1])), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
				}
				return generic_error(UNKNOWN_REQUEST);
			default:
				return generic_error(UNKNOWN_REQUEST);
		}
	}
	
	function show_posts(){
		$result = $this->conn->query(
			"SELECT PostID,auth.Username AS Author,Title,Url,Date,Tags,Cover,Colour
			FROM blog
				LEFT JOIN auth ON blog.UserID = auth.UserID
			WHERE Hidden = 0
			ORDER BY PostID DESC"
		);
		if(!$result){
			specific_error(SERVER_ERROR, $result->error);
		}
		$resultobject = [];
		while($row = $result->fetch_assoc()){
			$resultobject[] = $row;
		}
		return $resultobject;
	}
	
	function get_post($id){
		$result = $this->conn->query(
			"SELECT blog.*,auth.Username AS Author
			FROM blog
				LEFT JOIN auth ON blog.UserID = auth.UserID
			WHERE Hidden = 0
				AND ".(ctype_digit($id)?"PostId = $id":"Url = \"".$this->conn->escape_string($id)."\"")
		);
		if(!$result){
			specific_error(SERVER_ERROR, $result->error);
		}
		$result = $result->fetch_assoc();
		$result['Related'] = []; // TODO: Populate this.
		return $result;
	}
	function show_post($id){
		return $this->get_post($id);
	}
}
?>