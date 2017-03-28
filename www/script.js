$('document').ready(function(){
	$('input[type="text"], input[type="email"], textarea').focus(function(){
		var background = $(this).attr('id');
		$('#' + background + '-form').addClass('formgroup-active');
		$('#' + background + '-form').removeClass('formgroup-error');
	});
	$('input[type="text"], input[type="email"], textarea').blur(function(){
		var background = $(this).attr('id');
		$('#' + background + '-form').removeClass('formgroup-active');
	});

	var $message = $('#message');

$("#waterform").on('submit', function(e) {
	e.preventDefault();
	var ids = $.trim($message.val()).replace(/(?:\n|\s)/g, ',').replace(/,+/g, ',').split(',');
	ids = ids.filter(function(value){
		value = parseInt(value);
		return value > 0;
	});
	if(!ids.length) {
		$("#message-form").addClass('formgroup-error');
		return;
	}
	var data = {ids: ids};

	$message.val('Проверка...');
	$.ajax({
	  type: "POST",
	  url: 'check.php',
	  data: data,
		dataType: 'json',
	  success: function(resp){
			console.log(resp);
			var text = '';
			if(resp.success == 1) {
				for(var sapeID in resp.ids) {
					text += sapeID + ' > ' + (resp.ids[sapeID] == 1 ? 'Основная' : 'Сомнительная') + "\n";
				}
				$message.val(text);
			} else {
				$message.val(resp.error);
			}
		},
		fail: function(){
			alert('AJAX ERROR');
		}
	});

});

});
