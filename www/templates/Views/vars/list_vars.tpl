{extends file="Tmpls/ichnaea_root.tpl"}
{block name='title'}Ichnaea Home User{/block}
{block name='page'}
{init path="Controllers/Vars" function="display_var_list"}
<ul class="nav nav-tabs" id="myTab">
<li class="active" id="tab_variables"><a href="#home">Variables</a></li>
<li id="tab_new_var_form"><a href="#new_var">Add a new variable</a></li>
</ul>
     
<div class="tab-content">
	<div class="tab-pane active" id="home">
	<div class="list_vars-content" id="list_vars">
	<table id="__list_single_vars" class="table-striped">
	<th>Id</th><th>Seasons</th>
	<tr id="__template_single_var" class="template_hidden">
	<td class="single_var_name"><span class="single_var_name">Sample name</span></td>
	<td>
	  <table class="__template_single_var_season_list table-striped"">
	    <tr id="__template_single_var_season_item"><td><span class="single_var_season_name"></span></td><td><span class="single_var_season_notes"></span></td></tr>
	  </table>
	</td>
	</tr>
	</tbody>
	</table>
	</div>
</div>
<div class="tab-pane" id="new_var">
<form id="__new_var_form">
<fieldset>
<legend>New var</legend>
<label>Name</label>
<input type="text" placeholder="Single Variable's name" name="single_var_name" id="__single_var_name">
<label>Seasons <a id="__add_new_season_item" class="btn btn-mini"><i class="icon-plus"></i></a></label> 
<div class="season_variable_form" id="__seasons_list">
</div>
<button type="submit" class="btn">Save</button>
</fieldset>
</form>
</div>
</div>

<div class="hidden" data-template="_season_item">
    <input type="text" placeholder="Season's name" name="season_var_name" class="_season_name">
    <input type="text" placeholder="Notes or comments" name="season_var_name" class="_season_notes">
</div>
    
<script type="text/javascript">
systemChanged = true;
function renderNewVarForm(){
	if(systemChanged == true){
	  alert("Rendering form");
	}
};
function renderListSingleVariables(list){
    $.each(list.data, function(i, variable){
      var singleVariableView = $('#__template_single_var').clone();
      var id = variable.svid;
      singleVariableView.attr('class','single_var_item');
      singleVariableView.attr('id', id);
      singleVariableView.find('span.single_var_name').html(variable.name);
     
      if(variable.seasons){
      $.each(variable.seasons, function (j, season){
      	var seasonView = $('#__template_single_var_season_item').clone();
      	seasonView.attr('id', season.ssid);
      	seasonView.attr('class', 'single_var_season_item');
      	seasonView.find('.single_var_season_name').html(season.name);
      	seasonView.find('.single_var_season_notes').html(season.notes);
      	singleVariableView.find('.__template_single_var_season_list').append(seasonView);
        });
      }
      $('#__list_single_vars').append(singleVariableView);
    });
    systemChanged=false;
}

function save_single_variable(e){
  e.preventDefault();
  var name          = $('#__single_var_name').val();
  if(!name) { alert('No name'); }
  var seasons = new Array();
  $(this).find(".season_item").each(function(i, season){
	seasons.push(new Season($(season).find("._season_name").val(), $(season).find("._season_notes").val()));
  });
  var request = {
    uri    : "/api/singlevar",
    op     : "create",
    params : {
      name    : name,
      seasons : seasons
    }
  };
  send_event3(request);
  list_vars_rendered = false;
};

function renderNewSeasonItem(){
	var season_item = $('[data-template="_season_item"]').clone();
	season_item.attr("class", "form-inline  season_item");
	season_item.attr("data-template","");
	season_item.attr("id", "");
	$('#__seasons_list').append(season_item);
}

function requestListSingleVariables(){
  if(systemChanged == true){
  alert("Rerendering becaus a changed happened");
  var request = new Request('/api/singlevar','listComplete', {}).send(renderListSingleVariables);
  }
}

$(document).ready(function(){
  $('#myTab a').click(function (e) {
    e.preventDefault();
    $(this).tab('show');
  });
  $('#single_variable_tabs a').click(function (e) {
	    e.preventDefault();
	    $(this).tab('show');
  });
  $('#__add_new_season_item').click(renderNewSeasonItem);
  $('#__new_var_form').on('submit', save_single_variable);
  requestListSingleVariables();
  renderNewVarForm();
  $('#myTab #tab_variables').bind('show', function(e) {
	  requestListSingleVariables();
  });
  $('#myTab #tab_new_var_form').bind('show', function(e) {
	  renderNewVarForm();
  });
});

</script>
{/block}
