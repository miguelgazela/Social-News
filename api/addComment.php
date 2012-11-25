<?php
	session_start();
	
	function validateComment($comment){  
        if(strlen($comment) < 1)  // NOT VALID
            return false;  
        else  
            return true; 
    } 
	
	if(isset($_SESSION['username'])) { 
		if(!isset($_GET['newComment']))
			die('NO_COMMENT');
			
		if(!isset($_GET['newsID']))
			die('NO_NEWS_ID');
			
		$comment = $_GET['newComment'];
		$newsID = $_GET['newsID'];
		$userID = $_SESSION['userID'];
		
		if(!validateComment($comment))
			die('INVALID_COMMENT');
			
		$db = new PDO("sqlite:../socialnews.db");
		
		$insert = "INSERT INTO comments VALUES (NULL, '$newsID', '$userID', '$comment', DATETIME('now'))";
		$query = $db->query($insert);
		
		if($query == FALSE)
			die('QUERY_NOT_ABLE_TO_PERFORM_1');
		
		$select = "SELECT * FROM comments WHERE oid = (SELECT MAX(oid) FROM comments)";
		$query = $db->query($select);
	
		if($query == FALSE)
			die('QUERY_NOT_ABLE_TO_PERFORM_2');
	
		$result = $query->fetch(PDO::FETCH_ASSOC);
		
		$commentInserted['username'] = $_SESSION['username'];
		$commentInserted['comment'] = $result;
		
		die(json_encode($commentInserted));
	}
	else
		die('NO_ACCESS');
?>