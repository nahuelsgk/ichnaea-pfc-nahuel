{init path="Controllers/Matrix" function="displayMatrixsList" params="{$filter}"}
{if $matrixs}
<table class="matrixs_list">
<tr><th>Id matrix</th>
 <th>Name Matrix</th>
 <th>Public</th>
 <th>Operations</th>
</tr>
{section name=m loop=$matrixs}
<tr>
 <td>{$matrixs[m].id}</td>
 <td>{$matrixs[m].name}</td>
 <td>{$matrixs[m].public}
 <td><a href="/matrix/view?mid={$matrixs[m].id}">View</a> | <a href="/matrix/edit_new?mid={$matrixs[m].id}">Edit</a> |  <a id="delete_matrix" mid="{$matrixs[m].id}">Delete</a> </td>
</tr>
{/section}
</table>
<a href="/matrix/edit_new"></a>
{else}
There are not any selectable matrixs. 
You can create a matrix clicking <a href="/matrix/edit_new">here</a>. 
{/if}
<script language="javascript" type="text/javascript">
$("#delete_matrix").click(function (){
  var cont =  confirm('You are about to disable a matrix. Confirm?');
  if (!cont) return;
  var values = {
    "operation" : "disable_matrix",
    "mid": $(this).attr("mid")
  };
  send_event("Controllers/Matrix","disableMatrix", values);
});
</script>
