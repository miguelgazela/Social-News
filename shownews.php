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
    <script src="http://code.jquery.com/jquery-latest.js"></script>
    <script type="text/javascript" src="scripts/scripts.js"></script>
</head>

<body>
	<div id="wrapper">
    	<? include 'api/header.php'; ?>
        
        	<?php
				echo '<div id="content-wrapper">';
				if(isset($_GET['news_id'])) {
				
					$db = new PDO("sqlite:socialnews.db");
					$newsID = $_GET['news_id'];
										
					$select = "SELECT news.*, users.username, COUNT(comments.id) as numberOfComments from users, news LEFT JOIN comments ON news.id = comments.news_id WHERE news.id = '$newsID' AND users.id = news.author_ID GROUP BY news.id";
					$query = $db->query($select);
					
					if($query == FALSE)
						die('QUERY_NOT_ABLE_TO_PERFORM_1');
						
					$result = $query->fetch(PDO::FETCH_ASSOC);
					
					if(isset($result['id'])) {
						echo '<div class="fullNews">'
						, '<img src="' . $result['imgUrl'] . '" alt="news_image" />';
						
						// adds a icon to remove the news if the user is the author or if he is a admin
						if(isset($_SESSION['username'])) {
							if($_SESSION['userPermission'] == 2) {
								if($result['author_ID'] == $_SESSION['userID']) {
									echo '<img src="images/remove.png" class="remove" alt="remove news" onclick="removeNews(' . $result['id'] . ')" />';
									echo '<img class="editN" src="images/edit.png" alt="edit news" onclick="editNews('.$result['id'].')" />';
								}
							}
							else if($_SESSION['userPermission'] == 3) {
								echo '<img src="images/remove.png" class="remove" alt="remove news" onclick="removeNews(' .$result['id']. ')" />';
								echo '<img class="editN" src="images/edit.png" alt="edit news" onclick="editNews('.$result['id'].')" />';
							}
						}
						
						// adds the news title, introduction, text, author and submission date
						echo '<h3>' . $result['title'] . '</h3>'
						, '<p class="intro">' . $result['introduction'] . '</p>'
						, '<p class="text">' . $result['fulltext'] . '</p>'
						, '<span class="posted-by">Posted by: <a href="user.php?user_id=' . $result['author_ID'] . '">' . $result['username'] . '</a></span>'
						, '<span class="date">' . $result['submissionDate'] . '</span>';
						
						// check if the user has marked this news as favorite
						if(isset($_SESSION['username'])) {
							$userIDfav = $_SESSION['userID'];
							$newsIDfav = $_GET['news_id'];
							
							$favorite = "SELECT * from news_favorites WHERE user_id = '$userIDfav' and news_id = '$newsIDfav'";
							$queryFav = $db->query($favorite);
							
							if($queryFav == FALSE)
								die('QUERY_NOT_ABLE_TO_PERFORM_2');
								
							$resultFav = $queryFav->fetch(PDO::FETCH_ASSOC);
							
							if(isset($resultFav['user_id']))
								echo '<div id="favorite" class="on" onclick="favorite(' . $newsIDfav . ')"></div>';
							else
								echo '<div id="favorite" class="off" onclick="favorite(' . $newsIDfav . ')"></div>';
						}
												
						echo '</span>'
						, '</div>'; // closing fullNews
																		
						// show the tags of this news
						$newsID = $result['id'];
						$tagsSelect = "SELECT id, text, news_id FROM news_tags natural join tags WHERE news_id = '$newsID' AND tags.id = tag_id";
						$queryTags = $db->query($tagsSelect);
						if($queryTags == FALSE)
							die('QUERY_NOT_ABLE_TO_PERFORM_3');
						
						$resultTags = $queryTags->fetchAll(PDO::FETCH_ASSOC);
						
						echo '<div id="tags">';
						if(!empty($resultTags)) {
							foreach($resultTags as $tag) {
								if(($_SESSION['userPermission'] == 2 || $_SESSION['userPermission'] == 3) && $result['author_ID'] == $_SESSION['userID'])
									echo '<span class="tag" id="tag'.$tag['id'].'">'.ucwords($tag['text']).'<img src="images/remove8.png" alt="remove tag" onclick="removeTag('.$tag['id'].','.$newsID.')" /></span>';
								else
									echo '<span class="tag" id="tag'.$tag['id'].'">'.$tag['text'].'</span>';
							}
						}
						if(($_SESSION['userPermission'] == 2 || $_SESSION['userPermission'] == 3)  && $result['author_ID'] == $_SESSION['userID'])
							echo '<input type="text" id="tagReader" placeholder="new tag..." onkeyup="addTag(event, '.$newsID.');showHint(this.value);" autocomplete="off" /><p>Suggestions: <span id="tagHint"></span></p>';
						echo '</div>';
						
						echo '<div id="comments">'
						, '<p>Comments</p>';
						
						if($result['numberOfComments'] != 0) {
							$authorID = $result['author_ID'];
							$select = "SELECT comments.*, users.username FROM users, comments WHERE comments.news_id = '$newsID' AND comments.author_ID = users.id"; 
							$query = $db->query($select);
	
							if($query == FALSE)
								die('QUERY_NOT_ABLE_TO_PERFORM_1');
	
							$result = $query->fetchAll(PDO::FETCH_ASSOC);
							
							// load all comments with options according to the user logged in			
							if(!empty($result)) {
								foreach($result as $comment) {
									echo '<div class="comment id'.$comment['id'].'">';
																		
									if(isset($_SESSION['username'])) {
										if($_SESSION['userPermission'] == 1) {
											if($comment['author_ID'] == $_SESSION['userID']) {
												echo '<img src="images/remove.png" alt="remove comment" onclick="removeComment('.$comment['id'].','.$newsID.')" />';
											}
										}
										else if($_SESSION['userPermission'] == 2) {
											if($_SESSION['userID'] == $authorID || $_SESSION['userID'] == $comment['author_ID']) // can only delete comments on his news or his comments
												echo '<img src="images/remove.png" alt="remove comment" onclick="removeComment('.$comment['id'].','.$newsID.')" />';
										}
										else
											echo '<img src="images/remove.png" alt="remove comment" onclick="removeComment('.$comment['id'].','.$newsID.')" />';
										
										if($comment['author_ID'] == $_SESSION['userID'])
											echo '<img class="edit" src="images/edit.png" alt="edit comment" onclick="editComment('.$comment['id'].')" />';
									}
									
									echo '<h3><a href="user.php?user_id='.$comment['author_ID'].'">' . $comment['username'] . '</a> says</h3>'
									, '<h5>' . $comment['submissionDate'] . '</h5>'
									, '<p class="commentText">' . $comment['text'] .'</p>'
									, '</div>';
								}
							}
						}
						echo '</div>';
						
						// input for a new comment
						if(isset($_SESSION['username']) && isset($_SESSION['userPermission'])) {
							echo '<form id="newCommentForm">'
							, '<label for="newComment">New comment</label>'
							, '<textarea name="newComment" id="newCommentText" onblur="validateNewsComment()" onkeyup="validateNewsComment()" rows="4" cols="80"></textarea>'
							, '<input type="hidden" name="newsID" value="' . $_GET['news_id'] . '" />'
							, '<input type="button" value="Comment" onClick="addComment()" />'
							, '</form>';
						}
					}
					else
						echo '<p class="warning">No news with the provided id</p>';
				}
				else
					echo '<p class="warning">Please provide an id for the news</p>';
				
				echo '</div>';
			?>
        <div id="footer"></div>         
    </div>
	
</body>
</html>
