(function($) {
	$(function() {
        var jcarousel = $('.jcarousel');

        jcarousel
            .on('jcarousel:reload jcarousel:create', function () {
                var width = $('.middle_wrap').width();
                if (width >= 1800) {
                    // t_width = width / 3.6;
                    t_width = ($(window).width() - 616) /3;
                }
                if (width <= 1000) {
                    t_width = width / 4.8;
                }
                if ((width >= 1360) && (width < 1800)){
                    t_width = ($(window).width() - 616) /2.8;
                }
                if ((width > 1000) && (width < 1360)){
                    t_width = ($(window).width() - 616) /1.8;
                }
                
                // t_width = ($(window).width() - 616) /3;

                if(width <= 1359){
                    $('.jcarousel ul').css('left', -t_width+'px')
                }

                jcarousel.jcarousel('items').css('width', t_width+'px');                                     
                $('.jcarousel ul li:eq(2)').css('width', '600px');

                $('.slider_bg .note h1').html($('.jcarousel ul li:eq(2) span').html());
                $('.slider_bg a.more_button').attr('href', $('.jcarousel ul li:eq(2) a').attr('href'));
                $('.slider_bg .note p').html($('.jcarousel ul li:eq(2) .note p').html());
            })
            .jcarousel({
                start: 3,
                wrap: 'circular'
            });

        $('.jcarousel-control-prev')
            .jcarouselControl({
                target: '-=1'
            });

        $('.jcarousel-control-next')
            .jcarouselControl({
                target: '+=1'
            });

    });

    resizable_test = function(){
        var width = $('.middle_wrap').width();
        if (width >= 1800) {
            // t_width = width / 3.6;
            t_width = ($(window).width() - 616) /3;
        }
        if (width <= 1000) {
            t_width = width / 4.8;
        }
        if ((width >= 1360) && (width < 1800)){
            t_width = ($(window).width() - 616) /2.8;
        }
        if ((width > 1000) && (width < 1360)){
            t_width = ($(window).width() - 616) /1.8;
        }

        if(width <= 1359){
            $('.jcarousel ul').css('left', -t_width+'px')
        }

        $('.jcarousel').jcarousel('items').css('width', t_width+'px'); 
        $('.jcarousel ul li:eq(2)').css('width', '600px');

        $('.slider_bg .note h1').html($('.jcarousel ul li:eq(2) span').html());
        $('.slider_bg a.more_button').attr('href', $('.jcarousel ul li:eq(2) a').data('link'));
        $('.slider_bg .note p').html($('.jcarousel ul li:eq(2) .note p').html());

        var i = 1;
        $('.jcarousel ul li').each(function(){
            $(this).data('pos', i);
            i += 1;
        });
        // console.log($('.slider_bg .note h1').html());
    }

    showimage_init = function(){
        $('#main_slider .images img').each(function(){
            if(!$(this).parent('.images').hasClass('hide')){
                switch ($(this).data('pos')) {
                   case 1:
                      $(this).css('top', '55px');
                      $(this).css('right', '56px');
                      break;
                   case 2:
                      $(this).css('top', '-60px');
                      $(this).css('right', '143px');
                      break;
                   case 3:
                      $(this).css('top', '140px');
                      $(this).css('right', '165px');
                      break;
                   default:
                      break;
                }
            }
        })
    }

    hideimage_init = function(){
        $('#main_slider .images.hide img').each(function(){
            $(this).css('display', 'none');
            $(this).css('opacity', '0');

            switch ($(this).data('pos')) {
               case 1:
                  $(this).css('top', '55px');
                  $(this).css('right', '-250px');
                  break;
               case 2:
                  $(this).css('top', '-250px');
                  $(this).css('right', '143px');
                  break;
               case 3:
                  $(this).css('top', '350px');
                  $(this).css('right', '165px');
                  break;
               default:
                  break;
            }
        })
        $('#main_slider #leftArrow').addClass('active');
        $('#main_slider #leftArrow').removeClass('inactive');
        $('#main_slider #rightArrow').addClass('active');
        $('#main_slider #rightArrow').removeClass('inactive');
    }

    slide_animation = function(){
        $('#main_slider .images img').each(function(){
            if(!$(this).parent('.images').hasClass('hide')){
                switch ($(this).data('pos')) {
                   case 1:
                      $(this).animate({
                          opacity: 0,
                          top: '-155px', 
                          right: '-156px',
                      }, 1000);
                      break;
                   case 2:
                      $(this).animate({
                          opacity: 0,
                          top: '-260px', 
                          right: '343px',
                      }, 1000);
                      break;
                   case 3:
                      $(this).animate({
                          opacity: 0,
                          top: '340px', 
                          right: '165px',
                      }, 1000);
                      break;
                   default:
                      break;
                }
            }
            if($(this).parent('.images').hasClass('hide')){
                hide_obj = $(this).parent('.images');
            }
            else{
                show_obj = $(this).parent('.images');
            }
        })

        $('#main_slider .images.hide img').each(function(){
            $(this).css('display', 'block');

            switch ($(this).data('pos')) {
               case 1:
                  $(this).animate({
                      opacity: 1,
                      top: '55px', 
                      right: '56px',
                  }, 1000);
                  break;
               case 2:
                  $(this).animate({
                      opacity: 1,
                      top: '-60px', 
                      right: '143px',
                  }, 1000);
                  break;
               case 3:
                  $(this).animate({
                      opacity: 1,
                      top: '140px', 
                      right: '165px',
                  }, 1000);
                  break;
               default:
                  break;
            }
            if($(this).parent('.images').hasClass('hide')){
                hide_obj = $(this).parent('.images');
            }
            else{
                show_obj = $(this).parent('.images');
            }
        })
    }

    $(document).ready(function() {
        showimage_init();
        hideimage_init();

        $('.jcarousel-control-next, .jcarousel-control-prev').on('click', function(){
            if($(this).hasClass('active')){
                var curr_id = 1;
                if($(this).get(0) == $('.jcarousel-control-next').get(0)){
                  if($('.middle_wrap').width() <= 1359){
                    curr_id = $('.jcarousel ul li:eq(2) a').data('id')
                  }
                  else {
                    curr_id = $('.jcarousel ul li:eq(3) a').data('id')
                  }
                }
                if($(this).get(0) == $('.jcarousel-control-prev').get(0)){
                  curr_id = $('.jcarousel ul li:eq(2) a').data('id')
                }

                var j = 1;
                $('.slider_bg .images.hide img').each(function(){
                  $(this).attr('src','/application/includes/slides/'+curr_id+'/'+j+'.png');
                  j++;
                })
                // right_jsp();
                slide_animation();

                hide_obj.removeClass('hide');
                show_obj.addClass('hide');

                $('#main_slider #leftArrow').removeClass('active');
                $('#main_slider #leftArrow').addClass('inactive');
                $('#main_slider #rightArrow').removeClass('active');
                $('#main_slider #rightArrow').addClass('inactive');

                setTimeout(showimage_init, 1100);
                setTimeout(hideimage_init, 1100);
            }
            setTimeout(function (argument) {
                resizable_test();
            }, 400);
        })

        $('.jcarousel ul li a').on('click', function(){

            curr_id = $(this).data('id');
            var j = 1;
            $('.slider_bg .images.hide img').each(function(){
              $(this).attr('src','/application/includes/slides/'+curr_id+'/'+j+'.png');
              j++;
            })
            slide_animation();

            hide_obj.removeClass('hide');
            show_obj.addClass('hide');


            item = $(this).parent('li').data('pos');
            res = item - 3;
            // console.log(res);
            if($('.middle_wrap').width() <= 1359 && res == 1){
                res = 2;
            }
            $('.jcarousel').jcarousel('scroll', res);
            setTimeout(function (argument) {
                resizable_test();
            }, 500);
        }) 

        $(window).resize(function(){
            // if($('.middle_wrap').width() <= 1359){
                setTimeout(function (argument) {
                    // $('.jcarousel').jcarousel('scroll', 0);
                    resizable_test();
                }, 500);
            // }
        });


        
	});
})(jQuery);
