<?php
	$db = new PDO("sqlite:../socialnews.db");
	$query = "SELECT * FROM news";
	
	if($result = $db->query($query)) {
		$result_rows = $result->fetchAll(PDO::FETCH_ASSOC);
		echo json_encode($result_rows);
	}
?>