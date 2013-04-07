{extends file="Tmpls/ichnaea_root.tpl"}
{block name='title'}Ichnaea Home User{/block}
{block name='page'}
{init path="Controllers/Vars" function="display_var_list"}
<table id="__list_single_vars" class="table-striped">
<th>Id</th><th>Seasons</th>
<tr id="__template_single_var" class="template_hidden">
<td class="single_var_name"><span class="single_var_name">Sample name</span></td>
<td>
  <table class="__template_single_var_season_list table-striped"">
    <tr id="__template_single_var_season_item"><td><span class="single_var_season_name"></span></td><td><span class="single_var_season_notes"></span></td></tr>
  </table>
</td>
</tr>

</tbody>
</table>

<script type="text/javascript">
function renderListSingleVariables(list){
    $.each(list.data, function(i, variable){
      var singleVariableView = $('#__template_single_var').clone();
      var id = variable.svid;
      singleVariableView.attr('class','single_var_item');
      singleVariableView.attr('id', id);
      singleVariableView.find('span.single_var_name').html(variable.name);
     
      if(variable.seasons){
      $.each(variable.seasons, function (j, season){
      	var seasonView = $('#__template_single_var_season_item').clone();
      	seasonView.attr('id', season.ssid);
      	seasonView.attr('class', 'single_var_season_item');
      	seasonView.find('.single_var_season_name').html(season.name);
      	seasonView.find('.single_var_season_notes').html(season.notes);
      	singleVariableView.find('.__template_single_var_season_list').append(seasonView);
        });
      }
      $('#__list_single_vars').append(singleVariableView);
    });
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
  var request = new Request('/api/singlevar','listComplete', {}).send(renderListSingleVariables);

});

</script>
{/block}
