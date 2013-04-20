<div class="container">
<h1>System's seasons</h1>
 <div id="seasons_list" style="float: left; width: 50%; margin: 10px;">
   <h2>Seasons</h2>
    <ul class="nav nav-tabs" id="seasonTabs">
     <li id="list_seasons_tab" class="active"><a href="#list_season_tab_content">List of seasons</a></li>
     <li id="add_season_tab"><a href="#add_season_tab_content">Add season</a></li>
     <li id="edit_season_tab" class="hidden"><a href="#edit_season_tab_content">Edit season</a></li>
    </ul>
   
   <div class="tab-content">
   
    <!-- List season tab content -->
    <div class="tab-pane active" id="list_season_tab_content">
     <table>
      <tr>
      <th style="padding-left: 5px; padding-right: 5px;">Name</th>
      <th style="padding-left: 5px; padding-right: 5px;">Notes</th>
      <th style="padding-left: 5px; padding-right: 5px;">Start</th>
      <th style="padding-left: 5px; padding-right: 5px;">Finish</th>
      </tr>
      <tbody id="seasons_table"></tbody>
      </table>
    </div>
    
    <!-- Add season tab content -->
    <div class="tab-pane" id="add_season_tab_content">
    </div>
    
    <!-- Edit season tab content -->
    <div class="tab-pane" id="edit_season_tab_content">
    </div>
    
   </div>
 </div>
 <div id="season_content" style="float: right; width: 40%; margin: 10px;">
  <h2>Season content</h2>
  <textarea id="season_content_text"></textarea>
 </div>
</div>

<script language="text/x-jsrender" id="templateSeasonItem">
<tr data-id={{>id}} onclick="loadEditForm(this);">
 <td class="name">{{>name}}</td>
 <td class="notes">{{>notes}}</td>
 <td class="start_date">No working: {{>start_date}}</td>
 <td class="end_date">No working: {{>end_date}}</td>
</tr>
</script>

<script language="text/x-jsrender" id="templateSeasonForm">
<form id="seasonForm" class="form-horizontal">
 <div class="control-group">
  <label class="control-label" for="seasonName">Name</label>
   <div class="controls">
    <input type="text" id="seasonName" name="name" placeholder="Season's name">
   </div>
  </div>
  <div class="control-group">
   <label class="control-label" for="notes">Notes</label>
   <div class="controls">
    <input type="text" id="notes" name="notes" placeholder="Season's notes">
   </div>
  </div>
  <div class="control-group">
   <label class="control-label" for="start-date">Start date</label>
   <div class="controls">
    <input type="date" id="notes" name="start_date" placeholder="Season's start date">
   </div>
  </div>
  <div class="control-group">
   <label class="control-label" for="end-date">End date</label>
   <div class="controls">
    <input type="date" id="notes" name="end_date" placeholder="Season's end date">
   </div>
  </div>
  <div class="control-group">
   <div class="controls">
    <button class="btn" name="action" value="add" onclick="saveSeason();return false;">Save</button>
   </div>
 </div>
</form>
</script>

<script language="text/x-jsrender" id="templateSeasonEditForm">
<form id="seasonEditForm" class="form-horizontal">
 <div class="control-group">
  <label class="control-label" for="seasonName">Name</label>
   <div class="controls">
    <input type="text" id="seasonName" name="name" placeholder="Season's name" value="{{>name}}">
   </div>
  </div>
  <div class="control-group">
   <label class="control-label" for="notes">Notes</label>
   <div class="controls">
    <input type="text" id="notes" name="notes" placeholder="Season's notes" value="{{>notes}}">
   </div>
  </div>
  <div class="control-group">
   <label class="control-label" for="start-date">Start date</label>
   <div class="controls">
    <input type="date" id="notes" name="start_date" placeholder="Season's start date" value="{{>start_date}}">
   </div>
  </div>
  <div class="control-group">
   <label class="control-label" for="end-date">End date</label>
   <div class="controls">
    <input type="date" id="notes" name="end_date" placeholder="Season's end date" value="{{>start_date}}">
   </div>
  </div>
  <div class="control-group">
   <div class="controls">
    <button class="btn" name="action" data-id={{>id}} value="add" onclick="updateSeason(this);return false;">Update</button>
   </div>
 </div>
</form>
</script>

<script language="javascript" type="text/javascript">
function clean_forms(){
	$('#season_content_text').val('');
}

function renderEditForm(data){
	$('#edit_season_tab').attr('class', '');
	var content = $('#templateSeasonEditForm').render(data.data);
	$('#edit_season_tab_content').html(content);
	$('#season_content_text').val(data.data[0].content);
}

function loadEditForm(button){
	var id = $(button).attr('data-id');
	sendEvent('/api/seasons/'+id, 'GET', {}, renderEditForm);
}

function updateSeason(button){
	console.log("Updating the season");
	var data = {};
	$('form#seasonEditForm :input').each(function(i, element){
        console.log(element);
		var element_name  = $(element).attr('name');
		var element_value = $(element).val();
		data[element_name] = element_value;
    });
    var id = $(button).attr('data-id');
    var season_content = $('#season_content_text').val();
    data.content = season_content;
    sendEvent('/api/seasons/'+id, 'PUT', data, renderSeasonsList);
}

function saveSeason(){
	console.log("Save the season");
	var data = {};
    $('form#seasonForm :input').each(function(i, element){
        console.log(element);
		var element_name  = $(element).attr('name');
		var element_value = $(element).val();
		data[element_name] = element_value;
    });
    var season_content = $('#season_content_text').val();
    data.content = season_content;
    sendEvent('/api/seasons', 'PUT', data, renderSeasonsList);
    return false;
}

function renderSeasonsList(data){
	console.log('Clean forms');
	clean_forms();
	var content = $('#templateSeasonItem').render(data.list);
	$('#seasons_table').html(content);
}

function requestSeasonsList(){
	console.log('Requesting seasons list');
	sendEvent('api/seasons', 'GET', {}, renderSeasonsList);
};

function renderAddForm(){
	$('#add_season_tab_content').html($('#templateSeasonForm').render());
};

function init_autoload_tabs(){
	console.log('Autoload tabs');
	$('#seasonTabs #list_seasons_tab').bind('show', function(e) {
		console.log('Calling request');
		requestSeasonsList();
	});
	$('#seasonTabs #add_season_tab').bind('show', function(e){
 		console.log('Render Add form');
 		renderAddForm();
	});
};

function init_tabs(){
	$('#seasonTabs a').click(function (e) {
		e.preventDefault();
	    $(this).tab('show');
	  });
};

$(document).ready(function(){
	init_tabs();
	init_autoload_tabs();	
	requestSeasonsList();
});
</script>