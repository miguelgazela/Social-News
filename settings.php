<?php
	session_start();
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">

<head>
    <title>Social News &mdash; T06G09 &mdash; Miguel Oliveira &mdash; Daniel Nora</title>
    
	<meta charset="UTF-8">
    <meta name="author" content="Miguel Oliveira &amp; Daniel Nora" />
    <meta name="description" content="LTW Social News Project 2012" />
    
    <link href="style.css" rel="stylesheet" type="text/css" />
    <script src="jquery.js"></script>
    <script src="scripts/scripts.js"></script>
</head>

<body class="config" id="top">
	<div id="wrapper">
    	<? include 'api/header.php'; ?>
        <div id="content-wrapper">
        <?php
			if(!isset($_SESSION['username']))
				echo '<p class="warning">To access this page, you need to Sign in</p>';
			else {
				if(isset($_SESSION['userPermission']) && $_SESSION['userPermission'] == 3) { // admin
					echo '<div id="servers">'
					, '<p class="servers">Remote servers available:</p>';
					
					$db = new PDO("sqlite:socialnews.db");
					$select = "SELECT * from servers";
					
					$query = $db->query($select);

					if($query == FALSE)
						echo '<p class="warning">Something went wrong. Please try again later.</p>';
					else {
						$result = $query->fetchAll(PDO::FETCH_ASSOC);
						
						foreach($result as $server) {
							echo '<div class="server" id="server'.$server['id'].'"><span class="groupName">'.$server['group_name'].'</span><span class="serverName">'.$server['server_name'].'</span><img src="images/remove.png" alt="remove server" onclick="removeServer('.$server['id'].')" /></div>';	
						}
					}				
					echo '</div>';
					
					echo '<form id="addServerForm">'
					, '<div>'
                	, '<label for="servername">New server url</label>'  
                	, '<input id="servername" name="servername" type="text" placeholder="server url" onblur="validateServerName()" onkeyup="validateServerName()" autocomplete="off" />'  
                	, '<span id="servernameInfo">What\'s the server absolute url?</span>'  
            		, '</div>'
					, '<div><input id="addServer" name="addServer" type="button" value="Add server" onClick="addNewServer()" /></div>' 
					, '</form>';
					
					$select = "SELECT * from tags ORDER BY text";
					$query = $db->query($select);
					if($result = $query->fetchAll(PDO::FETCH_ASSOC)) {
						if(!empty($result)) {
							echo '<div id="settingsTags">'
							, '<p class="tags">Tags available:</p>';
							
							foreach($result as $tag) {
								echo '<span class="tag" id="tag'.$tag['id'].'">'.ucwords($tag['text']).'<img src="images/remove8.png" alt="remove tag" onclick="deleteTag('.$tag['id'].')" /></span>';
							}	
						}
						echo '<input type="text" id="tagReader" placeholder="new tag...hit space to add" onkeyup="addNewTag(event);" autocomplete="off" /></div>';
					}
				}
				else {
					echo '<p class="warning">You don\'t have permission to access this page</p>';
				}
			}
		?>
        </div>
        <div id="footer"></div>        
    </div>
</body>
</html>
