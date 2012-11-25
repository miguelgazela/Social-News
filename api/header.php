<?php
	session_start();
	
	echo '<div id="header">'
	, '	<nav id="main-nav"><ul>'
	, '		<li><a href="socialnews.php">Home</a></li>'
	, '		<li><a href="about.php">About</a></li>'
	, '		<li><a href="allnews.php">News</a></li>'
	, '		<li><a href="newsSearch.php">News Search</a><li>';
	
	if(isset($_SESSION['username']) && isset($_SESSION['userPermission'])) {
		if($_SESSION['userPermission'] != 1)
			echo '		<li><a href="addNews.php">Add News</a></li>';
		echo '		<li><a href="favorites.php">Favorites</a></li>';
		echo '		<li><a href="api/logout.php">Sign out ('.$_SESSION['username'].')</a></li>';
	}
	else {
		if(!isset($_SESSION['username'])) { 
			echo '		<li><a href="newAccount.php">Create account</a></li>';
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
?>