<?php
	session_start();
	
	function validateString($string){  
        if(strlen($string) < 1)  // NOT VALID
            return false;  
        else  
            return true;  
    } 
	
	if(isset($_SESSION['username']) && $_SESSION['userPermission'] == 3) {

		if(!isset($_POST['title']))
			die('NO_TITLE');
		if(!isset($_POST['date']))
			die('NO_DATE');
		if(!isset($_POST['text']))
			die('NO_TEXT');		
	
		$title = $_POST['title'];
		$date = $_POST['date'];
		$text = $_POST['text'];
		$img = "http://fakeimg.pl/300x300/?text=Imported";
		$userID = $_SESSION['userID'];
		$intro='This news was imported from another remote server, so a news intro is not available. Click the title or the "See more" link to see the text of the news.';

		if(!validateString($title))
			die('INVALID_TITLE');
		if(!validateString($date))
			die('INVALID_DATE');
		if(!validateString($text))
			die('INVALID_TEXT');
		if(!validateString($img))
			die('INVALID_IMG_URL');
			
		$db = new PDO("sqlite:../socialnews.db");
		$insert = "INSERT INTO news VALUES(NULL, '$userID', '$title', '$intro', '$text', '$date', '$img')";
		$query = $db->query($insert);
	
		if($query == FALSE)
			die('QUERY_NOT_ABLE_TO_PERFORM');
		
		die('OK');
	}
	else
		die('NO_ACCESS');
?>