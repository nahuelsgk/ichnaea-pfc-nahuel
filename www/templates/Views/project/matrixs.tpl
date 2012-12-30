{extends file="Tmpls/ichnaea_root.tpl"}

{block name="title"}{init path="Controllers/Projects" function="displayProjectName"}Matrixs from {$project_name}{/block}
{block name="headtitle"}Matrixs from {$project_name}{/block}
{block name="page"}
{init path="Controllers/Projects" function="displayProjectMatrixs"}
{* List projects *}
<form method="post">
<input type="submit" value="Perfom action" name="submit">
<table border="1">
<tr><th>Id</th><th>Name</th><th>Remove</tr>
{section name=m loop=$matrixs}
<tr>
<td>{$matrixs[m].id}</td>
<td>{$matrixs[m].name}</td>
<td><input type="checkbox" value="{$matrixs[m].id}" name="delete_matrix[]"></td>
<tr>
</form>
{/section}
{/block}
