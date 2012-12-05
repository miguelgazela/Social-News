<?php
	header('Content-Type: application/json');
	
	if(isset($_GET['start_date']))
	{		
		if(strlen($_GET['start_date'])==0)
		{
			//EMPTY START DATE TEXT FIELD, QUERY WON'T TAKE START DATE INTO ACCOUNT
			$unix_time_start = -1;
		}
		else
		{
			//START DATE TEXT FIELD NOT EMPTY, WILL CHECK FOR VALID DATE
			$unix_time_start = strtotime($_GET['start_date']);
			
			if(preg_match('/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})$/', $_GET['start_date']) != 1 || $unix_time_start == false)
			{
				$return_string = array(server_name => "T6G9", result => "error", reason => "Invalid start_date input", code => 4);
				echo json_encode($return_string);
				exit;
			}
		}
	}
	else
	{
		$return_string = array(server_name => "T6G9", result => "error", reason => "start_date field not given", code => 1);
		echo json_encode($return_string);
		exit;
	}
	
	if(isset($_GET['end_date']))
	{		
		if(strlen($_GET['end_date'])==0)
		{
			//EMPTY END DATE TEXT FIELD, QUERY WON'T TAKE END DATE INTO ACCOUNT
			$unix_time_end = -1;
		}	
		else
		{
			//END DATE TEXT FIELD NOT EMPTY, WILL CHECK FOR VALID DATE		
			$unix_time_end = strtotime($_GET['end_date']);
			
			if(preg_match('/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})$/', $_GET['end_date']) != 1 || $unix_time_end == false)
			{
				$return_string = array(server_name => "T6G9", result => "error", reason => "Invalid end_date input", code => 5);
				echo json_encode($return_string);
				exit;
			}
		}
	}
	else
	{
		$return_string = array(server_name => "T6G9", result => "error", reason => "end_date field not given", code => 2);
		echo json_encode($return_string);
		exit;
	}
	
	if($unix_time_start != -1 && $unix_time_end != -1 && $unix_time_start>$unix_time_end)
	{
		$return_string = array(server_name => "T6G9", result => "error", reason => "start_date is greater than end_date", code => 6);
		echo json_encode($return_string);
		exit;
	}
	
	
	if(isset($_GET['tags']) == FALSE)
	{
		$return_string = array(server_name => "T6G9", result => "error", reason => "tags field not given", code => 3);
		echo json_encode($return_string);
		exit;
	}
	
	if(strlen($_GET['start_date'])==0 && strlen($_GET['end_date'])==0 && strlen($_GET['tags'])==0)
	{
		$return_string = array(server_name => "T6G9", result => "error", reason => "specify at least one field (start_date, end_date or tags)", code => 7);
		echo json_encode($return_string);
		exit;
	}
	
	//Strip whitespaces from the beginning and end of the tags string, split tags string into individual tags. Tags are separated by one or more spaces.
	if(strlen($_GET['tags'])>0)
		$tags_array = preg_split('/\s+/', trim($_GET['tags']));
	
	$db = new PDO("sqlite:../socialnews.db"); 	
	
	$query_string = "SELECT news.id as id, news.title as title, news.imgUrl, news.introduction as intro, news.fulltext as text, strftime('%Y-%m-%dT%H:%M:%S',news.submissionDate) as date, users.username as posted_by, users.id as author_ID FROM news LEFT JOIN news_tags ON news.id = news_tags.news_id LEFT JOIN tags ON news_tags.tag_id = tags.id LEFT JOIN users on news.author_ID = users.id WHERE";
	
	//If start date was not empty, include it in the search
	if($unix_time_start!=(-1))
	{
		$query_string .= " news.submissionDate >= datetime(";
		$query_string .= $unix_time_start;
		$query_string .= ",'unixepoch')";
	}
	
	//If end date was not empty, include it in the search	
	if($unix_time_end!=(-1))
	{
		if($unix_time_start!=(-1))
			$query_string .= " AND";
		
		$query_string .= " news.submissionDate <= datetime(";
		$query_string .= $unix_time_end;	
		$query_string .= ",'unixepoch')";
	}
	
	//If tags was not empty, include it in the search
	if(count($tags_array)>0)
	{
		//AND IS ONLY NECESSARY IF ANY DATES HAVE BEEN DEFINED
		if($unix_time_start!=(-1) || $unix_time_end!=(-1))
			$query_string .= " AND";
			
		$query_string .= " (tags.text LIKE '%";
		$query_string .= $tags_array[0];
		$query_string .= "%'";
		
		$tags_index=0;
		
		foreach ($tags_array as $value)
		{
			//Don't repeat first tag
			if($tags_index > 0)
			{
				$query_string .= " OR tags.text LIKE '%";
				$query_string .= $value;
				$query_string .= "%'";
			}
			
			$tags_index++;			
		}
				
		//Order by news with most tags equal to tags string (descending)
		$query_string .= ") GROUP BY news.id ORDER BY COUNT(news.id) DESC, news.submissionDate DESC, news.id DESC";		
	}
	else	
		$query_string .= " GROUP BY news.id ORDER BY news.submissionDate DESC, news.id DESC";

	$stmt = $db->prepare($query_string);
	$stmt->execute();
	$result  = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	//Add url and tags for each news
	foreach ($result as $key => $value)
	{
		$news_id = $value['id'];
		$url = 'http://paginas.fe.up.pt/~ei10076/Social_News/shownews.php?news_id=';
		$url .= $news_id;
		$current_news=&$result[$key];
		$current_news['url'] = $url;
		
		$query_tags = "SELECT tags.text FROM tags, news_tags WHERE tags.id = news_tags.tag_id AND news_tags.news_id = ";
		$query_tags .= $news_id;
		$stmt2 = $db->prepare($query_tags);
		$stmt2->execute();
		$tags_for_news  = $stmt2->fetchAll(PDO::FETCH_COLUMN);
		$current_news['tags'] = $tags_for_news;
	}
	
	$server_answer['result'] = "success";
	$server_answer['server_name'] = "T6G9";	
	$server_answer['data'] = $result;	
					
	echo json_encode($server_answer);	
?>
