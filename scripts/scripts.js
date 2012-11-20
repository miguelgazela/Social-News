var MAX_INTRO_LENGTH = 500;
var NEWS_CONTAINER_HEIGHT = 220; // includes the 30px bottom margin
var FOOTER_HEIGHT = 50;
var MIN_NEWS = 10;
var NEWS_INC = 5;

var contentHeight;
var pageHeight = document.documentElement.clientHeight;
var scrollPosition;
var totalNews;
var newsDisplayed = 0;
var xmlhttp;

function loadLatestNews() {
	var xmlhttp;
	if(window.XMLHttpRequest)
		xmlhttp = new XMLHttpRequest(); // code for IE7+, Firefox, Chrome, Opera, Safari
	else
  		if(window.ActiveXObject)
  			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP"); // code for IE6, IE5
		else
			alert ("Bummer! Your browser does not support XMLHTTP!");
	
	xmlhttp.open("GET","./api/getNews.php",true);
	
	xmlhttp.onreadystatechange = function() {
		if(xmlhttp.readyState==4 && xmlhttp.status==200) { 
		
			var news = JSON.parse(xmlhttp.responseText);
			var newsLength = news.length;
			var numberNews;

			if(newsLength == 0) {
				$('p.warning').removeClass('hide');
			}
			else {
				newsLength > 10 ? numberNews = 10 : numberNews = newsLength;
			
				/* adding the latest news */
				for(var i = 0; i < numberNews; i++) {
					console.log(news[newsLength-(1+i)]);
					$('#content-wrapper').append('<div id="news_'+news[newsLength-(1+i)].id+'" class="news"></div>');
					$('div.news').last().append('<img src="'+news[newsLength-(1+i)].imgUrl+'" alt="news_image" /><h3><a href="shownews.php?news_id='+news[newsLength-(1+i)].id+'">'+news[newsLength-(1+i)].title+'</a></h3><p class="intro">'+validateIntro(news[newsLength-(1+i)].introduction)+'</p><span class="posted-by">Posted by: <a href="user.php?user_id='+news[newsLength-(1+i)].author_ID+'">'+news[newsLength-(1+i)].username+'</a><span class="date">'+checkSubmissionDate(news[newsLength-(1+i)].submissionDate)+'</span></span><ul><li><a href="shownews.php?news_id='+news[newsLength-(1+i)].id+'"><div class="seemoreIcon"></div>See more...</a></li><li><a href="shownews.php?news_id='+news[newsLength-(1+i)].id+'#comments"><div class="commentsIcon"></div>Comments <span class="num-comments">('+news[newsLength-(1+i)].numberOfComments+')</span></a></li></ul>');
				}
				$('div.news').first().css("margin-bottom","100px");
			}
		}
	}
	xmlhttp.send();  
}

