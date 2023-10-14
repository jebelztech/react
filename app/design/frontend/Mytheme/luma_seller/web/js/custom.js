jQuery(document).ready(function(){
    jQuery('.hero-slider').owlCarousel({
        loop: true,
        nav: true,
        dots: false,
        autoplay: true,
        lazyLoad: true,
        autoplaySpeed: 3000,
        paginationSpeed: 3000,
        autoplayTimeout: 3000,
        responsive: {
    
            320: {
                items: 1
            },
    
            580: {
                items: 1
            },
            768: {
                items: 1
            },
            1000: {
                items: 1
            }
        }
    });
    jQuery('.slider-allview').owlCarousel({
        loop: true,
        margin: 15,
        nav: true,
        dots: false,
        autoplay: true,
        lazyLoad: true,
        autoplaySpeed: 3000,
        paginationSpeed: 3000,
        autoplayTimeout: 3000,
        responsive: {
    
            320: {
                items: 1
            },
    
            580: {
                items: 1
            },
            768: {
                items: 3
            },
            1000: {
                items: 5
            }
        }
    });
  });
