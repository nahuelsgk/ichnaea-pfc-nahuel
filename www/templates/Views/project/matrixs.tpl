{extends file="Tmpls/ichnaea_root.tpl"}

{block name="title"}{init path="Controllers/Projects" function="displayProjectForm"}Matrixs from {$project_name}{/block}
{block name="headtitle"}Matrixs from {$project_name}{/block}
{block name="page"}
<div id="breadcrumbs">
<a href="/home">Home</a> &gt;&gt; Project  <a href="/project/edit_new?pid={$pid}"><i>{$project_name}</i></a> &gt;&gt; List of Matrixs
</div>

{include file="Tmpls/Matrix/ListMatrixs.tpl" filter="filterByProject"}
{/block}

