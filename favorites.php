<?php
	session_start();
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">

<head>
    <title>Social News &mdash; T06G09 &mdash; Miguel Oliveira &mdash; Daniel Nora</title>
    
	<meta charset="UTF-8">
    <meta name="author" content="Miguel Oliveira &amp; Daniel Nora" />
    <meta name="description" content="LTW Social News Project 2012" />
    
    <link href="style.css" rel="stylesheet" type="text/css" />
    <script src="jquery.js"></script>
    <script src="scripts/scripts.js"></script>
</head>

<body class="_Favorites" id="top">
	<div id="wrapper">
    	<? include 'api/header.php'; ?>
        <div id="content-wrapper">
        <?php
			if(!isset($_SESSION['username']))
        		echo '<p class="warning">To access this page, you need to Sign in</p>';
			else
				echo '<script type="text/javascript">loadFavoriteNews()</script>';
		?>
        </div>
        <div id="footer"></div> 
    </div>
</body>
</html>
