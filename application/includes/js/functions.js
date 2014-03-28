function Menu(id)
{
	var menu = document.getElementById('menu_' + id).style;
	if (menu.display == 'none')
	{
		menu.display = 'block';
	}
	else
	{
		menu.display = 'none';
	}
}

(function($) {

	$('#shadow').css('height', $('body').height()+'px');

	$('#shadow,.close_link').on('click',function(){
		$('#shadow,#ajaxFeedbackBlock, #ajaxSatusBlock, #ajaxGalleryBlock').fadeOut();
	});

	$('a[href=#status]').on('click',function(){
		$('#shadow').fadeIn();
		var elLeft = Math.round($(document).scrollLeft()+(document.documentElement.clientWidth-690)/2)+'px';
		var elTop  = Math.round($(document).scrollTop()+(document.documentElement.clientHeight-690)/2)+100+'px';
		$('#ajaxSatusBlock').css({'top':elTop,'left':elLeft}).fadeIn();
		return false;
	})

	$('a[href=#feedback_link]').on('click',function(){
		$('#shadow').fadeIn();
		var elLeft = Math.round($(document).scrollLeft()+(document.documentElement.clientWidth-690)/2)+'px';
		var elTop  = Math.round($(document).scrollTop()+(document.documentElement.clientHeight-690)/2)+100+'px';
		$('#ajaxFeedbackBlock').css({'top':elTop,'left':elLeft}).fadeIn();
		return false;
	})

	// $('#news_list li, #actions_list li, .action_block').on('click',function(){
	// 	location.href = $(this).find('a').attr('href');
	// })

	$('#file_view').on('click',function(){
		$('#file').trigger('click');
	})

	$('#file').on('change',function(){
		$('#file_visible').val($('#file').val());
	})

	$('#send_ajaxFeedback').on('click',function(){
		var errors = [];
		form = $(this).parents('form');
		var obj = form.find('[name="fio"]');
		if (obj.val() == '' || obj.val() == obj.attr('rel')){
			obj.next().html('Укажите свое имя').show();
			errors.push('Укажите свое имя');
		} else{
			obj.next().hide();
		}
		
		obj = form.find('[name="email"]');
		if (obj.val() == '' || obj.val() == obj.attr('rel')) {
			errors.push('Укажите свой e-mail');
			obj.next().html('Укажите свой e-mail').show();
		} else {			
			var template = /^[A-Za-z0-9](([_\.\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([\.\-]?[a-zA-Z0-9]+)*)\.([A-Za-z])+$/;
			if (! template.test(obj.val())) {
				obj.next().html('Указан некорректный e-mail').show();
				errors.push('Указан некорректный e-mail');
			} else{
				obj.next().hide();
			}
		}
		
		obj = form.find('[name="text"]');
		if (obj.val() == '' || obj.val() == obj.attr('rel')){
			errors.push('Введите текст');
			obj.next().html('Введите текст').show();
		} else{
			obj.next().hide();
		}

		
		// obj = form.find('#captcha');
		// if (obj.val() == '' || obj.val() == obj.attr('rel')) errors.push('Введите защитный код');
		
		if (errors.length > 0) {
			// alert(errors.join('\n\r'));
			return false;
		}
		
		$('#ajaxSatusBlock form').submit();
		$('#ajaxFeedbackBlock form').submit();
	})

	$.fn.scrollToTop = function() {
		$(this).hide().removeAttr("href");
		if ($(window).scrollTop() >= "250") $(this).fadeIn("slow")
		var scrollDiv = $(this);
		$(window).scroll(function() {
			if ($(window).scrollTop() <= "250") $(scrollDiv).fadeOut("slow")
			else $(scrollDiv).fadeIn("slow")
		});
		$(this).click(function() {
			$("html, body").animate({scrollTop: 0}, "slow")
		})
	}
	
	var histAPI=!!(window.history && history.pushState);
	
	$(document).ready(function(){	

		$('.fancy_fix').data('data-fancybox-type', 'ajax').attr('data-fancybox-type', 'ajax');

		$('[name=brands]').on('change',function(){
	       /*
	        * В переменную country_id положим значение селекта
	        * (выбранная страна)
	        */	       
		       var brand = $(this).val();
		       if (brand == '0'){
		       		return false;
		       }
		      	$.ajax({
			      	url:'/application/Ajax_brands_info',
			      	type : 'POST',
			      	dataType : 'JSON',
			      	data: {
					
			      		'alias':brand
			      	},
		      		beforeSend : function(){
		      			console.log(brand);
					},
					success : function(result){
						// r = $(result)
						var options = '';
						r = JSON.parse(result)
						options = '<option value="0"> Выберите модель</option>';
						$.each(r, function() {
                        	options += '<option value="' + this.alias + '">' + this.title + '</option>';
                    	});
                        $('[name=models]').html(options);
                        $('[name=models]').attr('disabled', false);
					}
				})
	      	})

		$('[name=models]').on('change',function(){
			location = '/catalog/'+ $('[name=brands]').val() + '/' + $('[name=models]').val();
		})


		$(window).resize(dynamic_resolution);

		$("#toUp").scrollToTop();
		
		//Обертка картинок в текстовой части
		// $('.content img').each(function() {
		// 	$(this).wrap('<div class="img_wrap"></div>');
		// 	if ( $(this).css('float') == 'left' ) {
		// 		$(this).css('float','none');
		// 		$(this).parent().addClass('left');
		// 	}
			
		// 	var note = $(this).attr('title') || '';
		// 	$(this).parent().append('<div class="img_note">'+note+'</div>');
		// });
		
		//черезполосица таблиц
		$('.content table').each(function() {
			$(this).find('tr').each(function(i,e) {
				if ((i % 2) == 0) $(this).addClass('odd');
				else $(this).addClass('even');
			});
		});
		//Выделение пункта меню
		/**/
		$('.menu_block li').on('mouseover',function() {
			$(this).addClass('hovered');
		}).on('mouseleave',function() {
			$(this).removeClass('hovered');
		});
		
		//Выделение пункта подменю
		/**/
		$('.submenu td').on('mouseover',function() {
			$(this).addClass('hovered');
		}).on('mouseleave',function() {
			$(this).removeClass('hovered');
		});		
		
		//Поля ввода текста с временным содержимым
		$('.def_text').each(function() {
			$(this).focus(function() {
				if ($(this).val() == $(this).attr('rel')) {
					$(this).css('color','black').val('');
				}
			}).blur(function() {
				if ($(this).val() == $(this).attr('rel')) {
					$(this).css('color', '')
				} else if ($(this).val() == '') {
					$(this).css('color', '').val($(this).attr('rel'));
				}
			});
		});
		
		//Выделение чекбокса
		$('.checkbox, .checkbox2').click(function() {checkbox($(this), false);});
		
		dynamic_resolution();
	});
	
	dynamic_resolution = function() {
		$('#main').css('height','');
		$('#main_table').css('height','');
		var w = parseInt($(window).height());
		var m = parseInt($('#main').height());
		var f = parseInt($('#footer_block').height());
		
		if ( (m+f)<w ) {
			$('#main').css('height',(w-f-2)+'px');
			$('#main_table').css('height',(w-f-2)+'px');
		}
	}
	
	
	//Обработчик нажатия чекбокса
	checkbox = function(self, stop) {
		var group = self.attr('rel');
		if (group) {
			$('.checkbox[rel="'+group+'"], .checkbox2[rel="'+group+'"]').removeClass('checked').find('input').val('0');
		}
		if (!self.hasClass('checked')) {	
			self.addClass('checked');
			self.find('input').val('1');
		} else {
			if (!group) {
				self.removeClass('checked');
				self.find('input').val('0');
			}
		}
		if (stop == true) return false;
	}
	
	
	search_ajax = function(page, text) {		
		var cur_page = $('.pager_tbl .sel tt').html();
		if ( cur_page == page ) {
			location.hash = 'top';
			return false;
		}
		
		if (histAPI) {
			history.pushState(null, null, '/popup/search/view/'+text+'/?page='+cur_page);
			history.replaceState(null, null, '/popup/search/view/'+text+'/?page='+page);
		}
		
		$.ajax({
			url:      '/popup/search/search_ajax/'+page+'/',
			type: 'POST',
			data: {'text':text},
			dataType: 'json',
			cache:    false,
			success:  function(res) {
				$('.search_res .box').html(res);
				dynamic_resolution();
				location.hash = 'top';
			}
		});
		return false;
	}
	
	
	table_ajax = function(page, pid) {		
		var cur_page = $('.pager_tbl .sel tt').html();
	
		
		/*if (histAPI) {
			history.pushState(null, null, '/faq/'+pid+'/?page='+cur_page);
			history.replaceState(null, null, '/faq/'+pid+'/?page='+page);
		}*/
			$.ajax({
			url:      '/catalog/catalog_ajax/'+pid+'/'+page+'/',
			dataType: 'json',
			cache:    false,
			success:  function(res) {
				$('#product_block').html(res);
				dynamic_resolution();
				//location.hash = 'top';
			}
		});
		return false;
	}
	// faq_ajax = function(page, pid) {		
	// 	var cur_page = $('.pager_tbl .sel tt').html();
	// 	if ( cur_page == page ) {
	// 		location.hash = 'top';
	// 		return false;
	// 	}
		
	// 	if (histAPI) {
	// 		history.pushState(null, null, '/faq/'+pid+'/?page='+cur_page);
	// 		history.replaceState(null, null, '/faq/'+pid+'/?page='+page);
	// 	}
		
	// 	$.ajax({
	// 		url:      '/faq/faq_ajax/'+pid+'/'+page+'/',
	// 		dataType: 'json',
	// 		cache:    false,
	// 		success:  function(res) {
	// 			$('#faq_list').html(res.faqListPrev+res.faqList);
	// 			dynamic_resolution();
	// 			location.hash = 'top';
	// 		}
	// 	});
	// 	return false;
	// }
	
	
	news_ajax = function(page, pid) {		
		var cur_page = $('.pager_tbl .sel tt').html();
		if ( cur_page == page ) {
			location.hash = 'top';
			return false;
		}
		
		if (histAPI) {
			history.pushState(null, null, '/news/'+pid+'/?page='+cur_page);
			history.replaceState(null, null, '/news/'+pid+'/?page='+page);
		}
		
		$.ajax({
			url:      '/news/news_ajax/'+pid+'/'+page+'/',
			dataType: 'json',
			cache:    false,
			success:  function(res) {
				$('#news_list').html(res.list);
				dynamic_resolution();
				location.hash = 'top';
			}
		});
		return false;
	}
	
	articles_ajax = function(page, pid) {		
		var cur_page = $('.pager_tbl .sel tt').html();
		if ( cur_page == page ) {
			location.hash = 'top';
			return false;
		}
		
		if (histAPI) {
			history.pushState(null, null, '/articles/'+pid+'/?page='+cur_page);
			history.replaceState(null, null, '/articles/'+pid+'/?page='+page);
		}
		
		$.ajax({
			url:      '/articles/articles_ajax/'+pid+'/'+page+'/',
			dataType: 'json',
			cache:    false,
			success:  function(res) {
				$('#articles_list').html(res.list);
				dynamic_resolution();
				location.hash = 'top';
			}
		});
		return false;
	}
	
	actions_ajax = function(page, pid) {		
		var cur_page = $('.pager_tbl .sel tt').html();
		if ( cur_page == page ) {
			location.hash = 'top';
			return false;
		}
		
		if (histAPI) {
			history.pushState(null, null, '/actions/'+pid+'/?page='+cur_page);
			history.replaceState(null, null, '/actions/'+pid+'/?page='+page);
		}
		
		$.ajax({
			url:      '/actions/actions_ajax/'+pid+'/'+page+'/',
			dataType: 'json',
			cache:    false,
			success:  function(res) {
				$('#actions_list').html(res.list);
				dynamic_resolution();
				location.hash = 'top';
			}
		});
		return false;
	}
	
	portfolio_ajax = function(page, pid) {		
		var cur_page = $('.pager_tbl .sel tt').html();
		if ( cur_page == page ) {
			location.hash = 'top';
			return false;
		}
		
		if (histAPI) {
			history.pushState(null, null, '/portfolio/'+pid+'/?page='+cur_page);
			history.replaceState(null, null, '/portfolio/'+pid+'/?page='+page);
		}
		
		$.ajax({
			url:      '/portfolio/portfolio_ajax/'+pid+'/'+page+'/',
			dataType: 'json',
			cache:    false,
			success:  function(res) {
				$('#portfolio_list').html(res.list);
				dynamic_resolution();
				location.hash = 'top';
			}
		});
		return false;
	}
	
})(jQuery);