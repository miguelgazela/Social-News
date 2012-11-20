<?php
	$db = new PDO("sqlite:../socialnews.db");
	$query = "SELECT news.*, users.username, COUNT(comments.id) as numberOfComments FROM users, news LEFT JOIN comments ON news.id = comments.news_id WHERE news.author_ID = users.id GROUP BY news.id";
	
	if($result = $db->query($query)) {
		$result_rows = $result->fetchAll(PDO::FETCH_ASSOC);
		echo json_encode($result_rows);
	}
?>