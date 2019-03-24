jQuery(window).on("load", function () {
  // Lazyload for unsupported background images
  var callback_reveal = function(element) {
    if(jQuery(element).hasClass('scm-bg')) {
      jQuery(element).addClass('loaded');
    }
  };

  var lazyLoadInstance = new LazyLoad({
    elements_selector: ".lazy",
    callback_reveal: callback_reveal // For background images
  });
});