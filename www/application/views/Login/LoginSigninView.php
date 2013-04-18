<div class="container">
<ul class="nav nav-tabs" id="myTab">
	<li class="active" id="tab_variables"><a href="#login">Login</a></li>
	<li id="tab_new_var_form" id="tab_login"><a href="#signin">Sig in</a></li>
	</ul>

	<div class="tab-content">
	<div class="tab-pane active" id="login">
	<?php echo form_open('verifylogin'); ?>
	<h1>Log in</h1>	
	<?php echo form_error('password'); ?>
	<p>
	<label for="username" class="uname" data-icon="u" > Your email</label>
	<input id="username" name="username" required="required" type="text" placeholder="myusername or mymail@mail.com"/>
	</p>
	<p>
	<label for="password" class="youpasswd" data-icon="p"> Your password </label>
	<input id="password" name="password" required="required" type="password" placeholder="eg. X8df!90EO" />
	</p>
	<p class="login button">
	<input type="submit" name="login" value="Login" />
	</p>
	</form>
	</div>
	
	<div class="tab-pane" id="signin">
	<?php echo form_open('signin#signin'); ?>
	<h1> Request an invitation </h1>
	<?php echo form_error('email_signin'); ?>
	<span style="text-align: justify;">Dear user,<br>
	This is a close application. Here you have a form to ask a invitation. As soon as possible, the system administrator will contact you to accept or decline your invitation.<br><br>
	</span>
	<center>
	
	</span>
	</center>
	<p>
	<label for="username" class="uname" data-icon="u">Your name and surname</label>
	<input id="username" name="username_signin" required="required" type="text" placeholder="John Doe" />
	</p>
	<p>
	<label for="email" class="youmail" data-icon="e" >Your email <i>(this will be use to login)</i></label>
	<input id="email" name="email_signin" required="required" type="email" placeholder="mysupermail@mail.com"/>
	</p>
	<p>
	<div id="message"></div>
	<label for="passwordsignup" class="youpasswd" data-icon="p">Your password </label>
	<input id="password_signin" name="password_signin" required="required" type="password" placeholder="eg. X8df!90EO"/>
	</p>
	<p>
	<label for="passwordsignup_confirm" class="youpasswd" data-icon="p">Please confirm your password </label>
	<input id="password_signin_confirm" name="password_signin_confirm" required="required" type="password" placeholder="eg. X8df!90EO"/>
	</p>
	<p class="signin button">
	<input type="submit" name="create_account" value="Sign up" onclick="return match_passwords();"/>
	</p>
	</form>
	</div>
</div>
</div>
</body>
<script type="text/javascript">
function match_passwords(){
	  var pass1 = document.getElementById('password_signin').value;
	  var pass2 = document.getElementById('password_signin_confirm').value;
	  var message = document.getElementById('message');
	  if (pass1 != pass2){
	    message.innerHTML = "Passwords Do Not Match!";
	    return false;
	  }
	  return true;
	}

$(document).ready(function(){
  $('#myTab a').click(function (e) {
    e.preventDefault();
    $(this).tab('show');
  });

  $(function() {
    var hash = window.location.hash;
    // do some validation on the hash here
    hash && $('ul.nav a[href="' + hash + '"]').tab('show');
  });
});
</script>
