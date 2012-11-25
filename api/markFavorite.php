<?php
	session_start();
	
	if(isset($_SESSION['username'])) { 
			
		if(isset($_GET['newsID'])) {
			
			if(isset($_GET['option'])) {
				$db = new PDO("sqlite:../socialnews.db");
			
				$newsID = $_GET['newsID'];
				$userID = $_SESSION['userID'];
				
				$insert = "INSERT INTO news_favorites VALUES ('$userID', '$newsID')";
				$delete = "DELETE FROM news_favorites WHERE user_id = '$userID' AND news_id = '$newsID'";
				
				if($_GET['option'] == 1)
					$query = $db->query($delete);
				else
					$query = $db->query($insert);
		
				if($query == FALSE)
					die('QUERY_NOT_ABLE_TO_PERFORM');
			
				die('OK');
			}
			else
				die('NO_OPTION');
		}
		else
			die('NO_NEWS_ID');
	}
	else
		die('NO_ACCESS');
?>