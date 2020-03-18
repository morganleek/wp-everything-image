// Global Lazy Objects
var callback_reveal, lazyLoadInstance;

jQuery(window).on("load", function () {
  // Lazyload for unsupported background images
  callback_reveal = function(element) {
    // Background fallback support
    if(jQuery(element).hasClass('wei-background')) {
      jQuery(element).addClass('loaded');
    }
    // Image finshed animating
    if(jQuery(element).is('img')) {
      setTimeout(function(element) {
        jQuery(element).addClass('animation-complete');
      }, 501, element); 
    }
  };

  lazyLoadInstance = new LazyLoad({
    elements_selector: ".lazy",
    callback_reveal: callback_reveal // For background images
  });
});
