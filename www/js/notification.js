var myMessages = ['info','warning','error','success']; // define the messages types		

function showMessage(type, duration, message)
{
  $(function(){
		$.pnotify({
			type: type,
			text: message
		});
	});
}