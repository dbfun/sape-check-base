"use strict";
$('document').ready(function(){

	var $message = $('#message'),
			$recaptcha = $('meta[name="recaptcha"]');
	var isRecaptcha = $recaptcha.length != 0,
			recaptchaKey = isRecaptcha ? $recaptcha.attr('content') : null,
			recaptchaTested = false,
			recaptchaWidgetId,
			data = {},
			baseCases = {"-1": "Сомнительная", "1": "Основная", "0": "ID не найден"};

	ui();

	function ui() {
		$('input[type="text"], input[type="email"], textarea').focus(function(){
			var background = $(this).attr('id');
			$('#' + background + '-form').addClass('formgroup-active');
			$('#' + background + '-form').removeClass('formgroup-error');
		});
		$('input[type="text"], input[type="email"], textarea').blur(function(){
			var background = $(this).attr('id');
			$('#' + background + '-form').removeClass('formgroup-active');
		});
		$("#waterform").on('submit', function(e) {
			e.preventDefault();
			submitForm();
		});
	}

	function submitForm() {
		if(isRecaptcha && !recaptchaTested) {
			alert('Введите каптчу!');
			return;
		}

		var ids = $.trim($message.val()).replace(/(?:\n|\s)/g, ',').replace(/,+/g, ',').split(',');
		ids = ids.filter(function(value){
			value = parseInt(value);
			return value > 0;
		});
		if(!ids.length) {
			$("#message-form").addClass('formgroup-error');
			return;
		}
		data.ids = ids;

		$message.val('Проверка...');

		$.ajax({
			type: "POST",
			url: 'check.php',
			data: data,
			dataType: 'json',
			success: function(resp){
				var text = '';
				if(resp.success == 1) {
					for(var sapeID in resp.ids) {
						text += sapeID + ' > ' + baseCases[resp.ids[sapeID]] + "\n";
					}
					$message.val(text);
				} else {
					$message.val(resp.error);
				}
			},
			fail: function(){
				alert('AJAX ERROR');
			},
			complete: function() {
				if(isRecaptcha) $('body').trigger('recaptcha-reload', {});
			}
		});
	}

	$('body').on('recaptcha-init', function() {
			recaptchaWidgetId = grecaptcha.render('g-recaptcha', {
				'sitekey' : recaptchaKey,
				'callback' : function(resp){
					data.recaptcha = resp;
					recaptchaTested = true;
				},
				'expired-callback': function() {
					$('body').trigger('recaptcha-reload', {});
				}
			});
	});

	$('body').on('recaptcha-reload', function() {
		data.recaptcha = '';
		recaptchaTested = false;
		grecaptcha.reset(recaptchaWidgetId);
	});

});

function initRecaptcha() {
	$('#g-recaptcha').css({"margin": "30px 0", "min-height": "78px" });
	$('body').trigger('recaptcha-init', {});
}
