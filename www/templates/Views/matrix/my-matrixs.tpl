{extends file="Tmpls/ichnaea_root.tpl"}

{block name="title"}My matrixs{/block}
{block name="headtitle"}My Matrixs{/block}
{block name="page"}
<div id="breadcrumbs">
<a href="/home">Home</a> &gt;&gt; My matrixs
</div>
{include file="Tmpls/Matrix/ListMatrixs.tpl" filter=""}
{/block}
