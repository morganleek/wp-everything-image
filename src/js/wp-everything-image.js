import LazyLoad from "vanilla-lazyload";

// Global Lazy Objects
var callback_reveal, lazyLoadInstance;

window.addEventListener( 'load', () => {
  // Lazyload for unsupported background images
  callback_reveal = function( image ) {
    // Fire event for customization
    const event = new Event( 'EverythingImage::ImageLoaded' );
    image.dispatchEvent( event );

    // Background fallback support
    if( image.classList.contains( 'wei-background' ) ) {
      image.classList.add( 'loaded' );
    }
    
    //  Image finshed animating
    if( image.tagName.toUpperCase() == "IMG" ) {
      setTimeout( function( image ) {
        image.classList.add( 'lazy-reveal' );
      }, 501, image ); 
    }
  };

  lazyLoadInstance = new LazyLoad( {
    elements_selector: ".lazy",
    callback_reveal: callback_reveal // For background images
  } );
} );

