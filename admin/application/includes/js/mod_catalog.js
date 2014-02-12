(function($) {
	
	$(document).ready(function() {
		reviewTechCharsSelect();
		
		$('#button_tech_add').click(function() {
			$('#tc_p').append($('#tc_b').html());
			reviewTechCharsSelect();
		});
		
		$('#tech_block select').live('change', function() {
			if($(this).val() == 'other') {
				var parent = $(this).parent();
				parent.html('<input type="text" class="text_field" name="tchars[n][]" value="" style="width:205px;" />');
				parent.find('input').focus();
			}
			reviewTechCharsSelect();
		});
		
		$('#tc_p .tc_del').live('click', function() {
			if(confirm('Вы уверены что хотите удалить эту характеристику?')) {
				$(this).parent().remove();
			}
			return false;
		});
		
	});
	
	// -- обновляем список в селектах
	reviewTechCharsSelect = function() {
		// -- список уже выбранных
		var data_sel = new Array();
		$('#tc_p .tc_sel_n option:selected').each(function() {
			if($(this).val() != '') {
				data_sel.push($(this).val());
			}
		});
		// -- пробегаемся по всем селектам
		$('#tc_p .tc_sel_n').each(function() {
			var select   = $(this);
			var sel_text = select.find('option:selected').text();
			select.html($('#tc_b .tc_sel_n').html());
			select.find('option').each(function() {
				if($(this).text() == sel_text) {
					$(this).attr('selected', 'selected');
				} else {
					for(var i = 0; i < data_sel.length; i++) {
						if($(this).text() == data_sel[i]) {
							$(this).remove();
						}
					}
				}
			});
		});
		
	}
	
	
})(jQuery);