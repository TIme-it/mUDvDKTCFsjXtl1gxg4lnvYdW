(function($) {
	  helperSlideShowNext = function() {
	    var index = $('#helperSlideNavigation ul li').index($('#helperSlideNavigation ul li.active'));
		var count = $('#helperSlideNavigation ul li').length;
		var width = $('#helperSlideShow ul li:first').width();
		if(index<(count-1)){
		  $('#helperSlideShow ul').stop();
		  $('#helperSlideNavigation ul li').removeClass('active');
		  $('#helperSlideNavigation ul li:eq('+(index+1)+')').addClass('active');
		  $('#helperSlideShow ul').animate({marginLeft:(-1*(index+1)*width)+'px'},'slow');
		  $('#helperSlideText').html($('#helperSlideShow ul li:eq('+(index+1)+') .text').html());
		}
		return false;
	  }
	  helperSlideShowPrev = function(){
	    var index = $('#helperSlideNavigation ul li').index($('#helperSlideNavigation ul li.active'));
		var count = $('#helperSlideNavigation ul li').length;
		var width = $('#helperSlideShow ul li:first').width();
		if(!empty(index)){
		  $('#helperSlideShow ul').stop();
		  $('#helperSlideNavigation ul li').removeClass('active');
		  $('#helperSlideNavigation ul li:eq('+(index-1)+')').addClass('active');
		  $('#helperSlideShow ul').animate({marginLeft:(-1*(index-1)*width)+'px'},'slow');
		  $('#helperSlideText').html($('#helperSlideShow ul li:eq('+(index-1)+') .text').html());
		}
		return false;
	  }
	  
	  helperNavigationClick = function(index){
		  var count = $('#helperSlideNavigation ul li').length;
		  var width = $('#helperSlideShow ul li:first').width();
	      $('#helperSlideShow ul').stop();
		  $('#helperSlideNavigation ul li').removeClass('active');
		  $('#helperSlideNavigation ul li:eq('+(index)+')').addClass('active');
		  $('#helperSlideShow ul').animate({marginLeft:(-1*(index)*width)+'px'},'slow');
		  $('#helperSlideText').html($('#helperSlideShow ul li:eq('+(index)+') .text').html());
		  return false;
	  }

// функции
function empty( mixed_var ) {
	return ( mixed_var === "" || mixed_var === undefined || mixed_var === "undefined" || mixed_var === 0   || mixed_var === "0" || mixed_var === null  || mixed_var === false  ||  ( is_array(mixed_var) && mixed_var.length === 0 ) );
}
function in_array(needle, haystack, strict) {   // Checks if a value exists in an array
	var found = false, key, strict = !!strict;
	for (key in haystack) {
	  if ((strict && haystack[key] === needle) || (!strict && haystack[key] == needle)) {
		found = true;
		break;
	  }
	}
	return found;
}
function is_array( mixed_var ) {
	return ( mixed_var instanceof Array );
}

	function helperShowPage(element) {
		alert('!');
		// if(!empty(element) && !empty(element.slides)) {
			// $('#helperSlideShow ul').empty();
			// for(var i = 0; i < element.slides.length; i++) {
				// $('#helperSlideShow ul').append(
					// '<li><img src="'+element.slides[i].img+'" />' + 
						// (!empty(element.slides[i].text)?'<div class="text">'+element.slides[i].text+'</div>':'')+
					// '</li>'
				// );
			// }
		// }
	}

	function helperBuildMenu(arr, contentUl) {
		var items_arr = arr;
		var item;
		var items_count = items_arr.length;
		if(!empty(items_arr) && items_count>0) {
			for(var j = 0; j < items_count; j++) {
				item = items_arr[j];
				var item_block = $('<li></li>').appendTo(contentUl);
				$('<a href="#">'+item.title+'</a>').
					data('itemArr', item).
					appendTo(item_block);
				// alert(item.title);
				if(!empty(items_arr[j].childs)) {
					$(item_block).addClass('childs');
					$(item_block).append('<ul></ul>');
					item_childs_block = $(item_block).find('ul');
					helperBuildMenu(item.childs, item_childs_block);
				}
			}

		}
	}

function helperSlideshowPepare(){
	if($('#helperSlideNavigation ul').length>0)	$('#helperSlideNavigation ul').empty();
	else $('#helperSlideNavigation').append('<ul></ul>');
	$('#helperSlideText').empty();
	$('#helperSlideShow ul').css({marginLeft:0})
	if($('#helperSlideShow').length>0){//если есть контейнер
	    items = $('#helperSlideShow ul li').length;
		for(i=1;i<=items;i++){
		  $('#helperSlideNavigation ul').append('<li><a href="#" onclick="helperNavigationClick('+(i-1)+');return false;">'+i+'</a></li>');
		}
		$('#helperSlideNavigation ul li:first').addClass('active');
		$('#helperSlideText').html($('#helperSlideShow ul li:first .text').html());
	  }
}
function vertical_centering(el){
  var elTop  = Math.round($(document).scrollTop()+($(window).height()-$(el).height())/2)+'px';
  $(el).css('top',elTop).show();
}

	// показать помошника
	helper_show = function() {
		$.ajax({
			url:   '/admin/dweb_helper/',
			type:  'POST',
			cache: false,
			success: function(response) {
				$('body').prepend(response);
				
				var elTop = Math.round($(document).scrollTop()+($(window).height()-$('#helperOuter').height())/2);
				if (elTop < 10) elTop = 10;
				$('#helperOuter').css('top', elTop+'px').show();
				
				var wndHeight = (parseInt(elTop) + $('#helperOuter').height()) - $(document).height();
				if (wndHeight>0) wndHeight += $(document).height();
				else wndHeight = $(document).height();
				$('#shadow').css('height', wndHeight+'px').show();
				
				helper_activate();
			}
		});
	}
	
	helper_activate = function() {
	
		// обработка клавиш
		document.onkeydown = function(e) {
			if(e == null) {
				keycode = event.keyCode; 
			} else {
				keycode = e.which; 
			}
			if(keycode == 27) {
				$('#helperClose a').click();
			}
		}
	
		// обработка дочерних элементов для меню
		$('#helperMenuList li a').live('click', function() {
			var element = $(this).data('itemArr');
			
			if(!empty(element) && !empty(element.steps)) {
				$('#helperSlideShow ul').empty();
				for(i = 0; i < element.steps.length; i++) {
					$('#helperSlideShow ul').append(
						'<li><img src="' + element.steps[i].url + '" />' + 
							(!empty(element.steps[i].title) ? '<div class="text">'+element.steps[i].title+'</div>' : '') +
						'</li>'
					);
				}
				helperSlideshowPepare();
			} else {
				$('#helperSlideShow ul').empty();
			}
		
			if($(this).parent().hasClass('childs')) {
				var par = $(this).parent();
				$(par).find('ul').slideToggle('slow', function() {
					if(!$(par).hasClass('clicked')) {
						$(par).addClass('clicked');
					} else {
						$(par).removeClass('clicked');
					}
				});
			} else {
				$('#helperMenuList li.active').removeClass('active');
				$(this).parent().addClass('active');
			}
			
			return false;
		});
			
		// подготовка
		if(!empty(helperCode)){
			helperBuildMenu(helperCode,$('#helperMenuList'));
		}
		
		// предподготовка слайдшоу 
		helperSlideshowPepare();
  
		// обработка кнопки закрыть
		$('#helperClose a').live('click', function() {
			$('#shadow').hide();
			$('#helperOuter').remove();
			$('#helperMenuList li a').die();
			$('#helperSlideShow ul li img').die(); 
			$('#helperSlideShowRight,#helperSlideEnd').die();
			$('#helperSlideShowLeft,#helperSlideStar').die();			
			$('#helperClose a').die();								
			return false;
		});
  
		$('#helperSlideShow ul li img').live('click', function() {
			helperSlideShowNext();
		});
		
		// клики по ссылкам
		$('#helperSlideShowRight,#helperSlideEnd').live('click', helperSlideShowNext);
		$('#helperSlideShowLeft,#helperSlideStart').live('click', helperSlideShowPrev);
				
	}
	
	$('span.help').live('click', function(){		
		var tree = new Array();
		tree = $(this).attr('rel').split(':');
		helper_show();
		setTimeout(function() {
			var list = $('#helperMenuList > li:eq('+(parseInt(tree[0]) - 1)+')');	
			$(list).find('a:first').click();
			if ($(list).hasClass('childs')) {
				$(list).find('ul:first li:eq('+(parseInt(tree[1]) - 1)+') a').click();
			}
		}, 1000);
		return false;			
	});
})(jQuery);