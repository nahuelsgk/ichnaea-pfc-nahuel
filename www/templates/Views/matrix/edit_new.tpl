{extends file="Tmpls/ichnaea_root.tpl"}

{block name='title'}Matrix Definition{/block}
{block name='page'}
{init path="Controllers/Matrix" function="pageMatrixEditNew"}
<h1>{if $is_edit eq 'n'}New matrix {else}Editing matrix <div class="name_matrix" style="display: inline;">"{$name_matrix}"{/if}</h1>
<div id="msgid"></div>
<div class="breadcrumbs">
<a href="/home">Home</a> &gt;&gt; Editing matrix
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
  <center><b>List of variables</b></center>
  <table>
  </tr>
<td>
  <table id="__variables">
  <tbody>
  <tr>
    <th style="padding: 0 20px;">Name</th>
    <th style="padding: 0 10px;">Threshold</th>
  </tr>
  {section name=v loop=$vars}
  <tr>
    <td><input name="name" tof="variable" vid="{$vars[v].id}" value="{$vars[v].name}" size="10"></td>
    <td><input tof="variable" name="threshold_limit" vid="{$vars[v].id}" value="{$vars[v].threshold}"></td>
  </tr>
  {/section}
  <tr><td><button id="__add_var" mid="{$mid}">Add a new var</button></td></tr>
  </tbody>
  </table>

</td>
</table>
{if $is_edit eq 'n'}
{else}
{/if}
<script language="javascript" type="text/javascript">
$('#__add_var').click(function(){
  var mid = $(this).attr("mid");
  $(this).remove();
  $('#__variables tr:last').after('<tr id="__new_var"><td><input type="text" id="__name_new_var" name="name_new_var"></td><td><input type="text" id="__threshold_new_var" name="threshold_new_var"></td><td><button id="__save_new_var" onclick="save_var('+mid+')">Save</button></td></tr>');
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
</script>
{/block}

{block name="help_text"}
{/block}
