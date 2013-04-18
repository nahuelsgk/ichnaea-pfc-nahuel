
<div class="container">
<h1>Testing Handsontable</h1>
<div id="dataTable"></div>

<script src="/js/handsontable/dist/jquery.handsontable.full.js"></script>
<link rel="stylesheet" media="screen" href="/css/handsontable/jquery.handsontable.full.css">
<script type="text/javascript">
function getTypeOfVar(){
 alert("Request to get the type of var");
}

var data = [
	["", "Kia", "Nissan", "Toyota", "Honda"],
	["2008", 10, 11, 12, 13],
	["2009", 20, 11, 14, 13],
	["2010", 30, 15, 12, 13]
];


$("#dataTable").handsontable({
	data: data,
	minRows: 100,
	minCols: 40,
	rowHeaders: true,
	colHeaders: true,
});

</script>
