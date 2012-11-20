<?php
	session_start();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">

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
				if(isset($_GET['news_id'])) {
				
					$db = new PDO("sqlite:socialnews.db");					
					$select = $db->prepare("SELECT news.*, users.username, COUNT(comments.id) as numberOfComments from users, news LEFT JOIN comments ON news.id = comments.news_id WHERE news.id=:news_id and users.id = news.author_ID GROUP BY news.id");
					$select->bindParam(':news_id', $_GET['news_id'], PDO::PARAM_INT);
					$select->execute();
					$result = $select->fetch();
					
					if(isset($result['id'])) {
						echo '<div class="fullNews">'
						, '<img src="' . $result['imgUrl'] . '" alt="news_image" />'
						, '<h3>' . $result['title'] . '</h3>'
						, '<p class="intro">' . $result['introduction'] . '</p>'
						, '<p>' . $result['fulltext'] . '</p>'
						, '<span class="posted-by">Posted by: <a href="user.php?user_id=' . $result['author_ID'] . '">' . $result['username'] . '</a>'
						, '<span class="date">' . $result['submissionDate'] . '</span></span>'
						, '</div>'
						, '<div id="comments">'
						, '<p>Comments</p>';
						
						if($result['numberOfComments'] != 0) {
							$newsID = $result['id'];
							$select = "SELECT comments.*, users.username FROM users, comments WHERE comments.news_id = '$newsID' AND comments.author_ID = users.id"; 
							$query = $db->query($select);
	
							if($query == FALSE)
								die('QUERY_NOT_ABLE_TO_PERFORM');
	
							$result = $query->fetchAll(PDO::FETCH_ASSOC);
													
							if(!empty($result)) {
								foreach($result as $comment) {
									echo '<div class="comment">'
									, '<h3>' . $comment['username'] . ' says</h3>'
									, '<h5>' . $comment['submissionDate'] . '</h5>'
									, '<p class="commentText">' . $comment['text'] .'</p></div>';
								}
							}
						}
						
						echo '</div>';
					}
					else
						echo '<p class="warning">No news with the provided id</p>';
				}
				else
					echo '<p class="warning">Please provide an id for the news</p>';
			?>
        </div>
        <div id="footer"></div>         
    </div>
	
</body>
</html>
