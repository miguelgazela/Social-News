<?php
	session_start();
	
	echo '<script>checkForNews();</script>';
	
	echo '<div id="header">'
	, '	<nav id="main-nav"><ul>'
	, '		<li><a class="_Home" href="socialnews.php">Home</a></li>'
	, '		<li><a class="_About" href="about.php">About</a></li>'
	, '		<li><a class="_AllNews" href="allnews.php">News</a></li>'
	, '		<li><a class="_NewsSearch" href="newsSearch.php">News Search</a><li>';
	
	if(isset($_SESSION['username']) && isset($_SESSION['userPermission'])) {
		if($_SESSION['userPermission'] != 1)
			echo '		<li><a class="_AddNews" href="addNews.php">Add News</a></li>';
		echo '		<li><a class="_Favorites" href="favorites.php">Favorites</a></li>';
		echo '		<li><a class="linkuser" href="api/logout.php">Sign out</a><a class="_User" href="user.php?user_id='.$_SESSION['userID'].'">('.$_SESSION['username'].')</a></li>';
		if($_SESSION['userPermission'] == 3) // admin
			echo '		<li class="config"><a class="config" href="settings.php"><img src="images/config.png" alt="config icon" /></a></li>';
	}
	else {
		if(!isset($_SESSION['username'])) { 
			echo '		<li><a class="_NewAccount" href="newAccount.php">Create account</a></li>';
		}
	}
	echo '	</ul></nav>'
	, '	<form method="get" id="searchform" action="newsSearch.php">'
	, '		<label for="s" class="assistive-text">Search</label>'
	, '		<input type="text" class="field" name="awesome_search" id="s" placeholder="Search" autocomplete="off"/>'
	, '		<input type="button" class="submit" name="submit" id="searchsubmit" value="Search" /></form>'
	, '</div>';
	
	if(!isset($_SESSION['username'])) {
		echo '<form id="login" accept-charset="UTF-8">'
		, '	<div><label for="login_username">Username</label><input type="text" name="login_username" onclick="hideInfo()" autocomplete="off" placeHolder="Username" /></div>'
		, '	<div><label for="login_password">Password</label><input type="password" name="login_password" placeHolder="Password" onclick="hideInfo()" autocomplete="off" /></div>'
		, '	<div><input type="button" value="Sign in" onclick="signin()" /></div>'
		, '</form>'
		, '	<div id="signinInfo" class="hide error" >signinInfo</div>';
	}

	echo '<p id="back-top"><a href="#top">Back to Top</a></p>';
?>