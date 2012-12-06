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

<body class="_NewAccount" id="top">
	<div id="wrapper">
    	<? include 'api/header.php'; ?>
        
        <div id="content-wrapper">
        	<?
        		if(!isset($_SESSION['username'])) {
					echo '<form id="newAccountForm">  
							<div>  
                				<label for="name">Name</label>  
                				<input id="name" name="name" type="text" placeholder="name" onblur="validateUserName()" onkeyup="validateUserName()" autocomplete="off" />  
                				<span id="nameInfo">Hello! What\'s your name stranger?</span>  
            				</div>  
            				<div>  
                				<label for="username">Username</label>  
                				<input id="username" name="username" type="text" placeholder="username" onblur="validateUsername()" onkeyup="validateUsername()" autocomplete="off" />  
                				<span id="usernameInfo">How do you want to be known?</span>  
            				</div>   
            				<div>  
                				<label for="pass1">Password</label>  
               					<input id="pass1" name="pass1" type="password" placeHolder="password" onblur="validatePass1()" onkeyup="validatePass1()" />  
                				<span id="pass1Info">At least 6 characters: letters, numbers and these: !@#$%^&*()_</span>  
            				</div>  
            				<div>  
                				<label for="pass2">Confirm Password</label>  
                				<input id="pass2" name="pass2" type="password" placeHolder="confirm password" onblur="validatePass2()" onkeyup="validatePass2()" />  
                				<span id="pass2Info">Confirm password</span>  
           					</div>  
            				<div>  
                				<input id="send" name="send" type="button" value="Send" onclick="createAccount()" />  
            					</div>  
        				</form>';
				}
				else
					echo '<p class="warning">You already have an account</p>';	
			?>
            <div id="searchResults">
            </div>
        </div>
        <div id="footer"></div>         
    </div>
	
</body>
</html>
