<?php
	session_start();
	
	function validateName($name) {
		if(preg_match('/^[A-Za-z ]{3,40}$/', $name))
			return true;
		return false;
	}
	
	function validateUsername($username){
		if(preg_match('/^[A-Za-z0-9_]{4,20}$/', $username));
			return true;
		return false;
    } 
	
	function validatePasswords($pass1, $pass2) {
		if(preg_match('/^[A-Za-z0-9!@#$%^&*()_]{6,20}$/',$pass1) && ($pass1 == $pass2))
			return true;
		return false;
    }
	
	if(!isset($_SESSION['username'])) { 
	
		if(!isset($_POST['name']))
			die('NO_NAME');
		if(!isset($_POST['usernameDesired']))
			die('NO_USERNAME');
		if(!isset($_POST['pass1']))
			die('NO_PASS1');
		if(!isset($_POST['pass2']))
			die('NO_PASS2');
		
		$newAccountAllowed = FALSE;
			
		if(isset($_SESSION['last_account_created'])) {
			if((time() - $_SESSION['last_account_created']) > 30) { // at least 30 seconds between 2 new accounts request from same session
				$newAccountAllowed = TRUE;
			}
		}
		else {
			$_SESSION['last_account_created'] = time();
			$newAccountAllowed = TRUE;
		}
		
		if($newAccountAllowed == TRUE) {
			$name = $_POST['name'];
			$username = $_POST['usernameDesired'];
			$pass1 = $_POST['pass1'];
			$pass2 = $_POST['pass2'];
			
			if(!validateName($name))
				die('INVALID_NAME');
			if(!validateUsername($username))
				die('INVALID_USERNAME');
			if(!validatePasswords($pass1, $pass2))
				die('INVALID_PASSWORDS');
				
			$db = new PDO("sqlite:../socialnews.db");
			
			$select = "SELECT id from users WHERE users.username = '$username'";
			$query = $db->query($select);
			
			if($query == FALSE)
				die('QUERY_NOT_ABLE_TO_PERFORM_1');
				
			$result = $query->fetch(PDO::FETCH_ASSOC);
			
			if(isset($result['id']))
				die('USERNAME_TAKEN');
			
			$insert = "INSERT INTO users VALUES (NULL, '$username', 1, DATETIME('now'), '$pass1', '$name', 'youremail@domain')";
			$query = $db->query($insert);
			
			if($query == FALSE)
				die('QUERY_NOT_ABLE_TO_PERFORM_2');
				
			$_SESSION['last_account_created'] = time();
			die('OK');
		}
		else
			die('WAIT');
	}
	else
		die('ALREADY_HAVE_ACCOUNT');
?>