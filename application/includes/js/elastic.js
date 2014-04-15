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
		if (width >= 1800) {
			$('#menu .main_menu').css('margin', '0 10px');
			$('#menu .main_menu a.m_it').css('padding', '0 10px');
			return false;
		}
		if (width <= 1000) {
			$('#menu .main_menu').css('margin', '0 0px');
			$('#menu .main_menu a.m_it').css('padding', '0 7px');
			return false;
		}
		if (width > 1000){
			diff = width - 1800;
			m = (diff/80) + 10;
			p = (diff/266.6666) + 10;
			$('#menu .main_menu').css('margin', '0 '+m+'px');
			$('#menu .main_menu a.m_it').css('padding', '0 '+p+'px');
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
			// $('#news_block .scroll-pane').css('width', '1182px');
			$('#news_block .scroll-pane .item').css('width', '310px');
			$('#news_block .scroll-pane .item').css('margin-right', '84px');
			return false;
		}
		if (width <= 1000) {
			// $('#news_block .scroll-pane').css('width', '490px');
			$('#news_block .scroll-pane .item').css('width', '145px');
			$('#news_block .scroll-pane .item').css('margin-right', '20px');
			return false;
		}
		if (width > 1000){
			diff = width - 1800;
			sp_w = diff/1.1561 + 1182;
			spi_w = diff/4.848484 + 310;
			spi_mr = diff/12.5 + 84;

			// $('#news_block .scroll-pane').css('width', sp_w+'px');
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
			$('#main_slider .slider_bg').css('left', '49%');
			// $('#main_slider ul li').css('margin', '0 5%');
			return false;
		}
		if (width <= 1000) {
			$('#main_slider .slider_bg').css('left', '24%');
			// $('#main_slider ul li').css('margin', '0 0');
			return false;
		}
		if (width > 1000){
			diff = width - 1800;
			msb_l = diff/32 + 49;
			muli_m = diff/160 + 5;

			$('#main_slider .slider_bg').css('left', msb_l+'%');
			// $('#main_slider ul li').css('margin', '0 '+muli_m+'%');
			return false;
		}

		if(width <= 1359){
            $('.jcarousel ul').css('left', -t_width+'px')
        }
	}

	$(document).ready(function() {

		/* QUESTION BLOCK BEGIN */ 

		$('#interview_block .radiobuttons input[type="radio"]').on('change', function(){
			$('#interview_block .answer_button').removeClass('inactive');
			$('#interview_block .answer_button').addClass('active');
			$('#interview_block .answer_button').css('top', 25*($(this).data('count')-1)+'px');



			 console.log($(this));
		})

		/* QUESTION BLOCK END */ 
		$('.test_fon').css('height', $('.questions ul').height()+100);

		resizable_menu($('.middle_wrap').width());
		resizable_middle($('body').width());
		resizable_news_block($('.middle_wrap').width());
		resizable_reviews_block($('.middle_wrap').width());
		resizable_slider_block($('.middle_wrap').width());
			
		$(window).resize(function(){
			$('.test_fon').css('height', $('.questions ul').height()+100);
			resizable_menu($('.middle_wrap').width());
			resizable_middle($('body').width());
			resizable_news_block($('.middle_wrap').width());
			resizable_reviews_block($('.middle_wrap').width());
			resizable_slider_block($('.middle_wrap').width());
		});
})

})(jQuery);
