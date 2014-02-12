var bannedID = new Array();

(function($) {

	multi_upload_init = function(type, pid, mid, def_w, def_h) {
		def_w = def_w || 500;
		def_h = def_h || 320;
		
		var fileDesc = 'Image Files';
		var fileExt  = '*.jpg;*.jpeg;*.gif;*.png';
		switch(type) {
			case 'videos': fileDesc = 'Video Files'; fileExt  = '*.avi;*.flv;*.mpeg;*.mov;*.3gp;*.mpg'; break;
			case 'files':  fileDesc = 'All Files';   fileExt  = '*.*'; break;
			default:       fileDesc = 'Image Files'; fileExt  = '*.jpg;*.jpeg;*.gif;*.png'; break;
		}
		if(type == 'files') {
			
			
		}
		$("#uploadify_"+type).uploadify({
			'uploader'       : '/admin/application/includes/js/uploadify/uploadify.swf',
			'script'         : '/admin/application/models/upload.php',
			'cancelImg'      : '/admin/application/includes/images/cancel.gif',
			'folder'         : 'uploads',
			'scriptData'     : {'type':type,'pid':pid,'mid':mid,'resize-w':def_w,'resize-h':def_h},
			'queueID'        : 'fileQueue_'+type,
			'auto'           : false,
			'multi'          : true,
			'fileDesc'       : fileDesc,
			'fileExt'        : fileExt,
			'displayData'    : 'percentage',
			'buttonImg'      : '/admin/application/includes/images/add_files.gif',
			'width'          : 134,
			'height'         : 24,
			'rollover'       : true,
			onSelect         : function(event, queueID, fileObj) {
				if(fileObj.size == 0) {
					alert('Файл '+fileObj.name+' не может быть загружен, т.к. его размер 0 байт');
					$(this).uploadifyCancel(queueID);
				}
				if(fileObj.size > 1024*1024*3) {
					alert('Файл '+fileObj.name+' не может быть загружен, т.к. его размер ('+(Math.round(fileObj.size/1024/1024*100)/100)+'Мб) превышает 3Мб');
					$(this).uploadifyCancel(queueID);
				}
				$('#uploadify_'+type+'_button').removeAttr('disabled');
			},
			onComplete       : function(event, queueID, fileObj, response, data) {
				$('#'+type+'_list').html(response);
			},
			onAllComplete    : function(event, uploadObj) {
				alert('Загрузка файлов завершена успешно');
				count = $('#'+type+'_list li').length;
				$('#'+type+'_list_count').html(count > 0 ? '(Добавлено <i>'+count+'</i> '+(type == 'files'?'файлов':'фото')+')' : '');
				switch(type) {
					case 'photos': $.getScript('/admin/application/includes/js/tiny/lists/image_list.js?'+Math.random()); break;
					default:       addSortable(type, pid, true); break;
				}
			},
			onError          : function(event, ID, fileObj, errorObj) {
				if(fileObj.size > 0) {
					alert('При загрузке файла '+fileObj.name+' произошла ошибка');
				}
			}
		});
	}
	
	multi_upload_press = function(type) {
		if($('#fileQueue_'+type).text() == '') {
			alert('Нет файлов для загрузки');
		} else {
			$('#uploadify_'+type+'_button').attr('disabled', 'disabled');
			$('#uploadify_'+type).uploadifyUpload();
		}
	}
	
	multi_upload_panel = function(type, act, id) {
		switch(act) {
			case 'wc':
			case 'wcc':
				$.ajax({
					url:   '/admin/application/multiupload/'+type+'/'+act+'/'+id+'/',
					type:  'GET',
					cache: false,
					success: function(response) {
						if(response != '') {
							$('#mu_point_'+type+'_'+id+' .browse').html(response);
						} else {
							alert('При повороте изображения произошла ошибка');
						}
					}
				});
				break;
			case 'del':
				if(!confirm('Вы действительно хотите удалить это изображение?')) return false;
				$('#mu_point_'+type+'_'+id).load('/admin/application/multiupload/'+type+'/del/'+id+'/');
				$('#mu_point_'+type+'_'+id).remove();
				count = $('#'+type+'_list li').length;
				$('#'+type+'_list_count').html(count > 0 ? '(Добавлено <i>'+count+'</i> фото)' : '');
				
				if (type=='photos') {
					$.getScript('/admin/application/includes/js/tiny/lists/image_list.js?'+Math.random());
				}
				break;
			case 'note':
				$.ajax({
					url:   '/admin/application/getnote/'+type+'/'+id+'/',
					type:  'GET',
					cache: false,
					success: function(response) {
						var note = prompt('Редактировать подпись к изображению', response);
						if(note || note == '') {
							$.ajax({
								url:   '/admin/application/setnote/'+type+'/'+id+'/',
								type:  'GET',
								data:  {'note': note},
								cache: false,
								success: function(response) { 

									//Заменяем подпись на картинках в тексте
									var iFrameDOM;
									$('.mceIframeContainer').each(function() {
										iFrameDOM = $('.mceIframeContainer iframe').contents();
										iFrameDOM.find('img').each(function() {
											var tmp =  /([^\/]+)$/;
											var src = $(this).attr('src');
											file = tmp.exec(src);
											file = file[0];
											file = file.split('?');
											
											if ( file && file[0] == id+'.jpg') {
												$(this).attr('title',note);
												$(this).attr('alt',note);
											}
										});
									});
								
									if (type=='photos') {
										$.getScript('/admin/application/includes/js/tiny/lists/image_list.js?'+Math.random());
									}
								}
							});
							$('#mu_point_'+type+'_'+id+' div.note').text(note);
						}
					}
				});
				break;
		}
		return false;
	}
	
})(jQuery);