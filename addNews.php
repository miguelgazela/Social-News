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
        <?php
			if(!isset($_SESSION['username']))
				echo '<p class="warning">To access this page, you need to Sign in</p>';
			else {
				if(isset($_SESSION['userPermission']) && $_SESSION['userPermission'] != 1) {
					echo '<form id="addNews">'
					, '	<div><label for="news_title">Título</label><input type="text" id="news_title_form" name="title" onblur="validateNewsTitle()" onkeyup="validateNewsTitle()" value="" autocomplete="off" /></div>'
					, '	<div><label for="news_intro">Introdução</label><textarea name="intro" id="news_intro_form" onblur="validateNewsIntro()" onkeyup="validateNewsIntro()" rows="7" cols="80"></textarea></div>'
					, '	<div><label for="news_text">Notícia</label><textarea name="text" id="news_text_form" onblur="validateNewsText()" onkeyup="validateNewsText()" rows="18" cols="80"></textarea></div>'
					, '	<div><input type="text" name="imgurl" placeHolder="Image url" id="img_url_form" onblur="validateImgUrl()" onkeyup="validateImgUrl()" autocomplete="off" /></div>'
					, '	<div><input type="button" value="Add" onClick="addNews()" /></div>'
					, '</form>';
				}
				else {
					echo '<p class="warning">You don\'t have permission to access this page</p>';
				}
			}
		?>
        </div>
        <div id="footer"></div>        
    </div>
</body>
</html>
