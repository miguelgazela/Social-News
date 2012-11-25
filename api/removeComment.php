<?php
	session_start();
	
	if(isset($_SESSION['username'])) { 
		if(!isset($_GET['commentID']))
			die('NO_COMMENT_ID');
		if(!isset($_GET['newsID']))
			die('NO_NEWS_ID');
			
		$commentID = $_GET['commentID'];
		$newsID = $_GET['newsID'];
		$userID = $_SESSION['userID'];
			
		$db = new PDO("sqlite:../socialnews.db");
		$delete;
		
		if($_SESSION['userPermission'] == 1)
			$delete = "DELETE FROM comments WHERE comments.id = '$commentID' AND comments.author_ID = '$userID'";
		else if($_SESSION['userPermission'] == 2)
			$delete = "DELETE FROM comments WHERE comments.id = '$commentID' AND comments.news_id = '$newsID'";
		else
			$delete = "DELETE FROM comments WHERE comments.id = '$commentID'";
			
		
		
		$query = $db->query($delete);
		
		if($query == FALSE)
			die('QUERY_NOT_ABLE_TO_PERFORM_1');
		die('OK');
	}
	else
		die('NO_ACCESS');
?>