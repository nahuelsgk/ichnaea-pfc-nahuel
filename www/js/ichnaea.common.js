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
			showMessage('success', 1500, data.msg);
		},
		error: function(data, type){
			  if(type==='timeout') {
				  showMessage('error', 10000, "Connection problems. Can't send request")
			  }
			  else{
				  showMessage('error', 10000, console.log(data.msg));
		      }
			  
		}
	});
};

function send_event3(request_obj, callback){
  console.log("Sent event v3.0");
  var request = $(request_obj);
  var uri = request.attr("uri");
  var op = request.attr("op");
  
  console.log(JSON.stringify(request_obj));
  $.ajax({
    type:     'POST',
    dataType: 'json',
    url:	'/api/project', 
    data:     JSON.stringify(request_obj),
    processData: false,
    success:  function(data){
      if (typeof callback != 'undefined') callback(data);
      if(data.status == "OK"){
        showMessage('Success', 1500, null);
      }
      else if (data.status == "KO"){
	var message_string = data.default_message;
        showMessage('Warning', 5000, message_string);
      }
    },
    error: function(data){
      showMessage('Error', 10000, JSON.stringify(data));
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
