// JavaScript Document

var MAX_INTRO_LENGTH = 500;

$(document).ready(function() {
  loadLatestNews();
});

function loadLatestNews() {
	var xmlhttp;
	if(window.XMLHttpRequest)
		xmlhttp = new XMLHttpRequest(); // code for IE7+, Firefox, Chrome, Opera, Safari
	else
  		if(window.ActiveXObject)
  			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP"); // code for IE6, IE5
		else
			alert ("Bummer! Your browser does not support XMLHTTP!");
	
	xmlhttp.open("GET","api/getNews.php",true);
	
	xmlhttp.onreadystatechange = function() {
		if(xmlhttp.readyState==4 && xmlhttp.status==200) { 
		
			var news = JSON.parse(xmlhttp.responseText);
			var newsLength = news.length;
			var numberNews;
			
			newsLength > 10 ? numberNews = 10 : numberNews = newsLength;
			
			/* adding the latest news */
			for(var i = 0; i < numberNews; i++) {
				$('#content-wrapper').append('<div class="news"></div>');
				$('div.news').last().append('<img src="http://ipsumimage.appspot.com/300x200,ff0077" alt="300x200" /><h3><a href="#">'+news[newsLength-(1+i)].title+'</a></h3><p class="intro">'+validateIntro(news[newsLength-(1+i)].introduction)+'</p><span class="posted-by">Posted by: '+news[newsLength-(1+i)].author_ID+'</span><ul><li><a href="noticia1.html">See more...</a></li><li><a href="comentarios1.html">Comments <span class="num-comments">(2)</span></a></li></ul>');
			}
			$('div.news').first().css("margin-bottom","100px");
		}
	}
	xmlhttp.send();  
}

function validateIntro(intro) {
	if(intro.length > MAX_INTRO_LENGTH)
		return intro.substring(0, MAX_INTRO_LENGTH) + '...';
	else
		return intro;
}

function addNews() {
	var ans = confirm("Adicionar esta notícia à base de dados?");
	
	if(ans) {
		console.log("Adicionar notícia");
		window.open("socialnews.html",  "_self", "", "");
	}
	else
		console.log("Notícia rejeitada");
}

/*
<div class="news">
                <img src="http://ipsumimage.appspot.com/300x200,ff0077" alt="300x200" />
                <h3><a href="#">Donec et metus</a></h3>
				<p class="intro">Donec et metus sit amet augue mattis bibendum et ut nunc. Donec eleifend laoreet suscipit. In <a href="link.html">ullamcorper leo</a> vel dui consectetur eu sodales nisl iaculis. Ut posuere ullamcorper nunc eu gravida. Maecenas pulvinar rutrum feugiat. Morbi dolor nibh, gravida vel lobortis quis, aliquet eget massa. In lectus risus, adipiscing id consequat non, ultricies non ligula. Maecenas a orci vitae erat elementum pretium. In ut arcu vel quam luctus faucibus vitae ac lectus. Mauris in lacus tristique augue aliquet tempor.</p>
                <span class="posted-by">Posted by: Miguel Oliveira</span>
				<ul>
					<li><a href="noticia1.html">See more...</a></li>
					<li><a href="comentarios1.html">Comments <span class="num-comments">(2)</span></a></li>
				</ul>
			</div>
*/