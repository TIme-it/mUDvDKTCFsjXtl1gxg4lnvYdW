(function($) {
	$(function() {
        var jcarousel = $('.jcarousel');

        jcarousel
            .on('jcarousel:reload jcarousel:create', function () {
                var width = $('.middle_wrap').width();

                if (width >= 1600) {
                    width = width / 3.6;
                } else if (width >= 1000) {
                    width = width / 4;
                }
                jcarousel.jcarousel('items').css('width', '483px');
                
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

    resizable_test = function(width){
        if (width >= 1800) {
            $('.jcarousel ul li').css('width', '15');
            return false;
        }
        if (width <= 1000) {
            $('.jcarousel ul li').css('width', '230px');
            return false;
        }
        if (width > 1000){
            diff = width - 1800;
            w = diff/5.22876 + 383;

            $('.jcarousel ul li').css('width', w+'px');
            return false;
        }
    }

    $(document).ready(function() {
        // $('.slider_bg').css('left', $('.jcarousel ul li:eq(2)').position().left-100+'px')
        
        $('.jcarousel-control-next').on('click', function(){
            $('.jcarousel ul li').css('display', 'inline-block');
            setTimeout(function (argument) {
                if($('.jcarousel ul li:eq(0)').data('jcarousel-clone') == 'undefined'){
                    $('.jcarousel ul li:eq(0)').css('display', 'none');
                }
                else {
                    $('.jcarousel ul li:eq(1)').css('display', 'none');
                }
            }, 400);
            resizable_test($('.middle_wrap').width());
            $('.jcarousel ul li').css('width', '483px');
            $('.jcarousel ul li:eq(2)').css('width', '600px');
        })

        // resizable_test($('.middle_wrap').width());

        $(window).resize(function(){
            // $('.slider_bg').css('left', $('.jcarousel ul li:eq(2)').position().left-100+'px')
            // resizable_test($('.middle_wrap').width());
        });
	});
})(jQuery);
