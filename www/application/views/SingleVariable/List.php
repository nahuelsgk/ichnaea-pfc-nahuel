<div class="container">
<!-- Tabs -->
<ul class="nav nav-tabs" id="myTab">
<li class="active" id="tab_variables"><a href="#home">Variables</a></li>
<li id="tab_new_var_form"><a href="#new_var">Add a new variable</a></li>
<li class="hidden" id="tab_edit_var_form"><a href="#edit_var">Edit single variable</a></li>
</ul>

<!-- Tab contents -->     
<div class="tab-content">
    <!-- List of variable content tab -->
	<div class="tab-pane active" id="home">
		<div class="list_vars-content" id="list_vars">
		<table id="__list_single_vars" class="table-striped">
		<thead>
		<th>&nbsp;</th><th align="center">Name</th><th align="center">Seasons</th>
		</thead>
		<tbody id="__list_single_var_content">
		</tbody>
		</table>
		</div>
 	</div>

	 <!-- Form new variable content tab -->
	<div class="tab-pane" id="new_var">
	<form id="__new_var_form">
	<fieldset>
	<legend>New single variable on the system</legend>
	<label>Name of the variable</label>
	<input type="text" placeholder="Single Variable's name" name="single_var_name" id="__single_var_name">
	<label>Seasons <a id="__add_new_season_item" class="btn btn-mini"><i class="icon-plus"></i></a></label> 
	<div class="season_variable_form" id="__seasons_list">
	</div>
	<button type="button" onclick="saveSingleVariable();" class="btn">Save</button>
	
	</fieldset>
	</form>
	</div>
	
	<div class="tab-pane" id="edit_var">
		<div class="edit_var_form"></div>
		<table>
  			<thead><tr><th>&nbsp;</th><th>System's id</th><th>Season name</th><th>Notes</th></tr></thead>
   			<tbody id="seasonList"></tbody>
 		</table>
 		<a id="__add_new_season_item" onclick="addSeason();" class="btn btn-mini"><i class="icon-plus"></i></a>
	</div>
</div>

<!-- Season form template -->
<div class="hidden" data-template="_season_item">
    <input type="text" placeholder="Season's name" name="season_var_name" class="_season_name">
    <input type="text" placeholder="Notes or comments" name="season_var_name" class="_season_notes">
</div>
</div>


<script id="editFormTemplate" type="text/x-jsrender">
<form id="edit_form">
 <fieldset>
 <legend>Edit single variable on the system</legend>
 <label>Name of the variable</label>
 <input type='text' data-id='{{>id}}' value={{>name}}>
</form>
</script>

<!-- Season of a single variable template -->
<script id="seasonTemplate" type="text/x-jsrender">
<tr>
 <td><a class="btn btn-mini" name="delete_season" title="Delete season" data-id="{{>season_id}}"><i class="icon-trash"></i></a></td>
 <td>{{>season_id}}</td>
 <td>{{>season_name}}</td>
 <td>{{>season_notes}}</td>
</tr>
</script>

<!-- New season of a single variable template -->
<script id="newSeasonTemplate" type="text/x-jsrender">
<tr clasS="newSeason">
 <td>&nbsp;</td>
 <td></td>
 <td><input type="text" name="season_name"></td>
 <td><input type="text" name="season_notes"></td>
</tr>
</script>

<!-- Single variable template -->
<script id="singleVariableTemplate" type="text/x-jsrender">
<tr>
 <td class="single_var_actions"><a class="btn btn-mini" name="edit_variable" title="Edit single variable" onclick="editSingleVariable(this);" data-svid="{{>id}}"><i class="icon-edit"></i></a></td>
 <td class="single_var_name" align="center" style="padding: 0 5px"><span class="single_var_name">{{>name}}</span></td>
 <td>
 {{for seasons}}
  <div>
   {{>season_name}} - <em>{{>season_notes}}</em>
 </div>
  {{/for}}
 </td>
</tr>
</script>

<script type="text/javascript">
var seasons = 0;
//Add a new tab: NOT WORKING!
$('#add_tab').click(function (){
	alert("New tab");
	$('.tab-content').append(
		    $('<div></div>')
	        .addClass('tab-pane')
	        .attr('id', 'newTab')
	);
	$('#newTab').tab('show');	
});

function addSeason(){
	$('#seasonList').append($('#newSeasonTemplate').render());
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


function saveSingleVariable(){
  var name          = $('#__single_var_name').val();
  if(!name) { alert('No name'); return false;}

  var seasons = new Array();
  $(".season_item").each(function (index, season){
	console.log("Element "+index);
	var season_name  = $(season).find('input._season_name').val();
	var season_notes = $(season).find('input._season_notes').val();
	seasons.push({name: season_name, notes: season_notes});
  });
  sendEvent('/api/single_variable_api/single_variable/format/json', 'PUT', {name: name, seasons: seasons}, clear_new_var_form);
};

function renderNewSeasonItem(){
	var season_item = $('[data-template="_season_item"]').clone();
	season_item.attr("class", "form-inline  season_item");
	season_item.attr("data-template","");
	season_item.attr("id", "");
	$('#__seasons_list').append(season_item);
}

function requestListSingleVariables(){
  sendEvent('/api/single_variable_api/single_variable/format/json','GET',{}, renderListSingleVariables2);
}

$(document).ready(function(){
  //Needs this to active the functionality of tabs
  $('#myTab a').click(function (e) {
    e.preventDefault();
    $(this).tab('show');
  });
  
  $('#single_variable_tabs a').click(function (e) {
	    e.preventDefault();
	    $(this).tab('show');
  });
  $('#__add_new_season_item').click(renderNewSeasonItem);
  $('#__new_var_form').on('submit', saveSingleVariable);
  requestListSingleVariables();

  //Everytime the tab variable is shown, show all the system variable
  $('#myTab #tab_variables').bind('show', function(e) {
	  requestListSingleVariables();
  });
  $('#myTab #tab_new_var_form').bind('show', function(e) {
  });
});
</script>