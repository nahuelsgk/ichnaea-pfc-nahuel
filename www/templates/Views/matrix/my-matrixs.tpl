{extends file="Tmpls/ichnaea_root.tpl"}

{block name="title"}My matrixs{/block}
{block name="headtitle"}My Matrixs{/block}
{block name="page"}
<div id="breadcrumbs">
<a href="/home">Home</a> &gt;&gt; My matrixs
</div>
{include file="Tmpls/Matrix/ListMatrixs.tpl" filter="filterByUser"}
{/block}

{block name="help_text""}
<p>Here you can see your selectable and viewable matrixs.<br></p>
<p>Ichnaea have some public default matrixs to start works with application.</p>
{/block}
