/* constants used in some functions */
var MAX_INTRO_LENGTH = 480;
var NEWS_CONTAINER_HEIGHT = 220; // includes the 30px bottom margin
var FOOTER_HEIGHT = 50;
var MIN_NEWS = 10;
var NEWS_INC = 5;

/* global variables used in some functions */
var contentHeight;
var pageHeight = document.documentElement.clientHeight;
var scrollPosition;
var totalNews = 0;
var newsDisplayed = 0;
var maxNewsID = -1; 

$.ajaxSetup ({  
    cache: false  
});

$(document).ready(function(){

	// hide #back-top first
	$("#back-top").hide();
	
	// fade in #back-top
	$(function () {
		$(window).scroll(function () {
			if ($(this).scrollTop() > 100) {
				$('#back-top').fadeIn();
			} else {
				$('#back-top').fadeOut();
			}
		});

		// scroll body to 0px on click
		$('#back-top a').click(function () {
			$('body,html').animate({
				scrollTop: 0
			}, 800);
			return false;
		});
	});
});

/* 
 * Loads the latest news to the main page of the website #
 */
function loadLatestNews() {
	$.ajax({url:"api/getNews.php", type:"GET", processData: "false", dataType:"JSON", data:{numNews:MIN_NEWS}}).done(function(response) {
		if(response['result'] == 'OK') {
			var news = response['data'];
			var newsLength = news.length;
	
			//adding the latest news to the DOM
			for(var i = 0; i < newsLength; i++) {
				$('#content-wrapper').append('<div id="news_'+news[i].id+'" class="news"></div>');
				$('div.news').last().append('<img src="'+news[i].imgUrl+'" alt="news_image" /><h3><a href="shownews.php?news_id='+news[i].id+'">'+news[i].title+'</a></h3><p class="intro">'+validateIntro(news[i].introduction)+'</p><span class="posted-by">Posted by: <a href="user.php?user_id='+news[i].author_ID+'">'+news[i].username+'</a><span class="date">'+checkSubmissionDate(news[i].submissionDate)+'</span></span><ul><li><a href="shownews.php?news_id='+news[i].id+'"><div class="seemoreIcon"></div>See more...</a></li><li><a href="shownews.php?news_id='+news[i].id+'#comments"><div class="commentsIcon"></div>Comments <span class="num-comments">('+news[i].numberOfComments+')</span></a></li></ul>');
			}
		
			if(response['moreNews'] == true) // has more news in the database
				$('#content-wrapper').append('<a href="allnews.php" id="showMore">See more news</a>');
			
			$('div.news').first().css("margin-bottom","100px");
		}
		else if(response['result'] == 'NO_NEWS')
			$('p.warning').removeClass('hide'); 
		else
			alert("It looks like we're having some troubles in our side. Please try again later");
	}).fail(function(textStatus) {alert("'loadLatestNews' request failed: " + textStatus['status']+" ("+textStatus['statusText']+")");});
}

/*
 * Loads a user favorite news #
 */
function loadFavoriteNews() {
	
	$.ajax({url:"api/getFavorites.php", processData:"false", type:"GET", dataType:"JSON"}).done(function(response) {
		if(response['result'] == 'OK') { // query was successful
			news = response['news'];
			if(news.length == 0)
				$('#content-wrapper').append('<p class="warning">You don\'t have favorite news</p>'); // user doesn't have favorite news
			else {
				for(var i = 0; i < news.length; i++) {
					$('#content-wrapper').append('<div id="news_'+news[i].id+'" class="news"></div>');
					$('div.news').last().append('<img src="'+news[i].imgUrl+'" alt="news_image" /><h3><a href="shownews.php?news_id='+news[i].id+'">'+news[i].title+'</a></h3><p class="intro">'+validateIntro(news[i].introduction)+'</p><span class="posted-by">Posted by: <a href="user.php?user_id='+news[i].author_ID+'">'+news[i].username+'</a><span class="date">'+checkSubmissionDate(news[i].submissionDate)+'</span></span><ul><li><a href="shownews.php?news_id='+news[i].id+'"><div class="seemoreIcon"></div>See more...</a></li><li><a href="shownews.php?news_id='+news[i].id+'#comments"><div class="commentsIcon"></div>Comments <span class="num-comments">('+news[i].numberOfComments+')</span></a></li></ul>');
				}
			}
		}
		else
			alert("It looks like we're having some troubles in our side. Please try again later");
	}).fail(function(textStatus) {alert("'loadFavoriteNews' request failed: " + textStatus['status']+" ("+textStatus['statusText']+")");});
}

/*
 *	Gets the login info given by the user and tries to sign him in #
 */
function signin() {
	var info = $('#signinInfo');
	var username = $('#login input[name="login_username"]');
	var password = $('#login input[name="login_password"]');
	
	if(username.val().length == 0 || password.val().length == 0) {
		info.text("Both fields must be filled!"); 
		info.show('fast');
		setTimeout(hideInfo, 10000); // hides the info text after 10 seconds
	}
	
	$.ajax({url:"api/login.php", processData:"false", type:"POST", data:{login_username:username.val(), login_password:password.val()}}).done(function(response){
		if(response == 'LOGIN_FAILURE') {
			info.text("Invalid username or password");
			info.show('fast', function(){});
			setTimeout(hideInfo, 10000);
		}
		else if(response == 'OK') {
			if(document.URL.indexOf('newAccount.php') == -1) // if login is not on the new account page
				setTimeout("location.reload(true);", 1); // reloads the page
			else
				window.open('socialnews.php', '_self', '', ''); // else sends to the main page
		}
	}).fail(function(textStatus) {alert("'signin' request failed: " + textStatus['status']+" ("+textStatus['statusText']+")");});
}

