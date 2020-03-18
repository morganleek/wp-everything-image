import $ from 'jquery';
import LazyLoad from "vanilla-lazyload";

// Global Lazy Objects
var callback_reveal, lazyLoadInstance;

$(window).on("load", function () {
  // Lazyload for unsupported background images
  callback_reveal = function(element) {
    // Background fallback support
    if($(element).hasClass('wei-background')) {
      $(element).addClass('loaded');
    }
    // Image finshed animating
    if($(element).is('img')) {
      setTimeout(function(element) {
        $(element).addClass('animation-complete');
      }, 501, element); 
    }
  };

  lazyLoadInstance = new LazyLoad({
    elements_selector: ".lazy",
    callback_reveal: callback_reveal // For background images
  });
});

