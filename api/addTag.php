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
		if(!isset($_POST['newsID']))
			die('NO_NEWS_ID');
			
		$tag = $_POST['tag'];
		$newsID = $_POST['newsID'];
		$userID = $_SESSION['userID'];
		
		if(!validateTag($tag))
			die('INVALID_TAG');
			
		$db = new PDO("sqlite:../socialnews.db");
		
		$selectTags = "SELECT * FROM tags";
		$query = $db->query($selectTags);
		if($query == FALSE)
			die('QUERY_NOT_ABLE_TO_PERFORM_1');
			
		$resultTags = $query->fetchAll(PDO::FETCH_ASSOC);
		
		if(!empty($resultTags)) {
			foreach($resultTags as $row)
				if(strtolower($row['text']) == strtolower($tag)) { // already exists tag
					$tagID = $row['id'];
					
					$selectRef = "SELECT news_id from news_tags WHERE tag_id = '$tagID'";
					$query = $db->query($selectRef);
					if($query == FALSE)
						die('QUERY_NOT_ABLE_TO_PERFORM_2');
						
					$resultRef = $query->fetchAll(PDO::FETCH_ASSOC);
					if(!empty($resultRef))
						foreach($resultRef as $ref)
							if($ref['news_id'] == $newsID) // the news already has a ref to that tag
								die('ALREADY_EXISTS');
								
					// add a ref to that tag to the news
					$insertTag = "INSERT INTO news_tags VALUES('$tagID', '$newsID')";
					$query = $db->query($insertTag);
					if($query == FALSE)
						die('QUERY_NOT_ABLE_TO_PERFORM_3');
					die('OK'.$tagID);
				}
		}
		
		// add a new tag and a refference to the news
		$insertNewTag = "INSERT INTO tags VALUES(NULL, '$tag')";
		$query = $db->query($insertNewTag);
		if($query == FALSE)
			die('QUERY_NOT_ABLE_TO_PERFORM_4');
			
		$getID = "SELECT id FROM tags WHERE oid = (SELECT MAX(oid) FROM tags)";
		$query = $db->query($getID);
		if($query == FALSE)
			die('QUERY_NOT_ABLE_TO_PERFORM_5');
			
		$result = $query->fetch(PDO::FETCH_ASSOC);
		$tagID = $result['id'];
		
		$insertNewTag = "INSERT INTO news_tags VALUES('$tagID', '$newsID')";
		$query = $db->query($insertNewTag);
		if($query == FALSE)
			die('QUERY_NOT_ABLE_TO_PERFORM_6');
	
		die('OK'.$tagID);
	}
	else
		die('NO_ACCESS');
?>