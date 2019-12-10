jQuery(function ($) {
    var $grid = $('.grid').masonry({
        itemSelector: '.grid-item',
        percentPosition: true
    })
    $grid.isotope({
        // options
        itemSelector: '.grid-item',
        layoutMode: 'fitRows'
    });
    $('.filters').click(function (e) {
        e.preventDefault();
        if ($(e.target).is('.active')){
            $('.grid').isotope({ filter: '*' });
            $(e.target).removeClass("active");
        }else{
            $('.grid').isotope({ filter: '.'+$(e.target).data('value') });
            $('.filters').find('.active').removeClass('active');
            $(e.target).addClass("active");
        }

    });
    $('.grid-item').venobox();
});