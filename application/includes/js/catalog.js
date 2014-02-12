function DeleteAjax(){
	if(confirm('Вы уверены что хотите удалить этот товар из корзины?')) {
		$('#basket button').attr('disabled', 'disabled');
		$.ajax ({
			url: '/popup/basket/delAjax/'+$(this).attr('rel')+'/',
			type: 'POST',
			beforeSend : function () {

				},

			success : function(result) {
				 $(' .basket_wrapper').html(result);
				 $('#basket .button_del').on('click', DeleteAjax);
		   }
		})
	}
	return false;
}
function AddAjax(){
	$('#basket button').attr('disabled', 'disabled');
	$.ajax ({
		url: '/popup/basket/addAjax/'+$(this).attr('rel')+'/',
		type: 'POST',
		beforeSend : function () {

			},

		success : function(result) {
			$(' .basket_wrapper').html(result);
			$('#basket .button_del').on('click', DeleteAjax);
	   }
	})
	alert("Товар добавлен в корзину");
	$("html, body").animate({scrollTop: $('#basket').offset().top}, "slow")
	return false;
}

(function($) {

	$(document).ready(function() {
		// -- обработка кол-ва товара
		query = false;
		$('[name="position_count"]').on('keyup', function(){
			count = $(this).val();
			pos = $(this).attr('rel');
			if(query){
				query.abort();
			}
			query = $.ajax({
				url: '/basket/BasketAjax/',
				type:'POST',
				data:{
					'id': pos,
					'count': count
				}
			})

		});

		query = false;
		// -- обработка состояний чекбоксов
		$('[name="checkbox_active"]').on('change', function(){
			checkbox_status = $(this).prop("checked");
			checkbox_id = $(this).attr('rel');
			if(query){
				query.abort();
			}
			query = $.ajax({
				url: '/basket/BasketCheckingAjax/',
				type:'POST',
				data:{
					'id': checkbox_id,
					'status': checkbox_status
				}
			})

		});


		// -- обрабытываем кнопку "купить"
		$('.button_bay').on('click', AddAjax);
				
		// -- обрабытываем кнопку "удалить" из корзины
		$('#basket .button_del').on('click', DeleteAjax);

		// -- обрабытываем кнопку "уменьшить кол-во товара" из корзины
		$('#basket .button_m').on('click', function() {
			var count = parseInt($(this).parent().find('.count').text());
			if(count == 1) {
				if(confirm('Вы уверены что хотите удалить этот товар из корзины?')) {
					$('#basket button').attr('disabled', 'disabled');
					location.href = '/basket/del/'+$(this).attr('rel')+'/';
				}
			} else {
				$('#basket button').attr('disabled', 'disabled');
				location.href = '/basket/count/m/'+$(this).attr('rel')+'/';
			}
			return false;
		});
		// -- обрабытываем кнопку "добавить кол-во товара" из корзины
		$('#basket .button_p').on('click', function() {
			$('#basket button').attr('disabled', 'disabled');
			location.href = '/basket/count/p/'+$(this).attr('rel')+'/';
			return false;
		});
		
	});
	
})(jQuery);

