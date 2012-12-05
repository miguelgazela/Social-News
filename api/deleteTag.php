<?php
	session_start();
	
	if(isset($_SESSION['username']) && $_SESSION['userPermission'] == 3) { 
		if(!isset($_GET['tagID']))
			die('NO_TAG_ID');
			
		$tagID = $_GET['tagID'];
		$db = new PDO("sqlite:../socialnews.db");
		
		$deleteRefs = "DELETE FROM news_tags WHERE tag_id = '$tagID'";
		$query = $db->query($deleteRefs);
		if($query == FALSE)
			die('QUERY_NOT_ABLE_TO_PERFORM_1');
			
		$deleteTag = "DELETE FROM tags WHERE id = '$tagID'";
		$query = $db->query($deleteTag);
		if($query == FALSE)
			die('QUERY_NOT_ABLE_TO_PERFORM_2');
			
		die('OK');
	}
	die('NO_ACCESS');
?>