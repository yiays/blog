<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo $title; ?></title>
	<meta name="keywords" content="<?php echo "blog, yiays, yesiateyoursheep, ".(isset($tags)?$tags:""); ?>">
	<meta name="description" content="<?php echo isset($desc)?$desc:"A description for this page has not yet been provided."; ?>">
	
	<link rel="apple-touch-icon" sizes="180x180" href="/icon/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/icon/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/icon/favicon-16x16.png">
	<link rel="manifest" href="/site.webmanifest">
		<meta name="theme-color" content="#000000">
	
	<link rel="stylesheet" href="https://cdn.yiays.com/normalize.css">
	
	<link href="https://fonts.googleapis.com/css2?family=Exo:ital,wght@0,200;0,400;0,700;1,200;1,400&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="/css/blog.css?v=94">
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