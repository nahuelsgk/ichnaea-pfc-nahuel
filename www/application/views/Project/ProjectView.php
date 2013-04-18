<div class="container">
<h1>Edit project <div class="project_name" style="display: inline;"><?php echo $project['name']; ?></div></h1>
<div id="breadcrumbs">
<a href="/project">Home</a> &gt;&gt; Edit project
</div>
<table>
<tr>
  <td>Project's name</td>
  <td><input size="30" data-pid="<?php echo $project['id']?>" type="text" name="project_name" id="__name_project" value="<?php echo $project['name']?>" placeholder="Write the name of your project" >
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
      <?php foreach ($matrixs_available as $matrix) {?>
      <option value="<?php echo $matrix->id; ?>"><?php echo $matrix->name;?></option>
      <?php } ?>
    </select>
  </td>
  <td>
  <button name="del" id="__del_matrix" pid="<?php echo $project['id']; ?>">&lt;&lt;</button>
  <button name="add" id="__add_matrix" pid="<?php echo $project['id']; ?>">&gt;&gt;</button>
  </td>
  <td>
  <select multiple="multiple" id="__matrixs_selected" name="matrixs_selected" style="width: 200px; height: 200px;">
      <?php foreach ($matrixs_included as $matrix) {?>
      <option value="<?php echo $matrix->id; ?>"><?php echo $matrix->name; ?></option>
      <?php } ?>
  </select>
  </td>
</tr>
</table>

<button id="save_project">Save changes</button>
<!-- For test
<button id="id_send_ichniter" onClick="sendit();">Send event to API REST in CODEIGNITER</button>
<button id="id_send_ichniter2" data-pid="<?php echo $project['id']?>" onClick="updateProject(this);">Send event GET to project</button>
 -->

<script language="javascript" type="text/javascript">
function updateProjectName(){
	var pid  = $(this).attr('data-pid');
	var name = $(this).val();
	sendEvent('/api/project/'+pid+'/format/json','PUT',{op:"name", name: name}, renderNewName(name));
}

function renderNewName(new_name){
	$("div.project_name").html(new_name);
}

function addMatrixsToProject(){
    var selectedOpts = $('#__matrixs_available option:selected');
    if (selectedOpts.length == 0) {
      alert("Nothing to move.");
      event.preventDefault();
    }
    $('#__matrixs_selected').append($(selectedOpts).clone());
    $(selectedOpts).remove();
    
    var pid = $(this).attr('pid');
    var matrixs_selected = new Array();
    
    $.each(selectedOpts, function (index, el){
      matrixs_selected.push( {  matrix_id : el.value, project_id: pid } );
    });
    sendEvent('/api/project/'+pid+'/format/json','PUT',{op:"addMatrixs", set: matrixs_selected});  
}

function deleteMatrixsFromProject(){
	var selectedOpts = $('#__matrixs_selected option:selected');
	if (selectedOpts.length == 0) {
      alert("Nothing to move.");
      event.preventDefault();
    }
    $('#__matrixs_available').append($(selectedOpts).clone());
    $(selectedOpts).remove();
    
    var pid = $(this).attr('pid');
    var matrixs_removed = new Array();
    
    $.each(selectedOpts, function (index, el){
      matrixs_removed.push( {  id : el.value } );
    });
    sendEvent('/api/project/'+pid+'/format/json','PUT',{op:"removeMatrixs", set: matrixs_removed});
}

$('#__add_matrix').click(addMatrixsToProject);
$('#__del_matrix').click(deleteMatrixsFromProject);
$('#__name_project').change(updateProjectName);
</script>