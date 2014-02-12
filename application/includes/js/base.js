var sliderCounter = 0;
var images = [];

var img_big_w = 640;
var img_big_h = 480;

(function($) {
	
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
	}

	$(document).ready(function() {

		// Заказ товара
		$('a.order_button').on('click', function(){
			var link = $(this);
			$('.fancy').fancybox({
				beforeShow   : function(){
					var title = link.data('title');
					var id = link.data('id');
					$('.fancybox-inner #question').html('Здравствуйте, меня интересует товар "' + title + '".');
					$('.fancybox-inner input[name="id"]').val(id);
	   			}
			});
		})

		// Карточка товара. Изображения товара
		$('.add_img a').on('click', function(){
			type = $(this).data('imgtype');
			id = $(this).data('id');
			$('.add_img').find('.active').removeClass('active');
			$(this).addClass('active');
			if(type == 'orig'){
				$('.item_img').html('<img src="/application/includes/catalog/catalog_product/b/'+ id +'.png" alt="">')
			}
			else {
				$('.item_img').html('<img src="/application/includes/catalog/catalog_product/d/b/'+ id +'.png" alt="">')
			}
			return false;
		})

		// Галерея на странице
		$('.wrap a').on('click', function(){
			id = $(this).data('id');
			$('.wrap').find('.active').removeClass('active');
			$(this).addClass('active');
			$('.gallery .item_img').html('<img src="/application/includes/img/b/'+ id +'.jpg" alt="">')
			resizable_gallery($('.middle_wrap').width());
			return false;
		})

		// календарь в форму обратной связи 
		$.datepicker.setDefaults(
			$.extend($.datepicker.regional["ru"])
		);
		$("#date_td").datepicker();

		// Отобразить форму заказа в футере 
		$('.footer_block .to_call').on('click', function(){
			$('.footer_block .to_call_form').stop().fadeIn("slow", function() {
				$(this).css('display', 'block');
		 	});
		})

		// Скрыть форму заказа в футере 
		$('.footer_block .to_call.close').on('click', function(){
			$('.footer_block .to_call_form').stop().fadeOut("slow", function() {
				$(this).css('display', 'none');
		 	});
		})

		// Кнопка отправки формы в футере
		$('.to_call_form a.credit_button').on('click', function(){
			$(this).parents('form').submit();
			return false;
		})

		// Кнопка направления на страницу с обратной связью (с заполненным полем "Модель")
		$('.testdrive_bbutton, .testdrive_button, .credit_button, .credit_bbutton').on('click', function(){
			$(this).parents('form').submit();
			return false;
		})

		var count_item = $('#previews .item').size();		
		var step = 175;

		$('#previews #wrap').css("width", count_item * step + "px");
		var api = $('.scroll-pane').jScrollPane({}).data('jsp');

		$('#previews #leftArrow').on('click', function() {			
			api.scrollByX(-step, 1000);
			return false;
		});
		
		$('#previews #rightArrow').on('click', function() {
			api.scrollByX(step, 1000);	
			return false;    
		});
		
		// Кнопки дилерский/сервисный центр
		$('#header_col3 a').on('click', function(){
			hide_obj = $('#header_col3 a.active');
			hide_obj.removeClass(' active');
			hide_class = hide_obj.attr('class')+'_info';
			
			show_obj = $(this);
			show_class = show_obj.attr('class')+'_info';
			show_obj.addClass('active');


			$('#header_col3 div.'+hide_class).css('display', 'none');
			$('#header_col3 div.'+show_class).css('display', 'block');
			return false;
		})

		// Кнопка заказать звонок
		$('.footer_block .to_call').on('click', function(){
			return false;
		})

		slider_init($(' .slider_link').data('id'));

		
		/* MENU BLOCK BEGIN */
		$('.main_menu').on('mouseout',function(){
			$(this).find('.submenu').css('display', 'none');
		})
		$('.main_menu').on('mouseover',function(){
			$(this).find('.submenu').css('display', 'block');
		})
		
		$('.menu_block').on('mouseleave',function(){
			$(this).find('.preactive .submenu').css('display', 'block');
			$(this).find('.parent.active .submenu').css('display', 'block');
		})
		$('.menu_block a.m_it').on('mouseenter',function(){
			$(this).parents('.menu_block').find('.preactive .submenu').css('display', 'none');
			$(this).parents('.menu_block').find('.parent.active .submenu').css('display', 'none');

		})

		/* MENU BLOCK END */


		intervalID = setInterval(function(){
			$('.right_arrow').trigger('click');
		}, 5000)


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
			clearInterval(intervalID);
			intervalID = setInterval(function(){
				$('.right_arrow').trigger('click');
			}, 5000)
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
			clearInterval(intervalID);
			intervalID = setInterval(function(){
				$('.right_arrow').trigger('click');
			}, 5000)
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
		//тест фансибокса
		// $('.fancy').fancybox();
		
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
		$('#preview ul').css('width', ($('#scroll li').length*150)+'px');		
		
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
