{extends file="Tmpls/ichnaea_root.tpl"}

{block name="js_extra"} 

{/block}
{block name="title"}Administer users{/block}

{block name="headtitle"}List of users{/block}

{block name="page"}
{init path="Util" function="requirePrivileges" params="administrator"}
{init path="Controllers/Admin" function="displayListUsersPage"}

<form method="post">
<input type="submit" value="Reset passwords">
{* List of users *}
<table>
<tr><th>id</th><th>Name</th><th>Login</th><th>Reset password</th></tr>
{section name=user loop=$users}
<tr>
  <td>{$users[user].id}</td>
  <td>{$users[user].name}</td>
  <td>{$users[user].login}</td>
  <td><input type="checkbox" value="Change Password" name="reset_paswd"></td>
<tr>
{/section}
</table>
</form>
{/block}
