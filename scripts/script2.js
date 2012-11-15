// JavaScript Document

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

$(document).ready(function() {
  loadNews();
  setInterval('scroll();', 250); // 1/4 of a second
});

function loadNews() {
	makeXMLHTTP();
	xmlhttp.open("GET","api/getNews.php",true);
	
	xmlhttp.onreadystatechange = function() {
		if(xmlhttp.readyState==4 && xmlhttp.status==200) { 
			var news = JSON.parse(xmlhttp.responseText); 
			totalNews = news.length;
						
			totalNews > MIN_NEWS ? newsDisplayed = MIN_NEWS : newsDisplayed = totalNews;
			
			// calculating contentHeight
			if((contentHeight = newsDisplayed * NEWS_CONTAINER_HEIGHT + FOOTER_HEIGHT) < 730)
				contentHeight = 730 + FOOTER_HEIGHT;
			
			// adding the latest news 
			for(var i = 0; i < newsDisplayed; i++)
				addNewsToPage(news[totalNews-(1+i)]);
		}
	}
	xmlhttp.send();  
}

function scroll() {
	if(newsDisplayed < totalNews) {
		if(navigator.appName == "Microsoft Internet Explorer")
			scrollPosition = document.documentElement.scrollTop;
		else
			scrollPosition = window.pageYOffset;
	  
	 	if((contentHeight - pageHeight - scrollPosition) < 300) {
			makeXMLHTTP();
			xmlhttp.open("GET","api/getNews.php",true);
		  
			xmlhttp.onreadystatechange = function() {
		  		if(xmlhttp.readyState==4 && xmlhttp.status==200) { 
			 		var news = JSON.parse(xmlhttp.responseText);
			  		var numberNews;
			  
			  		if( (numberNews = totalNews - newsDisplayed) < NEWS_INC )
						numberNews = totalNews - newsDisplayed;
					
				 	contentHeight += numberNews * NEWS_CONTAINER_HEIGHT;
			  
			  		// adding more news to the page 
			  		for(var i = 0; i < numberNews; i++)
						addNewsToPage(news[totalNews-newsDisplayed-(1+i)]);
					
					newsDisplayed += numberNews;
		  		}
	  		}
	 	 xmlhttp.send();  
	  	}
	}
}

function addNewsToPage(news) {
	$('#content-wrapper').append('<div class="news"></div>');
	$('div.news').last().append('<img src="http://ipsumimage.appspot.com/300x200,ff0077" alt="300x200" /><h3><a href="#">'+news.title+'</a></h3><p class="intro">'+validateIntro(news.introduction)+'</p><span class="posted-by">Posted by: '+news.author_ID+'</span><ul><li><a href="noticia1.html">See more...</a></li><li><a href="comentarios1.html">Comments <span class="num-comments">(2)</span></a></li></ul>');
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

function validateIntro(intro) {
	if(intro.length > MAX_INTRO_LENGTH)
		return intro.substring(0, MAX_INTRO_LENGTH) + '...';
	else
		return intro;
}

