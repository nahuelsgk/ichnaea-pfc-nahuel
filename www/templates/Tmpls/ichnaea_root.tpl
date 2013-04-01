{init path="Controllers/Ichnaea/MainAppController"}
{init path="Controllers/Ichnaea/Menu"}
<html>
<head>
<title>{block name="title"}{/block}</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" type="text/css" href="/css/style.css">
<link rel="stylesheet" type="text/css" href="/css/notifications.css">
<script src="/js/jquery-1.8.3.js"></script>
<script src="/js/jquery-ui-1.9.2.custom.min.js"></script>
<script language="javascript" type="text/javascript" src="/js/ichnaea.common.js"></script>
<script language="javascript" type="text/javascript">
var myMessages = ['info','warning','error','success']; // define the messages types		 
function hideAllMessages(){
  var messagesHeights = new Array(); // this array will store height for each
  for (i=0; i<myMessages.length; i++)
  {
    messagesHeights[i] = $('.' + myMessages[i]).outerHeight();
    $('.' + myMessages[i]).css('top', -messagesHeights[i]); //move element outside viewport	  
  }
}

function showMessage(type, duration, message)
{
  hideAllMessages();	
  $('.'+type+' > .message_deco').html(message);
  var height = $('.'+type).outerHeight();
  $('.'+type).animate( { top: "0" } , 500,  function(){ 
    setTimeout(function(){
      $('.'+type).animate( { top: -height } , 500);
    }, duration);
  } );
}

$(document).ready(function(){
  // Initially, hide them all
  hideAllMessages();
} ); 
</script>
{block name="css_extra"}{/block}
{block name="js_extra"}{/block}
</head>
<div>
<div class="info message">
  <h3>FYI, something just happened!</h3>
  <p>This is just an info notification message.</p>
</div>
<div class="error message">
  <h3>Ups, an error ocurred</h3>
  <p>This is just an error notification message.</p>
</div>
<div class="warning message">
  <h3>Wait, I must warn you!</h3>
  <p><div class="message_deco"></div></p>
</div>
<div class="success message">
  <h3>Succesful operation!</h3>
</div>
</div>
<div id="header">
<div id="logo">
<a href="/home">Logo</a>
</div>
<div id="menu">
{include file="Tmpls/Menu.tpl"}
</div>
<div class="clearfix"></div>

</div>
<div id="help_content" class="widget">
<center><h3>Draggable help online.</h3><br></center>
<div style="text-align : justify; padding: 5px;" >
{block name="help_text"}{/block}
</div>
</div>
<div id="container">
<div id="msgid"></div>
<h1>{block name="headtitle"}{/block}</h1>
{block name="page"}{/block}
</div>
</body>
</html>
