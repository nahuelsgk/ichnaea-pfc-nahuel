{extends file="Tmpls/ichnaea_root.tpl"}
{block name='title'}New Matrix for PROJECTNAME{/block}

{block name='page'}
{init path="Controllers/Matrix" function="displayMatrixForm"}

<form method="post" name="matrixForm">
<table>
<tr><td>Name</td><td><input type="text" name="name" placeholder="Name of the matrix" required></td></tr>
<tr><td>Variable</td><td>
<a href="#" id="addVar">Add Another Variable</a>
<div id="variables">
<p>
<input type="text" placeholder="Name of variable" id="variable" name="variable_name[]" placeholder="Name of the new variable"><br>
</p>
</div>
</td>
</tr>
<tr><td><input type="submit" value="Save matrix" name="submit_matrix"></td></tr>
</table>
</form>
<script language="javascript" type="text/javascript">
$(function() {
  var variablesDiv = $('#variables');
  var i = $('#variables p').size() + 1;
  $('#addVar').on('click', function() {
    $('<p><input type="text" placeholder="Name of variable" id="variable" name="variable_name[]" placeholder="Name of the new variable"> <a href="#" id="remVar">Remove</a></p>').appendTo(variablesDiv);
    i++;
    return false;
  });
  $('#remVar').live('click', function() {
    $(this).parents('p').remove();
  return false;
  });
});
</script>
{/block}

