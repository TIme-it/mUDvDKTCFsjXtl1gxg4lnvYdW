var map = false;
var map_points = [];
var tmp_point = false;
	
(function($) {	
	var inner_points = [];
	
	$(document).ready(function() {
		//Удаление карты
		$('.maps_list .delete_button').live('click',function() {
			if (!confirm('Вы действительно хотите удалить эту карту?')) return false;
			var self = $(this).parent();
			$.ajax({
				url: '/admin/maps/delete_map/'+$(this).parent().attr('rel'),
				cache: false,
				success: function() {
					$('#yandex_content').html('');
					self.remove();
				}
			});
			return false;
		});
		
		
		//Редактирование карты
		$('.maps_list .edit_map').live('click',function() {
			edit_map($(this).parent().attr('rel'));
		});
		
		
		//Редактирование точки
		$('.marks_list .mark_edit').live('click',function() {
			var index = parseInt($(this).attr('rel'))-1;
			
			//Восстанавливаем несохраненные положения
			for (i in map_points) {
				inner_points[i].setGeoPoint(new YMaps.GeoPoint(map_points[i].lat, map_points[i].lon));
				inner_points[i].setOptions({draggable: false});
			}
			
			//Возможность двигать выбранную точку
			inner_points[index].setOptions({draggable: true});
			
			if (tmp_point !== false) {
				map.removeOverlay(tmp_point);
				tmp_point.destroy;
				tmp_point = false;
			}
			
			//Запрос формы редактирования
			var self = $(this);
			$('.yamap_table .edit_list').html('');
			$.ajax({
				url: '/admin/maps/getMarkEdit/',
				dataType: 'json',
				cache: false,
				success: function(res) {
					$('.yamap_table .edit_list').html(res);
					
					$('#yamap_desc').val(map_points[index].not);
					
					//Реинициализируем tiny
					tinyMCE.init({
						mode : "exact",
						elements: "note_id,note_add_1,note_add_2,note_add_3,note_add_4,note_add_5,note_add_6,note_add_7,yamap_desc",
						theme : "advanced",
						language: "ru",
						width: "430",

						theme_advanced_buttons1 : "bold,link,unlink",
						theme_advanced_buttons2 : "",
						theme_advanced_buttons3 : "",

						content_css : "/admin/application/includes/css/content.css"
					});
					
					$('#save_point').attr('rel', self.parent().attr('rel')+'#'+index);
					
					map.panTo(new YMaps.GeoPoint(map_points[index].lat, map_points[index].lon));
					$('#map_title').val(map_points[index].tit);
				}
			});
			return false;
		});
		
		
		//Удаление точки
		$('.marks_list .delete_button').live('click', function() {
			if (!confirm('Вы действительно хотите удалить эту точку?')) return false;
			
			var index = $(this).next().attr('rel');
			var self = $(this).parent();
			$.ajax({
				url: '/admin/maps/delete_mark/'+$(this).parent().attr('rel'),
				cache: false,
				success: function() {
					self.remove();
					map.removeOverlay(inner_points[index]);
				}
			});
			return false;
		});
	});

	
	//Загрузка API или перерисовка карты
	open_map = function() {
		if ($('#YMapsID').length == 0) return false;
		
		if (map !== false) {
			map.redraw();
			return false;
		}
		YMaps.load(yamaps_init);
	}
	
	yamaps_init = function() {
		// Создает экземпляр карты и привязывает его к созданному контейнеру
		map = new YMaps.Map($("#YMapsID").get(0));

		// Устанавливает центр и масштаб карты
		var center;
		if (YMaps.location) {
			center = new YMaps.GeoPoint(YMaps.location.longitude, YMaps.location.latitude);
		} else {
			center = new YMaps.GeoPoint(37.6, 55.7);
		}
		
		
		map.addControl(new YMaps.TypeControl());
		map.addControl(new YMaps.ToolBar());
		map.addControl(new YMaps.Zoom());
		map.addControl(new YMaps.ScaleLine());
		map.disableDblClickZoom();
		
		var point;
		inner_points = [];
		if (map_points.length > 0) {
			for (i in map_points) {
				point = new YMaps.Placemark(new YMaps.GeoPoint(map_points[i].lat, map_points[i].lon), {balloonOptions :{ maxWidth:250, maxHeight:180}});
				point.setIconContent(map_points[i].tit);
				point.setBalloonContent(map_points[i].not);
				
				inner_points.push(point);
				map.addOverlay(point);
			}
			map.setCenter(inner_points[0].getGeoPoint(), 15);
		} else map.setCenter(center, 10);
	}
	
	
	//Возвращает точку по адресу
	geo_point = function(request, callback) {
		// искать все объекты с именем Москва, но вывести только первый
		var geocoder = new YMaps.Geocoder(request);
		YMaps.Events.observe(geocoder, geocoder.Events.Load, function () {
			if (this.length()) {
				callback += '(request, this.get(0))';
				with (this) {
					eval(callback);
				}
			} else {
				alert("Ничего не найдено");
			}
		})
		YMaps.Events.observe(geocoder, geocoder.Events.Fault, function (geocoder, errorMessage) {
			alert("Произошла ошибка: " + errorMessage);
		});
	}
	
	//Показ временной точки
	ShowTmp = function(request, point) {
		if (tmp_point != false) {
			map.removeOverlay(tmp_point);
		}
		
		//Ставим временную точку
		tmp_point = new YMaps.Placemark(point.getGeoPoint(), {draggable: true, balloonOptions :{ maxWidth:250, maxHeight:180}});
		tmp_point.setIconContent($('#map_title').val());
		var iFrameDOM = $('#yamap_desc_ifr').contents();
		var descript = iFrameDOM.find('body').html();
		tmp_point.setBalloonContent(descript);
		
		map.addOverlay(tmp_point);
		map.setCenter(tmp_point.getGeoPoint(),15);
		
		$('#make_point').attr('disabled', '');
	}
	
	
	//Редактирование карты
	edit_map = function(map_id) {
		if (tmp_point !== false) {
			map.removeOverlay(tmp_point);
			tmp_point.destroy;
			tmp_point = false;
		}
		
		$.ajax({
			url: '/admin/maps/edit_map/'+map_id,
			dataType: 'json',
			cache: false,
			success: function(res) {
				$('#yandex_content').html(res);
				
				map = false;
				open_map();
			}
		});
	}
	
	
	//Установка временной точки как постоянной
	make_Ypoint = function() {
		if (tmp_point == false) return;
		
		var title = $('#map_title').val();
		var iFrameDOM = $('#yamap_desc_ifr').contents();
		var note = iFrameDOM.find('body').html();
		
		var p = tmp_point.getGeoPoint();
		var lat = p.getX();
		var lon = p.getY();
		
		var index = map_points.length;
		inner_points.push(tmp_point);
		tmp_point.setOptions({draggable: false});
		
		var point = {	lat: lat, 
						lon: lon,
						tit: title,
						not: note};
		map_points.push(point);
		
		
		$.ajax({
			url: '/admin/maps/addMark/',
			type: 'post',
			data: {'pid':$('#yamap_id').val(), 'title':title, 'note':note, 'lat':lat, 'lon':lon},
			cache: false,
			dataType: 'json',
			success: function(res) {
				$('.marks_list ul').append(
					'<li rel="'+res+'"><a class="delete_button" href="javascript: void(0)"></a><a rel="'+(index+1)+'" class="mark_edit" href="javascript: void(0)">'+title+'</a></li>'
				);
				$('.yamap_table .edit_list').html('');
				alert('Изменения сохранены');
			}
		});
		
		//Очищаем форму
		$('#map_address').val('');
		$('#map_title').val('');
		$('#make_point').attr('disabled', 'disabled');
		iFrameDOM.find('body').html('');
		

		tmp_point = false;
	}
	
	//Сохранение точки
	save_Ypoint = function(self, header) {
		var mark_id = self.attr('rel').split('#');
		var index = mark_id[1];
		mark_id = mark_id[0];
		
		var title = $('#map_title').val();
		var iFrameDOM = $('#yamap_desc_ifr').contents();
		var note = iFrameDOM.find('body').html();
		
		inner_points[index].setIconContent(title);
		inner_points[index].setBalloonContent(note);
		
		var p = inner_points[index].getGeoPoint();
		var lat = p.getX();
		var lon = p.getY();
		
		map_points[index].lat = lat;
		map_points[index].lon = lon;
		
		$.ajax({
			url: '/admin/maps/saveMark/',
			type: 'post',
			data: {'id': mark_id, 'title':title, 'note':note, 'lat':lat, 'lon':lon},
			cache: false,
			dataType: 'json',
			success: function(res) {
				inner_points[index].setOptions({draggable: false});
				
				$('.yamap_table .marks_list li[rel="'+mark_id+'"] .mark_edit').html(title);
				$('.yamap_table .edit_list').html('');
				alert('Изменения сохранены');
			}
		});
	}
	
	create_Ymap = function(pid, mid) {
		var caption = prompt('Введите название карты', '');
		if (!caption) return false;
		
		$.ajax({
			url: '/admin/maps/create_map/'+pid+'/'+mid+'/'+caption+'/',
			dataType: 'json',
			cache: false,
			success: function(res) {
				edit_map(res);
				
				$('.maps_list').append('<li rel="'+res+'"><a href="javascript:void(0)" class="delete_button"></a><a href="javascript: void(0)" class="edit_map">'+caption+'</a></li>');
			}
		});
		return false;
	}
	
	//Создание точки. Загружаем форму
	create_mark = function() {
		$('.yamap_table .edit_list').html('');
		
		//Восстанавливаем несохраненные положения
		for (i in map_points) {
			inner_points[i].setGeoPoint(new YMaps.GeoPoint(map_points[i].lat, map_points[i].lon));
			inner_points[i].setOptions({draggable: false});
		}
		
		$.ajax({
			url: '/admin/maps/getMarkNew/',
			dataType: 'json',
			cache: false,
			success: function(res) {
				$('.yamap_table .edit_list').html(res);
				
				//Реинициализируем tiny
				tinyMCE.init({
					mode : "exact",
					elements: "note_id,note_add_1,note_add_2,note_add_3,note_add_4,note_add_5,note_add_6,note_add_7,yamap_desc",
					theme : "advanced",
					language: "ru",
					width: "430",

					theme_advanced_buttons1 : "bold,link,unlink",
					theme_advanced_buttons2 : "",
					theme_advanced_buttons3 : "",

					content_css : "/admin/application/includes/css/content.css"
				});
			}
		});
	}	
})(jQuery);