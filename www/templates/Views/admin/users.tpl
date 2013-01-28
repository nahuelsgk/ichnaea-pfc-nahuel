{extends file="Tmpls/ichnaea_root.tpl"}

{block name="title"}Administer users{/block}

{block name="headtitle"}List of users{/block}

{block name="page"}
{init path="Util" function="requirePrivileges" params="administrator"}
{init path="Controllers/Admin" function="displayListUsersPage"}

<form method="post">
{$error}
{* List of users *}
<table>
<tr><th>id</th><th>Name</th><th>Login</th><th>Reset password</th></tr>
{section name=user loop=$users}
<tr>
  <td>{$users[user].id}</td>
  <td>{$users[user].name}</td>
  <td>{$users[user].login}</td>
  <td><input type="checkbox" value="{$users[user].id}" name="reset_paswd[]"></td>
<tr>
{/section}
</table>
<input type="submit" value="Reset passwords" name="submit">
</form>
{/block}
{block name="help_text"}
<lu><li>Reset password: Select the checkbox and click "Reset Passwords". Will put a temporaly password to the user. The temp password by now is: fluzzy_909</li></ul>
{/block}
