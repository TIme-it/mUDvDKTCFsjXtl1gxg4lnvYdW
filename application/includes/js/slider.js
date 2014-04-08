(function($) {
	$(function() {
        var jcarousel = $('.jcarousel');

        jcarousel
            .on('jcarousel:reload jcarousel:create', function () {
                var width = $('.middle_wrap').width();
                if (width >= 1800) {
                    // t_width = width / 3.6;
                    t_width = 500;
                }
                if (width <= 1000) {
                    t_width = width / 2.7;
                }
                if ((width > 1000) && (width < 1800)){
                    t_width = width / 4;
                }
				
				t_width = ($(window).width() - 616) /2;

                // if(width <= 1280){
                //     $('.jcarousel ul').css('left', -t_width+'px')
                // }

                jcarousel.jcarousel('items').css('width', t_width+'px');   
                // $('.jcarousel ul li').css('text-align', 'left');                                    
                // $('.jcarousel ul li:eq(2)').css('width', '600px');
                // $('.jcarousel ul li:eq(2)').css('text-align', 'center');
            })
            .jcarousel({
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

        $('.jcarousel-pagination')
            .on('jcarouselpagination:active', 'a', function() {
                $(this).addClass('active');
            })
            .on('jcarouselpagination:inactive', 'a', function() {
                $(this).removeClass('active');
            })
            .on('click', function(e) {
                e.preventDefault();
            })
            .jcarouselPagination({
                perPage: 1,
                item: function(page) {
                    return '<a href="#' + page + '">' + page + '</a>';
                }
            });
    });

    resizable_test = function(){
        var width = $('.middle_wrap').width();
        if (width >= 1800) {
            // t_width = width / 3.6;
            t_width = 383;
        }
        if (width <= 1000) {
            t_width = width / 2.7;
        }
        if ((width > 1000) && (width < 1800)){
            t_width = width / 4;
        }
		
		t_width = ($(window).width() - 616) /2;

        if(width <= 1280){
            $('.jcarousel ul').css('left', -t_width+'px')
        }

        $('.jcarousel ul li').css('width', t_width+'px');
        $('.jcarousel ul li').css('text-align', 'left');
        $('.jcarousel ul li:eq(2)').css('width', '600px');
        $('.jcarousel ul li:eq(2)').css('text-align', 'center');
    }

    $(document).ready(function() {
        // $('.slider_bg').css('left', $('.jcarousel ul li:eq(2)').position().left-100+'px')
        // resizable_test();
        // $('.jcarousel ul li:eq(1)').css('width', '8%');
        // $('.jcarousel ul li:eq(2)').css('width', '600px');
        // $('.jcarousel-control-next').on('click', function(){
        //     setTimeout(function (argument) {
        //         resizable_test();
        //         // $('.jcarousel ul li:eq(1)').css('width', '8%');
        //     }, 400);
        // })


        // $(window).resize(function(){
        //     // $('.slider_bg').css('left', $('.jcarousel ul li:eq(2)').position().left-100+'px')
        //     setTimeout(function (argument) {
        //         resizable_test();
        //         // $('.jcarousel ul li:eq(1)').css('width', '8%');
        //     }, 400);
        // });
	});
})(jQuery);
