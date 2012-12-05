<?php
	header('Content-Type: application/json');
	
	$db = new PDO("sqlite:../socialnews.db");
	$query = "SELECT news.*, users.username, COUNT(comments.id) as numberOfComments FROM users, news LEFT JOIN comments ON news.id = comments.news_id WHERE news.author_ID = users.id GROUP BY news.id";
	
	if($result = $db->query($query)) {
		$return['result'] = 'OK';
		$return['data'] = $result->fetchAll(PDO::FETCH_ASSOC);
		echo json_encode($return);
	}
	else {
		$return['result'] = 'QUERY_NOT_ABLE_PERFORM';
		echo json_encode($return);
	}
?>