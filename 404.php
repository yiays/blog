<?php
$title = "404 - Not Found!";
http_response_code(404);
require('includes/header.php');

echo "
	<div class=\"wrapper\">
		<h1>Not found!</h1>
		<p>The page you requested was either deleted, or never existed!</p>
	</div>
";

require('includes/footer.php');
?>