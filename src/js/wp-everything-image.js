import LazyLoad from "vanilla-lazyload";

window.addEventListener( 'load', () => {
  // Lazyload for unsupported background images
  const callback_reveal = ( image ) => {
    // Fire event for customization
    const event = new Event( 'EverythingImage::ImageRevealed' );
    image.dispatchEvent( event );

    // Background fallback support
    if( image.classList.contains( 'wei-background' ) ) {
      image.classList.add( 'loaded' );
    }
    
    //  Image finshed animating
    if( image.tagName.toUpperCase() == "IMG" ) {
      setTimeout( () => {
        image.classList.add( 'lazy-reveal' );
      }, 501, image ); 
    }
  };

  const callback_loaded = ( image ) => {
    const event = new Event( 'EverythingImage::ImageLoaded' );
    image.dispatchEvent( event );
  };

  const lazyLoadInstance = new LazyLoad( {
    elements_selector: '.lazy',
    callback_reveal: callback_reveal,
    callback_loaded: callback_loaded // For background images
  } );
} );

