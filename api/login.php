<?php
	session_start();
	
	if(!isset($_POST['login_username']))
		die('NO_USERNAME');
		
	if(!isset($_POST['login_password']))
		die('NO_PASSWORD');
		 
	$username = $_POST['login_username'];
	$password = $_POST['login_password'];
	
	if(strlen($username) == 0)
		die('EMPTY_USERNAME');
		
	if(strlen($password) == 0)
		die('EMPTY_PASSWORD');
		
	$db = new PDO("sqlite:../socialnews.db");
	
	$select = "SELECT * FROM users WHERE username = '$username'";
	$query = $db->query($select);
	
	if($query == FALSE)
		die('QUERY_NOT_ABLE_TO_PERFORM');
	
	$result = $query->fetch(PDO::FETCH_ASSOC);
	
	if($result['password'] != $password)
		die('LOGIN_FAILURE');
	
	$_SESSION['username'] = $result['username'];
	$_SESSION['userID'] = $result['id'];
	$_SESSION['userPermission'] = $result['permission'];
	$_SESSION['userRegisterDate'] = $result['registerDate'];
	$_SESSION['userPassword'] = $result['password'];
	
	die('OK');
?>