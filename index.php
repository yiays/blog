<?php
require_once('api/api.php');
require_once('includes/blogrender.php');

$params = strstr($_SERVER['REQUEST_URI'], '?', true);
if(!$params) $params = $_SERVER['REQUEST_URI'];
$params = explode('/', $params);
$params = array_splice($params, 1);

if(strlen($params[0])){
	switch($params[0]){
		case 'user':
			
		break;
		case 'tag':
			
		break;
		default:
			if(!isset($_GET['old'])) {
				header("Location: https://yiays.com/blog/$params[0]");
				die();
			}
			
			$posts = new posts($conn);
			$post = $posts->get_post($params[0]);
			
			if($post){
				$title = "$post[Title] - Yiays Blog";
				$tags = $post['Tags'];
				$desc = $post['Preview'];
				
				require('includes/header.php');
				
				$postcolour = hexdec($post['Colour']);
				$postgrad = "linear-gradient(135deg, #".dechex($postcolour).", #".sprintf('%06x', $postcolour - 0x444444).")";
				
				echo "
					<div class=\"post-bg\" style=\"background-image: $postgrad;\">
						<div class=\"post-grid\">";
						
						print_post($post, $conn);
						print_post_comments();
						print_post_related($post['Related']);
						
						echo "
						</div>
					</div>
				";
			}
			else{
				require('404.php');
			}
	}
}else{
	$title = "Yiays Blog";
	$desc = "This is the official blog for the squabblings of Yiays - a student experienced with Full-Stack web development, game development, database design, and discord bot development.";
	require('includes/header.php');
	
	echo '<div id="gallery">';
	
	$posts = new posts($conn);
	print_post_previews($posts->show_posts(), 9);
	
	echo '</div>';
	
	require('includes/footer.php');
}
?>