<?php
	session_start();
	
	function validateUsername($username){  
        if(strlen($username) < 4)  // NOT VALID
            return false;  
        else  
            return true;  
    } 
	
	function validatePasswords($pass1, $pass2) {  
        if(strpos($pass1, ' ') !== false)  // DOESN'T MATCH  
            return false;  
        return ( ($pass1 == $pass2) && (strlen($pass1) > 5));  
    }
	
	if(!isset($_SESSION['username'])) { 
		if(!isset($_POST['usernameDesired']))
			die('NO_USERNAME');
			
		if(!isset($_POST['pass1']))
			die('NO_PASS1');
			
		if(!isset($_POST['pass2']))
			die('NO_PASS2');
			
		$username = $_POST['usernameDesired'];
		$pass1 = $_POST['pass1'];
		$pass2 = $_POST['pass2'];
		
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
		
		$insert = "INSERT INTO users VALUES (NULL, '$username', 1, DATETIME('now'), '$pass1')";
		$query = $db->query($insert);
		
		if($query == FALSE)
			die('QUERY_NOT_ABLE_TO_PERFORM_2');
			
		die('OK');
	}
	else
		die('ALREADY_HAVE_ACCOUNT');
?>