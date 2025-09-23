jQuery(document).ready(function($) {
    var owl = $('.owl-carousel2');
    owl.owlCarousel({
        margin:0,
        loop:true,
        nav:true,
        margin:40,
        navText: false,
        responsiveClass:true,
        responsive:{
            1:{
                items:1,
            },
            640:{
                items:2,
            }
        }
    });
});	