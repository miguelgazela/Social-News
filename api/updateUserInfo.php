<?php
	session_start();
	
	if(isset($_SESSION['username'])) { 
	
		if(!isset($_GET['userID']))
			die('NO_USER_ID');
		if(!isset($_GET['setthis']))
			die('NO_SET_THIS');
		if(!isset($_GET['tothis']))
			die('NO_TO_THIS');

		$userID = $_GET['userID'];
		$setThis = $_GET['setthis'];
		$toThis = $_GET['tothis'];
		
		if($_SESSION['userID'] == $userID || $_SESSION['userPermission'] == 3) {
			$db = new PDO("sqlite:../socialnews.db");
			$update = "UPDATE users SET ".$setThis." = '".$toThis."' WHERE id = '$userID'";
			$query = $db->query($update);
		
			if($query == FALSE)
				die('QUERY_NOT_ABLE_TO_PERFORM');
			die('OK');
		}
		die('NO_ACCESS');
	}
	die('NO_ACCESS');
?>