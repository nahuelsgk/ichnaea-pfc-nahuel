{extends file="Tmpls/ichnaea_root.tpl"}

{block name='title'}Matrix Definition{/block}
{block name='page'}
{init path="Controllers/Matrix" function="pageMatrixDefinition"}
<h1>{if $is_edit eq 'n'}New matrix {else}Editing matrix "{$name_matrix}"{/if}</h1>
<div id="msgid"></div>
<div class="breadcrumbs">
<a href="/home">Home</a> &gt;&gt; <a href="">Project(Edit)</a> &gt;&gt; {if $is_edit eq 'n'}New matrix{else}Editing matrix{/if}
</div>
<table>
<tr><td>Name of the matrix</td><td><input type="text" id="name_matrix" name="name" placeholder="Name of the matrix" value="{$name_matrix}" required></td></tr>
<tr><td>Visibility</td><td>
  <input type="radio" name="visibility" {if $public_matrix eq 'y'} checked {/if} value='public'>Public</input>
  <input type="radio" name="visibility" {if $public_matrix eq 'n'} checked {/if} value='private'>Private</input>
  </td>
</tr>
<tr><td></td><td><button id="add_variable">Add variable</button>  </td></tr>
</tr>
<td>
Definition of the variables<br>
</td>
<td>
  <table id="variables">
  <tbody>
  <tr>
    <th style="padding: 0 20px;">Name</th>
    <th style="padding: 0 10px;">Threshold</th>
    <th style="padding: 0 10px;"></th>
  </tr>
  {section name=v loop=$vars}
  <tr>
    <td><input name="name" tof="variable" vid="{$vars[v].id}" value="{$vars[v].name}" size="10"></td>
    <td><input tof="variable" name="threshold_limit" vid="{$vars[v].id}" value="{$vars[v].threshold_limit}"></td>
    <td><input type="checkbox" class="delete" name="select_var" vid="{$vars[v].id}"></td></tr>
  {/section}
  </tbody>
  </table>

</td>
</table>
{if $is_edit eq 'n'}
<button id="save_matrix">Save matrix definition</button>
{else}
<button id="update_matrix">Update matrix definition</button>
<button onclick='window.location.href="/matrix/view?mid={$mid}"'>Go and view the matrix!</button>
{/if}
<script language="javascript" type="text/javascript">
$(document).ready(function(){
  $('#add_variable').live('click',function(e){
    $('#variables > tbody:last').append('<tr class="variable_form_template"><td><input name="name_var" placeholder="Name of the variable"></td><td><input name="threshold_var" placeholder="Threshold treatment"></td></tr>');
  });

{if $is_edit eq 'n'}
  $('#save_matrix').click(function(){
    var name_matrix = $('#name_matrix');
    
    var data = {
      "ajaxDispatch": "Controllers/Matrix",
      "function": "dispatch_addNewMatrix",
      "values" : {
        "name_matrix": name_matrix.attr('value'),
	"variables" : [],
      }
    };
    
    var valueinputElements = $('table#variables tr.variable_form_template');
    $.each(valueinputElements, function(index, el){
      var name_var = $(el).find('input[name=name_var]');
      var threshold_var = $(el).find('input[name=threshold_var]');
      var new_value = {
        "name": name_var.val(), 
	"threshold": threshold_var.val() 
      };
      data.values.variables.push(new_value);
    });
    
    $.ajax({
      type:     'POST',
      dataType: 'json',
      processData: false,
      data:     JSON.stringify(data),
      success:  function(data){
	          if (data["mid"]){
		    window.location.href = "/matrix/edit_new?mid="+data["mid"];
		  }
		  else{
		    alert("Error");
		  }

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

{else}
  $('input[tof=variable]').change(function (){
    var database_field = $(this).attr('name');
    alert(database_field);
    var vid = $(this).attr('vid');
    var new_value = $(this).attr('value');
    var post = ' { "ajaxDispatch": "Controllers/Matrix", "function": "dispatch_updateMatrixVariables", "values": { "id": "' + vid + '", "'+database_field+'": "' + new_value +'", "action": "update"  } } ';
    alert(post);
    $.ajax({
      type:     'POST',
      dataType: 'json',
      data:     post,
      processData: false,
      success:  function(data){
                 alert(data);
		 location.reload();
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

  $('#update_matrix').click(function(){
    var data = { 
      "ajaxDispatch": "Controllers/Matrix", 
      "function": "dispatch_updateMatrix", 
      "values": { 
        "name_matrix": $('#name_matrix').val(),
	"visibility": $('input:radio[name=visibility]:checked').val()
      }  
    } 
    alert( JSON.stringify(data) );

    $.ajax({
      type:     'POST',
      dataType: 'json',
      data:     JSON.stringify(data) ,
      success:  function(data){
                  alert(data);
		  location.reload();
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
