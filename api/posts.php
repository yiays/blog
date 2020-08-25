<?php
require_once(dirname(__DIR__)."/api/api.php");
require_once(dirname(__DIR__)."/api/error.php");
require_once(dirname(__DIR__)."/api/stats.php");
require_once(dirname(__DIR__)."/includes/Parsedown.php");

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
			"SELECT PostID,auth.Username AS Author,Content,Title,Url,Date,Tags,Cover,Colour
			FROM blog
				LEFT JOIN auth ON blog.UserID = auth.UserID
			WHERE Hidden = 0
			ORDER BY PostID DESC"
		);
		if(!$result){
			return specific_error(SERVER_ERROR, $this->conn->error);
		}
		
		$Parsedown = new Parsedown();
		
		$resultobject = [];
		while($row = $result->fetch_assoc()){
			$preview = strip_tags($Parsedown->text($row['Content']));
			unset($row['Content']);
			$row['Preview'] = (strlen($preview)>128?substr($preview,0,128).'...':$preview);
			$resultobject[] = $row;
		}
		return $resultobject;
	}
	
	function get_post($id){
		$result = $this->conn->query(
			"SELECT
				blog.*,
				auth.Username AS Author,
				COUNT(likers.UserID) AS Likes,
				COUNT(dislikers.UserID) AS Dislikes,
				COUNT(comments.CommentID) AS Comments
			FROM blog
				LEFT JOIN auth ON blog.UserID = auth.UserID
				LEFT JOIN likers ON likers.PostID=blog.PostID
				LEFT JOIN dislikers ON dislikers.PostID=blog.PostID
				LEFT JOIN comments ON comments.PostID=blog.PostID
			WHERE
				blog.Hidden = 0
				AND ".(ctype_digit($id)?"blog.PostId = $id":"blog.Url = \"".$this->conn->escape_string($id)."\"")
		);
		if(!$result){
			return specific_error(SERVER_ERROR, $this->conn->error);
		}
		$row = $result->fetch_assoc();
		if(is_null($row['PostID'])) return []; // COUNT means there is one row regardless of if the post exists or not
		
		$Parsedown = new Parsedown();
		
		$preview = strip_tags($Parsedown->text($row['Content']));
		$row['Preview'] = (strlen($preview)>128?substr($preview,0,128).'...':$preview);
		
		$row['Related'] = []; // TODO: Populate this.
		
		// Record this successful request as a view
		if(!$this->conn->query("INSERT INTO viewers(POSTID) VALUES($row[PostID])"))
			return specific_error(SERVER_ERROR, "Failed to count view; ".$this->conn->error);
		
		return $row;
	}
	function show_post($id){
		return $this->get_post($id);
	}
}
?>