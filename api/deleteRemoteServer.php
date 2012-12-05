<?php
	session_start();
	
	if(isset($_SESSION['username']) && $_SESSION['userPermission'] == 3) { 
		if(!isset($_POST['serverID']))
			die('NO_SERVER_ID');
			
		$serverID = $_POST['serverID'];			
		$db = new PDO("sqlite:../socialnews.db");
		$delete = "DELETE FROM servers WHERE id = '$serverID'";
		
		$query = $db->query($delete);
		
		if($query == FALSE)
			die('QUERY_NOT_ABLE_TO_PERFORM_1');
		die('OK');
	}
	else
		die('NO_ACCESS');
?>