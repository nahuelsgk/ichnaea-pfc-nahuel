{extends file="Tmpls/ichnaea_root.tpl"}
{block name='title'}Ichnaea Home User{/block}
{block name='page'}
    {init path="Controllers/Ichnaea/Home/Dashboard" function="display_home"}
<h1>Your dashboard</h1>
    {* List projects *}
    <table id="__list_projects" class="table-striped">
    <th>Project name</th><th>&nbsp;</th>
    <tbody>
    {section name=p loop=$projects}
    <tr>
      <td>{$projects[p].name}</td>
      <td>
      <a title="Project's configuration" href="/project/edit_new?pid={$projects[p].id}" class="btn btn-mini"><i class="icon-pencil"></i></a> | 
	  <a title="View matrixs" class="btn btn-mini" href="/project/matrixs?pid={$projects[p].id}"><i class="icon-th-large"></i></a> |
	  <a title="Delete project" id="__add_new_season_item" pid="{$projects[p].id}" class="btn btn-mini" onclick='delete_project(this)'><i class="icon-trash"></i></a></td>
    </tr>
    {/section}
    </tbody>
    </table>
    <a title="Add new project" id="__add_project" class="btn btn-mini"><i class="icon-plus"></i></a>
<script type="text/javascript">
  $('#__add_project').click(function(){
    $(this).hide();
   $('#__list_projects > tbody:last').append('<tr id="__last_row_inserted"><td><input id="__new_project_name" type="text" placeholder="Name of the project"></td><td><button id="__save_project" onclick="save_project();">Save project</button><button id="__cancel_new_project" onclick="cancel_new_project()">Cancel</button></td></tr>');
  });

  function cancel_new_project(){
      $('#__last_row_inserted').remove();
      $('#__add_project').show();
  };
 
  function delete_project(sender){
    var cont =  confirm('You are about to disable a matrix. Confirm?');
    if (!cont) return;
    var callback = function (){
      $(sender).parent().parent().remove();
    }
    var request = {
      uri: "/api/project",
      op: "delete",
      params: {
        pid: $(sender).attr("pid")
      }
    };

    send_event3(request, callback);
  }

  function render_new_project(returned){
    var name_project = $("#__new_project_name").attr('value');
    $('#__last_row_inserted').remove();
    var pid = returned.data.pid;
    $('#__list_projects > tbody:last').append('"<tr><td>'+name_project+'</td><td><a title="Project configuration" href="/project/edit_new?pid='+pid+'" class="btn btn-mini"><i class="icon-pencil"></i></a> | <a title="View matrixs" class="btn btn-mini" href="/project/matrixs?pid='+pid+'"><i class="icon-th-large"></i></a> | <a title="Delete project" id="__add_new_season_item" pid="'+pid+'" class="btn btn-mini" onclick="delete_project(this)"><i class="icon-trash"></i></a></td>');
    $('#__add_project').show();
  }

  function save_project(){
    var name_project = $("#__new_project_name").attr('value');
    if (name_project == '' ) alert("Needs a name");
    var values = {
      uri: "/api/project",
      op: "create",
      params:{ 
        name: name_project 
      }
     
    };
    send_event3(values, render_new_project);
  }
</script>
{/block}

