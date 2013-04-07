{extends file="Tmpls/ichnaea_root.tpl"}
{block name='title'}Ichnaea Home User{/block}
{block name='page'}
  {init path="Controllers/Ichnaea/Home/Dashboard" function="display_home"}

  <header>
  <h1>Welcome to your Dashboard</h1>
  <div id="msgid"></div>
  </header>
    {* List projects *}
    <table id="__list_projects">
    <tr><th>Project name</th><th>Options</th></tr>
    {section name=p loop=$projects}
    <tr>
      <td>{$projects[p].name}</td>
      <td>
        <a href="/project/edit_new?pid={$projects[p].id}">Configuration</a> | 
	<a href="/project/matrixs?pid={$projects[p].id}">Matrixs</a> | 
	View trainings | 
	<button onclick='delete_project(this)' pid="{$projects[p].id}">Delete</button></td>
    </tr>
    {/section}
    </table>
  <button id ="__add_project" >New project!</a>
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
    $('#__list_projects > tbody:last').append("<tr><td>"+name_project+"</td><td><a href='/project/edit_new?pid="+pid+"'>Edit Project</a> | <a href='/project/matrixs?pid="+pid+"'>View matrixs</a> | View trainings | <button onclick='delete_project(this)' pid='"+pid+"'>Delete</button> ");
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

