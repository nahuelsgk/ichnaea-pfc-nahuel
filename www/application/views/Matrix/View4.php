
<div class="container">
<button onclick="requestAddSample();">Add sample</button>
<button onclick="requestAddVariable();">Add variable</button>

<table id="matrix" border="1">
 <thead>
  <tr id="headers">
   <th><?php echo $matrix["name"];?></th>
     <th class="last_column origin" id="origin_header" valign="bottom">
     Origin
     </th>
  </tr>
 </thead>
 <tbody id="list_of_samples">
 </tbody>
</table>
</div>

<script language="text/x-jsrender" id="templateValue">

</script>
</script>
<script language="text/x-jsrender" id="templateHeaderColumn">
<th data-column='{{>i_column}}' class="header" onClick="renderForm(this)">
 <div class="content_header">
  <div class="variable_name">
   {{>name}}
  </div>
  <div class="form_header">
  
  </div>
 </div>
</th>
</script>

<script language="text/x-jsrender" id="templateHeaderColumnForm">
<th data-column='0' class="header">
 <div class="form_header">
  <select>
   <option id="single_variable">Single variable</option>
   <option id="single_variable">Derived</option>
  </select>
 </div>
 <div class="variable_name">
  <input name="variable_alias">
 </div>
</a>
</th>
</script>

<script language="text/x-jsrender" id="templateSampleHeader">
<td class="sample_header" align="center">
  <input style="width: 100px;" type="text" id="" data-sample_id="" name="sample_name" placeholder="Sample's name"><br>
  <input style="width: 100px;" type="date" id="" data-sample_id="" name="sample_data" placeholder="Sample's date"><br>
</td>
</script>

<script language="text/x-jsrender" id="templateSampleOrigin">
<td class="origin" align="center"><input type="text" placeholder="Cow, pork or whatever"></type></td>
</script>

<script language="text/x-jsrender" id="templateSample">
<tr class="sample">

{{for #data tmpl="#templateSampleHeader"/}}

{{for headers}}
<td align="center" class="sample_header" data-sample-id="" data-row="" >
<input style="width: 100px;" type="text" data-row="" data-column="" onchange="updateValue();">
</td>
{{/for}}

{{for #data tmpl="#templateSampleOrigin"/}}
</tr>
</script>

<script language="text/x-jsrender" id="templateSampleFulfill">
<tr class="sample" data-row={{>i_sample}}>

{{for #data tmpl="#templateSampleHeader"/}}

{{for values ~i_sample=i_sample}}
<td align="center" class="sample_header" data-row="{{>~i_sample}}" data-column="{{:#index}}" >
<input style="width: 100px;" type="text" data-row="{{>~i_sample}}" data-column="{{:#index}}" onchange="updateValue(this);" value="{{>#data}}">
</td>
{{/for}}

{{for #data tmpl="#templateSampleOrigin"/}}
</tr>
</script>

<script type="text/javascript">
header_index = 0;
sample_index = 0;

//Returns a header structure. By now only counts and 
function getHeaders(){
	var headers = new Array();
	$('#headers').find('.header').each(function(index, element){
		headers.push({column: index});
	});
	return headers;
	
} 

function renderSample(sample, row ,n_headers){
	//For consistency in the GUI on incomplete samples
	for (var i=0; n_headers - sample.values.length; i++){
	  sample.values.push('');
	}
	console.log(sample);
	var content = $('#templateSampleFulfill').render(sample);
	$('#list_of_samples').append(content);
}

function add_sample(){
	var headers = getHeaders();
	console.log(headers);
	var content = $('#templateSample').render({headers: headers});
	$('#list_of_samples').append(content);
}

function add_variable(content){
	var header;
    if(content){
	  //Add in the headers
	  header = $('#templateHeaderColumn').render(content);
    }
    else{
      header = $('#templateHeaderColumnForm').render();
    }
	$('#matrix > thead > tr#headers > th#origin_header').before(header);
	
	//Add in the body
	$('#matrix > tbody').find('tr.sample').each(function(){
		$(this).find('td.origin').before('<td><input type="text"></td>');
	});
    
}


function populating(){}

function update_value(){}

function update_header_definition(){}

function update_name_header(){}

function update_sample_value(){}

function updateValue(input){
    var column = $(input).attr('data-column');
    var row    = $(input).attr('data-row');
    var value  = $(input).val();
    sendEvent('/api/matrix/<?php echo $matrix["id"];?>/values/'+row+'/'+column, 'PUT', {value: value}, {});
}
function renderMatrix(data){
	var number_of_headers = 0;
	$.each(data.data.headers, function(i, element){
		element.i_column = i;
 		add_variable(element);
 		number_of_headers++;
	});

	$.each(data.data.samples, function(i, element){
		element.i_sample = i;
		renderSample(element, i, number_of_headers);
	});
	$("#matrix").colResizable();
}

function requestAddVariable(){
	sendEvent('/api/matrix/<?php echo $matrix["id"];?>/variable', 'PUT', {}, add_variable());
}

function requestAddSample(){
    sendEvent('/api/matrix/<?php echo $matrix["id"];?>/sample', 'PUT', {}, add_sample());	
}
function requestMatrix(){
	sendEvent('/api/matrix/<?php echo $matrix["id"];?>/content', 'GET', {}, renderMatrix);
}
$(document).ready(function(){
 requestMatrix();	
});
</script>
