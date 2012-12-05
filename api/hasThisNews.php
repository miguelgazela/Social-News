<?php	
	header('Content-Type: application/json');
	
	if(!isset($_GET['title']))
		die('NO_TITLE');
	if(!isset($_GET['date']))
		die('NO_DATE');
		
	$db = new PDO("sqlite:../socialnews.db");
	$title = $_GET['title'];
	$date = $_GET['date'];		

	$select = "SELECT id FROM news WHERE title = '$title' AND submissionDate = '$date'";
	
	if($query = $db->query($select)) {
		$result = $query->fetch(PDO::FETCH_ASSOC);
		if(!empty($result)) {
			$response['result'] = 'YES';
			$response['id'] = $result['id'];
		}
		else
			$response['result'] = 'NO';
	}
	else
		$response['result'] = 'QUERY_ERROR';
		
	die(json_encode($response));
?>