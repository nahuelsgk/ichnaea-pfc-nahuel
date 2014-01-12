//@TODO: Web App common features
function clear_form(ele) {
	$(ele).find(':input').each(function() {
    switch(this.type) {
       case 'password':
        case 'select-multiple':
        case 'select-one':
        case 'text':
        case 'textarea':
            $(this).val('');
            break;
        case 'checkbox':
        case 'radio':
            this.checked = false;
    }
	});
}

function importFileIntoInput(fromInput, toInput){
    // Check for the various File API support.
    if (window.File && window.FileReader && window.FileList && window.Blob) {
      // Great success! All the File APIs are supported.
    } else {
    alert('The File APIs are not fully supported in this browser. You must copy the content of the season file for each one season.');
    }
    
    $('#'+fromInput).change(function (e){
      e = e || window.event;
      e.preventDefault(); 
 	  e = e.originalEvent || e;
 	  console.log(e);
      var file = e.target.files[0];  
      var reader = new FileReader();  
      reader.onload = function (evt) {  
               console.log(evt.target.result);
               $('#'+toInput).val(evt.target.result);  
      };
      reader.readAsText(file);   
    });
}

function sendEvent(uri, method, params, success_callback){
	console.log("Commons: Trying to send to new codeignite api");
	$.ajax({
		type: 		method,
		url: 		uri,
		dataType: 	'json',
		data: 		params,
		timeout: 	150000,
		success:	function(data){
			if(typeof success_callback != 'undefined') success_callback(data);
			//showMessage('success', 1500, data.msg);
		},
		error: function(data, type){
			  if(type==='timeout') {
				  errorMessage("Connection problems. Can't send request");
			  }
			  else{
				  errorMessage('Ajax error');
		      }
			  
		}
	});
};
function confirmMessage($message, $continueAction){
	  $('<div></div>').appendTo('body')
	  .html('<div><h6>'+$message+'</h6></div>')
	  .dialog({
	      modal: true, 
	      title: 'message', 
	      zIndex: 10000, 
	      autoOpen: true,
	      width: 'auto', 
	      resizable: false,
	      buttons: {
	          Yes: function () {
	        	  $continueAction();
	              $(this).dialog("close");
	        
	          },
	          No: function () {
	              $(this).dialog("close");
	              
	          }
	      },
	      close: function (event, ui) {
	          $(this).remove();
	      }
	});
}

function displayMessage($message, $continueAction){
	  $('<div></div>').appendTo('body')
	  .html('<div><h6>'+$message+'</h6></div>')
	  .dialog({
	      modal: true, 
	      title: 'message', 
	      zIndex: 10000, 
	      autoOpen: true,
	      width: 'auto', 
	      resizable: false,
	      buttons: {
	          Yes: function () {
	              $(this).dialog("close");
	        
	          },
	      },
	      close: function (event, ui) {
	          $(this).remove();
	      }
	});
}

function errorMessage($message){
	  $('<div></div>').appendTo('body')
	  .html('<div><h6>'+$message+'</h6></div>')
	  .dialog({
	      modal: true, title: 'message', zIndex: 10000, autoOpen: true,
	      width: 'auto', resizable: false,
	      close: function (event, ui) {
	          $(this).remove();
	      }
	});
}

function decode_result_event(data){
  if(data["result"] == '1'){
    if(data["operation"] == 'redirect'){
      window.location.href = data["redirectTo"];
    }
  }

}

//Matrix js functions

//Autoresize the matrix table
function dynamic_height(reduce_height) {
    var dyn_h = (($(window).height())-300-reduce_height);
 	$('#divfrozen').css({'height':dyn_h+'px'});
 	$('#contentscroll').css({'height':dyn_h+'px'});
}

//@TODO: move to jquery
function reposHead(e) 
{
    var h = document.getElementById('headscroll');
    h.scrollLeft = e.scrollLeft;
    var f = document.getElementById('divfrozen');
    f.scrollTop = e.scrollTop;
}

//@TODO: move to jquery
function reposHorizontal(e) {
    var h = document.getElementById('headscroll');
    var c = document.getElementById('contentscroll');
    h.scrollLeft = e.scrollLeft*50;
    c.scrollLeft = e.scrollLeft*50;
}

//@TODO: move to jquery
function reposVertical(e) {
    var h = document.getElementById('divfrozen');
    var c = document.getElementById('contentscroll');
    h.scrollTop = e.scrollTop*100;
    c.scrollTop = e.scrollTop*100;
}