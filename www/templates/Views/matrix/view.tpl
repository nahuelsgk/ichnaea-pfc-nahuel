{extends file="Tmpls/ichnaea_root.tpl"}
{block name='title'}View Matrix for PROJECTNAME{/block}

{block name='page'}
{init path="Controllers/Matrix" function="displayMatrixViewForm"}
<table border="1px" style="margin-left: 50px;">
<th>Sample\Variable</th>
{section name=v loop=$vars}
<th style="padding: 5px 10px 5px 10px">{$vars[v].name}</th>
{/section}
<th>Action</th>

{foreach name="row" key=key item=row from=$samples}
<tr>
<td>Sample {$key}</td>
{foreach key=var_id item=value from=$row}
<td>{$value}</td>
{/foreach}
</tr>
{/foreach}
<tr id="sample">
<td>New Sample</td>
{section name=v loop=$vars}
<td style="padding: 5px 10px 5px 10px"><input type="text" size="5" var_id="{$vars[v].id}" name="value"></td>
{/section}

<td><button id="save_new_sample" name="save_new_sample">Save</a></td>
</tr>
<table>
<input type="button" id="btnAdd" value="Add row"> </a>
<script language="javascript" type="text/javascript">
$(document).ready(function (){
  $("#btnAdd").live('click', function (e){
    var row = $("table tr:last").clone(true);
    row.insertAfter("table tr:last");
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
    //alert(post);
    $.ajax({
      type: 'POST',
      dataType: 'text',
      url: 'http://dev.ichnaea.lsi.upc.edu/matrix/view?mid={$mid}',
      data: { JSON: post },
      success: function(data){
        $('#results').html(data);
      }
    });
  });
});

</script>	
<div id="results">....</div>
{/block}

