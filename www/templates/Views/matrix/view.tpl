{extends file="Tmpls/ichnaea_root.tpl"}
{block name='title'}View Matrix for PROJECTNAME{/block}

{block name='page'}
{init path="Controllers/Matrix" function="displayMatrixViewForm2"}
<a href="">Home</a> &gt;&gt; <a href="">Project</a> &gt;&gt; <a href="/matrix/edit_new?pid={$pid}&mid={$mid}">Matrix definition</a>
<table border="1px" style="margin-left: 50px;">
<th><button id="add_sample">Add Sample</button></th>
{section name=v loop=$vars}
<th style="padding: 5px 10px 5px 10px">{$vars[v].name}</th>
{/section}
<th>Action</th>

{foreach name="row" key=key item=row from=$samples}
  <tr>
    <td>Sample {$key}</td>
    {foreach key=var_id item=value from=$row}
      <td><input type="text" size="15" vid="{$var_id}" val="{$value.id}" value={$value.value}></td>
    {/foreach}
  <td><button class="delete_sample" sample_id="{$key}" name="delete_sample">Delete</button></td>
  </tr>
{/foreach}


<tr id="sample" style="display: none;">
<td>New Sample</td>
{section name=v loop=$vars}
<td style="padding: 5px 10px 5px 10px"><input type="text" size="16" var_id="{$vars[v].id}" name="value"></td>
{/section}
<td><button id="save_new_sample" name="save_new_sample">Save</button></td>
</tr>
<table>
<!-- <input type="button" id="btnAdd" value="Add row"> </a> -->
<script language="javascript" type="text/javascript">
$(document).ready(function (){
  $("#add_sample").live('click', function (e){
    $("#sample").show();
  });
  
  $("#save_new_sample").click(function (){
    
    var valueinputElements = $('input[name=value]');
    
    var post = ' { ';
    post += '"ajaxDispatch": "Controllers/Matrix", "function": "dispatch_displayMatrixViewForm", "values": [ ';
    $.each(valueinputElements, function(index, el) {
      var id = $(el).attr('var_id');
      var value = $(el).attr('value');
      post += '{ "id": "'+id+'" , "value": "'+value+'"},';
    });
    post = post.substring(0,post.length-1);
    post += ']}';
    $.ajax({
      type:     'POST',
      dataType: 'text',
      data:     { JSON: post },
      success:  function(data){
                  //$('#results').html(data);
		  alert(data);
		  location.reload();
                }
    });
  });
  $(".delete_sample").click(function (){
    var id = $(this).attr('sample_id');  
    var post = ' { ';
    post += '"ajaxDispatch": "Controllers/Matrix", "function": "dispatch_removeSampleFromTheMatrix","values": [ { "id": "'+id+'" } ]';
    post += '}';
    alert(post);
    $.ajax({
      type:     'POST',
      dataType: 'text',
      data:	{ JSON: post },
      success:  function(data){
                  alert(data);
		  location.reload();
                }
    });  
  });
});

</script>	
{/block}

