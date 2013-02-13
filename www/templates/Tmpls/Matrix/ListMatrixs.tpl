{init path="Controllers/Matrix" function="displayMatrixsBasicInfoList" params="{$filter}"}
{if $matrixs}
<form method="post">
<table>
<tr><th>Id Project</th><th>Name Project</th><th>Id matrix</th><th>Name Matrix</th><th>Select</th><th>Operations</th> </tr>
{section name=m loop=$matrixs}
<tr>
<td>{$matrixs[m].id_project}</td>
<td>{$matrixs[m].name_project}</td>
<td>{$matrixs[m].id_matrix}</td>
<td>{$matrixs[m].name_matrix}</td>
<td><input type="checkbox" value="{$matrixs[m].id_matrix}" name="delete_matrix[]"></td>
<td>
  <a href="/matrix/edit_new?pid={$matrixs[m].id_project}&mid={$matrixs[m].id_matrix}">Edit matrix definition</a> | 
  <a href="/matrix/view?mid={$matrixs[m].id_matrix}">View matrix</a>
</td>
</tr>
{/section}
</table>
<input type="submit" value="Delete selected" name="submit">
</form>
{else}
Still you haven't create any matrixs. 
{if $pid}
You can create a matrix into this project clicking <a href="/matrix/edit_new?pid={$pid}">here</a>. 
{else}
You can create a matrix into a project <a href="/home">going home</a>! 
{/if}
{/if}

