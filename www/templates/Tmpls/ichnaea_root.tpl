{call_controller path="Ichnaea/MainAppController"}

<html>
<head>
<link rel="stylesheet" type="text/css" href="css/style.css">
<title>{block name="title"}{/block}</title>
<div id="menu">
{include file="../Tmpls/Menu/Menu.tpl"}
</div>
<div id="container">
{block name="page"}{/block}
</div>
</body>
</html>