/*
 * Loads a certain number of news to the page (MIN_NEWS or less if database has less than that) #
 */
function loadNews() {
	$.ajax({url:"api/getNews.php", type:"GET", processData:"false", dataType:"JSON", data:{numNews:MIN_NEWS}}).done(function(response){
		if(response['result'] == 'OK') {
			var news = response['data'];
			newsDisplayed = news.length; // MIN_NEWS or less
			
			// calculating contentHeight
			if((contentHeight = newsDisplayed * NEWS_CONTAINER_HEIGHT + FOOTER_HEIGHT) < 730)
				contentHeight = 730 + FOOTER_HEIGHT;
				
			// adding the latest news 
			for(var i = 0; i < newsDisplayed; i++)
				addNewsToPage_2(news[i]);
				
			setInterval('scroll();', 250); // 1/4 of a second
		}
		else if(response['result'] == 'NO_NEWS')
			$('p.warning').removeClass('hide');
		else			
			alert("It looks like we're having some troubles in our side. Please try again later");
	}).fail(function(textStatus) {alert("'loadNews' request failed: " + textStatus['status']+" ("+textStatus['statusText']+")");});
}

/*
 * Starts the initial steps for a search made by an admin #
 */ 
function adminSearch() {	
	var servers = new Array();
	$.each($("#serverPick input[name='server']:checked"), function() { // for each server that is checked
  		servers.push($(this).val());
	});
	
	if(servers.length == 0)
		alert("You have to pick at least 1 server");
	else {
		if($('#startDate_search').val() == '' && $('#endDate_search').val() == '' && $('#tags_search').val() == '') { // all fields empty
			alert('You have to use at least one of the fields');
			return false;
		}
		$('#searchResults').empty(); // clear possible old search results

		for(var i = 0; i < servers.length; i++) { // for each checked server
			new_location = servers[i];
			var startDate = $('#startDate_search').val();
			var endDate = $('#endDate_search').val();

			if(startDate == '')
				new_location += "?start_date=1970-01-01T08:00:00";
			else
				new_location += "?start_date=" + $('#startDate_search').val();

			if(endDate == '') {
				var dateNow = new Date();
				new_location += ("&end_date=" + dateNow.toISOString().substr(0,19));
			}
			else
				new_location += ("&end_date=" + $('#endDate_search').val());

			new_location += ("&tags=" + $('#tags_search').val());
			adminSearchAux(new_location, servers.length);
		}
		$('#searchResults').css("margin-top", "30px");
	}
}

/*
 * Makes the request to a specific remote server #
 */
function adminSearchAux(newLocation, totalServers) {
	console.log(newLocation);	
	$.ajax({url:"api/accessRemoteServer.php", type:"GET", dataType:"JSON", processData:"false", data: {servername:newLocation}}).done(function(response) {
		console.log(response);
		if(response['result'] == 'success') {
			var data = response['data'];
			$('#searchResults').append('<h4>Results from '+response['server_name']+' ('+newLocation.substr(0,newLocation.indexOf('?'))+'</h4>');
			$('#searchResults').append('<div id="'+response['server_name']+'"></div>');
			
			if(data.length == 0)
				$('#searchResults').append('<p class="serverWarning">This group doesn\'t have any news for you</p>');
				
			for(var j = 0; j < data.length; j++) {
				var news = data[j];
				listSearchResults(news, response['server_name']);
			}
		}
	}).fail(function(textStatus) {console.log("'adminSearchAux' request failed: " + textStatus['status']+" ("+textStatus['statusText']+")");});
}

function checkAll() {
	$('#serverPick input:checkbox').attr('checked', 'true');
}

/*
 * Adds the search result news to the DOM #
 */ 
function listSearchResults(news,server_name) {
	console.log(server_name);
	$.ajax({url:"api/hasThisNews.php", processData:"false", type:"GET", dataType:"JSON", data: {title:news.title, date:news.date.replace('T', ' ')}}).done(function(response) {
		var date = news.date.replace('T', ' ');
		// add the news 
		$('#searchResults #'+server_name).append('<div id="'+news.id+'" class="smallnews"></div>');
		$('#searchResults #'+server_name+' div.smallnews').last().append('<h3><a href="'+news.url+'" target="_blank">'+news.title+'</a></h3><span class="posted-by">Posted by: '+news.posted_by+'<span class="date">'+checkSubmissionDate(date)+'</span></span><ul><li><a href="'+news.url+'" target="_blank"><div class="seemoreIcon"></div>See more...</a></li><li><span class="cursor_pointer" id="add-'+news.id+'-'+server_name+'"><div class="addIcon"></div>Add...</span></li><li><span class="cursor_pointer" id="delete-'+news.id+'-'+server_name+'"><div class="deleteIcon"></div>Delete...</span></li><li><span class="cursor_pointer" id="refresh-'+news.id+'-'+server_name+'"><div class="refreshIcon"></div>Refresh...</span></li></ul>');
		
		// add option buttons
		$('#add-'+news.id+'-'+server_name).click({news: news, server: server_name}, addExternalNews);	
		$('#delete-'+news.id+'-'+server_name).click({news: news, server: server_name}, deleteExternalNews);						
		$('#refresh-'+news.id+'-'+server_name).click({news: news, server: server_name}, refreshExternalNews);

		// hide certain option buttons depending if the news is already on the database or not
		if(response['result'] == 'YES')
			$('#add-'+news.id+'-'+server_name).hide();
		else if(response['result'] == 'NO') {
			$('#delete-'+news.id+'-'+server_name).hide();
			$('#refresh-'+news.id+'-'+server_name).hide();
		}
		else
			alert("It looks like we're having some troubles in our side. Please try again later");
	}).fail(function(textStatus) {console.log("'listSearchResults' request failed: " + textStatus['status']+" ("+textStatus['statusText']+")");});
}

