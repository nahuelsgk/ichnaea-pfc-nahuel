var Season = function(name, notes){
  this.name  = name;
  this.notes = notes;
}
var Request = function(uri, operation, params){
	this.request = {
		uri    : uri,
		op     : operation,
	    params : params
    };
    this.send = function(successFunction){
		send_event3(this.request, successFunction);
    } 
}

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
