<?php
	header('Content-Type: application/json');
	$response;

	if(!isset($_GET['numNews']))
		$response['result'] = 'NO_NUMBER_NEWS';

	$numNews = $_GET['numNews'];
	$db = new PDO("sqlite:../socialnews.db");


	if(!isset($_GET['displayed']))
		$query = "SELECT news.*, users.username, COUNT(comments.id) as numberOfComments FROM users, news LEFT JOIN comments ON news.id = comments.news_id WHERE news.author_ID = users.id GROUP BY news.id ORDER BY news.submissionDate DESC, news.id DESC LIMIT '$numNews'";
	else {
		$displayed = $_GET['displayed'];
		$sum = $displayed + $numNews;
		$query = "SELECT news.*, users.username, COUNT(comments.id) as numberOfComments FROM users, news LEFT JOIN comments ON news.id = comments.news_id WHERE news.author_ID = users.id GROUP BY news.id ORDER BY news.submissionDate DESC, news.id DESC LIMIT '$sum'";
	}

	if($result = $db->query($query)) {
		$news = $result->fetchAll(PDO::FETCH_ASSOC);
		if(!empty($news)) {
			$response['result'] = 'OK';
			if(!isset($_GET['displayed'])) {
				$response['data'] = $news;

				$select = "SELECT COUNT(id) as numNews FROM news";
				if($query = $db->query($select)) {
					$result = $query->fetch(PDO::FETCH_ASSOC);
					$response['moreNews'] = ($result['numNews'] > $numNews);
				}
				else
					$response['moreNews'] = false;
			}
			else {
				$wantedNews;
				for($i = $_GET['displayed']; $i < count($news); $i++)
					$wantedNews[] = $news[$i];
				$response['data'] = $wantedNews;
			}
		}
		else
			$response['result'] = 'NO_NEWS';
	}
	else
		$response['result'] = 'QUERY_NOT_ABLE_PERFORM_1';

	die(json_encode($response));
?>