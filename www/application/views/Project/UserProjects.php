<div class="container">
<h1>Your dashboard</h1>

    <table id="__list_projects" class="table-striped">
    <th>Project name</th><th>&nbsp;</th>
    <tbody>
    <?php 
      foreach($projects as $project){
      	$pid = $project->id;
    ?>
    
    <tr id="pid_<?echo $pid;?>">
      <td><?php echo $project->name;?></td>
      <td>
      <a title="Project's configuration" href="/project/<?php echo $pid ?>" class="btn btn-mini"><i class="icon-pencil"></i></a> | 
	  <a title="Delete project" id="__add_new_season_item" data-id="<?php echo $pid ?>" class="btn btn-mini" onclick='deleteProject(this)'><i class="icon-trash"></i></a></td>
    </tr>
    <?php } ?>
    
    </tbody>
    </table>
    <a title="Add new project" id="__add_project" class="btn btn-mini"><i class="icon-plus"></i></a>

<script type="text/javascript">
  //Fix this horrible-newbie code
  $('#__add_project').click(function(){
    $(this).hide();
   $('#__list_projects > tbody:last').append('<tr id="__last_row_inserted"><td><input id="__new_project_name" type="text" placeholder="Name of the project"></td><td><button id="__save_project" onclick="save_project();">Save project</button><button id="__cancel_new_project" onclick="cancel_new_project()">Cancel</button></td></tr>');
  });

  function cancel_new_project(){
      $('#__last_row_inserted').remove();
      $('#__add_project').show();
  };

  function deleteProjectFromTable(id){
    $('tr#pid_'+id.id).remove();
  }

  //CLEAN THIS HORRIBLE CODE
  function deleteProject(button){
    var cont =  confirm('You are about to disable a matrix. Confirm?');
    if (!cont) return;
    var callback = function (){
      $(sender).parent().parent().remove();
    }
    var id = $(button).attr('data-id');
    sendEvent('/api/project/'+id+'/format/json','DELETE', {}, deleteProjectFromTable);
  }

  //CLEAN THIS HORRIBLE CODE
  function renderNewProject(project){
	console.log("Render created project: "+project);
    $('#__last_row_inserted').remove();
    $('#__list_projects > tbody:last').append('"<tr><td>'+project.name+'</td><td><a title="Project configuration" href="/project/'+project.id+'" class="btn btn-mini"><i class="icon-pencil"></i></a> | <a title="Delete project" id="__add_new_season_item" data-id="'+project.id+'" class="btn btn-mini" onclick="deleteProject(this)"><i class="icon-trash"></i></a></td>');
    $('#__add_project').show();
  }

  function save_project(){
    var name_project = $("#__new_project_name").attr('value');
    if (name_project == '' ) alert("Needs a name");
    sendEvent('/api/projects_api/projects/format/json', 'PUT', {name: name_project, creator: <?php echo $user_id; ?>}, renderNewProject);
  }
</script>
</div>
