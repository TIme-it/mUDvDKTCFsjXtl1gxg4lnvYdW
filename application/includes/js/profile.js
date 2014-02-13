(function($) {

	$(document).ready(function() {
		// -- обработчик на все формы авторизации
		$('.login_form').submit(function() {
			var data = {
				login:    $(this).find('input[name=login]').val(),
				pass:     $(this).find('input[name=pass]').val(),
				remember: $(this).find('input[name=remember]').attr('checked') ? 1 : 0
			};
			if(data.login == '' || data.pass == '') {
				alert('Необходимо указать логин и пароль');
			} else {
				var this_form   = $(this);
				var this_button = $(this).find('.submit');
				if(!this_form.data('is_create_loader')) {
					this_button.after('<span class="pb_ajax_proc">подождите...</span>');
					this_form.data('is_create_loader', true);
				}
				this_form.find('.pb_ajax_proc').show();
				this_button.attr('disabled', 'disabled');
				$.ajax({
					url:      '/profile/auth/',
					type:     'post',
					data:      data,
					dataType: 'json',
					cache:    false,
					success:  function(res) {
						switch(res.result) {
							case 0:
								this_form.find('.pb_ajax_proc').hide();
								alert('Неправильная пара логин/пароль');
								this_button.removeAttr('disabled');
								break;
							case 1:
								this_form.find('.pb_ajax_proc').hide();
								alert('Ваш аккаунт не подтвержден');
								this_button.removeAttr('disabled');
								break;
							case 2:
								location.href = res.href;
								break;
						}
					}
				});
			}
			return false;
		});
		
	});
	
})(jQuery);