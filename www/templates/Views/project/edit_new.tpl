{extends file="Tmpls/ichnaea_root.tpl"}

{block name="title"}Create a new project{/block}
{block name="page"}
{init path="Controllers/Projects" function="displayProjectForm"}
<h1>{if $is_edit eq 'n'}New Project {else}Editing project <i>{$project_name}</i>{/if}</h1>
<table>
<tr>
  <td>Name of the project:</td>
  <td><input size="30" type="text" name="name_project" id="name_project" value="{$project_name}" placeholder="Write the name of your project" >
  </td>
</tr>
</table>
<table>
<tr>
<th>Available matrixs</th><th></th><th>Project's matrixs</th>
<tr>
<tr>
<td>
  <select multiple="multiple" id="matrixs_available" name="matrixs_available" style="width: 200px; height: 200px;">
    {section name=matrix loop=$matrixs_available}
    <option value="{$matrixs_available[matrix].id}">{$matrixs_available[matrix].name}</option>
    {/section}
  </select>
</td>
<td>
<button name="del" id="del_matrix">&lt;&lt;</button>
<button name="add" id="add_matrix">&gt;&gt;</button>
</td>
<td>
<select multiple="multiple" id="matrixs_selected" name="matrixs_selected" style="width: 200px; height: 200px;">
    {section name=matrix loop=$matrixs_included}
    <option value="{$matrixs_included[matrix].id}">{$matrixs_included[matrix].name}</option>
    {/section}
</select>
</td>
</tr>
</table>
{if $is_edit eq 'n'}
<button id="save_project">Save changes</button>
{else}
<button id="update_project">Update project definition</button>
{/if}

<script language="javascript" type="text/javascript">

{if $is_edit eq 'y' }
  $('#add_matrix').click(function(){
    var selectedOpts = $('#matrixs_available option:selected');
    if (selectedOpts.length == 0) {
      alert("Nothing to move.");
      event.preventDefault();
    }
    $('#matrixs_selected').append($(selectedOpts).clone());
    $(selectedOpts).remove();
    var values = {
      "op"    : "aggregateMatrixs",
      "id"    : "{$pid}",
      "params": {
        "matrixs_selected" : []
      }
    };
    $.each(selectedOpts, function (index, el){
         alert("I");
         values.params.matrixs_selected.push( { "mid": el.value } );
    });
    send_event("Controllers/Projects", "updateProject", values);

  });

  $('#del_matrix').click(function(){
    var selectedOpts = $('#matrixs_selected option:selected');
    if (selectedOpts.length == 0) {
    alert("Nothing to move.");
      event.preventDefault();
    }
    $('#matrixs_available').append($(selectedOpts).clone());
    $(selectedOpts).remove();
    var values = {
      "op"    : "deaggregateMatrixs",
      "id"    : "{$pid}",
      "params": {
        "matrixs_removed" : []
      }
    };
    $.each(selectedOpts, function (index, el){
      alert("I");
      values.params.matrixs_removed.push( { "mid": el.value } );
    });
    send_event("Controllers/Projects", "updateProject", values);
								      
  });


{/if}

{if $is_edit eq 'n' }
$('#add_matrix').click(function(){
  var selectedOpts = $('#matrixs_available option:selected');
  if (selectedOpts.length == 0) {
    alert("Nothing to move.");
    event.preventDefault();
  }
  $('#matrixs_selected').append($(selectedOpts).clone());
  $(selectedOpts).remove();

});
$('#del_matrix').click(function(){
  var selectedOpts = $('#matrixs_selected option:selected');
  if (selectedOpts.length == 0) {
    alert("Nothing to move.");
    event.preventDefault();
  }
  $('#matrixs_available').append($(selectedOpts).clone());
  $(selectedOpts).remove();
});

$('#save_project').click(function (){
  var values = {
    "op": "newProject",
    "name": $('#name_project').val(),
    "matrixs_selected": [],
  }
  var selection = $('#matrixs_selected option');
  $.each(selection, function (index, el){
    values.matrixs_selected.push( { "mid": el.value } );
  });
  send_event("Controllers/Projects","newProject",values);
});
{/if}
</script>
{/block}

