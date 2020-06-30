<?php
require('api/api.php');
define('SPLASH_COLOURS', ['#3498db','#1abc9c','#e74c3c','#f39c12','#7f8c8d','#8e44ad','#34495e']);
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Portfolio</title>
	
	<link rel="stylesheet" href="https://cdn.yiays.com/normalize.css">
	
	<link href="https://fonts.googleapis.com/css2?family=Exo:ital,wght@0,200;0,400;0,700;1,200;1,400&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="/css/blog.css?v=15">
</head>
<body>
	<header>
		<a href="/"><h1>blog.yiays.com</h1></a>
	</header>
	<div id="gallery">
		<?php
			$posts = new posts($conn);
			$count = 0;
			foreach ($posts->show_posts() as $post) {
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
				echo "
				<a class=\"post-preview\">
					<div class=\"splash\" style=\"background:$splashcol;\"></div>
					<div class=\"meta\" style=\"background:$splashcol;\">
						<b>More content coming soon!</b>
					</div>
				</a>
				";
			}
		?>
	</div>
	<div id="post">
		
	</div>
	
	<script src="/js/blog.js?v=1"></script>
</body>
</html>