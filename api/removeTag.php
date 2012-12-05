<?php
	session_start();
	
	if(isset($_SESSION['username']) && $_SESSION['userPermission'] > 1) { 
		if(!isset($_POST['tagID']))
			die('NO_TAG_ID');
		if(!isset($_POST['newsID']))
			die('NO_NEWS_ID');
			
		$tagID = $_POST['tagID'];
		$newsID = $_POST['newsID'];
		$userID = $_SESSION['userID'];
		
		if($_SESSION['userID'] == $userID) {
			$db = new PDO("sqlite:../socialnews.db");
			$deleteRef = "DELETE FROM news_tags WHERE tag_id = '$tagID' AND news_id = '$newsID'";
			
			$query = $db->query($deleteRef);
			if($query == FALSE)
				die('QUERY_NOT_ABLE_TO_PERFORM');
			
			die('OK');
		}
		die('NO_ACCESS');	
	}
	die('NO_ACCESS');
?>