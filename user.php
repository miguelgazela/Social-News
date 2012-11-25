<?php
	session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Social News &mdash; T06G09 &mdash; Miguel Oliveira &mdash; Daniel Nora</title>
    
	<meta charset="UTF-8">
    <meta name="author" content="Miguel Oliveira &amp; Daniel Nora" />
    <meta name="description" content="LTW Social News Project 2012" />
    
    <link href="style.css" rel="stylesheet" type="text/css" />
    <script src="http://code.jquery.com/jquery-latest.js"></script>
    <script type="text/javascript" src="scripts/scripts.js"></script>
</head>

<body>
	<div id="wrapper">
    	<? include 'api/header.php'; ?>
        
        <div id="content-wrapper">
        	<?php
				if(isset($_GET['user_id'])) {
					
					$db = new PDO("sqlite:socialnews.db");
					$userID = $_GET['user_id'];
								
					// get the user info	
					$userSelect = "SELECT username, permission, registerDate FROM users WHERE id = '$userID'";
					$query = $db->query($userSelect);
					
					if($query == FALSE)
						die('QUERY_FAILURE');
					
					$result = $query->fetch(PDO::FETCH_ASSOC);
					if(isset($result['username'])) {
						
						echo '<p>'.$result['username'].'</p>';
						echo '<p>'.$result['permission'].'</p>';
						echo '<p>'.$result['registerDate'].'</p>';
						
						// query to show the users posted news					
						$newsSelect = "SELECT news.id, news.title, news.submissionDate FROM users, news WHERE users.id = '$userID' AND users.id = news.author_ID";
						$query = $db->query($newsSelect);
						
						if($query == FALSE)
							die('QUERY_FAILURE');
							
						$result = $query->fetchAll(PDO::FETCH_ASSOC);
						
						foreach($result as $row) {
							echo '<a href="shownews.php?news_id='.$row['id'].'"><p>'.$row['title'].'</p></a>';	
						}
					}
					else
						echo '<p class="warning">No user with the provided id</p>';
				}
				else
					echo '<p class="warning">Please provide an id for the user</p>';
			?>
        </div>
        <div id="footer"></div>         
    </div>
	
</body>
</html>
