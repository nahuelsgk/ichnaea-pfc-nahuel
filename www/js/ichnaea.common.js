$(function() {
$('#help_content').draggable();  
});

$(function() {
$('#help_trigger').click(function(){$('#help_content').toggle()});
});


function send_event(path, func, vals){
  var data = {
    "ajaxDispatch": path,
    "function": "dispatch_"+func,
    "values" : vals,
  };
  $.ajax({
    type:     'POST',
    dataType: 'json',
    data:     JSON.stringify(data),
    processData: false,
    success:  function(data){
      decode_result_event(data);
    },
    error: function(data){
      for(var key in data) {
        $('#msgid').append(key);
        $('#msgid').append('=' + data[key] + '<br />');
      }
      alert("KO");
    }

  });
};

/*More generic than first. Will attemp to call an api
 - obj: has all info necessary for send the event. "operation"
* Last update: 11 march 2013
*/
function send_event2(obj, path){
  alert("Sent event v2.0");
  var values = $(obj);
  var overridden = values.attr("operation");
 
  var data = {
  "ajaxDispatch": "ASYNC_API",
  "query" : overridden,
  };

  $.ajax({
    type:     'POST',
    dataType: 'json',
    data:     JSON.stringify(data),
    processData: false,
    success:  function(data){
      decode_result_event(data);
    },
    error: function(data){
      for(var key in data) {
        $('#msgid').append(key);
        $('#msgid').append('=' + data[key] + '<br />');
      }
      alert("KO");
    }

  });

}

function decode_result_event(data){
  alert(JSON.stringify(data));
  if(data["result"] == '1'){
    if(data["operation"] == 'redirect'){
      window.location.href = data["redirectTo"];
    }
  }
}
