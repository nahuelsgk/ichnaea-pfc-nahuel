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
				  //showMessage('error', 10000, "Connection problems. Can't send request")
			  }
			  else{
				  //showMessage('error', 10000, console.log(data.msg));
		      }
			  
		}
	});
};
function confirmMessage($message, $continueAction){
	  $('<div></div>').appendTo('body')
	  .html('<div><h6>'+$message+'</h6></div>')
	  .dialog({
	      modal: true, title: 'message', zIndex: 10000, autoOpen: true,
	      width: 'auto', resizable: false,
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

function decode_result_event(data){
  if(data["result"] == '1'){
    if(data["operation"] == 'redirect'){
      window.location.href = data["redirectTo"];
    }
  }

}
