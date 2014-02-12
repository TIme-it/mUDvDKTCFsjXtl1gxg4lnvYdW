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

	resizable_container = function(width){
		if (width >= 1600) {
			$('.page.container').css('width', '860px');
			$('.filesList .one_files').css('width', '231px');
			return false;
		}
		if (width <= 1000) {
			$('.page.container').css('width', '540px');
			$('.filesList .one_files').css('width', '215px');
			return false;
		}
		if (width > 1000){
			diff = width - 1600;
			cntr_w = diff/1.875 + 860;
			onef_m = diff/37.5 + 231;
			$('.page.container').css('width', cntr_w);
			$('.filesList .one_files').css('width', onef_m);
			return false;
		}
	}
	
	resizable_most_popular = function(width){
		if (width >= 1600) {
			$('.mp_item').css('width', '625');
			$('.mp_item').css('margin', '0px 45px 70px 65px');
			$('.mp_item .item_descript').css('width', '419');
			return false;
		}
		if (width <= 1000) {
			$('.mp_item').css('width', '429');
			$('.mp_item').css('margin', '0px 18px 150px 25px');
			$('.mp_item .item_descript').css('width', '223');
			return false;
		}
		if (width > 1000){
			diff = width - 1600;
			mpi_w = diff/3.06 + 625;
			mpid_w = diff/3.06 + 419;
			mpi_ml = diff/15 + 65;
			mpi_mr = diff/22.2 + 45;
			mpi_mb = -diff/7.5 + 70;
			$('.mp_item').css('width', mpi_w);
			$('.mp_item').css('margin', '0px '+mpi_mr+'px '+mpi_mb+'px '+mpi_ml+'px');
			$('.mp_item .item_descript').css('width', mpid_w);
			return false;
		}
	}
	
	resizable_popular = function(width){
		if (width >= 1600) {
			$('.popular ul').css('width', '1250px');
			$('.popular ul li.p_item').css('margin-right', '100px');
			// $('.popular ul').css('padding-left', '0px');
		}
		if (width <= 1000) {
			$('.popular ul').css('width', '635px');
			// $('.popular ul li.p_item').css('margin-right', '3px');
			// $('.popular ul').css('padding-left', '20px');
		}
		if (width > 1000){
			diff = width - 1600;
			pul_w = diff/0.9756 + 1250;
			pli_mr = diff/6.1855 + 100;
			pli_pl = -diff/30;

			$('.popular ul').css('width', pul_w);
			// $('.popular ul li.p_item').css('margin-right', pli_mr+'px');
			// $('.popular ul').css('padding-left', pli_pl+'px');
		}

		if(($('.popular ul').width() - ($('.popular ul li.p_item').outerWidth(true) * 3)) < 0){
			$('.popular ul li.right').css('display', 'none');
		}
		else {
			$('.popular ul li.right').css('display', 'block');
		}

		if($('.popular ul li:visible').size() == 4){
			e_place = $('.popular ul').width() - ($('.popular ul li.p_item').width() * 2)
			$('.popular ul li.p_item').css('margin-right', e_place/2.2+'px');
		}
		return false;
	}

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

	resizable_feedback = function(width){
		if (width >= 1600) {
			$('#ask_question_form').css('width', '780px');
			$('#ask_question_form').css('padding-right', '40px');
			$('#ask_question_form').css('padding-left', '40px');
			$('#ask_question_form fieldset.field_capcha').css('margin-left', '138px');
			$('#ask_question_form .left input, #ask_question_form .right input').css('width', '370px');
			$('#ask_question_form #fio, #ask_question_form #question').css('width', '768px');
			return false;
		}
		if (width <= 1000) {
			$('#ask_question_form').css('width', '500px');
			$('#ask_question_form').css('padding-right', '20px');
			$('#ask_question_form').css('padding-left', '20px');
			$('#ask_question_form fieldset.field_capcha').css('margin-left', '0px');
			$('#ask_question_form .left input, #ask_question_form .right input').css('width', '230px');
			$('#ask_question_form #fio, #ask_question_form #question').css('width', '488px');
			return false;
		}
		if (width > 1000){
			diff = width - 1600;
			aqf_w = diff/2.1428 + 780;
			aqf_pr_pl = diff/30 + 40;
			aqf_ffc = diff/4.3478 + 138;
			aqf_lilr = diff/4.2857 + 370;
			aqf_in = diff/2.1428 + 768;
			$('#ask_question_form').css('width', aqf_w);
			$('#ask_question_form').css('padding-right', aqf_pr_pl);
			$('#ask_question_form').css('padding-left', aqf_pr_pl);
			$('#ask_question_form fieldset.field_capcha').css('margin-left', aqf_ffc);
			$('#ask_question_form .left input, #ask_question_form .right input').css('width', aqf_lilr);
			$('#ask_question_form #fio, #ask_question_form #question').css('width', aqf_in);
			return false;
		}
	}

	resizable_categories = function(width){
		if (width >= 1440) {
			$('#category_block').css('width', '100%');
			$('#category_block').css('margin', '0');
			return false;
		}
		if (width <= 1439) {
			$('#category_block').css('width', '1000px');
			$('#category_block').css('margin', '0 auto');
			return false;
		}
	}

	resizable_product_list = function(width){
		if (width >= 1600) {
			$('#product_block').css('margin-left', '0px');
			$('#product_block .right_side').css('width', '910px');
			$('#product_block .product_info').css('height', '230px');
			return false;
		}
		if (width <= 1000) {
			$('#product_block').css('margin-left', '70px');
			$('#product_block .right_side').css('width', '410px');
			$('#product_block .product_info').css('height', '290px');
			return false;
		}
		if (width > 1000){
			diff = width - 1600;
			pb_ml = -diff/8.571;
			pbrs_w = diff/1.2 + 910;
			pbpi_h = -diff/10 + 230;
			$('#product_block').css('margin-left', pb_ml);
			$('#product_block .right_side').css('width', pbrs_w);
			$('#product_block .product_info').css('height', pbpi_h);
			return false;
		}
	}

	resizable_product_item = function(width){
		if (width >= 1600) {
			$('#product_top .container').css('margin-left', '300px');
			return false;
		}
		if (width <= 1000) {
			$('#product_top .container').css('margin-left', '175px');
			return false;
		}
		if (width > 1000){
			diff = width - 1600;
			cr_ml = diff/4.8 + 300;
			$('#product_top .container').css('margin-left', cr_ml);
			return false;
		}
	}

	resizable_search = function(width){
		if (width >= 1600) {
			$('.menu_block .formSearch').css('left', '210px');
			return false;
		}
		if (width <= 1000) {
			$('.menu_block .formSearch').css('left', '70px');
			return false;
		}
		if (width > 1000){
			diff = width - 1600;
			cr_ml = diff/4.2857 + 210;
			$('.menu_block .formSearch').css('left', cr_ml);
			return false;
		}
	}

	resizable_gallery = function(width){
		if (width >= 1600) {
			$('.gallery .item_img img').css('width', '800px');
			$('.gallery .item_img').css('width', '800px');
			$('.gallery .item_img').css('height', '600px');
			$('.gallery .wrap').css('left', '0px');
			// $('.gallery .wrap a:eq(4)').css('display', 'block');
			return false;
		}
		if (width <= 1000) {
			$('.gallery .item_img img').css('width', '540px');
			$('.gallery .item_img').css('width', '540px');
			$('.gallery .item_img').css('height', '405px');
			$('.gallery .wrap').css('left', '-30px');
			// $('.gallery .wrap a:eq(4)').css('display', 'none');
			return false;
		}
		if (width > 1000){
			diff = width - 1600;
			rg_w = diff/2.30769 + 800;
			rg_h = diff/3.07692 + 600;
			rg_l = diff/20 + 20;
			$('.gallery .item_img img').css('width', rg_w);
			$('.gallery .item_img').css('width', rg_w);
			$('.gallery .item_img').css('height', rg_h);
			if (width <= 1150){
				rg_l = diff/20 + 15;
				// $('.gallery .wrap a:eq(4)').css('display', 'none');
			}
			if (width >= 1151){
				rg_l = diff/20 + 26;
				// $('.gallery .wrap a:eq(4)').css('display', 'block');
			}
			$('.gallery .wrap').css('left', rg_l);
			return false;
		}
	}
	
	$(document).ready(function() {

		// Убирает углы при открытом втором уровне
		if($('.main_menu.parent.active').exists() || $('.main_menu.parent.preactive').exists()) {
			$('#menu').css('border-radius', '16px 16px 0px 0px');
		}

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
		resizable_most_popular($('.middle_wrap').width());
		resizable_container($('.middle_wrap').width());
		resizable_feedback($('.middle_wrap').width());
		resizable_categories($('.middle_wrap').width());
		resizable_product_list($('.middle_wrap').width());
		resizable_product_item($('.middle_wrap').width());
		resizable_search($('.middle_wrap').width());
		resizable_gallery($('.middle_wrap').width());
		resizable_popular($('.middle_wrap').width());
	
		$(window).resize(function(){
			// resizable_menu($('.middle_wrap').width());
			resizable_middle($('body').width());
			resizable_most_popular($('.middle_wrap').width());
			resizable_container($('.middle_wrap').width());
			resizable_feedback($('.middle_wrap').width());
			resizable_categories($('.middle_wrap').width());
			resizable_product_list($('.middle_wrap').width());
			resizable_product_item($('.middle_wrap').width());
			resizable_search($('.middle_wrap').width());
			resizable_gallery($('.middle_wrap').width());
			resizable_popular($('.middle_wrap').width());
		});
})

})(jQuery);
