<?php
require('api/api.php');
require('includes/Parsedown.php');
define('SPLASH_COLOURS', [0x55efc4,0x81ecec,0x74b9ff,0xa29bfe,0x636e72]);

$post = null;
$title = "Yiays Blog";
if(isset($_GET['post'])){
	$posts = new posts($conn);
	$post = $posts->get_post($_GET['post']);

	$title = "$post[Title] - Yiays Blog";
}
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo $title; ?></title>
	
	<link rel="stylesheet" href="https://cdn.yiays.com/normalize.css">
	
	<link href="https://fonts.googleapis.com/css2?family=Exo:ital,wght@0,200;0,400;0,700;1,200;1,400&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="/css/blog.css?v=83">
</head>
<body>
	<header>
		<a href="/"><h1>blog.yiays.com</h1></a>
		<div class="view-histogram">
			<?php
				$stats = new stats($conn);
				print_view_histogram($stats->show_views());
			?>
		</div>
	</header>
	<?php if(is_null($post)){ ?>
	<div id="gallery">
		<?php
			$posts = new posts($conn);
			$count = 0;
			
			print_post_previews($posts->show_posts(), 9);
		?>
	</div>
	<?php }else{
		$stats = new stats($conn, 0, $post['PostID']);
		
		$postdate = new DateTime($post['Date']);
		$postdate = $postdate->format("M d, Y");
		
		$postcolour = hexdec($post['Colour']);
		$postgrad = "linear-gradient(135deg, #".dechex($postcolour).", #".sprintf('%06x', $postcolour - 0x444444).")";
		
		echo "
			<div class=\"post-bg\" style=\"background-image: $postgrad;\">
				<div class=\"post-grid\">";
		// Decode and present tags
		$tags = [];
		foreach(explode(", ", $post['Tags']) as $tag){
			$tags[] = "<a href=\"/tag/".urlencode(strtolower($tag))."\">#$tag</a>";
		}
		$tags = implode(", ", $tags);
		
		// Decode content
		$Parsedown = new Parsedown();
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
			<div id=\"related\">
				<h2>Related articles</h2>
				<div id=\"gallery\">";
					print_post_previews($post['Related'], 3);
		echo "</div>
			</div>
		";
		echo "</div></div>";
	} ?>
	<script src="/js/blog.js?v=1"></script>
</body>
</html><?php

function print_view_histogram($stats){
	foreach($stats as $date=>$count){
		$dayssince = round((time() - strtotime($date)) / (60 * 60 * 24));
		$scaledcount = round(16*log($count,10))+4;
		echo "<span style=\"width:{$scaledcount}px;height:{$scaledcount}px;right:{$dayssince}px\"></span>";
	}
}

function print_post_previews($posts, $cap){
	$count = 0;
	foreach($posts as $post) {
		$count++;
		
		$postdate = new DateTime($post['Date']);
		$postdate = $postdate->format("M d, Y");
		
		echo "
		<a href=\"/$post[Url]\" class=\"post-preview\">
			<img src=\"https://cdn.yiays.com/blog/$post[Cover]\">
			<div class=\"meta\" style=\"background:#$post[Colour];\">
				<b>$post[Title]</b><br>
				by $post[Author] on $postdate | <i>$post[Tags]</i>
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


?>