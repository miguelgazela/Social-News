<?php
	session_start();
	
	function validateString($string){  
        if(strlen($string) < 1)  // NOT VALID
            return false;  
        else  
            return true;  
    } 
	
	if( isset($_SESSION['username'])) {
		if($_SESSION['userPermission'] != 1) {
			
			if(!isset($_POST['title']))
				die('NO_TITLE');
			if(!isset($_POST['intro']))
				die('NO_INTRO');
			if(!isset($_POST['text']))
				die('NO_TEXT');
			if(!isset($_POST['imgurl']))
				die('NO_IMG_URL');
		
			$title = $_POST['title'];
			$intro = $_POST['intro'];
			$text = $_POST['text'];
			$img = $_POST['imgurl'];
			$userID = $_SESSION['userID'];
	
			if(!validateString($title))
				die('INVALID_TITLE');
			if(!validateString($intro))
				die('INVALID_INTRO');
			if(!validateString($text))
				die('INVALID_TEXT');
			if(!validateString($img))
				die('INVALID_IMG_URL');
				
			$db = new PDO("sqlite:../socialnews.db");
			$insert = "INSERT INTO news VALUES(NULL, '$userID', '$title', '$intro', '$text', DATETIME('now'), '$img')";
			$query = $db->query($insert);
		
			if($query == FALSE)
				die('QUERY_NOT_ABLE_TO_PERFORM_2');
			
			die('OK');
		}
		else
			die('NO_PERMISSION');
	}
	else
		die('NO_ACCESS');
?>