{init path="Controllers/Login/loginRegistration" function="displayLoginRegistrationForm"}

<!DOCTYPE html>
<!--[if lt IE 7 ]> <html lang="en" class="no-js ie6 lt8"> <![endif]-->
<!--[if IE 7 ]>    <html lang="en" class="no-js ie7 lt8"> <![endif]-->
<!--[if IE 8 ]>    <html lang="en" class="no-js ie8 lt8"> <![endif]-->
<!--[if IE 9 ]>    <html lang="en" class="no-js ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html lang="en" class="no-js"> <!--<![endif]-->
<head>
<meta charset="UTF-8" />
<!-- <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">  -->
<title>Welcome to Ichnaea</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0"> 
<meta name="description" content="Login and Registration Form with HTML5 and CSS3" />
<meta name="author" content="Nahuel Velazco" />
<link rel="shortcut icon" href="../favicon.ico"> 
<link rel="stylesheet" type="text/css" href="/css/style.css" />
<link rel="stylesheet" type="text/css" href="/css/style3.css" />
<link rel="stylesheet" type="text/css" href="/css/animate-custom.css" />
</head>
<body>
<div class="registration">
<!-- Codrops top bar -->
<header>
<h1>Welcome to Ichnaea</h1>
</header>
<section>
<div id="container_demo" >
<!-- hidden anchor to stop jump http://www.css3create.com/Astuce-Empecher-le-scroll-avec-l-utilisation-de-target#wrap4  -->
<a class="hiddenanchor" id="toregister"></a>
<a class="hiddenanchor" id="tologin"></a>
<a class="hiddenanchor" id="toreset"></a>
<div id="wrapper">
<div id="login" class="animate form">
<form method="post" autocomplete="on">
<h1>Log in</h1>
{if $error eq "login"}
  <div style="color: red;">
  {$message}
  </div>
{/if}
<p>
<label for="username" class="uname" data-icon="u" > Your username</label>
<input id="username" name="username" required="required" type="text" placeholder="myusername or mymail@mail.com"/>
</p>
<p>
<label for="password" class="youpasswd" data-icon="p"> Your password </label>
<input id="password" name="password" required="required" type="password" placeholder="eg. X8df!90EO" />
</p>
<p class="login button">
<input type="submit" name="login" value="Login" />
</p>
<p class="change_link">
Not a member yet ?
<a href="#toregister" class="to_register">Join us</a>
<a href="#toreset" class="to_register">Reset password</a> 
</p>
</form>
</div>

<div id="reset" class="animate form">
<form method="post" autocomplete="on">
<h1> Forget your password </h1>
<center>
<span class="message" style="color: red;">
{if $error eq 1}
TODO: Specific error message
{/if }
</span>
</center>
<p>
<label for="email" class="youmail" data-icon="e">Write your email</label>
<input id="email" name="email" required="required" type="email" placeholder="mysupermail@mail.com"/>
</p>
<p class="change_link">
Are you a member ?
<a href="#tologin" class="to_register">Go and log in</a>
<a href="#toregister" class="to_register">Join us</a> 
</p>

</form>
</div>

<div id="register" class="animate form">
<form method="post" autocomplete="on">
<h1> Sign up </h1>
<center>
<span id="message" class="message" style="color: red;">
{if $error eq "register"}
{$message}
{/if }
</span>
</center>
<p>
<label for="username" class="uname" data-icon="u">Your name and surname</label>
<input id="username" name="username" required="required" type="text" placeholder="John Doe" />
</p>
<p>
<label for="email" class="youmail" data-icon="e" >Your email <i>(this will be use to login)</i></label>
<input id="email" name="email" required="required" type="email" placeholder="mysupermail@mail.com"/>
</p>
<p>
<label for="passwordsignup" class="youpasswd" data-icon="p">Your password </label>
<input id="passwordsignup" name="passwordsignup" required="required" type="password" placeholder="eg. X8df!90EO"/>
</p>
<p>
<label for="passwordsignup_confirm" class="youpasswd" data-icon="p">Please confirm your password </label>
<input id="passwordsignup_confirm" name="passwordsignup_confirm" required="required" type="password" placeholder="eg. X8df!90EO"/>
</p>
<p class="signin button">
<input type="submit" name="register" value="Sign up" onclick="return match_passwords();"/>
</p>
<p class="change_link">
Already a member ?
<a href="#tologin" class="to_register" > Go and log in </a>
<a href="#tologin" class="to_register" > Reset password </a>
</p>
</form>
<script type="text/javascript">
function match_passwords(){
  var pass1 = document.getElementById('passwordsignup').value;
  var pass2 = document.getElementById('passwordsignup_confirm').value;
  var message = document.getElementById('message');
  if (pass1 != pass2){
    message.innerHTML = "Passwords Do Not Match!";
    return false;
  }
  return true;
}
</script>
</div>

</div>
</div>
</section>
</div>
</body>
</html>
