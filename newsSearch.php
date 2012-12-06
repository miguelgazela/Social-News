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

<body class="_NewsSearch" id="top">
	<div id="wrapper">
    	<? include 'api/header.php'; ?>
        
        <div id="content-wrapper">
        	<?
        		if(!isset($_GET['awesome_search'])) {
					
					if(isset($_SESSION['username']) && $_SESSION['userPermission'] == 3) {
						echo '<form id="serverPick">'
						,'<p>Check the servers where you want to run the search</p>'
						, '<div><input type="checkbox" name="server" value="http://paginas.fe.up.pt/~ei10076/Social_News/api/news.php" checked="checked"><span>Our database</div>';
					
						// get the servers available
						$db = new PDO("sqlite:socialnews.db");
						$select = "SELECT * from servers";
						$query = $db->query($select);

						if($query != FALSE) {
							$result = $query->fetchAll(PDO::FETCH_ASSOC);
							foreach($result as $server)
								echo '<div><input type="checkbox" name="server" id="server'.$server['id'].'" value="'.$server['server_name'].'"><span>'.$server['server_name'].'</div>';	
						}
						echo '<input type="button" value="All" onClick="checkAll()">';
						echo '</form>';
					}
					
					echo '<div id="newsSearchForm">'
					, '	<p>Use at least one of the following fields</p>'
					, ' <div><input type="text" id="startDate_search" name="start_date" autocomplete="off" placeHolder="Start date (YYYY-MM-DDTHH:MM:SS)"></div>'
					, ' <div><input type="text" id="endDate_search" name="end_date" autocomplete="off" placeHolder="End date (YYYY-MM-DDTHH:MM:SS)"></div>'
					, ' <div><input type="text" id="tags_search" name="tags" autocomplete="off" placeHolder="tags separated by spaces"></div>';
					
					if(isset($_SESSION['username']) && $_SESSION['userPermission'] == 3)
						echo '	<div><input type="button" value="Search" name="search_submit" onClick="adminSearch()" /></div>';
					else
						echo '	<div><input type="button" value="Search" name="search_submit" onClick="newSearch(2)" /></div>';
						
					echo '</div>';
				}
				else {
					echo '<p class="hide" id="search_hidden">' . $_GET['awesome_search'] . '</p>';
					echo '<script type="text/javascript">'
					, 'newSearch(1);'
					, '</script>';
				}
			?>
            <div id="searchResults">
            </div>
        </div>
        <div id="footer"></div>         
    </div>
	
</body>
</html>