/*
 * adds an external news to the database #
 */
function addExternalNews(event) {
	var news = event.data.news;
	var server_name = event.data.server;
	maxNewsID++;
	
	$.ajax({url:"api/addExternalNews.php", processData:"false", type:"POST", data: {title:news.title, text:news.text, date:news.date.replace('T', ' ')}}).done(function(response) {
		if(response == 'OK')
		{
			$('#add-'+news.id+'-'+server_name).hide();	
			$('#delete-'+news.id+'-'+server_name).show();
			$('#refresh-'+news.id+'-'+server_name).show();			
		}
		else {
			alert("It looks like we're having some troubles in our side. Please try again later");			
			maxNewsID--;
		}
		}).fail(function(textStatus) {alert("'addExternalNews' request failed: " + textStatus['status']+" ("+textStatus['statusText']+")"); maxNewsID--;});
}

/*
 * deletes an external news from the database #
 */
function deleteExternalNews(event) {
	var news = event.data.news;
	var server_name = event.data.server;
	
	$.ajax({url:"api/deleteExternalNews.php", processData:"false", type:"POST", data: {title:news.title, date:news.date.replace('T', ' ')}}).done(function(response) {
		if(response == 'OK')
		{
			$('#add-'+news.id+'-'+server_name).show();	
			$('#delete-'+news.id+'-'+server_name).hide();
			$('#refresh-'+news.id+'-'+server_name).hide();			
		}
		else
			alert("It looks like we're having some troubles in our side. Please try again later");	
	}).fail(function(textStatus) {alert("'deleteExternalNews' request failed: " + textStatus['status']+" ("+textStatus['statusText']+")");});
}

/*
 * Refreshes an existing news of the database with the data of an external news #
 */
function refreshExternalNews(event) {
	var news = event.data.news;
	var server_name = event.data.server;

	$.ajax({url:"api/refreshExternalNews.php", processData:"false", type:"POST", data:{title: news.title, text:news.text, date:news.date.replace('T', ' ')}}).done(function(response) {
		if(response == 'OK')
			$('#refresh-'+news.id+'-'+server_name).hide();
		else
			alert("It looks like we're having some troubles in our side. Please try again later");
	}).fail(function(textStatus) {alert("'refreshExternalNews' request failed: " + textStatus['status']+" ("+textStatus['statusText']+")");});
}

/*
 * Make a new search
 */ 
function newSearch(option) {
	var new_location;
	
	if(option == 1) { // header search, only allows search with tags
		new_location = "api/news.php?start_date=&end_date=&tags=";
		new_location += $('#search_hidden').text();
	}
	else if(option == 2) { // advanced search
		if($('#startDate_search').val() == '' && $('#endDate_search').val() == '' && $('#tags_search').val() == '') {
			alert('You have to use at least one of the fields');
			return false;
		}
		new_location = "api/news.php?start_date=";
		new_location += $('#startDate_search').val();
		new_location += ("&end_date=" + $('#endDate_search').val());
		new_location += ("&tags=" + $('#tags_search').val());
	}
	else {
		alert("Misuse of newSearch()");	
		return false;
	}
	
	$.ajax({url:new_location, type:"GET", dataType:"JSON", processData:"false"}).done(function(searchResult) {
		if(searchResult['result'] == 'success') {
			$('#searchResults').empty(); // clear possible results from an old search
			var data = searchResult['data'];
								
			if(data.length == 0) {
				$('#searchResults').append('<p class="warning">Oops, it looks like we don\'t have any news for you</p>');
			}
			else { // returned at least 1 news
				for(var i = 0; i < data.length; i++) {
					var news = data[i];
					addNewsToPage_3(news);
				}
				$('#searchResults').css("margin-top", "30px");
			}
		}
		else {
			$('#searchResults').empty();
			alert("It looks like we're having some troubles in our side. Please try again later");		
		}
	}).fail(function(textStatus) {alert("'newSearch' request failed: " +textStatus['status']+" ("+textStatus['statusText']+")");});
}

/*
 * Creates a new user account #
 */
function createAccount() {	
	if(validateUserName() && validateUsername() && validatePass1() && validatePass2()) {
		var name = $('#name').val();
		var username = $('#username').val();
		var usernameInput = $('#username');
		var usernameInfo = $('#usernameInfo');
		var pass1 = $("#pass1").val();  
   		var pass2 = $("#pass2").val();

		$.ajax({url:"api/createAccount.php", type:"POST", processData:"false", data:{name:name, usernameDesired:username, pass1:pass1, pass2:pass2}}).done(function(response) {
			if(response == 'USERNAME_TAKEN') {
				usernameInput.addClass("error");  
   				usernameInfo.text("Oops, that username is already taken!");
    			usernameInfo.addClass("error");
			}
			else if(response == 'OK') {
				$('#newAccountForm').remove();
				$('#content-wrapper').append('<p class="warning">'+"You've got yourself a brand new account. You can now sign in.");
			}
			else if(response == 'WAIT')
				alert("You have to wait at least 30 seconds to create another account");
			else
				alert("It looks like we're having some troubles in our side. Please try again later");	
		}).fail(function(textStatus) {alert("'createAccount' request failed: " + textStatus['status']+" ("+textStatus['statusText']+")");});
	}
}

/*
 * Adds a comment to a news #
 */
