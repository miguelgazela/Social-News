<?php
	session_start();
	
	function validateTag($tag){  
        if(strlen($tag) < 1) 
            return false;  
        else  
            return true; 
    } 
	
	if(isset($_SESSION['username']) && ($_SESSION['userPermission'] == 2 || $_SESSION['userPermission'] == 3)) { 
		if(!isset($_POST['tag']))
			die('NO_TAG');
			
		$tag = $_POST['tag'];
		$userID = $_SESSION['userID'];
		
		if(!validateTag($tag))
			die('INVALID_TAG');
			
		$db = new PDO("sqlite:../socialnews.db");
		
		$selectTags = "SELECT * FROM tags";
		$query = $db->query($selectTags);
		if($query == FALSE)
			die('QUERY_NOT_ABLE_TO_PERFORM_1');
			
		$resultTags = $query->fetchAll(PDO::FETCH_ASSOC);
		
		if(!empty($resultTags))
			foreach($resultTags as $row)
				if(strtolower($row['text']) == strtolower($tag)) // already exists tag
					die('ALREADY_EXISTS');
		
		// add a new tag and a refference to the news
		$insertNewTag = "INSERT INTO tags VALUES(NULL, '$tag')";
		$query = $db->query($insertNewTag);
		if($query == FALSE)
			die('QUERY_NOT_ABLE_TO_PERFORM_2');
			
		$getID = "SELECT id FROM tags WHERE oid = (SELECT MAX(oid) FROM tags)";
		$query = $db->query($getID);
		if($query == FALSE)
			die('QUERY_NOT_ABLE_TO_PERFORM_3');
			
		$result = $query->fetch(PDO::FETCH_ASSOC);
		$tagID = $result['id'];
	
		die('OK'.$tagID);
	}
	else
		die('NO_ACCESS');
?>