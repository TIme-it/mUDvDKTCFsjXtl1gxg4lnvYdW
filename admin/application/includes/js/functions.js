function DeleteImg(){
	$('.del_img').attr('disabled', 'disabled');
	id = $(this).data('id');
	pid = $(this).data('pid');
	$.ajax({
		url: '/admin/catalog/del_img_ajax/' + id + '/' + pid + '/',
		type: 'POST',
		beforeSend : function(){

		},
		success : function(result){
			console.log(result);
			$(' .uploaded_img').html(result);
		}
	})
	return false;
}


(function($) {
	var img = new Image();
	img.src = '/admin/application/includes/images/delete.gif';
	
	var sliders = 0;
	var arr = new Array();
	
	arr['а'] = 'a'; arr['б'] = 'b'; arr['в'] = 'v';
	arr['г'] = 'g'; arr['д'] = 'd'; arr['е'] = 'e';
	arr['ё'] = 'e'; arr['ж'] = 'j'; arr['з'] = 'z';
	arr['и'] = 'i'; arr['й'] = 'i'; arr['к'] = 'k';
	arr['л'] = 'l'; arr['м'] = 'm'; arr['н'] = 'n';
	arr['о'] = 'o'; arr['п'] = 'p'; arr['р'] = 'r';
	arr['с'] = 's'; arr['т'] = 't'; arr['у'] = 'u';
	arr['ф'] = 'f'; arr['х'] = 'h'; arr['ц'] = 'c';
	arr['ч'] = 'ch'; arr['ш'] = 'sh'; arr['щ'] = 'csh';
	arr['ь'] = ''; arr['ы'] = 'y'; arr['ъ'] = '';
	arr['э'] = 'e'; arr['ю'] = 'yu'; arr['я'] = 'ya';
	arr[' '] = '_'; arr['/'] = '_'; arr['"'] = '';
	arr["'"] = '';

	$(document).ready(function() {

		// Кнопка удаления изображения
		$('.del_img').live('click', DeleteImg);
		//////

		// Кнопка загрузки нескольких файлов
		$('.add_file').live('click', function(){
			$(this).parents('#multiload_file').find('#dl_file').trigger('click');
			return false;
		})

		$('#dl_file').live('change', function(){
			$(this).parents('#multiload_file').after($(this).parents('#multiload_file').clone())
			$(this).parents('#multiload_file').find('.add_file').hide();
			$(this).parents('#multiload_file').find('.file_name').show();
			$(this).parents('#multiload_file').find('span.filename').html($(this).val());
		})

		$('.delfile').live('click', function(){
			$(this).parent('div').after($(this).parents('#multiload_file').remove())
			return false;
		})
		//////


		$('.del_techchar').live('click',function(){
			id = $(this).attr('rel')
			$.ajax({
				url: '/admin/catalog/del_techchar/'+id,
				type:'GET'				
			})
			$(this).parents('tr').fadeOut();
			return false;
		})
		
		$('.add_category_techchar').live('click',function(){						
			category = $(this).attr('rel');			
			$.ajax({
				url: '/admin/catalog/add_catalog_techchar/',
				type:'POST',
				data:{'category_id': category,
					'techchar_id':$('#addTechchars').val()
				},
				success:function(){
					showTechList(category);					
				}
			})			
			return false;			
		})

		$('.add_catalog_techchar').live('click',function(){						
			catalog = $(this).attr('rel');			
			$.ajax({
				url: '/admin/catalog/add_main_techchar/',
				type:'POST',
				data:{'catalog_id': catalog,
					'techchar_id':$('#addTechchars').val()
				},
				success:function(){
					showCatTechList(catalog);					
				}
			})			
			return false;			
		})
		
		$('.del_category_techchars').live('click',function(){						
			id = $(this).attr('rel')
			$.ajax({
				url: '/admin/catalog/del_catalog_techchar/'+id,
				type:'GET'				
			})
			$(this).parents('tr').fadeOut();				
			return false;
		})
		
		$('.del_catalog_techchars').live('click',function(){						
			id = $(this).attr('rel')
			$.ajax({
				url: '/admin/catalog/del_main_techchar/'+id,
				type:'GET'				
			})
			$(this).parents('tr').fadeOut();				
			return false;
		})
		
		showTechList = function(category){
			$.ajax({
				url: '/admin/catalog/get_techchar_list/'+category,
				type:'POST',
				success:function(result){
					$('#sortable_area').html(result);
				}
			})	
		}
		
		showCatTechList = function(catalog){
			$.ajax({
				url: '/admin/catalog/get_catalog_techchar_list/'+catalog,
				type:'POST',
				success:function(result){
					$('#sortable_area').html(result);
				}
			})	
		}
		
		/*  -- групповое удаление в списках (2.02b)
			-- связывает кнопку удаления c чекбоксами:
			-- 1. есть выбранные чексоксы / нет -> показывается кнопка / нет,
			-- 2. вопрос подтверждения удаления +  количество выбранных элементов */
		$('.group_delete').each(function() {
			var button    = $(this);
			var button_id = $(this).attr('id');
			// -- live('click') - позднее связывание
			$('.'+button_id).live('click', function() {
				var checked_count = $('.'+button_id+':checked').length;
				if(checked_count > 0) {
					button.removeAttr('disabled');
				} else {
					button.attr('disabled', 'disabled');
				}
			});
			// -- click() - ранее связывание
			button.click(function() {
				var checked_count = $('.'+button_id+':checked').length;
				return confirm('Вы действительно хотите удалить ('+checked_count+' шт.)?');
			});
		});
		
		// -- ajax версия для изображений
		$('.group_delete_ajax').each(function() {
			var button    = $(this);
			var button_id = $(this).attr('id');
			// -- live('click') - позднее связывание
			$('.'+button_id).live('click', function() {
				var checked_count = $('.'+button_id+':checked').length;
				if(checked_count > 0) {
					button.removeAttr('disabled');
				} else {
					button.attr('disabled', 'disabled');
				}
			});
			// -- click() - ранее связывание
			button.click(function() {
				var checked_count = $('.'+button_id+':checked').length;
				if(confirm('Вы действительно хотите удалить ('+checked_count+' шт.)?')) {
					$(this).attr('disabled', 'disabled');
					var type = $(this).attr('rel');
					var ids = new Array();
					$('.'+button_id+':checked').each(function() {
						id = $(this).val();
						ids.push(id);
						$('#mu_point_'+type+'_'+id).remove();
					});
					count = $('#'+type+'_list li').length;
					$('#'+type+'_list_count').html(count > 0 ? '(Добавлено <i>'+count+'</i> фото)' : '');
					$.ajax({
						url:   '/admin/application/'+button_id+'/',
						type:  'POST',
						data:  {'ids':ids.join()},
						cache: false,
						success: function(response) { }
					});
				}
			});
		});
		
		// -- айакс версия групповых экшенов для масс-аплодинг-сущностей (страшная вещь)
		$('.group_button').each(function() {
			var button    = $(this);
			var button_id = $(this).attr('id');
			// -- live('click') - позднее связывание
			$('.'+button_id).live('click', function() {
				var checked_count = $('.'+button_id+':checked').length;
				if(checked_count > 0) {
					button.removeAttr('disabled');
				} else {
					button.attr('disabled', 'disabled');
				}
			});
			// -- click() - ранее связывание
			button.click(function() {
				var data = $(this).attr('rel').split(':');
				var checked_count = $('.'+button_id+':checked').length;
				if(confirm('Вы действительно хотите '+data[1]+' ('+checked_count+' шт.)?')) {
					$(this).parent().find('input').attr('disabled', 'disabled');
					var ids = new Array();
					$('.'+button_id+':checked').each(function() {
						id = $(this).val();
						ids.push(id);
						if(data[2] == 'delete') {
							$('#mu_point_'+data[0]+'_'+id).remove();
						}
					});
					if(data[2] == 'delete') {
						count = $('#'+data[0]+'_list li').length;
						$('#'+data[0]+'_list_count').html(count > 0 ? '(Добавлено <i>'+count+'</i> '+data[3]+')' : '');
					}
					$.ajax({
						url:   '/admin/application/'+button_id+'/'+$('#page_id').val()+'/'+$('#module_id').val()+'/',
						type:  'POST',
						data:  {'ids':ids.join()},
						cache: false,
						success: function(response) {
							switch(data[2]) {
								case 'show':
								case 'hide':
									$('#'+data[0]+'_list').html(response);
									count = $('#'+data[0]+'_list li').length;
									$('#'+data[0]+'_list_count').html(count > 0 ? '(Добавлено <i>'+count+'</i> '+data[3]+')' : '');
									break;
							}
						}
					});
				}
			});
		});
		
	
		
		/*  -- календарик (2.00a)
			-- назначение календаря на ID: date_input, date_input_2
			-- кнопки календаря на ID:     datepicker_button, datepicker_button_2
			-- версия (2.02b)
		*/
		$.datepicker.setDefaults(
			$.extend($.datepicker.regional["ru"])
		);
		$("#date_input").datepicker();
		$("#date_input #datepicker_button").datepicker();
		$("#date_input #datepicker_button").live('click', function () {
			$("#date_input").focus();
			return false;
		});
		$("#date_input_2").datepicker();
		$("#date_input_2 #datepicker_button").datepicker();
		$("#date_input_2 #datepicker_button").live('click', function () {
			$("#date_input_2").focus();
			return false;
		});
		
		// -- поключения файла списка картинок
		$.getScript('/admin/application/includes/js/tiny/lists/image_list.js?'+Math.random());
		
		$(".list tr:nth-child(even)").css("background","#f0f0f0");
		$(".lists li:nth-child(even)").css("background","#f0f0f0");

		
		$('#banners_category').bind('change',function() {
			var val = $(this).val();
			$.getJSON('/admin/banner/getBannersSize/'+val+'/',function(response) {	
				$('#banner_width').attr('value',response.width);
				$('#banner_height').attr('value',response.height);
			})
			$(this).parent().next().removeClass('display_none');
		});
		
		$('#shadow').css('height', $('#main').outerHeight());
		
		$('.head_title, .head_link').live('change',function() {
			var value = $(this).val();
			var id = $(this).parent().attr('title');
			var name = $(this).attr('name');
			$.ajax({
				url: '/admin/config/headTitle/'+id+'/',
				type:'POST',
				data:name+'='+value,
				cache:false,
				success: function(response) {}
			})
		});
		
		// -- обработка "Посмотреть шаблон" для "Выберите шаблон"
		$('#template').bind('change',function() {
			var link = $('#templateLink').attr('href');
			link = link.split('/');
			link.pop();
			link.push($(this).val());
			link = link.join('/');
			$('#templateLink').attr('href',link+'.png');
		});
		
		
		//Выпадающий блок на форме
		$('.toggler').each(function() {
			$(this).addClass('plus3');
			var togg = $(this).next('div');
			if (togg.length == 0) togg = $(this).next().next('div');	//Для раздела помощи
			
			togg.css({
				'display': 'none',
				'padding-top':'4px',
				'padding-left':'12px',
				'padding-bottom': '4px',
			});
			
			$(this).click(function() {
				if($(this).hasClass('plus3')) {
					$(this).removeClass('plus3').addClass('minus3');
				} else if($(this).hasClass('minus3')) {
					$(this).removeClass('minus3').addClass('plus3');
				}
				
				togg.slideToggle(300);
				return false;
			});
		});
	});
	
	deleteHeadImage = function(id,object) {
		$.ajax({
			url: '/admin/config/deleteHeadImage/'+id+'/',
			type:'GET',
			cache:false,
			success: function(response) {
				$(object).parent().remove();
			}
		})
	}
	
	changeTitle = function(obj) {
		var val = $(obj).val()
		$(this).parent().append('<span class="head_title">'+val+'</span>');
		$(this).remove();
	}
	
	translit = function(str) {
		var i = 0;
		str = str.toLowerCase();
		var result ='';
		while (i < str.length) {
			result +=(arr[str.substr(i,1)]==undefined)?str.substr(i,1):arr[str.substr(i,1)];
			i++;
		}
		$('#url').attr("value",result);
	}
	
	calculate_cost = function(object) {
		var cost_m2 = parseInt($(object).val());
		var parent = $(object).parent();
		var square = parseInt(parent.prev().find('input').val());
		if (cost_m2 !== 'undefined' && square !== 'undefined') {
			var cost = cost_m2*square;
			parent.next().find('input').attr('value',cost);
		}
	}
	
	slide = function(object) {
		var li = $(object).parent().next();
		li.show();
		li.find('ul:first').slideToggle('slow',function(){
			if($(this).css('display') != 'block') {
				$(this).parent().hide();
			}
			$(object).removeAttr('onclick');
			$(object).toggleClass('minus');
		});
	}
	
	show_field = function(object) {
		var field;
		field = $(object).attr('name');
		if(($('span[title="'+field+'"]').css('display') == 'inline') || ($('span[title="'+field+'"]').hasClass('inline_display'))) {
			$('span[title="'+field+'"]').removeClass('inline_display');
			$('span[title="'+field+'"]').css('display','none');
			}
		else 
			$('span[title="'+field+'"]').css('display','inline');
	}
	
	getMenu = function(id, object) {
		if(object.hasClass('plus')) {
			object.removeClass('plus').addClass('minus');
			$('#childs_'+id).slideToggle('slow');
		} else if(object.hasClass('minus')) {
			object.removeClass('minus').addClass('plus');
			$('#childs_'+id).slideToggle('slow');
		}
		return false;
	}

	getTestMenu = function(id,active_id) {
		var object;
		$.ajax({
			url: '/admin/menu/ajax_menu/'+id+'/'+active_id+'/',
			type:'GET',
			cache:false,
			success: function(response) {
				$('#pid_'+id).after('<li><ul>'+response+'</ul></li>');
				object = $('#pid_'+id+' a:first');
				$(object).bind('click',function() {
					slide(object);
				});
				slide(object);
			}
		});
	}

	AjaxDelete = function(table, id, pid, module_id, obj_id) {
		var count = $('#'+obj_id+'_list_count i').text();
		var page_id = $('#page_id').val();
		var module = $('#module_id').val();
		count--;
		$('#'+obj_id+' .loading_img').show();
		if(obj_id+'_list' == 'photos_list') pid += pid + '/0';
		$.ajax({
			url: '/admin/pages/delete/'+table+'/'+id+'/'+module_id+'/'+pid,
			type:'GET',
			cache:false,
			success: function(response) {
				if(count == 0) $('#'+obj_id+'_list_count').remove();
				else $('#'+obj_id+'_list_count i').html(count);
				$('#'+obj_id+'_list').slideUp('slow',function() {
					$('#'+obj_id+'_list_link').toggleClass('minus2');
				});
				$('#'+obj_id+'_form').hide();
				setTimeout(function() {
					$.getScript('/admin/application/includes/js/tiny/lists/image_list.js?'+Math.random());
					listing(page_id,response,module);
				},700);
			}
		});
	}
	
	closeWin = function() {
		$('.popup').fadeOut();
		$('#placemark').hide();
		$('#shadow').hide();
	}

	add_module = function(action) {
		var alias;
		alias = $('#url').val();
		if($('#title').val() == '') {
			alert('Вы не ввели название страницы');
		} else {
			$.ajax({
			url: '/admin/module/exist_alias/'+alias+'/'+action+'/',
			type:'GET',
			cache:false,
			success: function(response) {
				if(response == 'true') {
					alert('Страница с таким названием уже существует');
				} else {
					$('#module_form').append('<input type="hidden" name="submt" />');
					$('#module_form').submit();
				}
			}
			});
		}
	}

	listing = function(id, mode, module_id) {
		var obj = '#'+mode+'_list'; 

		if($(obj).css('display') != 'block') {
			$.ajax({
				url: '/admin/application/listing/'+id+'/'+mode+'/'+module_id+'/',
				type:'GET',
				cache:false,
				success: function(response) {
					$(obj).html(response).slideDown('slow',function() {
						$('#'+mode+'_list_link').toggleClass('minus2');
					});
					$('#'+mode+'_form').slideDown('slow');
				}
			});
		}
	}

	getDate = function(day,month,year) {
		var value = year+'-'+(month+1)+'-'+day;
		$('#date_input').val(value);
		$('#date_picker').slideUp('hide');
	}

	changeField = function(table,field,value,id) {
		$.ajax({
			url: '/admin/pages/changeField/'+table+'/'+id+'/',
			type: 'POST',
			cache: false,
			data: field+'='+value,
			success: function(response) {}
		})
	}
	
	delete_image_main = function(id) {
		$.ajax({
			url: '/admin/config/delete_image_main/'+id+'/',
			type: 'GET',
			cache: false,
			success: function(response) {
				$('#main_images').html(response);
			}
		})
	}
	
	sort_this = function(id) {
		$.ajax({
			url: '/admin/structure/'+id+'/1/',
			type: 'GET',
			cache: false,
			success: function(response) {
				$('#sortable_area').html(response);
			}
		})
	}
	

	catalog_search = function(type,vendor,value) {
		$.ajax({
			url: '/admin/catalog/search/'+type+'/'+vendor+'/'+value+'/',
			type: 'GET',
			cache:false,
			success: function(response) {
				$('.listing').html(response);
			}
		})
	}
	
	addSortable = function(uid, pid, dis) {
		$(function() {
			$('#sortable_'+uid).sortable({
				placeholder: 'ui-state-highlight',
				stop: function(event, ui) {
					var arr = new Array();
					var i   = 0;
					$('#sortable_'+uid+' li').each(function() {
						attr_id = $(this).attr('id');
						attr_id = attr_id.split('_');
						arr[i++] = attr_id[attr_id.length-1];
					});
					$.ajax({
						url:   '/admin/application/ajaxsort/'+uid+'/'+pid+'/',
						type:  'POST',
						cache: false,
						data:  'data='+arr,
						success: function(response) { }
					});
				}
			});
			if(dis) {
				$('#sortable_'+uid).disableSelection();
			}
		});
	}
	
	file_rename = function(filename, ext, o) {
		if(newname = prompt('Переименовать файл', filename)) {
			$.ajax({
				url:   '/admin/application/file_rename/'+$(o).attr('href').substring(1)+'/',
				type:  'POST',
				cache: false,
				data:  {'filename' : newname},
				success: function(response) { }
			});
			$(o).text(newname+'.'+ext);
			$(o).attr('onClick', $(o).attr('onClick').replace(filename, newname));
		}
		return false;
	}
	
	file_delete = function(id) {
		if(!confirm('Вы уверены что хотите удалить этот файл?')) return false;
		var page_id = $('#page_id').val();
		var module  = $('#module_id').val();
		$.ajax({
			url:   '/admin/application/delete_files/'+page_id+'/'+module+'/',
			type:  'POST',
			cache: false,
			data:  {'ids': id},
			success: function(response) { }
		});
		$('#mu_point_files_'+id).remove();
		var count = $('#sortable_files li').length;
		$('#files_list_count').html(count > 0 ? '(Добавлено <i>'+count+'</i> файлов)' : '');
		return false;
	}
	
	
	files = function(action) {
		if($('#files_action_form .checkbox:checked').length == 0) {
			alert('Для выполнения этого действия требуется отметить файлы галочкой');
			return false;
		}
		if(action == 'delete' && !confirm('Вы действительно хотите удалить эти файлы?')) {
			return false;
		}
		var page_id = $('#page_id').val();
		var module  = $('#module_id').val();
		$.ajax({
			url:   '/admin/application/ajaxfiles/'+action+'/'+page_id+'/'+module+'/',
			type:  'POST',
			cache: false,
			data:  $('#files_action_form').serialize(),
			success: function(response) {
				$('#files_list').html(response);
				var count = $('#sortable_files li').length;
				$('#files_list_count').html(count > 0 ? '(Добавлено <i>'+count+'</i> файлов)' : '');
			}
		});
		return false;
	}
	
	screen_nav = function(num) {
		$('#nav li a').removeClass('act');
		$('#nav li a#num_'+num).addClass('act');
		$('.listing').hide();
		$('#listing_'+num).show();
	}
	
	config_form = function(num) {
		$('#bookmarks_config li a').removeClass('act');
		$('#bookmarks_config li a#num_'+num).addClass('act');
		$('.config_form').hide();
		$('#form_'+num).show();
		return false;
	}
	
	callbackfn = function() {
		alert('!');
	}
	
	gallery_header_change = function(me, pid, mid) {
		var value = (me.attr('checked'));
		if (value==true) value = 1;
		else value = 0;
		
		$.ajax({
			url:      '/admin/addmodules/update_gallery_header/'+pid+'/'+mid+'/'+value+'/',
			cache:    false,
			success:  function(res) {
			}
		});
	};
	
	navigate = function(self, page, module) {
		var pid = $('#pid').val();
		var center = $('#center_id').val();
		
		$.ajax({
			url: '/admin/'+module+'/ajax_navigate/'+pid+'/'+center+'/'+page+'/',
			type: 'GET',
			cache: false,
			dataType: 'json',
			success: function(res) {
				if (res) {
					self.parent().parent().find('a').removeClass('act');
					self.addClass('act');
					
					$('#navigate_'+module).replaceWith(res);
				}
			}
		});
		return false;
	}
	
})(jQuery);