function addComment() {
	var commentText = $('#newCommentText').val();
	var newsID = $('#newCommentForm input[name="newsID"]').val();

	$.ajax({url:"api/addComment.php", processData:"false", type:"GET", data:{newComment:commentText,newsID:newsID}}).done(function(response){
		if(response['result'] == 'OK') {
			var comment = response['comment'];
			$('#comments').append('<div class="hide comment id'+comment.id+'"><img src="images/remove.png" alt="remove comment" onclick="removeComment('+comment.id+','+comment.news_id +')" /><img class="edit" src="images/edit.png" alt="edit comment" onclick="editComment('+comment.id+')" /><h3>'+response['username']+'</h3><h5>'+comment.submissionDate+'</h5><p class="commentText">'+comment.text+'</p></div>');
			$('#newCommentText').val("");
			$('#comments div').last().show('slow');
		}
	}).fail(function(textStatus) {alert("'addComment' request failed: " + textStatus['status']+" ("+textStatus['statusText']+")");});
}

/*
 * Remove a comment from a news #
 */ 
function removeComment(commentID, newsID) {
	$.ajax({url:"api/removeComment.php", processData:"false", type:"GET", data:{commentID:commentID, newsID:newsID}}).done(function(response){
		if(response == 'OK') {
			$('#comments').children('div.id'+commentID).hide('slow');
			setTimeout(function() { $('#comments').children('div.id'+commentID).remove();}, 3000);
		}
		else
			alert("It looks like we're having some troubles in our side. Please try again later");	
	}).fail(function(textStatus) {alert("'removeComment' request failed: " + textStatus['status']+" ("+textStatus['statusText']+")");});
}

/*
 * Edit a comment from a news #
 */
function editComment(commentID) {
	var comment = $('#comments').children('div.id'+commentID);
	var text = comment.children('p.commentText');
	var icon = comment.children('img[src="images/edit.png"]');
	
	// check if the user is already editing the comment
	if(comment.children('textarea').length == 0) {
		icon.attr('src', 'images/save.png');
		comment.append('<textarea class="editComment">'+text.text()+'</textarea>');
	}
	else {
		var textarea = comment.children('textarea');
		if(textarea.val().length == 0) { // the new comment cannot be empty
			textarea.addClass("error");
		}
		else {
			textarea.removeClass("error");
			icon = comment.children('img[src="images/save.png"]');

			$.ajax({url:"api/editComment.php", type:"POST", processData:"false", data:{commentID:commentID, text:textarea.val()}}).done(function(response){
				if(response == 'OK')
					text.text(textarea.val());
				else
					alert("It looks like we're having some troubles in our side. Please try again later");	
				icon.attr('src', 'images/edit.png');
				textarea.remove();
			}).fail(function(textStatus) {alert("'editComment' request failed: " + textStatus['status']+" ("+textStatus['statusText']+")");});
		}
	}
}

/*
 * Edit the fields of a news #
 */ 
function editNews(newsID) {
	var title = $('.fullNews').children('h3');
	var intro = $('.fullNews').children('p.intro');
	var text = $('.fullNews').children('p.text');
	var date = $('.fullNews').children('span.date');
	var url = $('.fullNews').children('img');
	
	if($('#content-wrapper').children('#editNews').length == 0) { // check if user is already editing the news
		$('<div id="editNews"><input type="text" id="news_title_form" name="title" onblur="validateNewsTitle()" onkeyup="validateNewsTitle()" value="'+title.text()+'" /><textarea name="intro" id="news_intro_form" onblur="validateNewsIntro()" onkeyup="validateNewsIntro()" rows="7" cols="80">'+intro.text()+'</textarea><textarea name="text" id="news_text_form" onblur="validateNewsText()" onkeyup="validateNewsText()" rows="18" cols="80">'+text.text()+'</textarea><input type="text" name="imgurl" value="'+url.attr('src')+'" id="img_url_form" onblur="validateImgUrl()" onkeyup="validateImgUrl()" autocomplete="off" /><input type="button" value="Save changes" onClick="editNews('+newsID+')" /></div>').insertAfter('.fullNews');
	}
	else {
		if(validateNewsTitle() && validateNewsIntro() && validateNewsText() && validateImgUrl()) { // all the new fields are valid
			var xmlhttp;
			if(window.XMLHttpRequest)
				xmlhttp = new XMLHttpRequest(); // code for IE7+, Firefox, Chrome, Opera, Safari
			else
				if(window.ActiveXObject)
  					xmlhttp = new ActiveXObject("Microsoft.XMLHTTP"); // code for IE6, IE5
				else {
					alert ("Bummer! Your browser does not support XMLHTTP!");
					return;
				}
			
			var newTitle = $('#news_title_form').val();
			var newIntro = $('#news_intro_form').val();
			var newText = $('#news_text_form').val();
			var newUrl = $('#img_url_form').val();

			$.ajax({url:"api/editNews.php", type:"POST", processData:"false", data:{newsID:newsID, newsTitle:newTitle, newsIntro:newIntro, newsText:newText, newsUrl:newUrl}}).done(function(response){
				if(response == 'OK') {
					$('#editNews').remove();
					title.text(newTitle);
					intro.text(newIntro);
					text.text(newText);
					url.text(newUrl);	
				}
				else
					alert("It looks like we're having some troubles in our side. Please try again later");
			}).fail(function(textStatus) {alert("'editNews' request failed: " + textStatus['status']+" ("+textStatus['statusText']+")");});
		}
	}
}

/*
 * Remove a news from the database #
 */ 
