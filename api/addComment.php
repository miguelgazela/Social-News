<?php
	session_start();
	header('Content-Type: application/json');
	$response;

	function validateComment($comment){  
        if(strlen($comment) < 1)  // NOT VALID
            return false;  
        else  
            return true; 
    } 
	
	if(isset($_SESSION['username'])) { 
		if(!isset($_GET['newComment']))
			$response['result'] = 'NO_COMMENT';
			
		if(!isset($_GET['newsID']))
			$response['result'] = 'NO_NEWS_ID';
			
		$comment = $_GET['newComment'];
		$newsID = $_GET['newsID'];
		$userID = $_SESSION['userID'];
		
		if(!validateComment($comment))
			$response['result'] = 'INVALID_COMMENT';
			
		$db = new PDO("sqlite:../socialnews.db");
		
		$insert = "INSERT INTO comments VALUES (NULL, '$newsID', '$userID', '$comment', DATETIME('now'))";
		$query = $db->query($insert);
		
		if($query == FALSE)
			$response['result'] = 'QUERY_FAILURE_1';
		
		$select = "SELECT * FROM comments WHERE oid = (SELECT MAX(oid) FROM comments)";
		$query = $db->query($select);
	
		if($query == FALSE)
			$response['result'] = 'QUERY_FAILURE_2';
	
		$result = $query->fetch(PDO::FETCH_ASSOC);
				
		$response['result'] = 'OK';
		$response['comment'] = $result;
		$response['username'] = $_SESSION['username'];
	}
	else
		$response['result'] = 'NO_ACCESS';

	die(json_encode($response));
?>