// Global Lazy Objects
var callback_reveal, lazyLoadInstance;

jQuery(window).on("load", function () {
  // Lazyload for unsupported background images
  callback_reveal = function(element) {
    if(jQuery(element).hasClass('wei-background')) {
      jQuery(element).addClass('loaded');
      // Wait 1s and Remove Animation for Images 
      // Stop Conflicts with Local CSS
      setTimeout(function(element) {
        jQuery(element).find('img.lazy').addClass('animation-complete');
      }, 1000, element); 
    }
  };

  lazyLoadInstance = new LazyLoad({
    elements_selector: ".lazy",
    callback_reveal: callback_reveal // For background images
  });
});