function removeNews(newsID) {
	$.ajax({url:"api/removeNews.php", type:"GET", processData:"false", data:{newsID:newsID}}).done(function(response){
		if(response == 'OK') {
			$('#content-wrapper').remove();
			$('#wrapper').append('<div id="content-wrapper"><p class="warning">News successffully deleted</p></div>');
		}
		else
			alert("It looks like we're having some troubles in our side. Please try again later");	
	}).fail(function(textStatus) {alert("'removeNews' request failed: " + textStatus['status']+" ("+textStatus['statusText']+")");});
}

/*
 * Add a news to the database
 */
function addNews() {	
	if(validateNewsTitle() && validateNewsIntro() && validateNewsText() && validateImgUrl()) {
		var xmlhttp;
		if(window.XMLHttpRequest)
			xmlhttp = new XMLHttpRequest(); // code for IE7+, Firefox, Chrome, Opera, Safari
		else
			if(window.ActiveXObject)
  				xmlhttp = new ActiveXObject("Microsoft.XMLHTTP"); // code for IE6, IE5
			else {
				alert ("Bummer! Your browser does not support XMLHTTP!");
				return;
			}

		maxNewsID++;
				
		xmlhttp.open("POST","api/addNewNews.php",true);
		xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		xmlhttp.send($('#addNews').serialize());
				
		xmlhttp.onreadystatechange = function() {
			if(xmlhttp.readyState==4 && xmlhttp.status==200) { 				
				if(xmlhttp.responseText == 'OK') {
					$('#addNews').remove();
					$('#content-wrapper').append('<p class="warning">News successfully added to our database</p>');
				}
				else {
					maxNewsID--;
					alert("Oops, it looks like that something went wrong. Please try again later.");
				}
			}
		}
	}
}

function addNewsToPage_2(news) {
	$('#content-wrapper').append('<div id="news_'+news.id+'" class="news"></div>');
	$('div.news').last().append('<img src="'+news.imgUrl+'" alt="news image" /><h3><a href="shownews.php?news_id='+news.id+'">'+news.title+'</a></h3><p class="intro">'+validateIntro(news.introduction)+'</p><span class="posted-by">Posted by: <a href="user.php?user_id='+news.author_ID+'">'+news.username+'</a><span class="date">'+checkSubmissionDate(news.submissionDate)+'</span></span><ul><li><a href="shownews.php?news_id='+news.id+'"><div class="seemoreIcon"></div>See more...</a></li><li><a href="shownews.php?news_id='+news.id+'#comments"><div class="commentsIcon"></div>Comments <span class="num-comments">('+news.numberOfComments+')</span></a></li></ul>');
}

function addNewsToPage_3(news) {
	var date = news.date.replace('T', ' ');
	
	$('#searchResults').append('<div id="news_'+news.id+'" class="news"></div>');
	$('div.news').last().append('<img src="'+news.imgUrl+'" alt="news image" /><h3><a href="shownews.php?news_id='+news.id+'">'+news.title+'</a></h3><p class="intro">'+validateIntro(news.intro)+'</p><span class="posted-by">Posted by: <a href="user.php?user_id='+news.author_ID+'">'+news.posted_by+'</a><span class="date">'+checkSubmissionDate(date)+'</span></span><ul><li><a href="shownews.php?news_id='+news.id+'"><div class="seemoreIcon"></div>See more...</a></li><li><a href="shownews.php?news_id='+news.id+'"><div class="commentsIcon"></div>Comments</a></li></ul>');
}

/*
 * Marks a news as favorite for a user or removes the mark
 */
function favorite(newsID) {
	var star = $('#favorite');
	var option;
	
	if(star.hasClass('on'))
		option = 1; // remove favorite
	else
		option = 2; // add favorite
	
	$.ajax({url:"api/markFavorite.php", type:"GET", processData:"false", data:{newsID:newsID, option:option}}).done(function(response){
		if(response == 'OK') {
			if(star.hasClass('on')) { // remove favorite
				star.addClass('off');
				star.removeClass('on');	
			}
			else if(star.hasClass('off')) { // add favorite
				star.addClass('on');
				star.removeClass('off');	
			}
		}
		else 
			alert("It looks like we're having some troubles in our side. Please try again later");	
	}).fail(function(textStatus) {alert("'favorite' request failed: " + textStatus['status']+" ("+textStatus['statusText']+")");});
}

/*
 * Function that keeps adding news to the DOM as the user scrolls down the page #
 */
function scroll() {

	$.ajax({url:"api/getNumNews.php", processData:"false", type:"GET"}).done(function(response){
		if(response != 'QUERY_ERROR') {
			totalNews = response;

			if(newsDisplayed < totalNews) { // if there's still news to show
				if(navigator.appName == "Microsoft Internet Explorer")
					scrollPosition = document.documentElement.scrollTop;
				else
					scrollPosition = window.pageYOffset;
			  
			 	if((contentHeight - pageHeight - scrollPosition) < 400) { // if the user is approaching the end of the page
					$.ajax({url:"api/getNews.php", type:"GET", processData:"false", dataType:"JSON", data:{numNews:NEWS_INC, displayed:newsDisplayed}}).done(function(response){
						if(response['result'] == 'OK') {
							var news = response['data'];

							/*if((numberNews = totalNews - newsDisplayed) >= NEWS_INC)
								numberNews = NEWS_INC;*/
							numberNews = news.length;

							contentHeight += numberNews * NEWS_CONTAINER_HEIGHT;

							// adding more news to the page
							for(var i = 0; i < numberNews; i++)
								addNewsToPage_2(news[i]);
							
							newsDisplayed += numberNews;
						}
						else
							console.log("It looks like we're having some troubles in our side. Please try again later");
					}).fail(function(textStatus) {console.log("'scroll' request failed: " + textStatus['status']+" ("+textStatus['statusText']+")");});
			  	}
			}
		}
	})
}

/*
 * Deletes a user from the database #
 */
