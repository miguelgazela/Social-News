<?php
	session_start();
	
	if(isset($_SESSION['username']) && $_SESSION['userPermission'] == 3) { 
		if(!isset($_POST['server_name']))
			die('NO_SERVER_NAME');
			
		$serverName = $_POST['server_name'];
		$db = new PDO("sqlite:../socialnews.db");
		
		$selectServers = "SELECT * FROM servers";
		$query = $db->query($selectServers);
		if($query == FALSE)
			die('QUERY_NOT_ABLE_TO_PERFORM_1');
		
		$resultTags = $query->fetchAll(PDO::FETCH_ASSOC);
		if(!empty($resultTags)) {
			foreach($resultTags as $row) {
				if(strtolower($row['server_name']) == strtolower($serverName)) // server already exists
					die('ALREADY_EXISTS');
			}
		}
		
		$insert = "INSERT INTO servers VALUES(NULL,'$serverName')";
		$query = $db->query($insert);
		if($query == FALSE)
			die('QUERY_NOT_ABLE_TO_PERFORM_2');
			
		$getID = "SELECT id FROM servers WHERE oid = (SELECT MAX(oid) FROM servers)";
		$query = $db->query($getID);
		if($query == FALSE)
			die('QUERY_NOT_ABLE_TO_PERFORM_3');
			
		$result = $query->fetch(PDO::FETCH_ASSOC);
		$serverID = $result['id'];
		
		die('OK'.$serverID);
	}
	else
		die('NO_ACCESS');
?>