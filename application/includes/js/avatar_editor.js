var arc = 0;

(function($) {
	
	pic_rotate = function(deg) {
		arc += deg;
		if(arc ==  360 && arc == -360) arc = 0;
		var pe = $('#pic_editor');
		var pa = $('#pic_area');
		var pw = pe.width();
		var ph = pe.height();
		$('#pic_area_img').rotate(deg);
		$('#pic_shadow_img').rotate(deg);
		$('#pic_shadow,#pic_area_img,#pic_editor_img').css({'width':ph+'px','height':pw+'px'});
		pe.css({'width':ph+'px','height':pw+'px'});
		var pia_l = parseInt(pa.css('left'));
		var pia_t = parseInt(pa.css('top'));
		if(pia_l > ph-pa.width()) {
			pia_l = ph-pa.width()-2;
			pa.css('left', pia_l+'px');
		}
		if(pia_t > pw-pa.height()) {
			pia_t = pw-pa.height()-2;
			pa.css('top', pia_t+'px');
		}
		$('#pic_area_img').css({
			left: ((-1*pia_l)-1)+'px',
			top:  ((-1*pia_t)-1)+'px'
		});
		
		var elLeft = Math.round($(document).scrollLeft()+($(window).width()-$('#pic_editor').width())/2)+'px';
		var elTop  = Math.round($(document).scrollTop()+($(window).height()-$('#pic_editor').height())/2)+'px';
		$('#pic_editor').css({'top':elTop,'left':elLeft});
		return false;
	}
	
	pic_synch = function(o, ui) {
		var top  = ui.position.top;
		var left = ui.position.left;
		$('#pic_area_img').css({left:(-1*(left+1))+'px', top:(-1*(top+1))+'px'});
	}
	
	pic_init = function() {
		var al = new Image();
		al.src = '/images/ajax-loader.gif';
		$('#pic_area').resizable({
			containment: 'parent',
			aspectRatio: 1,
			minWidth:  100,
			minHeight: 100,
			stop: function(event, ui) {
				pic_synch(this, ui);
			}
		});
		$('#pic_area').draggable({
			containment: 'parent',
			drag: function(event, ui) {
				pic_synch(this, ui);
			},
			stop: function(event, ui) {
				pic_synch(this, ui);
			}
		});
	}
	
	pic_complete = function(user_id, token) {
		$('#fileQueue').html('<img src="/application/includes/images/ajax_loader_auth.gif" /> Идёт обработка изображения...');
		var pa = $('#pic_area');
		$.ajax({
			url:  '/profile/change_avatar_complete/',
			type: 'POST',
			dataType: 'json',
			data: {
				'step':    '2',
				'user_id': user_id,
				'token':   token,
				'arc':     arc,
				'w':       pa.css('width'),
				'h':       pa.css('height'),
				't':       pa.css('top'),
				'l':       pa.css('left')
			},
			cache:    false,
			success:  function(response) {
				if(response.result == 'ok') {
					$('#avatar_big img').attr('src',response.src);
					alert('Аватарка была успешно изменена');
				}
				$('#fileQueue').text('');
			}
		});
		$('#pic_editor').fadeOut();
		$('#shadow').hide();
		$('select').css('visibility', 'visible');
		return false;
	}
	
	close_win = function() {
		$('#pic_editor').fadeOut();
		$('#shadow').fadeOut();
	}
	
})(jQuery);