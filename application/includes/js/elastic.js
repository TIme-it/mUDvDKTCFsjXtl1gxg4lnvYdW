(function($) {

	// Проверка на существование элемента
	jQuery.fn.exists = function() {
	   return $(this).length;
	}

	// экспериментальная функция
	uni_elastic = function(current_w, max_w, min_w, selector){
		if (current_w >= max_w) {
			$(selector).css('padding-right', 'auto');
			return false;
		}
		if (current_w <= min_w) {
			$(selector).css('padding-right', '-200px');
			return false;
		}
		if (current_w > min_w){
			diff = current_w - max_w;
			right = diff/3;
			$(selector).css('padding-right', right);
			return false;
		}
	}

	// МЕНЮ
	resizable_menu = function(width){
		if (width >= 1600) {
			$('#menu a.m_it').css('padding', '0 31px');
			$('.fon').css('width', '775px');
			// $('.submenu').css('width', '775px');
			// if (width == 1600) {
			// 	$('.submenu').css('left', '-1px');
			// }
			return false;
		}
		if (width <= 1000) {
			$('#menu a.m_it').css('padding', '0 14px');
			$('.fon').css('width', '570px');
			// $('.submenu').css('width', '570px');
			return false;
		}
		if (width > 1000){
			diff = width - 1000;
			tmp = (diff/35.3) + 14;
			right = '0 ' + tmp +'px';
			fon = (diff/2.9238) + 570;
			$('#menu a.m_it').css('padding', right);
			$('.fon').css('width', fon);
			// $('.submenu').css('width', fon);
			// $('.submenu').css('left', '0');
			return false;
		}
	}
	
	// СЛОЙ ЦЕНТРОВКИ
	resizable_middle = function(width){
		if (width >= 1600) {
			$('.middle_wrap').css('padding-right', 'auto');
			return false;
		}
		if (width <= 1000) {
			$('.middle_wrap').css('padding-right', '-200px');
			return false;
		}
		if (width > 1000){

			diff = width - 1600;
			right = diff/3;
			$('.middle_wrap').css('padding-right', right);
			return false;
		}
	}

	// НОВОСТИ НА ГЛАВНОЙ
	resizable_news_block = function(width){
		if (width >= 1800) {
			$('#news_block .scroll-pane').css('width', '1182px');
			$('#news_block .scroll-pane .item').css('width', '310px');
			$('#news_block .scroll-pane .item').css('margin-right', '84px');
			return false;
		}
		if (width <= 1000) {
			$('#news_block .scroll-pane').css('width', '490px');
			$('#news_block .scroll-pane .item').css('width', '140px');
			$('#news_block .scroll-pane .item').css('margin-right', '20px');
			return false;
		}
		if (width > 1000){
			diff = width - 1800;
			sp_w = diff/1.1561 + 1182;
			spi_w = diff/4.70588 + 310;
			spi_mr = diff/12.5 + 84;

			$('#news_block .scroll-pane').css('width', sp_w+'px');
			$('#news_block .scroll-pane .item').css('width', spi_w+'px');
			$('#news_block .scroll-pane .item').css('margin-right', spi_mr+'px');
			return false;
		}
	}

	// ОТЗЫВЫ НА ГЛАВНОЙ
	resizable_reviews_block = function(width){
		if (width >= 1800) {
			$('#reviews_block ul').css('width', '1725px');
			$('#reviews_block li').css('width', '425px');
			$('#reviews_block li').css('margin', '0 75px');

			return false;
		}
		if (width <= 1000) {
			$('#reviews_block ul').css('width', '920px');
			$('#reviews_block li').css('width', '225px');
			$('#reviews_block li').css('margin', '0 35px');
			return false;
		}
		if (width > 1000){
			diff = width - 1800;
			rbul_w = diff/0.99379 + 1725;
			rbli_w = diff/4 + 425;
			rbli_m = diff/20 + 75;

			$('#reviews_block ul').css('width', rbul_w+'px');
			$('#reviews_block li').css('width', rbli_w+'px');
			$('#reviews_block li').css('margin', '0 '+rbli_m+'px');
			return false;
		}
	}


	// СЛАЙДЕР НА ГЛАВНОЙ
	resizable_slider_block = function(width){
		if (width >= 1800) {
			$('#main_slider .slider_bg').css('left', '43%');
			$('#main_slider ul li').css('margin', '0 5%');
			return false;
		}
		if (width <= 1000) {
			$('#main_slider .slider_bg').css('left', '20.5%');
			$('#main_slider ul li').css('margin', '0 0');
			return false;
		}
		if (width > 1000){
			diff = width - 1800;
			msb_l = diff/35.55556 + 43;
			muli_m = diff/160 + 5;

			$('#main_slider .slider_bg').css('left', msb_l+'%');
			$('#main_slider ul li').css('margin', '0 '+muli_m+'%');
			return false;
		}
	}


	showimage_init = function(){
		$('#main_slider .images img').each(function(){
			if(!$(this).parent('.images').hasClass('hide')){
				switch ($(this).data('pos')) {
				   case 1:
				      $(this).css('top', '55px');
				      $(this).css('right', '56px');
				      break;
				   case 2:
				      $(this).css('top', '-60px');
				      $(this).css('right', '143px');
				      break;
				   case 3:
				      $(this).css('top', '140px');
				      $(this).css('right', '165px');
				      break;
				   default:
				      break;
				}
			}
		})
	}

	hideimage_init = function(){
		$('#main_slider .images.hide img').each(function(){
			$(this).css('display', 'none');
			$(this).css('opacity', '0');

			switch ($(this).data('pos')) {
			   case 1:
			      $(this).css('top', '55px');
			      $(this).css('right', '-250px');
			      break;
			   case 2:
			      $(this).css('top', '-250px');
			      $(this).css('right', '143px');
			      break;
			   case 3:
			      $(this).css('top', '350px');
			      $(this).css('right', '165px');
			      break;
			   default:
			      break;
			}
		})
		$('#main_slider .left_arrow').addClass('active');
		$('#main_slider .left_arrow').removeClass('inactive');
		$('#main_slider .right_arrow').addClass('active');
		$('#main_slider .right_arrow').removeClass('inactive');
	}

	slide_animation = function(){
		$('#main_slider .images img').each(function(){
			if(!$(this).parent('.images').hasClass('hide')){
				switch ($(this).data('pos')) {
				   case 1:
				   	  $(this).animate({
						  opacity: 0,
						  top: '-155px', 
						  right: '-156px',
					  }, 1000);
				      break;
				   case 2:
				      $(this).animate({
						  opacity: 0,
						  top: '-260px', 
						  right: '343px',
					  }, 1000);
				      break;
				   case 3:
				      $(this).animate({
						  opacity: 0,
						  top: '340px', 
						  right: '165px',
					  }, 1000);
				      break;
				   default:
				      break;
				}
			}
			if($(this).parent('.images').hasClass('hide')){
				hide_obj = $(this).parent('.images');
			}
			else{
				show_obj = $(this).parent('.images');
			}
		})

		$('#main_slider .images.hide img').each(function(){
			$(this).css('display', 'block');

			switch ($(this).data('pos')) {
			   case 1:
			   	  $(this).animate({
					  opacity: 1,
					  top: '55px', 
					  right: '56px',
				  }, 1000);
			      break;
			   case 2:
			      $(this).animate({
					  opacity: 1,
					  top: '-60px', 
					  right: '143px',
				  }, 1000);
			      break;
			   case 3:
			      $(this).animate({
					  opacity: 1,
					  top: '140px', 
					  right: '165px',
				  }, 1000);
			      break;
			   default:
			      break;
			}
			if($(this).parent('.images').hasClass('hide')){
				hide_obj = $(this).parent('.images');
			}
			else{
				show_obj = $(this).parent('.images');
			}
		})
	}
	
	$(document).ready(function() {

		/* SLIDER BLOCK BEGIN */
		showimage_init();
		hideimage_init();
		
		// Правая кнопка слайдера

		$('#main_slider .right_arrow').on('click', function(){
			if($(this).hasClass('active')){
				slide_animation();

				hide_obj.removeClass('hide');
				show_obj.addClass('hide');

				$('#main_slider .left_arrow').removeClass('active');
				$('#main_slider .left_arrow').addClass('inactive');
				$('#main_slider .right_arrow').removeClass('active');
				$('#main_slider .right_arrow').addClass('inactive');

				setTimeout(showimage_init, 1100);
				setTimeout(hideimage_init, 1100);
			}
			return false;
		})

		/* SLIDER BLOCK END */
 
		// Отсчитывает отступ от подменю
		$('#menu .submenu').each(function(){
			if($(this).css('display') == 'block'){
				var height = $(this).height() / 2;
				var newMargin = parseInt($('.middle_wrap .middle').css('margin-top')) + height;
				$('.middle_wrap .middle').css('margin-top', newMargin);
				return false;
			}
		})

		// resizable_menu($('.middle_wrap').width());
		resizable_middle($('body').width());
		resizable_news_block($('.middle_wrap').width());
		resizable_reviews_block($('.middle_wrap').width());
		resizable_slider_block($('.middle_wrap').width());
	
		$(window).resize(function(){
			// resizable_menu($('.middle_wrap').width());
			resizable_middle($('body').width());
			resizable_news_block($('.middle_wrap').width());
			resizable_reviews_block($('.middle_wrap').width());
			resizable_slider_block($('.middle_wrap').width());
		});
})

})(jQuery);
