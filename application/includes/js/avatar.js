(function($) {
	$(document).ready(function() {
		$('body').prepend('<div id="pic_editor"></div>');
		$('#uploadify_up').click(function() {
			if($('#fileQueue').text() == '') {
				alert('Нет файлов для загрузки');
			} else {
				$('#uploadify').uploadifyUpload();
			}
			return false;
		});
	})

	uploadifyInit = function(user_id, token) {
		$("#uploadify").uploadify({
			'uploader'       : '/application/includes/js/uploadify/uploadify.swf',
			'script'         : '/profile/change_avatar/',
			'cancelImg'      : '/application/includes/js/uploadify/cross.gif',
			'folder'         : '',
			'scriptData'     : {'user_id':user_id,'token':token},
			'queueID'        : 'fileQueue',
			'auto'           : false,
			'multi'          : false,
			'fileDesc'       : 'JPEG Image',
			'fileExt'        : '*.jpg;*.jpeg;',
			'displayData'    : 'percentage',
			'buttonImg'      : '/application/includes/js/uploadify/add_files.gif',
			'width'          : 134,
			'height'         : 24,
			'rollover'       : true,
			onSelect         : function(event, queueID, fileObj) {
				$('#uploadify_up').removeAttr('disabled');
			},
			onComplete       : function(event, queueID, fileObj, response, data) {
				// alert(response);
				if(response != 'error') {
					$('#shadow').css('height', $(document).height()+'px');
					$('#pic_editor').html(response);
					
					var elLeft = Math.round($(document).scrollLeft()+($(window).width()-600)/2)+'px';
					var elTop  = Math.round($(document).scrollTop()+($(window).height()-600)/2)+'px';
					$('select').css('visibility', 'hidden');
					$('#shadow').show();
					$('#pic_editor').css({'top':elTop,'left':elLeft}).show();
					$('#pic_editor').show();
					var elLeft = Math.round($(document).scrollLeft()+($(window).width()-$('#pic_editor').width())/2)+'px';
					var elTop  = Math.round($(document).scrollTop()+($(window).height()-$('#pic_editor').height())/2)+'px';
					$('#pic_editor').css({'top':elTop,'left':elLeft});
					pic_init();
				}
				return false;
			},
			onAllComplete    : function(event, uploadObj) {
				// location.href='?img_load='+uploadObj.filesUploaded;
			},
			onError          : function(event, ID, fileObj, errorObj) {
				alert('При загрузке файлов произошла ошибка');
			}
		});
	}
	
})(jQuery);