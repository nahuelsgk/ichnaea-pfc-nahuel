<div class="container">
<!-- Tabs -->
<ul class="nav nav-tabs" id="tabs">
<li class="active" id="tab_variables"><a href="#list">Variables</a></li>
<li class="" id="tab_add_variable"><a href="#add">Add variable</a></li>
<li class="hidden" id="tab_edit_variable"><a href="#edit">Edit variable</a></li>
</ul>

<!-- Tab contents -->     
<div class="tab-content">
    <!-- List of variable content tab -->
	<div class="tab-pane active" id="list">
		<table id="table_variables" class="table-striped">
		<thead>
		 <th align="center" style="padding-left: 10px;padding-right: 10px;">&nbsp;</th>
		 <th align="center" style="padding-left: 10px;padding-right: 10px;">Name</th>
		 <th align="center" style="padding-left: 10px;padding-right: 10px;">Description</th>
		</thead>
		<tbody id="__list_single_var_content">
		</tbody>
		</table>
 	</div>

	 <!-- Form new variable content tab -->
	<div class="tab-pane" id="add">
	
	</div>
	
	<div class="tab-pane" id="edit">
	
	</div>
</div>

<script type="text/x-jsrender" id="templateAddVariableForm" >
<form id="new_variable_form">
	<fieldset>
	<legend>New variable on the system</legend>
	<label>Name of the variable</label>
	<input type="text" placeholder="Variable's name" name="var_name" id="var_name">
	<label>Description of the variable</label>
	<input type="text" placeholder="Variable's description" name="description" id="var_name">
	</fieldset>
    <button type="button" onclick="saveVariable();return false;" class="btn">Save</button>
	</form>
</script>

<script type="text/x-jsrender" id="templateEditVariableForm" >
<form id="edit_variable_form">
	<fieldset>
	<legend>Edit variable on the system</legend>
	<label>Name of the variable</label>
	<input type="text" placeholder="Variable's name" name="var_name" id="var_name" value={{>name}}>
	<label>Description of the variable</label>
	<input type="text" placeholder="Variable's description" name="description" id="var_name" value="{{>description}}">
	</fieldset>
    <button type="button" data-id={{>id}} onclick="updateVariable(this);return false;" class="btn">Update</button>
	</form>
</script>

<!-- Single variable template -->
<script id="singleVariableTemplate" type="text/x-jsrender">
<tr>
 <td class="single_var_actions"><a class="btn btn-mini" name="edit_variable" title="Edit single variable" onclick="editVariable(this);" data-id="{{>id}}"><i class="icon-edit"></i></a></td>
 <td class="single_var_name" align="center" style="padding: 0 5px"><span class="single_var_name">{{>name}}</span></td>
 <td>{{>description}}
 </td>
</tr>
</script>

<script type="text/javascript">
function renderEditVariable(data){
	$('#edit').html($('#templateEditVariableForm').render(data.data));
}

function editVariable(button){
	$('#tab_edit_variable').attr('class','');
	var id = $(button).attr('data-id');
	sendEvent('api/variable/'+id, "GET", {}, renderEditVariable);	
}

function renderEditFormVariable(list){
	$('#tab_edit_var_form').attr('class', '');
	$('#edit_var > .edit_var_form').html($('#editFormTemplate').render(list.list[0]));
	$('#seasonList').html($('#seasonTemplate').render(list.list));
}

function editSingleVariable(button){
	//Enable tab
	var id = $(button).attr("data-svid");
	sendEvent('/api/single_variable_api/single_variable/id/'+id+'/format/json','GET',{}, renderEditFormVariable);
}

function renderListSingleVariables2(list){
	var content = $('#singleVariableTemplate').render(list.list);
	$('#__list_single_var_content').html(content);
	
}

function clear_new_var_form(){
	clear_form('#__new_var_form');
	seasons=0;
}

function showVariablesTab(){
	console.log("");
}

function updateVariable(button){
	var variable_name 	     = $('#edit_variable_form input[name="var_name"]').val();
	var variable_description = $('#edit_variable_form input[name="description"]').val();
	var id = $(button).attr('data-id');
	sendEvent('api/variable/'+id, 'PUT', {name: variable_name, description: variable_description}, showVariablesTab); 	
}

function renderNewSeasonItem(){
	var season_item = $('[data-template="_season_item"]').clone();
	season_item.attr("class", "form-inline  season_item");
	season_item.attr("data-template","");
	season_item.attr("id", "");
	$('#__seasons_list').append(season_item);
}

function renderAddedVariable(){
	renderAddVariableForm();
}

function saveVariable(){
	var variable_name = $('#new_variable_form input[name="var_name"]').val();
	var description   = $('#new_variable_form input[name="description"]').val();
	sendEvent('api/variable', 'PUT', {name: variable_name, description: description}, renderAddedVariable);
	return false;
}

function renderAddVariableForm(){
	$('#add').html($('#templateAddVariableForm').render());
}

function renderVariablesList(list){
  console.log("Rendering variables");
  $('#__list_single_var_content').html($('#singleVariableTemplate').render(list.list));
}

function requestListVariables(){
  sendEvent('/api/variable','GET',{}, renderVariablesList);
}

function init_tabs(){
 //Needs this to active the functionality of tabs
 $('#tabs a').click(function (e) {
  e.preventDefault();
  $(this).tab('show');
 });

 $('#tabs #tab_variables').bind('show', function(e) {
	  requestListVariables();
 });	
 $('#tabs #tab_add_variable').bind('show', function(e) {
	  renderAddVariableForm();
});	
}

$(document).ready(function(){
  init_tabs();  
  requestListVariables();
});
</script>