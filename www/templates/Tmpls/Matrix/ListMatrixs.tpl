{init path="Controllers/Matrix" function="displayMatrixsList" params="{$filter}"}
<table id="__list_matrixs" class="matrixs_list">
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
 <td> <a href="/matrix/edit_new?mid={$matrixs[m].id}">Configuration</a> | <a href="/matrix/view?mid={$matrixs[m].id}">View</a> </td>
</tr>
{/section}
</table>
<button id="__new_matrix" onclick="add_new_matrix(this)" {if isset($pid)} pid="{$pid}" {/if}>Add a matrix to this project</button>
<a href="/matrix/edit_new"></a>
<script language="javascript" type="text/javascript">
function add_new_matrix(trigger){
  var el = $(trigger);
  var pid = el.attr("pid");
  el.remove(); 
  $('#__list_matrixs > tbody:last').append('<tr id="__new_matrix_form"><td></td><td><input type="input" name="name_matrix" placeholder="Name of the matrix"></td><td><select name="privacity"><option value="public">Public</option><option value="private">Private</option></select></td><td><button id="__save_matrix" onclick="save_new_matrix('+pid+')">Save matrix</button></td>');
  
}

function save_new_matrix(ppid){
  var name_matrix = $("#__new_matrix_form input[name=name_matrix]").val();
  if (name_matrix == ''){
    alert("You must write a name");
    return;
  }

  var privacity = $("#__new_matrix_form select[name=privacity]").val();

  var callback = function(data){
    $("#__new_matrix_form").remove();
    var mid=data.data.mid;
    $('#__list_matrixs > tbody:last').append("<tr><td>"+mid+"</td><td>"+name_matrix+"</td><td>"+privacity+"<td><a href='/matrix/view?mid="+mid+"'>View</a> | <a href='/matrix/edit_new?mid="+mid+"'>Edit</a> </td></tr>");
  };

  var request = {
    uri: "/api/matrix",
    op: "create",
    params: {
      name : name_matrix ,
      pid : ppid ,
      public_matrix : privacity,
    }
  };
  send_event3(request, callback);
}

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
