<?php
require('api/api.php');
require('includes/Parsedown.php');
define('SPLASH_COLOURS', [0x55efc4,0x81ecec,0x74b9ff,0xa29bfe,0x636e72]);
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Portfolio</title>
	
	<link rel="stylesheet" href="https://cdn.yiays.com/normalize.css">
	
	<link href="https://fonts.googleapis.com/css2?family=Exo:ital,wght@0,200;0,400;0,700;1,200;1,400&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="/css/blog.css?v=50">
</head>
<body>
	<header>
		<a href="/"><h1>blog.yiays.com</h1></a>
		<div class="views">
			<?php
				$stats = new stats($conn);
				foreach($stats->show_views() as $date=>$count){
					$dayssince = round((time() - strtotime($date)) / (60 * 60 * 24));
					$scaledcount = round(16*log($count,10))+4;
					echo "<span style=\"width:{$scaledcount}px;height:{$scaledcount}px;right:{$dayssince}px\"></span>";
				}
			?>
		</div>
	</header>
	<?php if(!isset($_GET['post'])){ ?>
	<div id="gallery">
		<?php
			$posts = new posts($conn);
			$count = 0;
			foreach($posts->show_posts() as $post) {
				$count++;
				echo "
				<a href=\"/$post[Url]\" class=\"post-preview\">
					<img src=\"https://cdn.yiays.com/blog/$post[Cover]\">
					<div class=\"meta\" style=\"background:#$post[Colour];\">
						<b>$post[Title]</b><br>
						<i>$post[Tags]</i>
					</div>
				</a>";
			}
			while($count<9){
				$count++;
				$splashcol = SPLASH_COLOURS[array_rand(SPLASH_COLOURS)];
				$splashgrad = "linear-gradient(135deg, #".dechex($splashcol)." 20%, #".dechex($splashcol - 0x444444)." 20%)";
				echo "
				<a class=\"post-preview\">
					<div class=\"splash\" style=\"background:$splashgrad;\"></div>
					<div class=\"meta\" style=\"background:#".dechex($splashcol).";\">
						<b>More content coming soon!</b>
					</div>
				</a>";
			}
		?>
	</div>
	<?php }else{ ?>
	<div id="post">
		<?php
			$posts = new posts($conn);
			$post = $posts->get_post($_GET['post']);
			
			// Decode and present tags
			$tags = [];
			foreach(explode(", ", $post['Tags']) as $tag){
				$tags[] = "<a href=\"/tag/".urlencode(strtolower($tag))."\">$tag</a>";
			}
			$tags = implode(", ", $tags);
			
			// Decode content
			$Parsedown = new Parsedown();
			$content = $Parsedown->text($post['Content']);
			
			echo "
				<div class=\"post\" data-id=\"$post[PostId]\">
					<div class=\"post-header\" style=\"background:#$post[Colour]\">
						<div class=\"content\">
							<h2>$post[Title]</h2>
							<div class=\"author\">Authored by <a href=\"/user/yiays\">Yiays</a></div>
							<div class=\"tags\">$tags</div>
						</div>
						<div class=\"views\">
							
						</div>
					</div>
					<div class=\"post-image\">
						<img src=\"https://cdn.yiays.com/blog/$post[Cover]\"/>
					</div>
					<div class=\"post-body\">
						$content
					</div>
					<div class=\"post-footer\">
						
					</div>
				</div>";
		?>
	</div>
	<?php } ?>
	<script src="/js/blog.js?v=1"></script>
</body>
</html>