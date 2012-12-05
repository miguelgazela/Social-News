<?php
	session_start();
	
	function validateString($string){  
        if(strlen($string) < 1)  // NOT VALID
            return false;  
        else  
            return true;  
    } 
	
	if(isset($_SESSION['username']) && $_SESSION['userPermission'] == 3) 
	{	 
		if(!isset($_POST['title']))
			die('NO_TITLE');
		if(!isset($_POST['date']))
			die('NO_DATE');
		if(!isset($_POST['text']))
			die('NO_TEXT');		
		
		$title = $_POST['title'];
		$date = $_POST['date'];
		$text = $_POST['text'];			
		$intro = '';
	
		if(!validateString($title))
			die('INVALID_TITLE');
		if(!validateString($date))
			die('INVALID_DATE');
		if(!validateString($text))
			die('INVALID_TEXT');
				
		$db = new PDO("sqlite:../socialnews.db");
		$stmt = $db->prepare("SELECT news.id FROM news WHERE news.title = '$title' AND news.submissionDate = '$date'");
		$stmt->execute();

		$result  = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$counter = 0;
		foreach ($result as $key => $value)
		{
			if($counter>0)
				break;	
				
			$news_id = $value['id'];
			
			$update = "UPDATE news SET title = '$title', fulltext = '$text', submissionDate = '$date' WHERE id = '$news_id'";
			$query = $db->query($update);
		
			if($query == FALSE)
				die('QUERY_NOT_ABLE_TO_PERFORM');
				
			$counter++;
		}
		
		die('OK');
	}
	else
		die('NO_ACCESS');
?>