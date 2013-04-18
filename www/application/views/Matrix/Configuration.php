<div class="container">
<h1>Editing <div class="name_matrix" style="display: inline;"><i><?php echo $matrix["name"]?></i> matrix</h1>
<div id="msgid"></div>
<div class="breadcrumbs">
<a href="/home">Home</a> &gt;&gt; Matrix configuration &gt;&gt; <a href="/matrix/<?php echo $matrix["id"]?>/view">View matrix</a> 
</div>
<table>
<tr><td valign="top">
  <table valign="top">
    <tr><td colspan="2"><center><b>Basic Information</b></center></td></tr>
    <tr><td>Name</td><td><input type="text" id="__name_matrix" name="name" placeholder="Name of the matrix" value="<? echo $matrix["name"];?>" mid="<? echo $matrix["id"];?>" required ></td></tr>
  </table>
</td>

<!-- TABS -->
<td valign="top">
<ul class="nav nav-tabs" id="myTab">
  <li class="active"><a href="#add_variables">Add a single var</a></li>
  <li><a href="#origin">Origin</a></li>
  <li id='derived_add_form'><a href="#derived_add_form_content">Add derived</a></li>
  <li id='configuration_list'><a href="#list_configured_variables">Variables configuration</a></li>
</ul>

<!-- TABS CONTENT -->
<div class="tab-content">
	<div class="tab-pane active" id="add_variables">
		<div>
		<table>
		<th>System's variable</th><th>Seasons</th>
		<tr>
		 <td>
		  <select size="100" id="__single_vars_available" name="single_vars_available" style="width: 200px; height: 200px;">
		 </td>
		 <td>
		  <select  size="100" id="__seasons_available" name="seasons_available" style="width: 400px; height: 200px;">
		 </td>
		 <td><button id="__add_single_var-season" data-mid="" onClick="add_single_var_configuration(this);">Add variable</td>
		</tr>
		</table>
		</div>
	</div>
	<div class="tab-pane" id="origin">
	Origin
	</div>
	
	<!-- TAB Add derived -->
	<div class="tab-pane" id="derived_add_form_content">
		<table>
		<th>Operator A</th><th></th><th>Operator B</th>
		<tr>
		 <td>
		  <select size="100" id="list_configured_table_op1" name="list_configured_table_op1" style="width: 200px; height: 200px;">
		 </td>
		 <td><select id="operation_derived" style="width: 50px;">
		 	<option>+</option>
		 	<option>-</option>
		 	<option>/</option>
		 	<option>*</option>
		 </td>
		 <td>
		  <select  size="100" id="list_configured_table_op2" name="list_configured_table_op2" style="width: 200px; height: 200px;">
		 </td>
		 <td>
		 	<div><input type="text" placeholder="Derived variable name"></input><div>
		 	<button id="__add_single_var-season" data-mid="" onClick="add_single_var_configuration(this);">Add variable</button>
		 </td>
		</tr>
		</table>	
	</div>
	<div class="tab-pane" id="list_configured_variables">
	<table>
	<tbody id="list_configured_variables_table"></tbody>
	</table>
		
	</div>
</div>
</div>
<table id="template_list_configuration" class="hidden">
			<tr id="template_configuration_item" class="hidden"><td class="single_var_name"></td><td class="season_name"></td><td><a class="btn btn-mini" data-cid="" onclick="deleteConfiguration(this)"><i class="icon-trash"></td></tr>
</table>

<script language="text/x-jsrender" id="templateConfiguredVariable">
<option value="{{>configuration_id_single_var}}">{{>single_var_name}} - {{>season_name}} <em>{{>season_notes}}</em></option>
</script>

<script language="text/x-jsrender" id="templateConfiguredVariableTr">
<tr>
 <td class="single_var_name">{{>single_var_name}}</td>
 <td class="season_name">{{>season_name}}</td>
 <td><a class="btn btn-mini" data-cid="{{>configuration_id_single_var}}" onclick="deleteConfiguration(this)"><i class="icon-trash"></td>
