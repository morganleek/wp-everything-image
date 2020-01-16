// Global Lazy Objects
var callback_reveal, lazyLoadInstance;

jQuery(window).on("load", function () {
  // Lazyload for unsupported background images
  callback_reveal = function(element) {
    if(jQuery(element).hasClass('wei-background')) {
      jQuery(element).addClass('loaded');
    }
  };

  lazyLoadInstance = new LazyLoad({
    elements_selector: ".lazy",
    callback_reveal: callback_reveal // For background images
  });
});
