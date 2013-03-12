{extends file="Tmpls/ichnaea_root.tpl"}
{block name='title'}View Matrix{/block}

{block name='page'}
{init path="Controllers/Matrix" function="displayMatrixViewForm2"}
<div id="breadcrumbs">
<a href="/home">Home</a> &gt;&gt; <a href="">Project</a> &gt;&gt; <a href="/matrix/edit_new?&mid={$mid}">Matrix definition</a> &gt;&gt; Matrix
</div>
<div id="msgid"></div>
<table border="1px" style="margin-left: 50px;">
<th><button id="add_sample">Add Sample</button></th>
{section name=v loop=$vars}
<th style="padding: 5px 10px 5px 10px">
  {$vars[v].name}<br>
  <i style="font-size: 10px; color: #888">{$vars[v].threshold_limit}</i>
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
<!-- <input type="button" id="btnAdd" value="Add row"> </a> -->
<script language="javascript" type="text/javascript">

$(document).ready(function (){
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
