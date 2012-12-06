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

<body onload="loadNews()" class="_AllNews" id="top">
	<div id="wrapper">
    	<? include 'api/header.php'; ?>
        <div id="content-wrapper">
        	<p class="warning hide">No news are available</p>
        </div>
        <div id="footer"></div> 
    </div>
</body>
</html>
