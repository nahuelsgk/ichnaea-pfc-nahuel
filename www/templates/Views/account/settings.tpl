{extends file="Tmpls/ichnaea_root.tpl"}


{block name='title'}Account settings{/block}

{block name='headtitle'}Your personal information{/block}
{block name='page'}

  <header>
  <form method="post" autocomplete="on">
  <input id="username" name="username" required="required" type="text" data-icon="u" placeholder="John Doe" value="{$username}"/>
  </p>
  <p>
  <input id="passwordsignup_confirm" name="passwordsignup_confirm" type="password" placeholder="eg. X8df!90EO"/>    
  <label for="passwordsignup_confirm" class="youpasswd" >Change your password </label>
  </p>
  <p>
  <input id="passwordsignup" name="passwordsignup" type="password" placeholder="eg. X8df!90EO"/>
  <label for="passwordsignup" class="youpasswd">Change your password </label>
  </p>
  <p>
  <input id="enable_change_password" name="enable_change_password" type="checkbox"><label>Click here to change your password</label>
  </p>

  <p class="signin button">
  <input type="submit" name="register" value="Sign up" onclick="return match_passwords();"/>
  </p>
  </form>
{/block}

