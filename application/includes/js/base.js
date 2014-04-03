var sliderCounter = 0;
var images = [];

var img_big_w = 640;
var img_big_h = 480;

(function($) {

	CheckSignUpForm = function(form) {
		var errors = [];
		var obj = form.find('#fio');
		if (obj.val() == '' || obj.val() == obj.attr('placeholder')) errors.push('Введите свое имя');

		obj = form.find('#phone');
		if (obj.val() == '' || obj.val() == obj.attr('placeholder')) errors.push('Введите номер телефона');

		obj = form.find('#email');
		if (obj.val() == '' || obj.val() == obj.attr('placeholder')) errors.push('Введите электронную почту');
		
		if (obj.val() != '') {
			var template = /^[A-Za-z0-9](([_\.\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([\.\-]?[a-zA-Z0-9]+)*)\.([A-Za-z])+$/;
			if (! template.test(obj.val())) {
				errors.push('Введен не корректный e-mail');
			}
		} else {
			// errors.push('Введите свой e-mail');
		}

		obj = form.find('#capcha');
		if (obj.val() == '' || obj.val() == obj.attr('placeholder')) errors.push('Введите защитный код');
		
		if (errors.length > 0) {
			alert(errors.join('\n\r'));
			return false;
		}
		return true;
	}
	
	makeMain = function (id) {
		$('#mainImg').html('<a href="javascript:void(0);" id="mainLeftArrow" onClick="return prevImg('+id+');"></a>'+
			'<a href="#" onClick="return nextImg('+id+');"><img src="/application/includes/img/b/'+id+'.jpg" width="'+'" /></a>' +
			'<a href="javascript:void(0);" id="mainRightArrow" onClick="return nextImg('+id+');"></a>');
		$('#scroll li').removeClass('active');
		$('#scroll a[href=#'+id+']').parent().addClass('active');
	};

	function change_slide(id){
		$(' .slider_link').removeClass('active');
		link = $(" .slider_links").find("[data-id='" + id + "']");
		header =  $(" .slider_links").find("[data-header-id='" + id + "']");
		note =  $(" .slider_links").find("[data-note-id='" + id + "']");
		url = $(" .slider_links").find("[data-link-id='" + id + "']");
		link_adress =  $(" .slider_links").find("[data-link-id='" + id + "']");
		link.addClass('active');
		$('#slider_background').stop().fadeOut();
	
		$('#slider_background').find('.link_adress').attr('href',link_adress.html());
		 setTimeout(function(){
		 		$('#slider_background').css('background', 'transparent url("/application/includes/slides/'+link.data('id')+'.jpg") 0 0 no-repeat')
				$('.note_block .slider_header').html(header.html());
				$('.note_block .slider_note').html(note.html());
				$('.note_block a').attr('href', url.html());
		 		$('#slider_background').stop().fadeIn();
		 }, 420)

				

	}
	function slider_init(id){
		$(' .slider_link').removeClass('active');
		link = $(" .slider_links").find("[data-id='" + id + "']");
		url = $(" .slider_links").find("[data-link-id='" + id + "']");
		header =  $(" .slider_links").find("[data-header-id='" + id + "']");
		note =  $(" .slider_links").find("[data-note-id='" + id + "']");
		link_adress =  $(" .slider_links").find("[data-link-id='" + id + "']");
		link.addClass('active');
	
		$('#slider_background').find('.link_adress').attr('href',link_adress.html());
 		$('#slider_background').css('background', 'transparent url("/application/includes/slides/'+link.data('id')+'.jpg") 0 0 no-repeat')

				
		$('.note_block .slider_header').html(header.html());
		$('.note_block .slider_note').html(note.html());
		$('.note_block a').attr('href', url.html());

	}

	ui_init = function(root){
		if(!root){
			root = $('body')
		}

		$(root + ' .submit_button').on('click', function(){
			$(this).parents('form').submit();
			return false;
		})

		// год в списке 
		$('a.news_year').on('click', function(){
			change_news_year_ajax($(this).data('newscount'), $(this).data('pid'), $(this).data('year'));
			return false;
		});


		/* sexycombo количества в списке новостей начало */

		$("#news_count").sexyCombo();

		$.sexyCombo.deactivate("#news_count");
    	$("#activate").bind("click", function () {
    		$.sexyCombo.activate("#news_count");
    	});

    	$("#empty-combo").sexyCombo({
    		emptyText: "Choose a state..."
    	});

    	$("#autofill-combo").sexyCombo({
    		autoFill: true
    	});

    	$('.select_outer_news .combo div.icon').on('click', function(){
	    	if ($('.list-wrapper').hasClass('visible')){
	    		$(this).css('background', 'url("/application/includes/images/news/news_counter_btn.png") 0px -81px');
	    	}
	    	if ($('.list-wrapper').hasClass('invisible')){
	    		$(this).css('background', '');
	    	}
    	})

    	$('.select_outer_news .list-wrapper ul li.visible').on('click', function(){
	    	if ($('.list-wrapper').hasClass('visible')){
	    		$('.combo div.icon').css('background', 'url("/application/includes/news/news_counter_btn.png") 0px -81px');
	    	}
	    	if ($('.list-wrapper').hasClass('invisible')){
	    		$('.combo div.icon').css('background', '');
	    	}
    	})

    	$('.select_outer_news .combo').focusout(function(){
	    	$('.combo div.icon').css('background', '');
    	})

    	$('.select_outer_news .combo div.icon').on('mouseover',function(){
    		if ($('.list-wrapper').hasClass('visible')){
	    		$('.combo div.icon').css('background', 'url("/application/includes/images/news/news_counter_btn.png") 0px -108px');
	    	}
    	})

    	$('.select_outer_news .combo div.icon').on('mouseout',function(){
    		if ($('.list-wrapper').hasClass('visible')){
	    		$('.combo div.icon').css('background', 'url("/application/includes/images/news/news_counter_btn.png") 0px -81px');
	    	}
    	})

    	/* sexycombo количества в списке новостей конец */

		// количество новостей в списке 
		$('#news_count').on('change', function(){
			change_news_count_ajax($('#news_count option:selected').val(), $('#news_count option:selected').data('pid'));
		});
	}

	// change_news_count_init = function(root){
	// 	if(!root){
	// 		root = $('body')
	// 	}
	// 	// количество новостей в списке 
	// 	$(root + ' #news_count').on('change', function(){
	// 		change_news_count_ajax($('#news_count option:selected').val(), $('#news_count option:selected').data('pid'));
	// 	});
	// }


	

	
	
	$(document).ready(function() {
		// год в списке 
		$('a.news_year').on('click', function(){
			change_news_year_ajax($(this).data('newscount'), $(this).data('pid'), $(this).data('year'));
			return false;
		});

		
		// ссылки в селекте курсов

		$('.select_outer #subtype').on('change', function(){
			active = $('.select_outer .list-wrapper ul li.active span').html();
			$('.select_outer #subtype option').each(function(){
				if($(this).html() == active){
					url = $(this).data('url');
					window.location.href = url;
				}
			});
		})

		// Заглшука на ссылки
		$('a[href="#"]').on('click', function(){
			return false;
		})
		
		
		/* sexycombo курсов начало */

		$("#subtype").sexyCombo();

		$.sexyCombo.deactivate("#subtype");
    	$("#activate").bind("click", function () {
    		$.sexyCombo.activate("#subtype");
    	});

    	$("#empty-combo").sexyCombo({
    		emptyText: "Choose a state..."
    	});

    	$("#autofill-combo").sexyCombo({
    		autoFill: true
    	});

    	$('.select_outer .combo div.icon').on('click', function(){
	    	if ($('.list-wrapper').hasClass('visible')){
	    		$(this).css('background', 'url("/application/includes/images/sexycombo/sexy/title_select_btn.png") 0px -110px');
	    	}
	    	if ($('.list-wrapper').hasClass('invisible')){
	    		$(this).css('background', '');
	    	}
    	})

    	$('.select_outer .list-wrapper ul li.visible').on('click', function(){
	    	if ($('.list-wrapper').hasClass('visible')){
	    		$('.combo div.icon').css('background', 'url("/application/includes/images/sexycombo/sexy/title_select_btn.png") 0px -110px');
	    	}
	    	if ($('.list-wrapper').hasClass('invisible')){
	    		$('.combo div.icon').css('background', '');
	    	}
    	})

    	$('.select_outer .combo').focusout(function(){
	    	$('.combo div.icon').css('background', '');
    	})

    	$('.select_outer .combo div.icon').on('mouseover',function(){
    		if ($('.list-wrapper').hasClass('visible')){
	    		$('.combo div.icon').css('background', 'url("/application/includes/images/sexycombo/sexy/title_select_btn.png") 0px -147px');
	    	}
    	})

    	$('.select_outer .combo div.icon').on('mouseout',function(){
    		if ($('.list-wrapper').hasClass('visible')){
	    		$('.combo div.icon').css('background', 'url("/application/includes/images/sexycombo/sexy/title_select_btn.png") 0px -110px');
	    	}
    	})

    	/* sexycombo курсов конец */

		/* sexycombo в профиле */

		$("#day, #month, #year, #sex").sexyCombo();

		$.sexyCombo.deactivate("#day, #month, #year, #sex");
    	$("#activate").bind("click", function () {
    		$.sexyCombo.activate("#day, #month, #year, #sex");
    	});

    	$("#empty-combo").sexyCombo({
    		emptyText: "Choose a state..."
    	});

    	$("#autofill-combo").sexyCombo({
    		autoFill: true
    	});

    	$('.select_outer_day .combo div.icon, .select_outer_month .combo div.icon, .select_outer_year .combo div.icon, .select_outer_sex .combo div.icon').on('click', function(){
	    	if ($('.list-wrapper').hasClass('visible')){
	    		$(this).css('background', 'url("/application/includes/images/news/news_counter_btn.png") 0px -81px');
	    	}
	    	if ($('.list-wrapper').hasClass('invisible')){
	    		$(this).css('background', '');
	    	}
    	})

    	$('.select_outer_day .list-wrapper ul li.visible, .select_outer_month .list-wrapper ul li.visible, .select_outer_year .list-wrapper ul li.visible, .select_outer_sex .list-wrapper ul li.visible').on('click', function(){
	    	if ($('.list-wrapper').hasClass('visible')){
	    		$('.combo div.icon').css('background', 'url("/application/includes/news/news_counter_btn.png") 0px -81px');
	    	}
	    	if ($('.list-wrapper').hasClass('invisible')){
	    		$('.combo div.icon').css('background', '');
	    	}
    	})

    	$('.select_outer_day .combo, .select_outer_month .combo, .select_outer_year .combo, .select_outer_sex .combo').focusout(function(){
	    	$('.combo div.icon').css('background', '');
    	})

    	$('.select_outer_day .combo div.icon, .select_outer_month .combo div.icon, .select_outer_year .combo div.icon, .select_outer_sex .combo div.icon').on('mouseover',function(){
    		if ($(this).next('.list-wrapper').hasClass('visible')){
	    		$(this).css('background', 'url("/application/includes/images/news/news_counter_btn.png") 0px -108px');
	    	}
    	})

    	$('.select_outer_day .combo div.icon, .select_outer_month .combo div.icon, .select_outer_year .combo div.icon, .select_outer_sex .combo div.icon').on('mouseout',function(){
    		if ($(this).next('.list-wrapper').hasClass('visible')){
	    		$(this).css('background', 'url("/application/includes/images/news/news_counter_btn.png") 0px -81px');
	    	}
    	})

    	/* sexycombo в профиле конец */

    	/* JSP для слайдера */ 
    	

		slider_jsp_init = function(l_stp, r_stp, reinit){
			var count_item = $('#slider_jsp #wrap_left .item').size();			
			if(count_item){
				var left_step = l_stp+25;
				var right_step = r_stp;
				var reinit = reinit;
				
				
				var count_visible_item = $('#slider_jsp #scroll_left').width() / right_step
				var settings = {		
					// autoReinitialise: true
				};
				var lf = true
				var rf = true

				$('#slider_jsp #wrap_left').css("width", count_item * left_step + "px");
				$('#slider_jsp #wrap_right').css("width", count_item * right_step + "px");
				
				if(reinit){
					slider_api.reinitialise();
					slider_api2.reinitialise();
				}
				else {
					slider_api = $('.left_pane').jScrollPane(settings).data('jsp');
					slider_api2 = $('.right_pane').jScrollPane(settings).data('jsp');
				}



				slider_api2.scrollByX(right_step*count_visible_item);

				$('#slider_jsp #leftArrow').on('click', function() {		
					$('#slider_jsp #wrap_left').prepend( $('#slider_jsp #wrap_left .item').last() );		
					slider_api.scrollByX(left_step);
					slider_api.scrollByX(-left_step, 1000);

					if(!lf) $('#slider_jsp #wrap_right').prepend( $('#slider_jsp #wrap_right .item').last() );
					slider_api2.scrollByX(right_step);
					slider_api2.scrollByX(-right_step, 1000);

					lf = false
					return false;
				});	
			}


			right_jsp = function(){
				curr_num = $('#wrap_left .item.active').data('num');
				$('#wrap_left .item.active').removeClass('active');

				$('#wrap_left .item').each(function(){
					if(curr_num >= $('#wrap_left .item').length){
						curr_num = 0;
					}
					if($(this).data('num') == curr_num+1){
						$(this).addClass('active');
					}
				})
				$('#wrap_right .item.active').removeClass('active');

				$('#wrap_right .item').each(function(){
					if(curr_num >= $('#wrap_right .item').length){
						curr_num = 0;
					}
					if($(this).data('num') == curr_num+1){
						$(this).addClass('active');
					}
				})

				$('.slider_bg .note h1').html($('#wrap_left .item.active span').html());
				$('.slider_bg a.more_button').attr('href', $('#slider_jsp .item.active a').attr('href'));
				$('.slider_bg .note p').html($('#wrap_left .item.active .note p').html());

				if(!rf) $('#slider_jsp #wrap_left').append( $('#slider_jsp #wrap_left .item').first() );		
				slider_api.scrollByX(-left_step);
				slider_api.scrollByX(left_step, 1000);	
				
				$('#slider_jsp #wrap_right').append( $('#slider_jsp #wrap_right .item').first() );
				slider_api2.scrollByX(-right_step);
				slider_api2.scrollByX(right_step, 1000);	
				
				rf = false
				$('#wrap_left .item').each(function(){
					$(this).css('visibility', 'visible');				
				});
				$('#wrap_left .item.first').removeClass('first');
				$('#wrap_left').children('.item').eq(1).addClass('first');

				$('#wrap_left .item.first').css('display', 'block');
				if ($('.middle_wrap').width() <= 1600) {
					setTimeout(visibile_item, 400);
				}
				
				return false;  
			}

			left_jsp = function(){
				curr_num = $('#wrap_left .item.active').data('num');
				$('#wrap_left .item.active').removeClass('active');

				$('#wrap_left .item').each(function(){
					if(curr_num <= 1){
						curr_num = $('#wrap_left .item').length+1;
					}
					if($(this).data('num') == curr_num-1){
						$(this).addClass('active');
					}
				})
				// console.log(curr_num);
				$('#wrap_right .item.active').removeClass('active');

				$('#wrap_right .item').each(function(){
					if(curr_num <= 1 ){
						curr_num = $('#wrap_right .item').length+1;
					}
					if($(this).data('num') == curr_num-1){
						$(this).addClass('active');
					}
				})

				$('.slider_bg .note h1').html($('#wrap_left .item.active span').html());
				$('.slider_bg a.more_button').attr('href', $('#slider_jsp .item.active a').attr('href'));
				$('.slider_bg .note p').html($('#wrap_left .item.active .note p').html());

				$('#slider_jsp #wrap_left').prepend( $('#slider_jsp #wrap_left .item').last() );		
				slider_api.scrollByX(left_step);
				slider_api.scrollByX(-left_step, 1000);

				if(!lf) $('#slider_jsp #wrap_right').prepend( $('#slider_jsp #wrap_right .item').last() );
				slider_api2.scrollByX(right_step);
				slider_api2.scrollByX(-right_step, 1000);

				lf = false

				$('#wrap_left .item').each(function(){
					$(this).css('visibility', 'visible');				
				});
				$('#wrap_left .item.first').removeClass('first');
				$('#wrap_left').children('.item').eq(1).addClass('first');

				$('#wrap_left .item.first').css('display', 'block');
				if ($('.middle_wrap').width() <= 1600) {
					setTimeout(visibile_item, 400);
				}
				
				return false;  
			}

			visibile_item = function(){
				$('#wrap_left .item.first').css('visibility', 'hidden');
			}
		}

		slider_jsp_init(420, 450);
		// window.onload = function(){
		$(window).load(function(){
			$('.test_fon').css('height', $('.questions ul').height()+100);
		})
		$(window).resize(function(){
			$('.test_fon').css('height', $('.questions ul').height()+100);
			l_stp = $('#wrap_left .item').outerWidth(true);
			r_stp = $('#wrap_right .item').outerWidth(true);
			reinit = false;
			if(slider_api != null){
				reinit = true;
			}

			slider_api.scrollToX(0, 0);
			slider_api2.scrollToX($('#wrap_right').width(), 0);

			$('#wrap_left .item').each(function(){
				left_tmp = $('#wrap_left .item.active').prev('#wrap_left .item').data('num');
				left_elem = (left_tmp-2) * $('#wrap_left .item').outerWidth(true);
			})

			$('#wrap_right .item').each(function(){
				right_tmp = $('#wrap_left .item.active').next('#wrap_left .item').data('num');
				right_elem = right_tmp * $('#wrap_left .item').outerWidth(true);
			})

			slider_api.scrollByX(left_elem, 0);
			slider_api.scrollByX(-right_elem, 0);

			slider_jsp_init(l_stp, r_stp, reinit);
		});

		$('#slider_jsp .item a').on('click', function(){
			return false;
		})
    	/* -- JSP для слайдера */ 


		
		slider_init($(' .slider_link').data('id'));
		
    	/* JSP для галереи */ 
		var count_item = $('#previews .item').size();		
		var step = 180;

		$('#previews #wrap').css("width", count_item * step + "px");
		var api = $('.scroll-pane').jScrollPane({}).data('jsp');
		if (api != null){
			$(window).resize(function(){
				api.reinitialise();
			});
		}

		$('#previews #leftArrow').on('click', function() {	
			if(count_item > 3){		
				api.scrollByX(-step, 1000);
			}
			return false;
		});
		
		$('#previews #rightArrow').on('click', function() {
			if(count_item > 3){		
				api.scrollByX(step, 1000);	
			}
			return false;    
		});
    	/* -- JSP для галереи */ 

    	/* JSP для блока новостей на главной */ 
		var news_count_item = $('#news_block .item').size();		
		var news_step = $('#news_block .item').outerWidth(true);

		$('.scroll-pane').off('scroll');

		$('#news_block #wrap').css("width", (news_count_item * news_step) + "px");
		$('#news_block .jspContainer').css("height", $('#news_block .jspPane').height());
		$('#news_block .scroll-pane').css("height", $('#news_block .jspPane').height());
		var news_api = $('#news_block .scroll-pane').jScrollPane({}).data('jsp');

		if (news_api != null){
			$(window).resize(function(){
				news_count_item = $('#news_block .item').size();		
				news_step = $('#news_block .item').outerWidth(true);
				$('#news_block #wrap').css("width", (news_count_item * news_step) + "px");
				$('#news_block .jspContainer').css("height", $('#news_block .jspPane').height());
				$('#news_block .scroll-pane').css("height", $('#news_block .jspPane').height());
				// news_api.reinitialise();
			});
		}

		$('#news_block #leftArrow').on('click', function() {	
			if(news_count_item > 3){		
				news_api.scrollByX(-news_step, 1000);
			}
			return false;
		});
		
		$('#news_block #rightArrow').on('click', function() {
			if(news_count_item > 3){		
				news_api.scrollByX(news_step, 1000);	
			}
			return false;    
		});
    	/* -- JSP для галереи */ 

		
		/* MENU BLOCK BEGIN */
		$('.menu_block .parent').on('mouseover',function(){
			if((!$(this).hasClass('active')) && (!$(this).hasClass('preactive'))){
				$('.menu_block .parent.active ').find('.submenu').css('display', 'none');
				$('.menu_block .parent.preactive ').find('.submenu').css('display', 'none');
			}
		});
		$('.menu_block .parent').on('mouseleave',function(){
			if((!$(this).hasClass('active')) && (!$(this).hasClass('preactive'))){
				$('.menu_block .parent.active ').find('.submenu').css('display', 'block');
				$('.menu_block .parent.preactive ').find('.submenu').css('display', 'block');
			}
		});

		/* MENU BLOCK END */


		// intervalID = setInterval(function(){
		// 	$('.right_arrow').trigger('click');
		// }, 5000)


		// -- слайдер на главной
		$(' .slider_link').on('click', function(){
			change_slide($(this).data('id'));
			clearInterval(intervalID);
			intervalID = setInterval(function(){
				$('.right_arrow').trigger('click');
			}, 5000)
			return false;
				})
		$('.right_arrow').on('click',function(){
			link = parseInt($(" .slider_links .active").attr('href'));
			link++;
			next = $(' .slider_links [href^="'+link+'"]');

			if(next.length!=0){
				next.trigger('click');
			}
			else{
				next = $(" .slider_links [href^='0']");
				next.trigger('click');
			}
			// clearInterval(intervalID);
			// intervalID = setInterval(function(){
			// 	$('.right_arrow').trigger('click');
			// }, 5000)
			return false;

		})
		$('.left_arrow').on('click',function(){
			link = parseInt($(" .slider_links .active").attr('href'));
			link--;

			prev = $(' .slider_links [href^="'+link+'"]');

			if(prev.length!=0){
				prev.trigger('click');
			}
			else{
				count = $(" .slider_link").length;
				count--;
				prev = $(' .slider_links [href^="'+count+'"]');
				prev.trigger('click');
			}
			// clearInterval(intervalID);
			// intervalID = setInterval(function(){
			// 	$('.right_arrow').trigger('click');
			// }, 5000)
			return false;

		})


		// -- определяем высоту #shadow
		$('#shadow').css('height', $('body').height()+'px');
		// -- клик на фото превью в основном документе
		$('.ajaxGallery a').removeAttr('onClick').on('click', function() {
			var id = $(this).attr('href').substring(1);
			if(window.photo_ids) {
				for(var i in photo_ids) {
					img = new Image();
					img.src = '/application/includes/img/b/'+photo_ids[i]+'.jpg';
					images.push(img);
					
					if(photo_ids[i] == id) {
						sliderCounter = parseInt(i);
					}
				}
			}
			
			if ( sliderCounter > ($('#scroll ul li').length-4) ) {
				sliderCounter = $('#scroll ul li').length-4;
			}
			scroll_to();
			
			
			makeMain(id);
			$('#mainNote').html($(this).next('.img_note').html());
			
			var hei = $('#ajaxGalleryBlock').css({'visibility': 'hidden', 'display': 'fixed'}).height();
			$('#ajaxGalleryBlock').css({'visibility': '', 'display': ''});
			
			var elLeft = Math.round($(document).scrollLeft()+(document.documentElement.clientWidth-$('#ajaxGalleryBlock').width())/2)+'px';
			var elTop  = Math.round($(document).scrollTop()+(document.documentElement.clientHeight-hei)/2);
			
			if (elTop < ($(document).scrollTop()+10)) elTop = $(document).scrollTop()+10;
			$('#ajaxGalleryBlock').css({'top':elTop+'px','left':elLeft}).fadeIn();
			
			var wndHeight = (elTop + $('#ajaxGalleryBlock').height()) - $(document).height();
			if (wndHeight>0) wndHeight += $(document).height();
			else wndHeight = $(document).height();
			$('#shadow').css('height', wndHeight+'px').show();

			return false;
		});

		showImg = function(id) {
			var elLeft = Math.round($(document).scrollLeft()+(document.documentElement.clientWidth-690)/2)+'px';
			var elTop  = Math.round($(document).scrollTop()+(document.documentElement.clientHeight-690)/2)+'px';
			$('#shadow').show();
			$('#ajaxGalleryBlock').css({'top':elTop,'left':elLeft}).fadeIn();
			
			makeMain(id);
		}
		replaceImg = function(id, o) {
			makeMain(id);
			$('#mainNote').html($(o).next('.img_note').html());			//?????????????????
			
			/*for(var i in photo_ids) {
				if(photo_ids[i] == id) {
					sliderCounter = i;
					break;
				}
			}*/
			return false;
		}
		
		prevImg = function(id) {
			for(var i in photo_ids) {
				if(photo_ids[i] == id) {
					prev = parseInt(i)-1;
					
					if(prev < 0) {
						prev = photo_ids.length-1;
					}
					
					var t = prev;
					if ( t < (5-$('#scroll ul li').length) ) {
						t = 5-$('#scroll ul li').length;
					}
					if (sliderCounter != t) {
						sliderCounter = t;
						scroll_to();
					};
					
					makeMain(photo_ids[prev]);
					$('#mainNote').html($('#scroll a[href=#'+photo_ids[prev]+']').next('.img_note').html()); //????????????????
					break;
				}
			}
			return false;
		}
		
		nextImg = function(id) {
			for(var i in photo_ids) {
				if(photo_ids[i] == id) {
					next = parseInt(i)+1;
					if(photo_ids[next] == undefined) {
						next = 0;
					}
					
					var t = next;
					if ( t > ($('#scroll ul li').length-5) ) {
						t = $('#scroll ul li').length-5;
					}
					if (sliderCounter != t) {
						sliderCounter = t;
						scroll_to();
					};
					
					makeMain(photo_ids[next]);
					$('#mainNote').html($('#scroll a[href=#'+photo_ids[next]+']').next('.img_note').html()); //??????????????????
					break;
				}
			}
			return false;
		}

		closeGallery = function() {
			$('#ajaxGalleryBlock').fadeOut();
			$('#shadow').hide();
		}

		scroll_right = function() {
			if ( ($('#scroll ul li').length-4) == sliderCounter) {
				sliderCounter = 0;
			} else {
				sliderCounter++;
			}
			scroll_to();
		}
		
		scroll_left = function() {
			if(sliderCounter > 0) {
				sliderCounter--;
			} else {
				sliderCounter = ($('#scroll ul li').length-4);
			}
			scroll_to();
		}
		
		scroll_to = function() {
			var widthPreview = $('#scroll').find('li').width();
			$('#scroll ul').animate({
				marginLeft :'-'+(sliderCounter*(widthPreview))+'px'
			}, 200);
		}
		
		mousewheel1 = function(objEvent, intDelta){
			if (intDelta < 0){
				scroll_right();
			}
			else if (intDelta > 0){
				scroll_left();
			}
			return false;
		}

		
		images_ajax = function(page, pid, mid) {
			$.ajax({
				url:      '/all/images_ajax/'+pid+'/'+mid+'/'+page+'/',
				dataType: 'json',
				cache:    false,
				success:  function(res) {
					$('#galereya').replaceWith(res);
					window.location = '#galereya';
					dynamic_resolution();
					refresh_shadows();
				}
			});
			return false;
		}




		// печать страницы 
		$('.print').on('click',function(){
			window.print();
		})
		$('.toggler').next().hide();

		// //Выпадающий блок на форме
		// $('.toggler').each(function() {
		// 	$(this).addClass('plus3');
		// 	var togg = $(this).next('div');
		// 	if (togg.length == 0) togg = $(this).next().next('div');	//Для раздела помощи
			
		// 	togg.css({
		// 		'display': 'none',
		// 		'padding-top':'4px',
		// 		'padding-left':'12px',
		// 		'padding-bottom': '4px',
		// 	});
			
		// 	$(this).click(function() {
		// 		if($(this).hasClass('plus3')) {
		// 			$(this).removeClass('plus3').addClass('minus3');
		// 		} else if($(this).hasClass('minus3')) {
		// 			$(this).removeClass('minus3').addClass('plus3');
		// 		}
				
		// 		togg.slideToggle(300);
		// 		if($(this).hasClass('togtype'))
		// 			return false;
		// 	});
		// });

		$("div.current_type").css('display', 'block')
		$("div.current_type").children('div').css('display', 'none')
		$("div.current").parent('div').css('display', 'block')
		$("div.current").css('display', 'block')
		$("div.active_cat").parent('div').parent('div').css('display', 'block')
		$("div.active_cat").parent('div').css('display', 'block')
		// $("div.active_cat").parent('div').parent('div').slideToggle(1)
		// $("div.active_cat").parent('div').slideToggle(1)
		// $("div.current").parent('div').slideToggle(1)
		// $("div.current").slideToggle(1)
		// $("div.current_type").slideToggle(1)
		// $("div.current_type").children('div').slideToggle(1)
		$('.tabs table span:contains("+"), .tabs table td:contains("-")').each(function(){
			$(this).css('font-size', '18px');
		})

		$('.tabs').each(function(){
			$(this).find('[id^="tabs"]').hide()   

			$(this).find('a[href^="#tabs"]').on('click',function(){
			// return false;
				tabs = $(this).parents('.tabs')    
				tab_num = $(this).attr('href').match(/\d+/)[0]

				tabs.find('a[href^="#tabs"]').removeClass('active')
				$(this).addClass('active')

				tabs.find('[id^="tabs"]').hide()
				tabs.find('[id="tabs-'+tab_num+'"]').show()
				  
				return false
			})

			setTimeout(function(){
				$(window).scrollTop(0)
			}, 5)
		})
		$(this).find('a[href^="#tabs"]:eq(0)').trigger('click')
		// $(this).find('a[href^="#tabs"]':eq(0)).trigger('click')
		$(' .proj_slider').on('click',function(){
			target = ' .slide_'+$(this).data('num')

			$(this).parents(' .our_project').find('[class^="slide_"]').addClass('not_active');
			$(this).parents(' .our_project').find(' .proj_slider').removeClass('active');
			$(this).parents(' .our_project').find(target).removeClass('not_active');
			$(this).addClass('active');
		})


		// чересполосица в таблице
		$('.page_wrapper table tr:odd').addClass('odd')

		// чересполосица в таблице
		$('.pages_container .tabs div table tr:odd').addClass('odd')

		//кнопка-ссылка
		$('.submit_button').click(function(){
			$(this).parents('form').submit();
			return false;
		})

		// для ховера менюшки
		$('.menu_block .submenu li a').hover(function(){
			$(this).parents('li').find('span').toggleClass('ahover');
			$(this).parents('td').toggleClass('tdhover');
		})

		$('.fancy').fancybox();

		$('.ask_question_button.fancy').on('click', function(){
			var pid = $(this).data('pid');
			$('.ask_question_button.fancy').fancybox({
				beforeShow   : function(){
					$('#faqForm input[name="pid"]').val(pid);
	   			}
			});
		})
		
		
		$('ul#product .fancy').on('click', function(){
			var title = $(this).data('title');
			$('.fancy').fancybox({
				beforeShow   : function(){
					$('#faqForm input[name="course_title"]').val(title);
	   			}
			});
		})
		
		$('a[href$="ajax/1"], a[href$="ajax/2"], a[href$="ajax/3"], a[href$="ajax/4"], a[href$="ajax/5"], a[href$="ajax/6"]').on('click', function(){
			$('.fancy').fancybox({
				beforeShow   : function(){
					$('.fancybox-inner #question').html('');
	   			}
			});
		})

		$('a[href$="ajax/7"]').on('click', function(){
			var link = $(this);
			$('.fancy').fancybox({
				beforeShow   : function(){
					var title = link.data('title');
					$('.fancybox-inner #question').html('Здравствуйте, меня интересует светильник ' + title);
	   			}
			});
		})

		$('a[href$="ajax/8"]').on('click', function(){
			var link = $(this);
			$('.fancy').fancybox({
				beforeShow   : function(){
					var title = link.data('title');
					$('.fancybox-inner #question').html('Здравствуйте, я хочу учавствовать в акции "' + title + '"');
	   			}
			});
		})

		$('.btn_close').on('click', function(){
			$.fancybox.close()
		})


		// -- определяем высоту #shadow
		$('#shadow, #shadow2, #shadow_left, #shadow_right').css('height', $('body').height()+'px');
		var lrshw = Math.round(($(window).width() - $('#shadow2').width()) / 2);
		lrshw = (lrshw > 0) ? lrshw : 0;
		$('#shadow_left, #shadow_right').css({'width':lrshw});		
		
		// -- устанавливаем ширину ul в #ajaxGalleryBlock
		// $('#preview ul').css('width', ($('#scroll li').length*150)+'px');		
		
		$('#shadow').on('click', function() {
			$('#ajaxGalleryBlock').fadeOut();
			$('#pic_editor').fadeOut();
			$(this).hide();
			$('#shadow_left, #shadow_right').hide();
		});
		
		// -- скроллер в фотогаллерее
		$('#rightArrow').on('click',function() {
			scroll_right();
		});
		
		$('#leftArrow').on('click',function() {
			scroll_left();
		});
		
		//window.onmousewheel = document.onmousewheel = mousewheel1;
		$("#scroll").bind('mousewheel', mousewheel1);
		
		// -- клик на фото превью в основном документе
		$('.ajaxGallery a').removeAttr('onClick').on('click', function() {
			var id = $(this).attr('href').substring(1);
			for(var i in photo_ids) {
				img = new Image();
				img.src = '/application/includes/img/b/'+photo_ids[i]+'.jpg';
				images.push(img);
				
				if(photo_ids[i] == id) {
					sliderCounter = parseInt(i);
				}
			}
			
			if ( sliderCounter > ($('#scroll ul li').length-4) ) {
				sliderCounter = $('#scroll ul li').length-4;
			}
			scroll_to();
			
			
			makeMain(id);
			$('#mainNote').html($(this).next('.img_note').html());
			
			var hei = $('#ajaxGalleryBlock').css({'visibility': 'hidden', 'display': 'fixed'}).height();
			$('#ajaxGalleryBlock').css({'visibility': '', 'display': ''});
			
			var elLeft = Math.round($(document).scrollLeft()+(document.documentElement.clientWidth-$('#ajaxGalleryBlock').width())/2)+'px';
			var elTop  = Math.round($(document).scrollTop()+(document.documentElement.clientHeight-hei)/2);
			
			if (elTop < ($(document).scrollTop()+10)) elTop = $(document).scrollTop()+10;
			$('#ajaxGalleryBlock').css({'top':elTop+'px','left':elLeft}).fadeIn();
			
			var wndHeight = (elTop + $('#ajaxGalleryBlock').height()) - $(document).height();
			if (wndHeight>0) wndHeight += $(document).height();
			else wndHeight = $(document).height();
			$('#shadow2, #shadow_left, #shadow_right').css('height', wndHeight+'px').show();

			return false;
		});
		
		
		// -- select begin
		var flag  = false;
		var index = 8;
		$('div.select').each(function() {
			var select = $('div.select[id='+$(this).attr('id')+']');
			select.css('z-index', index--);
			select.find('div.input').on('click', function() {
				$('div.select ul').hide();
				var ul = select.find('ul');
				ul.css('display', ((ul.css('display')=='block')?'none':'block'));
				$(this).blur();
				flag = true;
			});
			select.find('li').on('click', function() {
				select.find('div.input').text($(this).text());
				var input = select.find('input');
				var input_id = input.attr('id');
				if(input.attr('id') == 'change_floor_1') {
					$('#change_floor_1_insert').load('/main/load_room_list/', {'floor_str': $(this).text()});
				}
				if(input.attr('id') == 'change_floor_2') {
					$('#change_floor_2_insert').load('/main/load_room_list_free/', {'floor_str': $(this).text()});
				}
				input.attr('value', $(this).text());
				select.find('ul').toggle();
				select.find('li').removeClass('active');
				$(this).addClass('active').blur();
				flag = true;
			});
		});
		
		$('body').on('click', function() {
			if(!flag) {
				$('div.select ul').hide();
			}
			flag = false;
		});
		
		$('div.select ul li').on('mouseover', function() {
			$(this).addClass('hover');
		});
		
		$('div.select ul li').on('mouseout', function() {
			$(this).removeClass('hover');
		});
		// -- select end
		
		
		
		$('.text img').each(function() {
			if ( $(this).hasClass('nowrap') ) return;
			
			$(this).wrap('<div class="wrap_img"></div>');
			var title = $(this).attr('title');
			if (title != '') {
				$(this).after('<span class="img_note">'+title+'</span>');
			}
			
			if (  $(this).css('float') == 'left' ) {
				$(this).css('float','none');
				$(this).parent().addClass('small');
				$(this).parent().parent('p').addClass('small_picture');
			} else {
				$(this).parent().addClass('big');
			}
		});
		
		$('.text table.table').each(function() {
			$(this).find('tr').each(function(i, el) {
				if ( i == 0 ) $(this).addClass('header');
				if ( i % 2 == 0 ) {
					$(this).addClass('even');
				} else {
					$(this).addClass('odd');
				}
			});
		});
	});

	actions_ajax = function(page, pid) {	
		$.ajax({
			url:  '/popup/actions/actions_ajax/',
			type: 'POST',
	      	dataType : 'JSON',
	      	data: {
	      		'page':page,
	      		'pid':pid
	      	},

			success:  function(result) {
				$('.actions_content').html(result);
			}
		});
		return false;
	}

	productBlock_ajax = function(page, pid, cid) {	
		$.ajax({
			url:  '/popup/catalog/productBlock_ajax/',
			type: 'POST',
	      	dataType : 'JSON',
	      	data: {
	      		'page':page,
	      		'cid':cid,
	      		'pid':pid
	      	},

			success:  function(result) {
				$('.catalog .left_block').html(result);
				resizable_product_list($('.middle_wrap').width());
			}
		});
		return false;
	}

	news_ajax = function(page, pid) {	
		$.ajax({
			url:  '/popup/news/news_ajax/',
			type: 'POST',
	      	dataType : 'JSON',
	      	data: {
	      		'page':page,
	      		'pid':pid
	      	},

			success:  function(result) {
				$('.news_content').html(result);
			}
		});
		return false;
	}

	change_news_count_ajax = function(news_count, pid) {	
		$.ajax({
			url:  '/popup/news/news_ajax/',
			type: 'POST',
	      	dataType : 'JSON',
	      	data: {
	      		'news_count':news_count,
	      		'page':1,
	      		'pid':pid
	      	},

			success:  function(result) {
				$('.news_content').html(result);
			}
		});
		return false;
	}

	change_news_year_ajax = function(news_count, pid, year) {	
		$.ajax({
			url:  '/popup/news/news_ajax/',
			type: 'POST',
	      	dataType : 'JSON',
	      	data: {
	      		'news_count':news_count,
	      		'page':1,
	      		'pid':pid,
	      		'news_year':year
	      	},

			success:  function(result) {
				$('.news_content').html(result);
			}
		});
		return false;
	}

	faq_ajax = function(page, pid) {	
		$.ajax({
			url:  '/popup/faq/faq_ajax/',
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

	reviews_ajax = function(page, pid) {	
		$.ajax({
			url:  '/popup/reviews/reviews_ajax/',
			type: 'POST',
	      	dataType : 'JSON',
	      	data: {
	      		'page':page,
	      		'pid':pid
	      	},

			success:  function(result) {
				$('.questions').html(result);
			}
		});
		return false;
	}
	
	showImg = function(id) {
		var elLeft = Math.round($(document).scrollLeft()+(document.documentElement.clientWidth-690)/2)+'px';
		var elTop  = Math.round($(document).scrollTop()+(document.documentElement.clientHeight-690)/2)+'px';
		$('#shadow2, #shadow_left, #shadow_right').show();
		$('#ajaxGalleryBlock').css({'top':elTop,'left':elLeft}).fadeIn();
		
		makeMain(id);
	}
	
	replaceImg = function(id, o) {
		makeMain(id);
		$('#mainNote').html($(o).next('.img_note').html());			//?????????????????
		
		/*for(var i in photo_ids) {
			if(photo_ids[i] == id) {
				sliderCounter = i;
				break;
			}
		}*/
		return false;
	}
	
	prevImg = function(id) {
		for(var i in photo_ids) {
			if(photo_ids[i] == id) {
				prev = parseInt(i)-1;
				
				if(prev < 0) {
					prev = photo_ids.length-1;
				}				
				
				makeMain(photo_ids[prev]);
				$('#mainNote').html($('#scroll a[href=#'+photo_ids[prev]+']').next('.img_note').html()); //????????????????
				break;
			}
		}
		return false;
	}
	
	nextImg = function(id) {
		for(var i in photo_ids) {
			if(photo_ids[i] == id) {
				next = parseInt(i)+1;
				if(photo_ids[next] == undefined) {
					next = 0;
				}
				
				var t = next;
				if ( t > ($('#scroll ul li').length-4) ) {
					t = $('#scroll ul li').length-4;
				}
				if (sliderCounter != t) {
					sliderCounter = t;
					scroll_to();
				};
				
				makeMain(photo_ids[next]);
				$('#mainNote').html($('#scroll a[href=#'+photo_ids[next]+']').next('.img_note').html()); //??????????????????
				break;
			}
		}
		return false;
	}

	closeGallery = function() {
		$('#ajaxGalleryBlock').fadeOut();
		$('#shadow2, #shadow_left, #shadow_right').hide();
	}
	
	scroll_right = function() {
		if ( ($('#scroll ul li').length-4) == sliderCounter) {
			sliderCounter = 0;
		} else {
			sliderCounter++;
		}
		scroll_to();
	}
	
	scroll_left = function() {
		if(sliderCounter > 0) {
			sliderCounter--;
		} else {
			sliderCounter = ($('#scroll ul li').length-4);
		}
		scroll_to();
	}
	
	scroll_to = function() {
		var widthPreview = $('#scroll').find('li').width();
		$('#scroll ul').animate({
			marginLeft :'-'+(sliderCounter*(widthPreview))+'px'
		}, 200);
	}
	
	mousewheel1 = function(objEvent, intDelta){
		if (intDelta < 0){
			scroll_right();
		}
		else if (intDelta > 0){
			scroll_left();
		}
		return false;
	}

	
	images_ajax = function(page, pid, mid) {
		$.ajax({
			url:      '/all/images_ajax/'+pid+'/'+mid+'/'+page+'/',
			dataType: 'json',
			cache:    false,
			success:  function(res) {
				$('#galereya').replaceWith(res);
				window.location = '#galereya';
				dynamic_resolution();
				refresh_shadows();
			}
		});
		return false;
	}
})(jQuery);
