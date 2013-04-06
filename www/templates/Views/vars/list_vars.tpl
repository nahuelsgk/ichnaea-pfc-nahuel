{extends file="Tmpls/ichnaea_root.tpl"}
{block name='title'}Ichnaea Home User{/block}
{block name='page'}
{init path="Controllers/Vars" function="display_var_list"}
<table id="__list_single_vars" cellspacing=0 border=1>
<tr>
<th>Name of the var</th>
<th>Seasons</th>
<th>&nbsp;</th></tr>
<tbody>
{foreach name="sv" key=svid item=single_var from=$single_vars}
<tr class="single_var" data-svid={$svid}>
<td><span class="single_var_name" data-svid="{$svid}">{$svid} - {$single_var.name}</span></td>
<td>
  <button id="__add_season_to_a_single_var" disabled>Add season!</button>
  <table id="__list_single_var_season" data-svid="1">
    {if isset($single_var.seasons)}
    <tr class="season_single_var" data-seasonid="1" data-svid="1">
      <td><span>{$single_var.seasons[0].name}</span></td>
      <td><span>{$single_var.seasons[0].notes}</span></td>
      <td>&nbsp;</td></button>
      </td>
    </tr>
    {/if}
  </table>
<td>&nbsp;</td>
</tr>
{/foreach}
<tr class="single_var" data-svid="1" >
<form id="__new_var_form">

<td><input type="text" id="single_var_name" class="single_var_name" name="single_var_name" placeholder="Name of the variable" data-svid="1" required></td>
<td>
  <button id="__add_season_to_a_single_var" disabled>Add season!</button>
  <table id="__list_single_var_season" data-svid="1">
    <tr class="season_single_var" data-seasonid="1" data-svid="1">
      <td><input type="text" id="season_single_var_name" class="season_single_var_name" placeholder="Name of the season" data-seasonid></td>
      <td><input type="text" id="season_single_var_description" class="season_single_var_description" placeholder="Notes" data-seasonid></td>
      <td><input type="file" class="season_single_var_file" placeholder="File of the season" data-seasonid> <button type="submit" class="red-button" id="uploadButton">Upload</button>
      </td>
    </tr>
  </table>
<td><button type="submit" name="save_single_var" class="save_single_var" id="__save_single_var">Save single var!</button></td>
</form>

</tr>
</tbody>
</table>
<script type="text/javascript">

var Season = function(name, notes){
  this.name  = name;
  this.notes = notes;
}

function save_single_variable(e){
  alert("Save");
  var name          = $('#single_var_name').val();
  var season_name   = $('#season_single_var_name').val();
  var season_notes  = $('#season_single_var_description').val();
  var season_of_var = new Season(season_name, season_notes);
  
  var request = {
    uri    : "/api/singlevar",
    op     : "create",
    params : {
      name    : name,
      seasons : new Array(season_of_var)
    }
  };
  alert(JSON.stringify(request));
  send_event3(request);
  return false;
};

$(document).ready(function(){
  $('#__new_var_form').on('submit', save_single_variable);
});

</script>
{/block}
