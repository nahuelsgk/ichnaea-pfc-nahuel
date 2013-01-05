{init path="Controllers/Ichnaea/MainAppController"}
{init path="Controllers/Ichnaea/Menu"}
<html>
<head>
<title>{block name="title"}{/block}</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" type="text/css" href="/css/style.css">
<script src="/js/jquery-1.8.3.js"></script>
<script src="/js/jquery-ui-1.9.2.custom.min.js"></script>
<script language="javascript" type="text/javascript" src="/js/ichnaea.common.js"></script>
{block name="css_extra"}{/block}
{block name="js_extra"}{/block}
</head>

<div id="logo">
<a href="/home">Logo</a>
</div>
<div id="menu">
{include file="Tmpls/Menu.tpl"}
</div>

<div id="help_content" class="widget">
<center><h3>This is a draggable help online.</h3><br></center>
<p>Please be pacient! We are underconstruction.<br>
{block name="help_text"}{/block}</div>
<div id="container">
<h1>{block name="headtitle"}{/block}</h1>
{block name="page"}{/block}
</div>
</script>
</body>
</html>