</tr>
</script>

<script language="javascript" type="text/javascript">
/*
 * Todo:
	 - Add variable cleans system matrix. Must not clean the system variable
	 - Load only systems variable available
	 - Load season available
 */

/******************************/
/***Add single var functions***/
/******************************/
function add_single_var_configuration_request(matrix_id, svid, season_id){
	var request = {
		uri: "/api/var_configuration",
	    op:  "add_single_var_configuration",
	    params: { mid: matrix_id, svid: svid, season_id: season_id }
	};
	send_event3(request, remove_elements_from_selectable_list(svid,season_id));
}

function remove_elements_from_selectable_list(svid, season_id){
	$('#__single_vars_available option[value='+svid+']').remove();
	$('#__seasons_available option[value='+season_id+']').remove();
}

function add_single_var_configuration(button){
  var svid 		= $('#__single_vars_available option:selected').val();
  var season_id = $('#__seasons_available option:selected').val();
  if (svid == '' || season_id == '') {
	  console.log('Dont have nothing to select');
	  return;
  }
  console.log("Single variable: "+svid+" - Season: "+season_id);
  sendEvent('/api/matrixs_api/matrixs/id/<?php echo $matrix["id"]?>/format/json', 'POST', {svid: svid, season_id: season_id} );
}

function renderListSeasons(list){
	  console.log("Rendering this season list: " + list);
	  $('#__seasons_available').empty();
	  $.each(list.list, function(i, season){
		  console.log(season);
		  $('#__seasons_available')
		   .append($('<option class="season_var_item"></option>')
				   .attr("value",season.id)
				   .text(season.name + ' (' +season.notes+')'));	
	  });
}

function renderListSimpleSingleVariables(list){
	  $.each(list.list, function(i, single_variable){
		  $('#__single_vars_available')
		   .append($('<option class="single_var_item"></option>')
				   .attr("value",single_variable.id)
				   .text(single_variable.name));	
	  });  
}

function loadSeasons(){
	var svid = $(this).val();
	var data = { id : svid } ;
	sendEvent('/api/seasons_api/'+svid+'/format/json', 'GET', {}, renderListSeasons);
}

function renderListSingleVariablesConfiguration(list){
	$('#list_configured_variables_table').empty();
	var content = $('#templateConfiguredVariableTr').render(list.list);
	console.log(content);
	$('#list_configured_variables_table').append(content);
}

function renderAddDerivedForm(list){
  $('#list_configured_table_op1').empty();
  $('#list_configured_table_op2').empty();
  var content = $('#templateConfiguredVariable').render(list.list);
  console.log(content);
  $('#list_configured_table_op1').append(content);
  $('#list_configured_table_op2').append(content);
}

function prepareAddDerivedForm(){
	sendEvent('/api/configuration_api/configuration_single_variables/matrix_id/<?php echo $matrix["id"]?>/format/json', 'GET', {}, renderAddDerivedForm);
}

function requestConfigurationList(){
	sendEvent('/api/configuration_api/configuration_single_variables/matrix_id/<?php echo $matrix["id"]?>/format/json', 'GET', {}, renderListSingleVariablesConfiguration);
}

function init_single_variables(){
	sendEvent('/api/single_variable_api/format/json', 'GET', {type: "simple"}, renderListSimpleSingleVariables);
}

function init_autoload_tabs(){
	$('#myTab #configuration_list').bind('show', function(e) {
		requestConfigurationList();
	});

	$('#myTab #derived_add_form').bind('show', function(e) {
		prepareAddDerivedForm();
	});	
}

function init_tabs(){
	//Needed to active the tab
	$('#myTab a').click(function (e) {
		e.preventDefault();
	    $(this).tab('show');
	  });
}
$(document).ready(function(){
	init_tabs();
	init_single_variables();
	$('#__single_vars_available').change(loadSeasons);
	init_autoload_tabs();
});
</script>