<?php
	session_start();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">

<!--
           __                                           _                 ___ 
 _      __/ /_  __  __   _________     _______  _______(_)___  __  ______/__ \
| | /| / / __ \/ / / /  / ___/ __ \   / ___/ / / / ___/ / __ \/ / / / ___// _/
| |/ |/ / / / / /_/ /  (__  ) /_/ /  / /__/ /_/ / /  / / /_/ / /_/ (__  )/_/  
|__/|__/_/ /_/\__, /  /____/\____/   \___/\__,_/_/  /_/\____/\__,_/____/(_)   
             /____/                                                           
-->

<head>
    <title>Social News &mdash; T06G09 &mdash; Miguel Oliveira &mdash; Daniel Nora</title>
    
	<meta charset="UTF-8">
    <meta name="author" content="Miguel Oliveira &amp; Daniel Nora" />
    <meta name="description" content="LTW Social News Project 2012" />
    
    <link href="style.css" rel="stylesheet" type="text/css" />
    <script src="http://code.jquery.com/jquery-latest.js"></script>
    <script type="text/javascript" src="scripts/scripts.js"></script>
</head>

<body onload="loadLatestNews()">
	<div id="wrapper">
    	<? include 'api/header.php'; ?>
        <div id="content-wrapper">
        	<p class="warning hide">No news are available</p>
        </div>
        <div id="footer"></div> 
    </div>
	
</body>
</html>