function deleteUser(userID) {
	$.ajax({url:"api/deleteUser.php", type:"GET", processData:"false", data:{userID:userID}}).done(function(response){
		if(response == 'OK') {
			$('#content-wrapper').remove();
			$('#wrapper').append('<div id="content-wrapper"><p class="warning">User successffully deleted</p></div>');
		}
		else 
			alert("It looks like we're having some troubles in our side. Please try again later");
	}).fail(function(textStatus) {alert("'deleteUser' request failed: " + textStatus['status']+" ("+textStatus['statusText']+")");});
}

/*
 * Promotes or demotes a user or an editor respectively #
 */
function changePermission(newPermission, userID) {
	$.ajax({url:"api/updateUserPermission.php", type:"POST", data: {userID: userID, newPermission: newPermission}, processData:"false"}).done(function(response) {
		if(response == 'OK') 
			window.location.reload();
		else 
			alert("It looks like we're having some troubles in our side. Please try again later");
	}).fail(function(textStatus) {alert("'changePermission' request failed: " + textStatus['status']+" ("+textStatus['statusText']+")");});	
}

/*
 * Deletes a tag from the database. It involves removing it from the database and remove the reference of any news that had that tag. #
 */
function deleteTag(tagID) {
	$.ajax({url:"api/deleteTag.php", type:"GET", processData:"false", data: {tagID: tagID}}).done(function(response) {
		if(response == 'OK') {
			$('#tag'+tagID).hide('slow');
			setTimeout(function(){$('#tag'+tagID).remove();}, 3000);
		}
		else
			alert("It looks like we're having some troubles in our side. Please try again later");
	}).fail(function(textStatus) {alert("'deleteTag' request failed: " + textStatus['status']+" ("+textStatus['statusText']+")");});
}

/*
 * Removes the reference of a tag from a news #
 */ 
function removeTag(tagID, newsID) {
	$.ajax({url:"api/removeTag.php", type:"POST", processData:"false", data: {tagID: tagID, newsID: newsID}}).done(function(response) {
		if(response == 'OK') {
			$('#tag'+tagID).hide('slow');
			setTimeout(function(){$('#tag'+tagID).remove();}, 3000);
		}
		else
			alert("It looks like we're having some troubles in our side. Please try again later");
	}).fail(function(textStatus) {alert("'removeTag' request failed: " + textStatus['status']+" ("+textStatus['statusText']+")");});
}

/*
 * Adds a new tag to the database and adds a reference to it to a news #
 */
function addTag(e, newsID) {
	if(e.keyCode == 32) { // everytime the space key is pressed 
		var tag = $('#tagReader').val();
		tag = tag.substr(0, tag.length-1); // removes the space
		tag = capitaliseFirstLetter(tag.toLowerCase()); // only the first letter is capitalized
		
		if(tag.length > 0) {	
			$.ajax({url:"api/addTag.php", processData:"false", type:"POST", data:{tag:tag, newsID:newsID}}).done(function(response){
				if(response.indexOf('OK') != -1) { // if response has OK in it
					var tagID = response.substr(2, response.length-2);
					$('<span class="tag" id="tag'+tagID+'">'+tag+'<img src="images/remove8.png" alt="remove tag" onclick="removeTag('+tagID+','+newsID+')" /></span>').insertBefore('#tagReader');
				}
			}).fail(function(textStatus) {alert("'addTag' request failed: " + textStatus['status']+" ("+textStatus['statusText']+")");});
		}
		$('#tagReader').val('');
	}
}

function addNewTag(event) {
	if(event.keyCode == 32) { // everytime the space key is pressed
		var tag = $('#tagReader').val();
		tag = tag.substr(0, tag.length-1); // removes the space
		tag = capitaliseFirstLetter(tag.toLowerCase()); // only the first letter is capitalized
		
		if(tag.length > 0) {	
			$.ajax({url:"api/addNewTag.php", processData:"false", type:"POST", data:{tag:tag}}).done(function(response){
				if(response.indexOf('OK') != -1) { // if response has OK in it
					var tagID = response.substr(2, response.length-2);
					$('<span class="tag justAdded" id="tag'+tagID+'">'+tag+'<img src="images/remove8.png" alt="remove tag" onclick="deleteTag('+tagID+')" /></span>').insertBefore('#tagReader');
				}
				else if(response != 'ALREADY_EXISTS')
					alert("It looks like we're having some troubles in our side. Please try again later");
			}).fail(function(textStatus) {alert("'addNewTag' request failed: " + textStatus['status']+" ("+textStatus['statusText']+")");});
		}
		$('#tagReader').val('');
	}
}


/*
 * Capitalizes the first letter of a string #
 */
function capitaliseFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

/*
 * Function that shows hints when a user is writing a new tab. The hints are tags that exist in the database that partially match the text wrote by the user. #
 */ 
function showHint(str) {
	if(str.length == 0) {
		$('#tagHint').text('');	
	}
	else {
		$.ajax({url:"api/getHint.php", processData:"false", type:"POST", data:{str:str}}).done(function(response){
			if(response != 'NO_STR' && response != 'NO_ACCESS' && response != 'QUERY_NOT_ABLE_TO_PERFORM') {
				$('#tagHint').text(response);
			}	
		}).fail(function(textStatus) {alert("'showHint' request failed: " + textStatus['status']+" ("+textStatus['statusText']+")");});
	}
}

/*
 * Removes a remote server from the database
 */
function removeServer(serverID) {

	$.ajax({url:"api/deleteRemoteServer.php", processData:"false", type:"POST", data:{serverID:serverID}}).done(function(response){
		if(response == 'OK')
			$('#server'+serverID).remove();
		else 
			alert("It looks like we're having some troubles in our side. Please try again later");
	}).fail(function(textStatus) {alert("'removeServer' request failed: " + textStatus['status']+" ("+textStatus['statusText']+")");});
}

