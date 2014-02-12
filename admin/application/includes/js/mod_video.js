(function($) {
	
	$(document).ready(function() {
		$('#videos_form').submit(function() {
			$('#video_msg').text('Идёт Загрузка. Видео может загружаться в течение нескольких минут.');
			$('#yt_button').attr('disabled', 'disabled');
			return true;
		});
	});
	
	check_video = function(back_url) {
		var title = $('#ytube_title').val();
		var desc  = $('#ytube_desc').val();
		var cat   = $('#ytube_cat').val();
		var pid   = $('#page_id').val();
		var mid   = $('#module_id').val();
		if(title == '' || desc == '' || cat == '') {
			alert('Необходимо заполнить все поля');
			return false;
		}
		$('#video_msg').text('Пожалуйста, подождите, идёт подключение к YouTube...');
		$('.video_dis').attr('disabled', 'disabled');
		$.ajax({
			url: '/admin/video/get_token/',
			type: 'POST',
			dataType: 'json',
			data: {'title':title,'desc':desc,'cat':cat,'pid':pid,'mid':mid},
			cache: false,
			success: function(res) {
				switch(res.result) {
					case 'error':
						$('#video_msg').text('Произошла ошибка, возможно сервер перегружен...');
						$('.video_dis').removeAttr('disabled');
						break;
					case 'ok':
						$('#video_msg').text('Подключение прошло успешно');
						$('#video_upload .video_dis').removeAttr('disabled');
						$('#video_token').val(res.token);
						$('#videos_form').attr('action', res.url+'?nexturl='+back_url+'?uid='+res.id);
						$('#video_upload').fadeIn();
						break;
				}
				
			}
		});
	}
	
	video_delete = function(id) {
		if(!confirm('Вы уверены что хотите удалить этот файл?')) return false;
		$.ajax({
			url:   '/admin/video/delete/'+id+'/',
			type:  'GET',
			cache: false,
			success: function(response) { }
		});
		$('#video_'+id).remove();
		var count = $('#video_list li').length;
		$('#videos_list_count').html(count > 0 ? '(Добавлено <i>'+count+'</i> файлов)' : '');
		return false;
	}
	
})(jQuery);