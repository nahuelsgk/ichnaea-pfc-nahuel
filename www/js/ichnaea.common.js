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
  alert(JSON.stringify(data));
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

function decode_result_event(data){
  alert(JSON.stringify(data));
  if(data["result"] == '1'){
    if(data["operation"] == 'redirect'){
      window.location.href = data["redirectTo"];
    }
  }
}
