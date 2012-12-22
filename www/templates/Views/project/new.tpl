{extends file="Tmpls/ichnaea_root.tpl"}

{block name="title"}Create a new project{/block}
{block name="headtitle"}Create a new project{/block}
{block name="page"}
{init path="Controllers/Projects" function="displayProjectNewForm"}
<form method="post">
<table>
<tr>
  <td>Name of the project:</td>
  <td><input type="text" name="name_project" placeholder="Write the name of your project" required></td>
</tr>
</table>
<input type="submit" value="Save Project" name="saveproject">
</form>
{/block}
