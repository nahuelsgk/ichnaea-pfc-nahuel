{init path="Controllers/Ichnaea/MainAppController"}
<html>
<head>
<title>{block name="title"}{/block}</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" type="text/css" href="/css/style.css">
<script src="/js/jquery-1.8.3.js"></script>
{block name="css_extra"}{/block}
{block name="js_extra"}{/block}
</head>

<div id="logo">
<a href="/home">Logo</a>
</div>
<div id="menu">
{include file="Tmpls/Menu/Menu.tpl"}
</div>
<div id="container">
<h1>{block name="headtitle"}{/block}</h1>
{block name="page"}{/block}
</div>
</body>
</html>
