<?php
	session_start();
	
	if(isset($_SESSION['username']) && $_SESSION['userPermission'] == 3) { 
		if(!isset($_POST['userID']))
			die('NO_USER_ID');
		if(!isset($_POST['newPermission']))
			die('NO_PERMISSION');

		$userID = $_POST['userID'];
		$newPermission = $_POST['newPermission'];
			
		$db = new PDO("sqlite:../socialnews.db");
		$update = "UPDATE users SET permission = '$newPermission' WHERE id = '$userID'";
		$query = $db->query($update);
		
		if($query == FALSE)
			die('QUERY_NOT_ABLE_TO_PERFORM');
		die('OK');
	}
	else
		die('NO_ACCESS');
?>