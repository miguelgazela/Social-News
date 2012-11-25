<?php
	session_start();
	
	if(isset($_SESSION['username'])) { 
	
		if(!isset($_GET['newsID']))
			die('NO_NEWS_ID');
			
		$newsID = $_GET['newsID'];
		$userID = $_SESSION['userID'];
			
		$db = new PDO("sqlite:../socialnews.db");
		$deleteNews;
		
		if($_SESSION['userPermission'] == 1)
			die('NO_ACCESS');
		else if($_SESSION['userPermission'] == 2)
			$deleteNews = "DELETE FROM news WHERE news.id = '$newsID' AND news.author_ID == '$userID'";
		else
			$deleteNews = "DELETE FROM news WHERE news.id = '$newsID'";
			
		$query = $db->query($deleteNews);
		
		if($query == FALSE)
			die('QUERY_NOT_ABLE_TO_PERFORM_1');
			
		$deleteComments = "DELETE FROM comments WHERE comments.news_id = '$newsID'";
		$query = $db->query($deleteComments);
			
		if($query == FALSE)
			die('QUERY_NOT_ABLE_TO_PERFORM_2');
			
		$deleteTags = "DELETE FROM news_tags WHERE news_id = '$newsID'";
		$query = $db->query($deleteTags);
			
		if($query == FALSE)
			die('QUERY_NOT_ABLE_TO_PERFORM_3');
			
		$deleteFavorites = "DELETE FROM news_favorites WHERE news_id = '$newsID'";
		$query = $db->query($deleteFavorites);
			
		if($query == FALSE)
			die('QUERY_NOT_ABLE_TO_PERFORM_4');
			
		die('OK');
	}
	else
		die('NO_ACCESS');
?>