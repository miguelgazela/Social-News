<?php
	session_start();
	
	if(isset($_SESSION['username']) && $_SESSION['userPermission'] == 3) { 
		if(!isset($_GET['userID']))
			die('NO_USER_ID');

		$userID = $_GET['userID'];
			
		$db = new PDO("sqlite:../socialnews.db");
		$delete = "DELETE FROM users WHERE id = '$userID'";
		$query = $db->query($delete);
		
		if($query == FALSE)
			die('QUERY_NOT_ABLE_TO_PERFORM');
		die('OK');
	}
	else
		die('NO_ACCESS');
?>