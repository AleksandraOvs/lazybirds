window.addEventListener('DOMContentLoaded', function() {
    jQuery(document).ready(function($){
      $('#faq').accordion({
          active: 0,
          animate: {
              duration: 300,
              easing: '',
          },
          heightStyle: 'content',
          collapsible: true,
          icons: false,
          active: false,
      });

      $('#woocommerce-tabs').accordion({
        active: 0,
        animate: {
            duration: 300,
            easing: '',
        },
        heightStyle: 'content',
        collapsible: true,
        icons: false,
        active: false,
    });
  });
  
  })
  