<?php
	session_start();
	
	if(isset($_SESSION['username']) && $_SESSION['userPermission'] == 3) 
	{
		if(!isset($_POST['title']))
			die('NO_TITLE');
		if(!isset($_POST['date']))
			die('NO_DATE');
			
		$title = $_POST['title'];
		$date = $_POST['date'];
			
		$db = new PDO("sqlite:../socialnews.db");
		
		$select = "SELECT news.id FROM news WHERE news.title = '$title' AND news.submissionDate = '$date'";
		$query = $db->query($select);
		$result  = $query->fetchAll(PDO::FETCH_ASSOC);
		$counter = 0;
		
		foreach ($result as $key => $value)
		{	
			if($counter>0)
				break;
				
			$news_id = $value['id'];
			
			$deleteNews = "DELETE FROM news WHERE news.id = '$news_id'";
			$query = $db->query($deleteNews);
			
			if($query == FALSE)
				die('QUERY_NOT_ABLE_TO_PERFORM_1');
				
			$deleteComments = "DELETE FROM comments WHERE comments.news_id = '$news_id'";
			$query = $db->query($deleteComments);
				
			if($query == FALSE)
				die('QUERY_NOT_ABLE_TO_PERFORM_2');
				
			$deleteTags = "DELETE FROM news_tags WHERE news_id = '$news_id'";
			$query = $db->query($deleteTags);
				
			if($query == FALSE)
				die('QUERY_NOT_ABLE_TO_PERFORM_3');
				
			$deleteFavorites = "DELETE FROM news_favorites WHERE news_id = '$news_id'";
			$query = $db->query($deleteFavorites);
				
			if($query == FALSE)
				die('QUERY_NOT_ABLE_TO_PERFORM_4');		
				
			$counter++;	
		}	
		die('OK');
	}
	else
		die('NO_ACCESS');
?>