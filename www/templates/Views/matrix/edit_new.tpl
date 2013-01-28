{extends file="Tmpls/ichnaea_root.tpl"}

{block name='title'}Matrix Definition{/block}
{block name='page'}
{init path="Controllers/Matrix" function="displayMatrixForm2"}
<h1>{if $is_edit eq 'n'}New matrix{else}Editing matrix "{$name_matrix}"{/if}</h1>
<table>
<tr><td>Name of the matrix</td><td><input type="text" id="name_matrix" name="name" placeholder="Name of the matrix" value="{$name_matrix}" required></td></tr>
<tr>
<td>Definition of the variables</td>
<td>
  <table id="variables">
  <tbody>
  {section name=v loop=$vars}
  <tr><td><input name="name" tof="variable" vid="{$vars[v].id}" value="{$vars[v].name}" size="10"></td><td><input tof="variable" name="threshold_limit" vid="{$vars[v].id}" value="{$vars[v].threshold_limit}"></td><td><input type="checkbox" class="delete" name="select_var" vid="{$vars[v].id}"></td></tr>
  {/section}
  </tbody>
  </table>
<button id="add_variable">Add variable</button>  

</td>
</table>
{if $is_edit eq 'n'}
<button id="save_matrix">Save matrix definition</button>
{else}
<button id="update_matrix">Update matrix definition</button>
{/if}
<a href="/matrix/view?mid={$mid}">Go and view the matrix!</a>
<script language="javascript" type="text/javascript">
$(document).ready(function(){
  $('#add_variable').live('click',function(e){
    $('#variables > tbody:last').append('<tr class="variable_form_template"><td><input name="name_var" placeholder="Name of the variable"></td><td><input name="threshold_var" placeholder="Threshold treatment"></td></tr>');
  });
{if $is_edit eq 'n'}
  $('#save_matrix').click(function(){
    var post = ' { ';
    post += '"ajaxDispatch": "Controllers/Matrix", "function": "dispatch_addNewMatrix", "values": { ';
    var name_matrix = $('#name_matrix');
    post += ' "name_matrix": "'+ name_matrix.attr('value')+'"  , "variables": [ ';

    //New variables
    var valueinputElements = $('table#variables tr.variable_form_template');
    $.each(valueinputElements, function(index, el) {
      var name_var = $(el).find('input[name=name_var]');
      var threshold_var = $(el).find('input[name=threshold_var]');
      post += '{ "name": "'+name_var.val()+'", "threshold": "'+threshold_var.val()+'"},';
    });
    
    post = post.substring(0,post.length-1);
    post += '] } } ';
    alert(post);
    
    $.ajax({
      type:     'POST',
      dataType: 'text',
      data:     { JSON: post },
      success:  function(data){
                  alert(data);
                }
    ;);

  });
{else}
  $('input[tof=variable]').change(function (){
    var database_field = $(this).attr('name');
    alert(database_field);
    var vid = $(this).attr('vid');
    var new_value = $(this).attr('value');
    var post = ' { ';
    post += '"ajaxDispatch": "Controllers/Matrix", "function": "dispatch_updateMatrixVariables", "values": { "id": "' + vid + '", "'+database_field+'": "' + new_value +'", "action": "update"  }';
    post += '} ';
    alert(post);
    $.ajax({
      type:     'POST',
      dataType: 'text',
      data:     { JSON: post },
      success:  function(data){
                 alert(data);
                }
      });

  });

  $('#update_matrix').click(function(){
    var post = ' { ';
    post += '"ajaxDispatch": "Controllers/Matrix", "function": "dispatch_updateMatrix", "values": { ';

    var name_matrix = $('#name_matrix');
    post += ' "name_matrix": "'+ name_matrix.attr('value')+'"  , "variables": [ ';

    var valueinputElements = $('table#variables tr.variable_form_template');
    $.each(valueinputElements, function(index, el) {
      var name_var = $(el).find('input[name=name_var]');
      var threshold_var = $(el).find('input[name=threshold_var]');
      post += '{ "name": "'+name_var.val()+'", "threshold": "'+threshold_var.val()+'", "action": "new" },';
    });
    
    //selected variables
    var valueCheckboxElements = $('input[name=select_var]:checked');
    $.each(valueCheckboxElements , function(index, el){
     post += '{ "action": "delete", "id": "'+$(el).attr('vid')+'" },';
    });
    
    post = post.substring(0,post.length-1);
    post += '] } } ';
    alert(post);
    
    $.ajax({
      type:     'POST',
      dataType: 'text',
      data:     { JSON: post },
      success:  function(data){
                  alert(data);
                }
    });

  });

{/if}
});
</script>
{/block}

{block name="help_text"}
Here you can update the matrix main definition:
<ul style="padding-left: 10px;">
<li>Name of the matrix: if you are editing it, must click "Update"</li>
<li>Update Variables: name, threshold. It's update directly on modifying it</li>
<li>Adding variables: must fill the data and click "Update"</li>
<li>Checkbox: BECAREFUL. This is the select for deleting the variable. If you select it, and click update it: will perform a delete of the variable, with its all samples and values
</ul>
All this process is underconstruction!
{/block}
