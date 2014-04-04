(function($) {

	$('#tumbler_subscribe').on('click', function(){
		var flg	=	"off";		
		if ($(this).hasClass('on')) 
			flg = 'on';		
		$(this).removeClass('on off');		
		$('.subscription .subs_img').removeClass('on off');		
		if (flg == 'off') {
			$(this).addClass('on');
			$('.subscription .subs_img').addClass('on');
			$('#subscribe').val(1);
			$('.subscription .subs_txt').text('Подписка активирована');
		} else {
			$(this).addClass('off');
			$('.subscription .subs_img').addClass('off');
			$('#subscribe').val(0);	
			$('.subscription .subs_txt').text('Подписка отменена');
		}						
	})	
	
	$('.upload_button.avatar').on('click', function(){
		$('#avatar_file').click();
	});	

	$(document).ready(function() {

		faq_user_ajax = function(page, pid) {	
			$.ajax({
				url:  '/popup/faq/faq_user_ajax/',
				type: 'POST',
				dataType : 'JSON',
				data: {
					'page':page,
					'pid':pid
				},

				success:  function(result) {
					$('.questions').html(result);
					$('.test_fon').css('height', $('.questions ul').height()+100);
				}
			});
			return false;
		}	
	
		// -- обработчик на все формы авторизации
		// $('.login_form').submit(function() {
		// 	var data = {
		// 		login:    $(this).find('input[name=login]').val(),
		// 		pass:     $(this).find('input[name=pass]').val(),
		// 		remember: $(this).find('input[name=remember]').attr('checked') ? 1 : 0
		// 	};
		// 	if(data.login == '' || data.pass == '') {
		// 		alert('Необходимо указать логин и пароль');
		// 	} else {
		// 		var this_form   = $(this);
		// 		var this_button = $(this).find('.submit');
		// 		if(!this_form.data('is_create_loader')) {
		// 			this_button.after('<span class="pb_ajax_proc">подождите...</span>');
		// 			this_form.data('is_create_loader', true);
		// 		}
		// 		this_form.find('.pb_ajax_proc').show();
		// 		this_button.attr('disabled', 'disabled');
		// 		$.ajax({
		// 			url:      '/profile/auth/',
		// 			type:     'post',
		// 			data:      data,
		// 			dataType: 'json',
		// 			cache:    false,
		// 			success:  function(res) {
		// 				switch(res.result) {
		// 					case 0:
		// 						this_form.find('.pb_ajax_proc').hide();
		// 						alert('Неправильная пара логин/пароль');
		// 						this_button.removeAttr('disabled');
		// 						break;
		// 					case 1:
		// 						this_form.find('.pb_ajax_proc').hide();
		// 						alert('Ваш аккаунт не подтвержден');
		// 						this_button.removeAttr('disabled');
		// 						break;
		// 					case 2:
		// 						location.href = res.href;
		// 						break;
		// 				}
		// 			}
		// 		});
		// 	}
		// 	return false;
		// });
		
	});
	
})(jQuery);
