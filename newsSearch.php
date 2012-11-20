<?php
	session_start();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">

<head>
    <title>Social News &mdash; T06G09 &mdash; Miguel Oliveira &mdash; Daniel Nora</title>
    
	<meta charset="UTF-8">
    <meta name="author" content="Miguel Oliveira &amp; Daniel Nora" />
    <meta name="description" content="LTW Social News Project 2012" />
    
    <link href="style.css" rel="stylesheet" type="text/css" />
    <script src="http://code.jquery.com/jquery-latest.js"></script>
    <script type="text/javascript" src="scripts/scripts.js"></script>
</head>

<body>
	<div id="wrapper">
    	<? include 'api/header.php'; ?>
        
        <div id="content-wrapper">
        	<?
        		if(!isset($_GET['awesome_search'])) {
					echo '<div id="newsSearchForm">'
					, '	<p>Use at least one of the following fields</p>'
					, ' <div><input type="text" name="start_date" autocomplete="off" placeHolder="Start date (YYYY-MM-DDTHH:MM:SS)"></div>'
					, ' <div><input type="text" name="end_date" autocomplete="off" placeHolder="End date (YYYY-MM-DDTHH:MM:SS)"></div>'
					, ' <div><input type="text" name="tags" autocomplete="off" placeHolder="tags"></div>'
					, '	<div><input type="button" value="Search" name="search_submit" onClick="newSearch(2)" /></div>'
					, '</div>';
				}
				else {
					echo '<p class="hide" id="search_hidden">' . $_GET['awesome_search'] . '</p>';
					echo '<script type="text/javascript">'
					, 'newSearch(1);'
					, '</script>';
				}
			?>
            <div id="searchResults">
            	<p class="warning hide">Oops, it looks like we haven't covered that topic yet</p>
            </div>
        </div>
        <div id="footer"></div>         
    </div>
	
</body>
</html>
