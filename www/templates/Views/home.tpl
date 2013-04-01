{extends file="Tmpls/ichnaea_root.tpl"}
{block name='title'}Ichnaea Home User{/block}
{block name='page'}
  {init path="Controllers/Ichnaea/Home/Dashboard" function="display_home"}

  <header>
  <h1>Welcome to your Dashboard</h1>
  <div id="msgid"></div>
  </header>
  {if $empty}
    There aren't any project.
  {else}
    {* List projects *}
    <table id="__list_projects">
    <tr><th>Project name</th><th>Options</th></tr>
    {section name=p loop=$projects}
    <tr>
      <td>{$projects[p].name}</td>
      <td>
        <a href="/project/edit_new?pid={$projects[p].id}">Edit Project</a> | 
	<a href="/project/matrixs?pid={$projects[p].id}">View matrixs</a> | 
	View trainings | 
	<button onclick='delete_project(this)' pid="{$projects[p].id}">Delete</button></td>
    </tr>
    {/section}
    </table>
  {/if}
  <button id ="__add_project" >Add a new project!</a>
<script type="text/javascript">
  $('#__add_project').click(function(){
    $(this).remove();
    $('#__list_projects > tbody:last').append('<tr id="__last_row_inserted"><td><input id="__new_project_name" type="text" placeholder="Name of the project"></td><td><button id="__save_project" onclick="save_project();">Save project</button></td></tr>');
  });


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
    $('#__list_projects > tbody:last').append("<tr><td>"+name_project+"</td><td><a href='/project/edit_new?pid="+pid+"'>Edit</a> | <a href='/project/matrixs?pid="+pid+"'>View matrixs</a> | <a href='/matrix/edit_new?pid="+pid+"'>Create Matrixs</a> |View trainings | Create Trainings | <button onclick='delete_project(this)' pid='"+pid+"'>Delete 2</button> ");
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
{block name='help_text'}
<h4>Welcome to your dashboard!</h4>
<p>
Here you can see your projects currently open.
<ul>
<li>Edit project: You can add matrixs that exists on ichnaea to this project.</li>
<li>View matrixs: You can see the matrixs of this project
<li>View trainings: Currently developing</li>
<li>Delete: You can disable this project</li>
<li>Add a new project:You can add a new project on Ichnaea</li>
</ul>
</p>
{/block}

