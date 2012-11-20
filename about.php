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
</head>

<body>
	<div id="wrapper">
    	<? include 'api/header.php'; ?>
        <div id="content-wrapper">
        	<div id="about">
            	<h3>Projecto realizado no âmbito da cadeira LTW - MIEIC 2012/2013</h3>
                <h4>Implementação feita por <a href="https://sigarra.up.pt/feup/pt/fest_geral.cursos_list?pv_num_unico=200700604">Miguel Oliveira</a> e <a href="https://sigarra.up.pt/feup/pt/fest_geral.cursos_list?pv_num_unico=201000699">Daniel Nora</a></h4>
                <p>Tarefas concluídas:</p>
                <ul>
                	<li>Especificação e desenvolvimento de uma base de dados que permita armazenar notícias e três níveis de permissões de utilizadores (leitor, apenas com permissões de read; editor com permissões de read e write; administrador com capacidade para (des)promover utilizadores já registados a editores e gerir as ligações com outros servidores</li>
                    <li>Criação de um serviço web em PHP que permita fazer pesquisa de notícias na base de dados usando um protocolo conhecido</li>
                    <li>Visualização das notícias e interface da página em HTML e CSS
                    	<ul>
                        	<li>Listagem das últimas notícias inseridas</li>
                        </ul>
                    </li>
                    <li>Criação de um formulário para inserção local de notícias (validação parcial dos campos através de Javascript)</li>
                    <li>Utilização de AJAX/JSON para fazer a pesquisa de notícias existentes na base de dados local a cada servidor com integração das notícias no DOM usando funções de Javascript (através de jQuery)</li>
                </ul>
            </div>
        </div>
        <div id="footer"></div>
    </div>
	
</body>
</html>
