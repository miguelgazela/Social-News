<?php
	$db = new PDO("sqlite:../socialnews.db");
	$select = "SELECT MAX(oid) as max FROM news";
	
	if($query = $db->query($select)) {
		$result = $query->fetch(PDO::FETCH_ASSOC);
		echo $result['max'];
	}
	else
		die('QUERY_ERROR');
?>