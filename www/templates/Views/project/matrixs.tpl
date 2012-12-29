{extends file="Tmpls/ichnaea_root.tpl"}

{block name="title"}View matrix from PROJECTNAME{/block}
{block name="headtitle"}Matrixs from PROJECTNAME{/block}
{block name="page"}
{init path="Controllers/Projects" function="displayProjectMatrixs"}
{* List projects *}
<table border="1">
<tr><th>Id</th><th>Name</th></tr>
{section name=m loop=$matrixs}
<tr>
<td>{$matrixs[m].name}</td>
<td>{$matrixs[m].id} </td>
<tr>
{/section}
{/block}
