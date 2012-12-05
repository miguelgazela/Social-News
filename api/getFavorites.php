<?php
	session_start();
	header('Content-Type: application/json');
	$response;

	if(isset($_SESSION['username'])) { 
		$db = new PDO("sqlite:../socialnews.db");
		
		$userID = $_SESSION['userID'];
	
		$query = "SELECT news.*, users.username, COUNT(comments.id) as numberOfComments FROM news_favorites, users, news LEFT JOIN comments ON news.id = comments.news_id WHERE news.author_ID = users.id AND news.id = news_favorites.news_id AND news_favorites.user_id = '$userID' GROUP BY news.id ORDER BY news.submissionDate DESC";
	
		if($result = $db->query($query)) {
			$result_rows = $result->fetchAll(PDO::FETCH_ASSOC);
			$response['result'] = 'OK';
			$response['news'] = $result_rows;
		}
		else
			$response['result'] = 'QUERY_NOT_ABLE_TO_PERFORM';
	}
	else
		$response['result'] = 'NO_ACCESS';

	die(json_encode($response));
?>