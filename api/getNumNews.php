<?php
	$db = new PDO("sqlite:../socialnews.db");
	$select = "SELECT COUNT(id) as numNews FROM news";
	
	if($query = $db->query($select)) {
		$result = $query->fetch(PDO::FETCH_ASSOC);
		echo $result['numNews'];
	}
	else
		die('QUERY_ERROR');
?>