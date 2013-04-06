{extends file="Tmpls/ichnaea_root.tpl"}
{block name='title'}View Matrix{/block}

{block name='page'}
{init path="Controllers/Matrix" function="displayMatrixViewForm3"}
<div id="breadcrumbs">
<a href="/home">Home</a> &gt;&gt; <a href="/matrix/edit_new?&mid={$mid}">Matrix definition</a> &gt;&gt; Matrix
</div>
<div id="msgid"></div>
<table border="1px" style="margin-left: 50px;">
<th><button id="add_sample">Add Sample</button></th>
{section name=v loop=$vars}
<th style="padding: 5px 10px 5px 10px">
  {$vars[v].name}<br>
  <i style="font-size: 10px; color: #888">{$vars[v].threshold}</i>
</th>
{/section}
<th>Action</th>

{foreach name="row" key=key item=row from=$samples}
  <tr>
    <td>Sample {$smarty.foreach.row.index} <br><i style="font-size: 7px; color: #888">Tag: {$key}</i></td>
    {foreach key=var_id item=value from=$row}
      <td><input type="text" size="15" vid="{$var_id}" valid="{$value.id}" value="{$value.value}" sid="{$key}"></td>
    {/foreach}
  <td><button class="delete_sample" sample_id="{$key}" name="delete_sample">Delete</button></td>
  </tr>
{/foreach}


<tr id="sample" style="display: none;">
<td>New Sample</td>
{section name=v loop=$vars}
<td style="padding: 5px 10px 5px 10px"><input type="text" size="16" vid="{$vars[v].id}" valid="" sid="" name="new_sample"></td>
{/section}
<td><button id="save_new_sample" name="save_new_sample">Save</button></td>
</tr>
<table>
<table id="__view_matrix_table" border=1px></table>


<div id='__template_matrix_table'>
<table id='__matrix' border=1px>
 <tr id="__template_tr_header">
   <th id="__template_header_cell"><span id='__template_header_title'>Name of the var</span></th>
 </tr>
 <tbody>
 <tr id="__template_sample">
   <td id="__template_sample_header" style="text-align: center;"></td>
   <td id="__template_sample_value" style="text-align: center;"></td>
 </tr>
 </tbody>
</div>
<input id="__template_sample_input" type="text" size="30" data-sid="" placeholder="Name of the sample"></input>
<input id="__template_value_input" type="text" size="15" placeholder="Write a sample "></input>
<table id="__table_form"/>

<!-- <input type="button" id="btnAdd" value="Add row"> </a> -->
<script language="javascript" type="text/javascript">
var matrixBuilt = {};
var template_row = {};
var buildMatrix =  function (matrixJson){
  alert(JSON.stringify(matrixJson));
  //Build firs row empty for headers
  $('#__table_form').append('<tr/>');
  //Build an empty cell for the values
  $('#__table_form tr:last').append('<td><button id="__add_sample">Add a sample!</td>');
  //Build each th
  $.each(matrixJson.data.headers, function(i, variable){
    var header = $('#__template_header_cell').clone();
    header.find('#__template_header_title').text(variable.name);
    $('#__table_form tr:first').append(header);
  });

  //Build each sample
  $.each(matrixJson.data.samples, function(i, sample){
    var body = $('#__table_form > tbody');
    body.append('<tr/>');
    var current_row = $('#__table_form tr:last');
    var header_sample = $('#__template_sample_header').clone();
    header_sample.html(sample.name);
    header_sample.attr('id', i);
    current_row.append(header_sample);

    $.each(sample.values, function(j, jvalue){
      var cell_value = $('#__template_sample_value').clone();
      cell_value.html(jvalue.value);
      cell_value.attr('id',j);
      current_row.append(cell_value);
    });
  });
  $('#__add_sample').click(new_sample);
};

function render_new_sample(sid){
  var sid = sid.data.sid;
  var template_row_scheme = $('#__table_form > tbody > tr:last').clone();
  
  var new_sample_form = $('<tr/>');
  template_row_scheme.find('td').each(function(index, element){
    var cell = $(element);
    if(index == 0){
      var cell = $('<td/>')
      var sample_input = $('#__template_sample_input').clone();
      sample_input.attr('id',sid);
      cell.append(sample_input);
      new_sample_form.append(cell);
   }
   else{
      var cell = $('<td/>')
      var sample_input = $('#__template_value_input').clone();
      cell.append(sample_input);
      new_sample_form.append(cell);
   }
   
  });
  $('#__table_form > tbody ').append(new_sample_form);
}

function new_sample(){
  var request = { uri : "/api/matrix", op : "newSample", params: { mid: {$mid} } };
  send_event3(request, render_new_sample);

}


function render_view_matrix(){
  var request = {
    uri : "/api/matrix",
    op : "buildMatrix",
    params: { mid: {$mid} }
  };
  
  send_event3(request, buildMatrix);
}


$(document).ready(function (){
  render_view_matrix();
  $("#add_sample").live('click', function (e){
   var data = { 
     "ajaxDispatch": "Controllers/Matrix", 
     "function": "dispatch_addSample", 
     "values": { "mid": "{$mid}" }
   };
   $.ajax({
     type: 'POST',
     dataType: 'json',
     data:  JSON.stringify(data),
     success: function(data){
       window.location.reload()
              },
     error: function(data){
       alert("KO");
       alert(data);
       for(var key in data) {
         $('#msgid').append(key);
         $('#msgid').append('=' + data[key] + '<br />');
       }
    }
     
   });
  });
  
  $("input").change(function(){
    var new_value = $(this).attr('value');
    var valid = $(this).attr('valid');
    var sid = $(this).attr('sid');
    var vid = $(this).attr('vid');
    var data = { 
      "ajaxDispatch": "Controllers/Matrix", 
      "function": "dispatch_updateMatrixValues", 
      "values": { 
        "value": new_value,
	"valid": valid, 
	"sample_id": sid, 
	"vid": vid
      }
    };

    $.ajax({
      type:     'POST',
      dataType: 'json',
      data:     JSON.stringify(data) ,
      success:  function(data){
        window.location.reload();
                },
      error: function(data){
        alert("KO");
	}
      });

  });

  $(".delete_sample").click(function (){
    var id = $(this).attr('sample_id');  
    var values = [{ "id": id }];
    send_event("Controllers/Matrix","removeSampleFromTheMatrix", values);  
  });
});
  
</script>	
{/block}
{block name="help_text"}
From here you can set the values from each sample. Modifying the variable, automaticly saves the value
<ul>
<li>
  Variables: as a header, from the top to the bottom you have:
  <ul>
    <li>Name of the variable</li>
    <li>Threshold of the variable</li>
  </ul>
</li>
<li>
  Sample
  <ul>
    <li>Sample number</li>
    <li>Sample tag: unique identifier</li>
  </ul>
</li>
</ul>
All this process is under construction. The AJAX reloads the windows on any change to ensure the correct action to the Database
{/block}
