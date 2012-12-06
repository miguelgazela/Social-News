<?php
	session_start();
	header('Content-Type: application/json');
	$response;

	if(isset($_SESSION['username']) && $_SESSION['userPermission'] == 3) { 
		if(!isset($_GET['servername']))
			$response['result'] = 'NO_SERVER_NAME';
			
		$serverName = $_GET['servername'];
		
		if($result = file_get_contents($serverName))
			die($result);
		else
			$response['result'] = 'FAILURE';
	}
	else
		$response['result'] = 'NO_ACCESS';

	die(json_encode($response));
?>