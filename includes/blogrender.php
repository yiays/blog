<?php
define('SPLASH_COLOURS', [0x55efc4,0x81ecec,0x74b9ff,0xa29bfe,0x636e72]);
require_once('includes/Parsedown.php');
require_once('api/api.php');


function print_view_histogram($stats){
	foreach($stats as $date=>$count){
		$dayssince = round((time() - strtotime($date)) / (60 * 60 * 24))*4-8;
		$scaledcount = round(8*log($count,10))+4;
		echo "<span style=\"width:{$scaledcount}px;height:{$scaledcount}px;right:{$dayssince}px\"></span>";
	}
}

function print_post_previews($posts, $cap){
	$count = 0;
	foreach($posts as $post) {
		$count++;
		
		$postdate = new DateTime($post['Date']);
		$postdate = $postdate->format("M d, Y");
		
		// Decode tags
		$tags = [];
		foreach(explode(", ", $post['Tags']) as $tag){
			$tags[] = "#$tag";
		}
		$tags = implode(", ", $tags);
		
		echo "
		<a href=\"/$post[Url]\" class=\"post-preview\">
			<img src=\"https://cdn.yiays.com/blog/$post[Cover]\">
			<div class=\"meta\" style=\"background:#$post[Colour];\">
				<b>$post[Title]</b><br>
				by $post[Author] on $postdate | <i>$tags</i>
			</div>
		</a>";
		if($count >= $cap) break;
	}
	while($count<$cap){
		$count++;
		$splashcol = SPLASH_COLOURS[array_rand(SPLASH_COLOURS)];
		$splashgrad = "linear-gradient(135deg, #".dechex($splashcol)." 20%, #".sprintf('%06x', $splashcol - 0x444444)." 20%)";
		echo "
		<a class=\"post-preview\">
			<div class=\"splash\" style=\"background:$splashgrad;\"></div>
			<div class=\"meta\" style=\"background:#".dechex($splashcol).";\">
				<b>More content coming soon!</b>
			</div>
		</a>";
	}
}

function print_post($post, $conn){
	$stats = new stats($conn, 0, $post['PostID']);
	
	// Decode date
	$postdate = new DateTime($post['Date']);
	$postdate = $postdate->format("M d, Y");
	
	// Decode tags
	$tags = [];
	foreach(explode(", ", $post['Tags']) as $tag){
		$tags[] = "<a href=\"/tag/".urlencode(strtolower($tag))."\">#$tag</a>";
	}
	$tags = implode(", ", $tags);
	
	// Decode content
	$Parsedown = new Parsedown();
	$Parsedown->setSafeMode(true); // TODO: disable if the author is trusted.
	$content = $Parsedown->text($post['Content']);
	
	echo "
		<div class=\"post\" data-id=\"$post[PostID]\">
			<div class=\"post-header\">
				<div class=\"content\">
					<h2>$post[Title]</h2>
					<div class=\"author\">Authored by <a href=\"/user/".urlencode(strtolower($post['Author']))."\">$post[Author]</a> on $postdate</div>
					<div class=\"tags\">$tags</div>
				</div>
				<div class=\"view-histogram\">";
					print_view_histogram($stats->show_views());
	echo "</div>
			</div>
			<img class=\"post-image\" src=\"https://cdn.yiays.com/blog/$post[Cover]\"/>
			<div class=\"post-body\">
				$content
			</div>
			<!--<div class=\"post-footer\"></div>-->
		</div>
	";
}
function print_post_comments(){
	echo "
		<div id=\"comments\">
			<h2>Comments</h2>
			<div class=\"comment-feed\">
				<div class=\"comment\" data-id=\"1\">
					<img src=\"https://yiays.com/img/pfp.jpg\"/>
					<div class=\"comment-body\">
						This is a test comment
						<div class=\"comment-meta\"> - <a href=\"/user/yiays\">yiays</a> | <a>Reply</a></div>
					</div>
					<!--<div class=\"comment-replies\">
						<div class=\"reply\">
							<img src=\"https://yiays.com/img/pfp.jpg\"/>
							<div class=\"reply-body\">
								This is a reply to a test comment
								<div class=\"reply-meta\"> - <a href=\"/user/yiays\">yiays</a></div>
							</div>
						</div>
					</div>-->
				</div>
			</div>
		</div>
	";
}
function print_post_related($related){
	echo "
		<div id=\"related\">
			<h2>Related articles</h2>
			<div id=\"gallery\">";
				print_post_previews($related, 3);
				echo "
			</div>
		</div>
	";
}
?>