/*
 * Adds a new remote server to the database
 */
function addNewServer() {
	var servername = $('#servername');
	var servernameInfo = $('#servernameInfo');
	
	if(validateServerName()) {	
		// check the response with AJAX to see if it's a valid server
		$.ajax({url:"api/accessRemoteServer.php", type:"GET", dataType:"JSON", processData:"false", data:{servername:servername.val() }}).done(function(response) {
			if(response['result'] == 'error' || response['result'] == "success") {
				addNewServerAux();
			}
			else if(response['result'] == 'FAILURE') {
				servername.addClass("error");
				servernameInfo.text("The url is not valid");
				servernameInfo.addClass("error");
			}
			else {
				servername.addClass("error");
				servernameInfo.text("The API of that server is not working correctly");
				servernameInfo.addClass("error");
			}
		}).fail(function(textStatus) {alert("'addNewServer' request failed: " + textStatus['status']+" ("+textStatus['statusText']+")");});
	}
}
	
function addNewServerAux() {
	var servername = $('#servername');
	var servernameInfo = $('#servernameInfo');
	
	$.ajax({url:"api/addRemoteServer.php", processData:"false", type:"POST", data: {server_name: servername.val()} }).done(function(response) {
		if(response.indexOf('OK') != -1) {
			var serverID = response.substr(2, response.length - 2);
			$('#servers').append('<div class="server" id="server'+serverID+'"><span class="serverName">'+servername.val()+'</span><img src="images/remove.png" alt="remove server" onclick="removeServer('+serverID+')" /></div>');
			$('#servername').val('');
			servernameInfo.removeClass("error");
			servernameInfo.text("What\'s the server absolute url?");	
		}
		else if(response == 'ALREADY_EXISTS') {
			servernameInfo.text("That remote server is already in the database");
			servernameInfo.addClass("error");
		}
		else 
			alert("It looks like we're having some troubles in our side. Please try again later");	
	}).fail(function(textStatus) {alert("'addNewServerAux' request failed: " + textStatus['status']+" ("+textStatus['statusText']+")");});
}

/*
 * Changes the name of a user. It receives the ID of that user
 */
function changeUserName(userID) {
	var updateButton = $('#updateUserName');
	var name = $('#name');
	
	if(updateButton.val() == 'Edit name') {
		updateButton.val("Save changes");
		// adds a new input for the name change
		$('<div id="nameEditor"><input id="inputNameEditor" type="text" value="'+name.text()+'" autocomplete="off" /><span id="infoNameEditor" class="error"></span></div>').insertAfter(name.parent());
	}
	else {
		var ck_name = /^[A-Za-z ]{3,40}$/;
		var newName = $('#inputNameEditor');
			
		if(ck_name.test(newName.val())) {
			var changes = newName.val();
			
			updateButton.val("Edit name");
			$('#nameEditor').remove();
			
			// update name of user
			$.ajax({url:"api/updateUserInfo.php", type:"GET", processData:"false", data: { userID: userID, setthis: 'name' , tothis: changes}}).done(function(response) {
				if(response == 'OK')
					name.text(changes);
				else
					alert("It looks like we're having some troubles in our side. Please try again later");
			}).fail(function(textStatus) {alert("'changeUserName' request failed: " + textStatus['status']+" ("+textStatus['statusText']+")");});
		}
		else
			$('#infoNameEditor').text('invalid name');
	}
}

/*
 * Changes the email of a user. It receives the ID of that user
 */
function changeUserEmail(userID) {
	var updateButton = $('#updateUserEmail');
	var email = $('#email');
	
	if(updateButton.val() == 'Edit e-mail') {
		updateButton.val("Save changes");
		// adds a new input for the email change
		$('<div id="emailEditor"><input id="inputEmailEditor" type="text" value="'+email.text()+'" autocomplete="off" /><span id="infoEmailEditor" class="error"></span></div>').insertAfter(email.parent());
	}
	else {
		var ck_email = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
		var newEmail = $('#inputEmailEditor');
			
		if(ck_email.test(newEmail.val())) {
			var changes = newEmail.val();
			
			updateButton.val("Edit e-mail");
			$('#emailEditor').remove();
			
			// update email of user
			$.ajax({url:"api/updateUserInfo.php", type:"GET", processData:"false", data: { userID: userID, setthis: 'email' , tothis: changes}}).done(function(response) {
				if(response == 'OK')
					email.text(changes);
				else
					alert("It looks like we're having some troubles in our side. Please try again later");
			}).fail(function(textStatus) {alert("'changeUserEmail' request failed: " + textStatus['status']+" ("+textStatus['statusText']+")");});
		}
		else
			$('#infoEmailEditor').text('invalid email');
	}
}

/*
 * Checks if there's been a new addition to the database, if so, it warns the user
 */ 
function checkForNews() {
		$.ajax({url:"api/getMaxNewsId.php", processData:"false", type:"GET"}).done(function(response) {
			if(maxNewsID == -1) { // still doesn't have the max news ID
				if(response != 'QUERY_ERROR') {
					maxNewsID = response;
					setInterval('checkForNews()', 1000); // checks again every second
				}
				else
					checkForNews();
			}
			else {
				if(response != 'QUERY_ERROR') {
					if(response > maxNewsID) {
						alert("There's been a new addition to our database!");
						maxNewsID = response;
					}
				}
			}
		});
}

/*
 * Checks if the submission date of a news is on the currrent day or not.
 */
