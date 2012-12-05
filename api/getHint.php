<?php
	session_start();
	
	if(isset($_SESSION['username']) && ($_SESSION['userPermission'] == 2 || $_SESSION['userPermission'] == 3)) {
		$db = new PDO("sqlite:../socialnews.db");	
	
		if(!isset($_POST['str']))
			die('NO_STR');
		
		// Fill up array with names
		$select = "SELECT text FROM tags";
		$query = $db->query($select);
		if($query == FALSE)
			die('QUERY_NOT_ABLE_TO_PERFORM_');
		
		
	
		$resultTags = $query->fetchAll(PDO::FETCH_ASSOC);
		if(!empty($resultTags))
			foreach($resultTags as $tag)
				$a[] = $tag['text'];

		$tag = $_POST["str"];

		//lookup all hints from array if length of q > 0
		if (strlen($tag) > 0) {
			$hint = "";
  
			for($i = 0; $i < count($a); $i++) {
				if (strtolower($tag)==strtolower(substr($a[$i],0,strlen($tag)))) {
					if ($hint=="")
						$hint=$a[$i];
					else
						$hint=$hint." , ".$a[$i];
				}
			}

			// Set output to "no suggestion" if no hint were found
			// or to the correct values
			if ($hint == "")
				$response = "no suggestion";
			else
				$response = $hint;

			//output the response
			echo $response;
		}
	}
	else
		die('NO_ACCESS');
?>