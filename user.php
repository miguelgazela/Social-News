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
        
        <div id="content-wrapper" class="userProfile">
        	<?php
				if(isset($_GET['user_id'])) {
					
					$db = new PDO("sqlite:socialnews.db");
					$userID = $_GET['user_id'];
								
					// get the user info	
					$userSelect = "SELECT username, permission, registerDate, name, email FROM users WHERE id = '$userID'";
					$query = $db->query($userSelect);
					
					if($query == FALSE)
						die('QUERY_FAILURE');
					
					$result = $query->fetch(PDO::FETCH_ASSOC);
					if(isset($result['username'])) {
						$userPermission = $result['permission'];
						
						echo '<div><span class="info">Username:</span><h3>'.$result['username'].'</h3>';
						
						if(isset($_SESSION['userPermission']) && $_SESSION['userPermission'] == 3 && $userPermission == 1)
							echo '<input type="button" id="deleteUser" onClick="deleteUser('.$userID.')" value="Delete user" />';
						echo '</div>';
						
						// show user name and allow him or an admin to change it
						echo '<div><span class="info">Name:</span><h3 id="name">'.$result['name'].'</h3>';
						if( ($_SESSION['userPermission'] == 3 && $userPermission == 1) || $_SESSION['userID'] == $userID )
							echo '<input type="button" id="updateUserName" onClick="changeUserName('.$userID.')" value="Edit name" />';
						echo '</div>';
						
						// show user email and allow him or an admin to change it
						echo '<div><span class="info">E-mail:</span><h3 id="email">'.$result['email'].'</h3>';
						if( ($_SESSION['userPermission'] == 3 && $userPermission == 1) || $_SESSION['userID'] == $userID )
							echo '<input type="button" id="updateUserEmail" onClick="changeUserEmail('.$userID.')" value="Edit e-mail" />';
						echo '</div>';
						
						echo '<div><span class="info">User permission:</span>';
						if($userPermission == 1)
							echo '<h3>User</h3>';
						else if($userPermission == 2)
							echo '<h3>Editor</h3>';
						else if($userPermission == 3)
							echo '<h3>Admin</h3>';
						
						if($_SESSION['userPermission'] == 3 && $userPermission == 1)
								echo '<input type="button" id="changeUserPermission" onClick="changePermission(2, '.$userID.')" value="Promote to editor" />';
							else if($_SESSION['userPermission'] == 3 && $userPermission == 2)
								echo '<input type="button" id="changeUserPermission" onClick="changePermission(1, '.$userID.')" value="Demote to user" />';
						
						echo '</div>';
						
						
						echo '<div><span class="info">Registration date:</span><h3>'.$result['registerDate'].'</h3></div>';
						
						// query to show the users posted news					
						$newsSelect = "SELECT news.id, news.title, news.submissionDate FROM users, news WHERE users.id = '$userID' AND users.id = news.author_ID ORDER BY news.submissionDate DESC";
						$query = $db->query($newsSelect);
						
						if($query == FALSE)
							die('QUERY_FAILURE');
							
						$result = $query->fetchAll(PDO::FETCH_ASSOC);
						
						echo '<div id="userNews">'
						, '<h4>Posted news:</h4>'
						, '<ul>';
						
						foreach($result as $row) {
							echo '<li><span class="newsDate">'.$row['submissionDate'].'</span><a href="shownews.php?news_id='.$row['id'].'"><h3>'.$row['title'].'</h3></a></li>';	
						}
						echo '</ul></div>';
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
