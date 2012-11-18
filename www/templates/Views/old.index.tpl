{call_controller path="Login/login"}
<html>
<center>
<h1>Welcome to Ichnaea:</h1>
{if $error eq 1}
<div id="error" style="color: red">
Email or password incorrect
</div>
{/if}
<form method="post">
<table>
<tr>
<td>Email</td><td><input type="text" name="email"/></td>
</tr>
<tr>
<td>Password</td><td><input type="password" name="passwd"/></td>
</tr>
<tr><input type="submit" value"Accept"></tr>
<tr><td><a href="/ichnaea/www/user/new">Forget your password?<td></tr>
<table>
</form>
</center>
