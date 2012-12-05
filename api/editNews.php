<?php
	session_start();
	
	if(isset($_SESSION['username'])) {
		 
		if(!isset($_POST['newsID']))
			die('NO_NEWS_ID');
		if(!isset($_POST['newsTitle']))
			die('NO_NEWS_TITLE');
		if(!isset($_POST['newsIntro']))
			die('NO_NEWS_INTRO');
		if(!isset($_POST['newsText']))
			die('NO_NEWS_TEXT');
		if(!isset($_POST['newsUrl']))
			die('NO_NEWS_IMG_URL');
	
		$newsID = $_POST['newsID'];
		$newsTitle = $_POST['newsTitle'];
		$newsIntro = $_POST['newsIntro'];
		$newsText = $_POST['newsText'];
		$newsUrl = $_POST['newsUrl'];
		
		$userID = $_SESSION['userID'];
		
		$db = new PDO("sqlite:../socialnews.db");
		$update;
		
		if($_SESSION['userPermission'] == 1)
			die('NO_ACCESS');
		else if ($_SESSION['userPermission'] == 2)
			$update = "UPDATE news SET title = '$newsTitle', introduction = '$newsIntro', fulltext = '$newsText', imgUrl = '$newsUrl' WHERE id = '$newsID' AND author_ID = '$userID'";
		else
			$update = "UPDATE news SET title = '$newsTitle', introduction = '$newsIntro', fulltext = '$newsText', imgUrl = '$newsUrl' WHERE id = '$newsID'";
					
		$query = $db->query($update);
		
		if($query == FALSE)
			die('QUERY_NOT_ABLE_TO_PERFORM');
		die('OK');
	}
	else
		die('NO_ACCESS');
?>