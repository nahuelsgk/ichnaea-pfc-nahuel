{extends file="Tmpls/ichnaea_root.tpl"}

{block name="title"}Create a new project{/block}
{block name="page"}
{init path="Controllers/Projects" function="displayProjectForm"}
<h1>{if $is_edit eq 'n'}New Project {else}Editing project <i><div class="project_name" style="display: inline;">{$project_name}</div></i>{/if}</h1>
<div id="breadcrumbs">
<a href="/home">Home</a> &gt;&gt; Edit project
</div>
<table>
<tr>
  <td>Project's name</td>
  <td><input size="30" type="text" name="project_name" id="__name_project" value="{$project_name}" placeholder="Write the name of your project" >
  </td>
</tr>
<tr>
  <td>Project's matrixs</td>
  <td>
  <table>
  <tr>
  <th>Available matrixs</th><th></th><th>Project's matrixs</th>
  <tr>
  <tr>
  <td>
    <select multiple="multiple" id="__matrixs_available" name="matrixs_available" style="width: 200px; height: 200px;">
    u
      {section name=matrix loop=$matrixs_available}
      <option value="{$matrixs_available[matrix].id}">{$matrixs_available[matrix].name}</option>
      {/section}
    </select>
  </td>
  <td>
  <button name="del" id="__del_matrix" pid="{$pid}">&lt;&lt;</button>
  <button name="add" id="__add_matrix" pid="{$pid}">&gt;&gt;</button>
  </td>
  <td>
  <select multiple="multiple" id="__matrixs_selected" name="matrixs_selected" style="width: 200px; height: 200px;">
      {section name=matrix loop=$matrixs_included}
      <option value="{$matrixs_included[matrix].id}">{$matrixs_included[matrix].name}</option>
      {/section}
  </select>
  </td>
</tr>
</table>
{if $is_edit eq 'n'}
<button id="save_project">Save changes</button>
{/if}

<script language="javascript" type="text/javascript">

{if $is_edit eq 'y' }
  $('#__add_matrix').click(function(){
    var selectedOpts = $('#__matrixs_available option:selected');
    if (selectedOpts.length == 0) {
      alert("Nothing to move.");
      event.preventDefault();
    }
    $('#__matrixs_selected').append($(selectedOpts).clone());
    $(selectedOpts).remove();
    
    var ppid = $(this).attr('pid');

    var request = {
      uri: "/api/project",
      op: "aggregateMatrixs",
      params: {
        pid: ppid,
        matrixs_selected: [],
      }
    }
    $.each(selectedOpts, function (index, el){
      request.params.matrixs_selected.push( {  mid : el.value } );
    });
    send_event3(request);
  });

  $('#__del_matrix').click(function(){
    var selectedOpts = $('#__matrixs_selected option:selected');
    if (selectedOpts.length == 0) {
    alert("Nothing to move.");
      event.preventDefault();
    }
    $('#__matrixs_available').append($(selectedOpts).clone());
    $(selectedOpts).remove();
    var values = {
      "op"    : "deaggregateMatrixs",
      "id"    : "{$pid}",
      "params": {
        "matrixs_removed" : []
      }
    };
    var ppid = $(this).attr('pid');
    var request = {
      uri: "/api/project",
      op: "deaggregateMatrixs",
      params: {
        pid: ppid,
        matrixs_removed: [],
      }
    };
 
    $.each(selectedOpts, function (index, el){
       request.params.matrixs_removed.push( {  mid : el.value } );
        values.params.matrixs_removed.push( { "mid": el.value } );
    });
    send_event3(request);
  });

  $('#__name_project').change(function(){
    var new_name_project = $(this).val();
    var request = {
      uri: "/api/project",
      op : "updateName",
      params:{
        new_name: new_name_project
      }
    };
    var render_new_name_project = function(data){
      $("div.project_name").html(new_name_project);
    };
    var values = {
      "op"     : "updateName",
      "id"     : "{$pid}",
      "params" : {
        "name" : $(this).attr("value"),
      }
    };
    send_event3(request, render_new_name_project);
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