function checkSubmissionDate(submissionDate) {
	submissionDate = submissionDate.replace(' ', 'T'); // so that new Date(submissionDate) works in firefox
	var newsDate = new Date(submissionDate);
	var dateNow = new Date();
		
	/* if day is the same, shows the hour as well, if not, it only shows the date */
	if( newsDate.getFullYear() == dateNow.getFullYear() && newsDate.getMonth() == dateNow.getMonth() && newsDate.getDate() == dateNow.getDate() ) 
		return submissionDate.replace('T',' ');
	else
		return newsDate.getFullYear() + '-' + (newsDate.getMonth()+1) + '-' + newsDate.getDate();
}

/*
 * Checks if a news intro fits in the css model used. If not, it returns a substring that does
 */
function validateIntro(intro) {
	if(intro.length > MAX_INTRO_LENGTH)
		return intro.substring(0, MAX_INTRO_LENGTH) + '(...)';
	else
		return intro;
}

function hideInfo() {
	$('#signinInfo').hide('fast', function(){});
}

/* The following functions are pretty self-explanatory */

function validateServerName() {
	var servername = $('#servername');
	var servernameInfo = $('#servernameInfo');
	
	if(servername.val().length < 15) { // has to have at least http:// and news.php	
		servername.addClass("error");
		servernameInfo.text("The length of the url is too small for it to be valid");
		servernameInfo.addClass("error");
		return false;
	}
	else {
		if(servername.val().indexOf('http://') != -1 && servername.val().indexOf('news.php') != -1) {
			servername.removeClass("error");  
        	servernameInfo.text("What\'s the server absolute url?");  
        	servernameInfo.removeClass("error");  
        	return true; 
		}
		else {
			servername.addClass("error");
			servernameInfo.text("The url is missing 'http://' or 'news.php'");
			servernameInfo.addClass("error");
			return false;
		}
	}
}

function validateUserName(){ 
	var name = $('#name');
	var nameInfo = $('#nameInfo'); 
	var ck_name = /^[A-Za-z ]{3,40}$/;
	 
    //if it's NOT valid  
    if(!ck_name.test(name.val())){  
        name.addClass("error");  
        nameInfo.text("Name must be between 3 and 40 characters long and no special ones");  
        nameInfo.addClass("error");  
        return false;  
    }  
    //if it's valid  
    else { 
        name.removeClass("error");  
        nameInfo.text("Hi "+name.val()+" =)");  
        nameInfo.removeClass("error");  
        return true;  
    }  
} 

function validateUsername(){ 
	var username = $('#username');
	var usernameInfo = $('#usernameInfo'); 
	var ck_username = /^[A-Za-z0-9_]{4,20}$/;
	
	if(!ck_username.test(username.val())) {
		username.addClass("error");  
        usernameInfo.text("Username must be between 3 and 20 characters long and no special ones except underscore!");  
        usernameInfo.addClass("error");  
        return false;  
	}
	else {
		username.removeClass("error");  
        usernameInfo.text("How do you want to be known?");  
        usernameInfo.removeClass("error");  
        return true;  
	}
} 

function validatePass1(){  
    var pass1 = $("#pass1");  
    var pass2 = $("#pass2");
	var pass1Info = $('#pass1Info');
	var ck_pass = /^[A-Za-z0-9!@#$%^&*()_]{6,20}$/;
  
    //it's NOT valid  
    if(!ck_pass.test(pass1.val())){  
        pass1.addClass("error");  
        pass1Info.text("Ey! Remember: At least 6 characters: letters, numbers and these: !@#$%^&*()_");  
        pass1Info.addClass("error");  
        return false;  
    }  
    else{             
        pass1.removeClass("error");  
        pass1Info.text("At least 6 characters: letters, numbers and these: !@#$%^&*()_");  
        pass1Info.removeClass("error");  
        validatePass2();  
        return true;  
    }  
}

function validatePass2(){  
    var pass1 = $("#pass1");  
    var pass2 = $("#pass2");  
	var pass1Info = $('#pass1Info');
	var pass2Info = $('#pass2Info');
	
    //are NOT valid  
    if( pass1.val() != pass2.val() ){  
        pass2.addClass("error");  
        pass2Info.text("Passwords doesn't match!");  
        pass2Info.addClass("error");  
        return false;  
    }  
    //are valid  
    else{  
        pass2.removeClass("error");  
        pass2Info.text("Confirm password");  
        pass2Info.removeClass("error");  
        return true;  
    }  
}

function validateNewsTitle() {
	var newsTitle = $('#news_title_form');
	if(newsTitle.val().length < 1) {
		newsTitle.addClass("error");
		return false;	
	}
	else {
		newsTitle.removeClass("error");
		return true;
	}
}

function validateNewsIntro() {
	var newsIntro = $('#news_intro_form');
	
	if(newsIntro.val().length < 1) {
		newsIntro.addClass("error");
		return false;	
	}
	else {
		newsIntro.removeClass("error");
		return true;
	}
}

function validateNewsText() {
	var newsText = $('#news_text_form');
	
	if(newsText.val().length < 1) {
		newsText.addClass("error");
		return false;	
	}
	else {
		newsText.removeClass("error");
		return true;
	}
}

function validateImgUrl() {
	var imgUrl = $('#img_url_form');
	
	if(imgUrl.val().length < 1) {
		imgUrl.addClass("error");
		return false;	
	}
	else {
		imgUrl.removeClass("error");
		return true;
	}
}

function validateNewsComment() {
	var comment = $('#newCommentText');
	
	if(comment.val().length < 1) {
		comment.addClass("error");
		return false;	
	}
	else {
		comment.removeClass("error");
		return true;
	}
}


