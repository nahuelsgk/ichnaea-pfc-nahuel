{extends file="Tmpls/ichnaea_root.tpl"}

{block name="title"}My matrixs{/block}

{block name="headtitle"}My Matrixs{/block}

{block name="page"}
{init path="Controllers/Matrix" function="displayMyMatrixs"}
{* List of users *}
<table>
<tr><th>Id Project</th><th>Name Project</th><th>Id matrix</th><th>Name Matrix</th></tr>
{section name=m loop=$matrixs}
<tr>
  <td>{$matrixs[m].id_project}</td>
  <td>{$matrixs[m].name_project}</td>
  <td>{$matrixs[m].id_matrix}</td>
  <td>{$matrixs[m].name_matrix}</td>
<tr>
{/section}
</form>
{/block}
