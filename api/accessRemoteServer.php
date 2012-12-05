<?php
	session_start();
	header('Content-Type: application/json');

	if(isset($_SESSION['username']) && $_SESSION['userPermission'] == 3) { 
		if(!isset($_GET['servername']))
			die('NO_SERVER_NAME');
			
		$serverName = $_GET['servername'];
		
		if($result = file_get_contents($serverName))
			die($result);
		else {
			$result['result'] = 'FAILURE';
			die(json_encode($result));
		}
	}
	else
		die('NO_ACCESS');
?>