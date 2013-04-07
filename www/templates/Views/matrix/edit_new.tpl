{extends file="Tmpls/ichnaea_root.tpl"}

{block name='title'}Matrix Definition{/block}
{block name='page'}
{init path="Controllers/Matrix" function="pageMatrixEditNew"}
<h1>Editing matrix <div class="name_matrix" style="display: inline;">{$name_matrix}</h1>
<div id="msgid"></div>
<div class="breadcrumbs">
<a href="/home">Home</a> &gt;&gt; Editing matrix &gt;&gt; <a href="/matrix/view?mid={$mid}">View matrix</a>
</div>
<table>
<tr><td valign="top">
  <table valign="top">
    <tr><td colspan="2"><center><b>Basic Information</b></center></td></tr>
    <tr><td>Name of the matrix</td><td><input type="text" id="__name_matrix" name="name" placeholder="Name of the matrix" value="{$name_matrix}" mid="{$mid}" required ></td></tr>
    <tr><td>Visibility</td>
      <td>
        <input type="radio" name="visibility" {if $public_matrix eq 'y'} checked {/if} value='public' mid="{$mid}">Public</input>
        <input type="radio" name="visibility" {if $public_matrix eq 'n'} checked {/if} value='private' mid="{$mid}">Private</input>
      </td>
    </tr>
  </table>
</td><td valign="top">
<ul class="nav nav-tabs" id="myTab">
  <li class="active"><a>Add a single var</a></li>
  <li><a>Origin</a></li>
</ul>

<div data-template="add_single_variable_form">
<table>
<th>System's variable</th><th>Seasons</th>
<tr>
 <td>
  <select size="100" id="__single_vars_available" name="matrixs_available" style="width: 200px; height: 200px;">
 </td>
 <td>
  <select  size="100" id="__seasons_available" name="matrixs_available" style="width: 200px; height: 200px;">
 </td>
</tr>
</table>
</div>
<script language="javascript" type="text/javascript">
$('#__add_var').click(function(){
  var mid = $(this).attr("mid");
  $(this).remove();
  $('#__variables tr:last').after('<tr id="__new_var"><td><input type="text" id="__name_new_var" name="name_new_var"></td><td><input type="text" id="__threshold_new_var" name="threshold_new_var"></td><td><button id="__save_new_var" onclick="save_var('+mid+')">Save</button><button id="__cancel" onclick="cancel_last_action()">Cancel</td></tr>');
});

function save_var(mid){
  var name_var = $('#__name_new_var').attr('value');
  if (name_var == "") { alert("Needs a name"); return;}
  var threshold_var = $('#__threshold_new_var').attr('value');
  if (threshold_var == "" ) { alert("Needs a threshold"); return; }
  
  var request = {
    uri: "/api/var",
    op: "create",
    params: {  vars: [ { mid: mid, name: name_var, threshold: threshold_var } ] }
  };
  // { "status":"OK","data": [ { "name":"a","threshold":"788","vid":21,"mid":5 } ] }
  send_event3(request);
}
$('#__name_matrix').change(function(){
  var new_name = $(this).val();
  var mid = $(this).attr("mid");

  var render_new_name_matrix = function(){
    $("div.name_matrix").html(new_name);
  }
  var request = {
    uri: "/api/matrix",
    op: "update",
    params: {
      mid	: mid, 
      field     : "name",
      new_value : new_name 
    }
  };
  send_event3(request, render_new_name_matrix);
});
$("input[name='visibility']").change(function(){
  var visibility = this.value;
  var mid = $(this).attr("mid");
  var request = {
    uri: "/api/matrix",
    op: "update",
    params: {
      mid       : mid,
      field     : "visibility",
      new_value : visibility
    }
  };
  send_event3(request);
});
function renderListSeasons(list){
	  $('#__seasons_available').empty();
	  $.each(list.data, function(i, season){
		  $('#__seasons_available')
		   .append($('<option class="season_var_item"></option>')
				   .attr("value",season.svid)
				   .text(season.name));	
	  });
}
function loadSeasons(){
	var svid = $(this).val();
	var data = { id : svid } ;
	var request = new Request('/api/singlevar','listSeasons', { svid: svid } ).send(renderListSeasons);
}

function renderListSimpleSingleVariables(list){
  var arrayList = { data : [ { svid: "29" ,  name : "ICHXX" } , { svid: "30" , name: "ICHYY"} ] };
  $.each(list.data, function(i, single_variable){
	  $('#__single_vars_available')
	   .append($('<option class="single_var_item"></option>')
			   .attr("value",single_variable.svid)
			   .text(single_variable.name));	
  });
  $('#__single_vars_available').change(loadSeasons);
}
$(document).ready(function(){ 
	var request = new Request('/api/singlevar','listSimple', {}).send(renderListSimpleSingleVariables);
});
</script>
{/block}