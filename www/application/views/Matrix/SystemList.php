<div class="container">
<table id="__list_matrixs" class="matrixs_list">
<tr>
 <th>Name Matrix</th>
 <th>&nbsp;</th>
</tr>
<?php foreach ($matrixs as $matrix){?>
<tr id="template_row">
 <td name="matrix_name"><?php echo $matrix->name; ?></td>
 <td><a class="btn btn-mini" name="matrix_configuration" title="Matrix configuration" href="/matrix/<?php echo $matrix->id;?>"><i class="icon-edit"></i></a>|<a class="btn btn-mini" name="matrix_view" title="Matrix configuration" href="/matrix/<?php echo $matrix->id;?>/view" title="View matrix"><i class="icon-zoom-in"></i></a></td>
</tr>
<?php } ?>

<tr class= "hidden" id="__new_matrix_form" data-form=""><td><input type="text" name="name_matrix" placeholder="Name of the matrix"></td><td><button data-form="" onclick="save(this);return false;">Save matrix</button></td></tr>
</table>
<a class="btn btn-mini" onClick="renderFormNewMatrix()"><i class="icon-plus"></i></a>
</div>

<!-- Template form for adding a matrix -->

<!--  <form class="hidden" data-template="__matrix_item" id="__new_matrix_form"><table><tr data-original-template="__form_add_new_matrix"><td><input type="input" name="name_matrix" placeholder="Name of the matrix"></td><td><button id="__save_matrix" onclick="save()">Save matrix</button></td><tr></tr><table></form> -->
<!-- Template form for adding a matrix -->

<script language="javascript" type="text/javascript">
forms=0;
function renderFormNewMatrix(button){
  var tr = $('#__new_matrix_form').clone();
  tr.attr('class', '');
  tr.attr('id', 'form'+forms);
  tr.find(':submit').attr('data-form', 'form'+forms);
  forms++;
  $('#__list_matrixs tr:last').after(tr);
}

function renderFormAsTr(matrix, form_id){
	var matrix_name = $('tr#'+form_id+' td input').val();
	console.log(matrix_name);
	var id = matrix.id;
	console.log(matrix.id);
	var template = $('#template_row').clone();
	template.find('td[name="matrix_name"]').html(matrix_name);
	template.find("a[name='matrix_configuration']").attr('href', '/matrix/'+id);
	template.find("a[name='matrix_view']").attr('href', '/matrix/'+id+'/view');
	$('tr#'+form_id).replaceWith(template);
	console.log(template);
}

function save(submit){
  var form_id 	  = $(submit).attr('data-form');
  var matrix_name = $('tr#'+form_id+' td input').val();
  console.log(matrix_name);
  if (matrix_name == '') alert('Matrix needs a name');
  sendEvent('/api/matrixs_api/matrixs', 'PUT', {name: matrix_name}, function(data){renderFormAsTr(data, form_id)});
  return false;
}
</script>
