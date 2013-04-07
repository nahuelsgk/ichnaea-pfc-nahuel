var myMessages = ['info','warning','error','success']; // define the messages types		

function showMessage(type, duration, message)
{
  $(function(){
		$.pnotify({
			title: type,
			text: message
		});
	});
}