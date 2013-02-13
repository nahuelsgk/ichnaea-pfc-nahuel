{extends file="Tmpls/ichnaea_root.tpl"}

{block name="title"}{init path="Controllers/Projects" function="displayProjectName"}Matrixs from {$project_name}{/block}
{block name="headtitle"}Matrixs from {$project_name}{/block}
{block name="page"}
<div id="breadcrumbs">
<a href="/home">Home</a> &gt;&gt; Project  <a href=""><i>{$project_name}</i></a> &gt;&gt; List of Matrixs
</div>

{include file="Tmpls/Matrix/ListMatrixs.tpl" filter="filterByProject"}
{/block}

{block name="help_text"}
Here you can see all the matrixs from a concrete project.
<ul style="padding-left: 10px;" >
<li>Delete matrix: select the matrixs you want to delete and click "Delete selected"
<li>Edit matrix definition: you can change the definition of its variable(name, threshold, etc.)
<li>View matrix: you can see the matrixs values</li>
</ul>
{/block}
