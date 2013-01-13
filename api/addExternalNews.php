<?php
	session_start();
	
	function validateString($string){  
        if(strlen($string) < 1)  // NOT VALID
            return false;  
        else  
            return true;  
    }
	
	if(isset($_SESSION['username']) && $_SESSION['userPermission'] == 3) {

		if(!isset($_POST['title']))
			die('NO_TITLE');
		if(!isset($_POST['date']))
			die('NO_DATE');
		if(!isset($_POST['text']))
			die('NO_TEXT');
		if(!isset($_POST['tags']))
			die('NO_TAGS');
	
		$title = $_POST['title'];
		$date = $_POST['date'];
		$text = $_POST['text'];
		$img = "http://fakeimg.pl/300x300/?text=Imported";
		$tags = $_POST['tags'];
		$intro='This news was imported from another remote server, so a news intro is not available.';
		$userID = $_SESSION['userID'];

		// add the news to the database
		if(!validateString($title))
			die('INVALID_TITLE');
		if(!validateString($date))
			die('INVALID_DATE');
		if(!validateString($text))
			die('INVALID_TEXT');
		if(!validateString($img))
			die('INVALID_IMG_URL');
			
		$db = new PDO("sqlite:../socialnews.db");
		$insert = "INSERT INTO news VALUES(NULL, '$userID', '$title', '$intro', '$text', '$date', '$img')";
		$query = $db->query($insert);
	
		if($query == FALSE)
			die('QUERY_NOT_ABLE_TO_PERFORM_1');
		

		// add the news tags and references
		$select = "SELECT MAX(oid) as max FROM news";
		$newsID;
	
		if($query = $db->query($select)) {
			$result = $query->fetch(PDO::FETCH_ASSOC);
			$newsID = $result['max'];
		}
		else
			die('QUERY_NOT_ABLE_TO_PERFORM_2');

		foreach($tags as $tag) {
			if(strlen($tag) < 1) // invalid tag
				continue;
							
			$selectTags = "SELECT * FROM tags";
			$query = $db->query($selectTags);
			if($query == FALSE)
				continue;
				
			$resultTags = $query->fetchAll(PDO::FETCH_ASSOC);
			$exists = FALSE;
			
			if(!empty($resultTags)) {
				foreach($resultTags as $row)
					if(strtolower($row['text']) == strtolower($tag)) { // already exists tag
						$tagID = $row['id'];
						$exists = TRUE;
						
						$selectRef = "SELECT news_id from news_tags WHERE tag_id = '$tagID'";
						$query = $db->query($selectRef);
						if($query == FALSE)
							continue;
							
						$resultRef = $query->fetchAll(PDO::FETCH_ASSOC);
						if(!empty($resultRef))
							foreach($resultRef as $ref)
								if($ref['news_id'] == $newsID) // the news already has a ref to that tag
									continue;
									
						// add a ref to that tag to the news
						$insertTag = "INSERT INTO news_tags VALUES('$tagID', '$newsID')";
						$query = $db->query($insertTag);
					}
			}
			
			if($exists == FALSE) {
				// add a new tag and a refference to the news
				$insertNewTag = "INSERT INTO tags VALUES(NULL, '$tag')";
				$query = $db->query($insertNewTag);
				if($query == FALSE)
					continue;
					
				$getID = "SELECT id FROM tags WHERE oid = (SELECT MAX(oid) FROM tags)";
				$query = $db->query($getID);
				if($query == FALSE)
					continue;
					
				$result = $query->fetch(PDO::FETCH_ASSOC);
				$tagID = $result['id'];
				
				$insertNewTag = "INSERT INTO news_tags VALUES('$tagID', '$newsID')";
				$query = $db->query($insertNewTag);
			}
		}
		die('OK');
	}
	else
		die('NO_ACCESS');
?>