function signin() {
	var info = $('#signinInfo');
	var username = $('#login input[name="login_username"]');
	var password = $('#login input[name="login_password"]');
	
	if(username.val().length == 0 || password.val().length == 0) {
		info.text("Both fields must be filled!"); 
		info.show('fast', function(){});
		setTimeout(hideInfo, 10000); 
	}
	
	makeXMLHTTP();
	
	xmlhttp.open("POST","./api/login.php",true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	xmlhttp.send($('#login').serialize());
				
	xmlhttp.onreadystatechange = function() {
		if(xmlhttp.readyState==4 && xmlhttp.status==200) { 
			if(xmlhttp.responseText == 'LOGIN_FAILURE') {
				info.text("Invalid username or password");
				info.show('fast', function(){});
				setTimeout(hideInfo, 10000);
			}
			else if(xmlhttp.responseText == 'OK')
				if(document.URL.indexOf('newAccount.php') == -1) // login not on the new account page
					setTimeout("location.reload(true);", 1);
				else
					window.open('socialnews.php', '_self', '', '');
		}
	}
}

function loadNews() {
	makeXMLHTTP();
	xmlhttp.open("GET","./api/getNews.php",true);
	
	xmlhttp.onreadystatechange = function() {
		if(xmlhttp.readyState==4 && xmlhttp.status==200) { 
			var news = JSON.parse(xmlhttp.responseText); 
			totalNews = news.length;
			
			if(totalNews == 0) {
				$('p.warning').removeClass('hide');
			}
			else {
				totalNews > MIN_NEWS ? newsDisplayed = MIN_NEWS : newsDisplayed = totalNews;
			
				// calculating contentHeight
				if((contentHeight = newsDisplayed * NEWS_CONTAINER_HEIGHT + FOOTER_HEIGHT) < 730)
					contentHeight = 730 + FOOTER_HEIGHT;
			
				// adding the latest news 
				for(var i = 0; i < newsDisplayed; i++)
					addNewsToPage_2(news[totalNews-(1+i)]);
				
				setInterval('scroll();', 250); // 1/4 of a second
			}
		}
	}
	xmlhttp.send();  
}

function newSearch(option) {
	var new_location;
	
	if(option == 1) { // header search
		new_location = "api/news.php?start_date=&end_date=&tags=";
		new_location += $('#search_hidden').text();
	}
	else if(option == 2) { // advanced search
		new_location = "api/news.php?start_date=";
		new_location += $('#newsSearchForm input[name="start_date"]').val();
		new_location += ("&end_date=" + $('#newsSearchForm input[name="end_date"]').val());
		new_location += ("&tags=" + $('#newsSearchForm input[name="tags"]').val());
	}
	else {
		alert("Misuse of newSearch()");	
		return false;
	}
		
	makeXMLHTTP();
	
	xmlhttp.open("GET", new_location, true);
	xmlhttp.send();
	
	xmlhttp.onreadystatechange = function() {
		if(xmlhttp.readyState==4 && xmlhttp.status==200) { 
			var searchResult = JSON.parse(xmlhttp.responseText);
			
			if(searchResult['result'] == 'success') {
				var data = searchResult['data'];
				if(data.length == 0) {
					$('p.warning').removeClass('hide');
				}
				else { // returned at least 1 news
					// clear possible results from an old search
					$('#searchResults').empty();
					
					for(var i = 0; i < data.length; i++) {
						var news = data[i];
						addNewsToPage_3(news);
					}
					
					$('#searchResults').css("margin-top", "30px");
				}
			}
			else {
				alert("Erro de pesquisa");	
			}
		}
	}
}

function createAccount() {	
	if(validateUsername() && validatePass1() && validatePass2()) {
		makeXMLHTTP();
		
		var username = $('#username').val();
		var usernameInput = $('#username');
		var usernameInfo = $('#usernameInfo');
		var pass1 = $("#pass1").val();  
   		var pass2 = $("#pass2").val();
	
		xmlhttp.open("POST","./api/createAccount.php",true);
		xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		xmlhttp.send("usernameDesired="+username+"&pass1="+pass1+"&pass2="+pass2);
				
		xmlhttp.onreadystatechange = function() {
			if(xmlhttp.readyState==4 && xmlhttp.status==200) { 				
				if(xmlhttp.responseText == 'USERNAME_TAKEN') {
					usernameInput.addClass("error");  
       				usernameInfo.text("Oops, that username is already taken!");
        			usernameInfo.addClass("error");
				}
				else if(xmlhttp.responseText == 'OK') {
					//window.open('socialnews.php', '_self', '', '');
					var accountInfo = $('#createAccountInfo');
					accountInfo.text("You've got yourself a brand new account. You can now sign in!");
					accountInfo.show('fast', function(){});
				}
				else {
					alert("It looks like we're having some troubles in our side. Please try again later");	
				}
			}
		}
	}
}

function addNews() {	
	if(validateNewsTitle() && validateNewsIntro() && validateNewsText() && validateImgUrl()) {
		makeXMLHTTP();
	
		xmlhttp.open("POST","./api/addNewNews.php",true);
		xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		xmlhttp.send($('#addNews').serialize());	
				
		xmlhttp.onreadystatechange = function() {
			if(xmlhttp.readyState==4 && xmlhttp.status==200) { 				
				if(xmlhttp.responseText == 'OK') {
					$('#addNews').remove();
					$('#content-wrapper').append('<p class="warning">News successfully added to our database</p>');
				}
				else
					alert("Oops, it looks like that something went wrong. Please try again later.");
			}
		}
	}
}

function addNewsToPage_2(news) {
	$('#content-wrapper').append('<div id="news_'+news.id+'" class="news"></div>');
	$('div.news').last().append('<img src="'+news.imgUrl+'" alt="300x200" /><h3><a href="shownews.php?news_id='+news.id+'">'+news.title+'</a></h3><p class="intro">'+validateIntro(news.introduction)+'</p><span class="posted-by">Posted by: <a href="user.php?user_id='+news.author_ID+'">'+news.username+'</a><span class="date">'+checkSubmissionDate(news.submissionDate)+'</span></span><ul><li><a href="shownews.php?news_id='+news.id+'"><div class="seemoreIcon"></div>See more...</a></li><li><a href="shownews.php?news_id='+news.id+'#comments"><div class="commentsIcon"></div>Comments <span class="num-comments">('+news.numberOfComments+')</span></a></li></ul>');
}

function addNewsToPage_3(news) {
	var date = news.date.replace('T', ' ');
	
	$('#searchResults').append('<div id="news_'+news.id+'" class="news"></div>');
	$('div.news').last().append('<img src="'+news.imgUrl+'" alt="news image" /><h3><a href="shownews.php?news_id='+news.id+'">'+news.title+'</a></h3><p class="intro">'+validateIntro(news.intro)+'</p><span class="posted-by">Posted by: <a href="user.php?user_id='+news.author_ID+'">'+news.posted_by+'</a><span class="date">'+checkSubmissionDate(date)+'</span></span><ul><li><a href="shownews.php?news_id='+news.id+'"><div class="seemoreIcon"></div>See more...</a></li><li><a href="shownews.php?news_id='+news.id+'"><div class="commentsIcon"></div>Comments</a></li></ul>');
}

function scroll() {
	if(newsDisplayed < totalNews) {
		if(navigator.appName == "Microsoft Internet Explorer")
			scrollPosition = document.documentElement.scrollTop;
		else
			scrollPosition = window.pageYOffset;
	  
	 	if((contentHeight - pageHeight - scrollPosition) < 400) {
			makeXMLHTTP();
			xmlhttp.open("GET","./api/getNews.php",true);
		  
			xmlhttp.onreadystatechange = function() {
		  		if(xmlhttp.readyState==4 && xmlhttp.status==200) { 
			 		var news = JSON.parse(xmlhttp.responseText);
			  		var numberNews;
			  
			  		if( (numberNews = totalNews - newsDisplayed) < NEWS_INC )
						numberNews = totalNews - newsDisplayed;
					else
						numberNews = NEWS_INC;
					
				 	contentHeight += numberNews * NEWS_CONTAINER_HEIGHT;
			  
			  		// adding more news to the page 
			  		for(var i = 0; i < numberNews; i++)
						addNewsToPage_2(news[totalNews-newsDisplayed-(1+i)]);
					
					newsDisplayed += numberNews;
		  		}
	  		}
	 	 xmlhttp.send();  
	  	}
	}
}

function makeXMLHTTP() {
	if(window.XMLHttpRequest)
		xmlhttp = new XMLHttpRequest(); // code for IE7+, Firefox, Chrome, Opera, Safari
	else
		if(window.ActiveXObject)
  			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP"); // code for IE6, IE5
		else
			alert ("Bummer! Your browser does not support XMLHTTP!");
}

function checkSubmissionDate(submissionDate) {
	var newsDate = new Date(submissionDate);
	var dateNow = new Date();
	
	/* if day is the same, shows the hour as well, if not, it only shows the date */
		
	if( newsDate.getFullYear() == dateNow.getFullYear() && newsDate.getMonth() == dateNow.getMonth() && newsDate.getDate() == dateNow.getDate() ) 
		return submissionDate;
	else
		return newsDate.getFullYear() + '-' + (newsDate.getMonth()+1) + '-' + newsDate.getDate();
}

function validateIntro(intro) {
	if(intro.length > MAX_INTRO_LENGTH)
		return intro.substring(0, MAX_INTRO_LENGTH) + '...';
	else
		return intro;
}

function hideInfo() {
	$('#signinInfo').hide('fast', function(){});
}

function validateUsername(){ 
	var username = $('#username');
	var usernameInfo = $('#usernameInfo'); 
	 
    //if it's NOT valid  
    if(username.val().length < 4){  
        username.addClass("error");  
        usernameInfo.text("We want usernames with more than 3 letters!");  
        usernameInfo.addClass("error");  
        return false;  
    }  
    //if it's valid  
    else{  
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
  
    //it's NOT valid  
    if(pass1.val().length <5){  
        pass1.addClass("error");  
        pass1Info.text("Ey! Remember: At least 5 characters: letters, numbers and '_'");  
        pass1Info.addClass("error");  
        return false;  
    }  
    //it's valid  
    else{             
        pass1.removeClass("error");  
        pass1Info.text("At least 5 characters: letters, numbers and '_'");  
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


