<?php
	session_start();
	
	if(isset($_SESSION['username'])) {
		 
		if(!isset($_POST['commentID']))
			die('NO_COMMENT_ID');
		if(!isset($_POST['text']))
			die('NO_COMMENT_TEXT');
			
		$commentID = $_POST['commentID'];
		$text = $_POST['text'];
		$userID = $_SESSION['userID'];
			
		$db = new PDO("sqlite:../socialnews.db");
		$update = "UPDATE comments SET text = '$text' WHERE id = '$commentID' AND author_ID = '$userID'";
		
		$query = $db->query($update);
		
		if($query == FALSE)
			die('QUERY_NOT_ABLE_TO_PERFORM');
		die('OK');
	}
	else
		die('NO_ACCESS');
?>