{extends file="Tmpls/ichnaea_root.tpl"}


{block name='title'}Account settings{/block}

{block name='headtitle'}Your personal information{/block}
{block name='page'}
{init path="Controllers/Account" function="displaySettings"}
  <form method="post" autocomplete="on">
  <input id="username" name="username" required="required" type="text" data-icon="u" placeholder="John Doe" value="{$username}"/>
  </p>
  <p>
  <input id="passwordsignup" name="passwordsignup" type="password" placeholder="eg. X8df!90EO">    
  <label for="passwordsignup" class="youpasswd">Change your password </label>
  </p>

  <p class="signin button">
  <input type="submit" name="save" value="Save Settings">
  </p>
  </form>
{/block}
{block name='help_text'}
Actually, here you can change your password. 
{/block}
