require(['jquery', 'jquery/ui'], function($){
    $(document).ready(function () {
        var searchIcon = $('.header-search-icon').html();
        $('.header-search-icon').on('click', function () {
            jQuery('.header-search-icon').html(function(i, text){
               return text === searchIcon ? '<i class="fa fa-times" aria-hidden="true"></i>' : searchIcon;
            });
            $('#header-search-form-wrapper').animate({width:'toggle'},1000);
        });

        var orgElementPos = $('.aside-minicart-wrapper').offset();


        $(window).scroll(function () {
            if($('.aside-minicart-wrapper').length > 0) {
                if ($(this).scrollTop() >= orgElementPos.top) {
                    $('.aside-minicart-wrapper').addClass('aside-minicart-wrapper-fixed');
                    if($('body').hasClass('catalog-product-view')){
                        $('.content-aside-container').css('position', 'absolute');
                    }
                } else {
                    $('.aside-minicart-wrapper').removeClass('aside-minicart-wrapper-fixed');
                    if($('body').hasClass('catalog-product-view')){
                        $('.content-aside-container').css('position', 'relative');
                    }
                }

            }
        });
    });

    function changeMinicartStyle() {
        if($('body').hasClass('catalog-product-view')){
            $('.content-aside-container').css('position', 'absolute');
        }else{
            $('.content-aside-container').css('position', 'relative');
        }
    }
});


