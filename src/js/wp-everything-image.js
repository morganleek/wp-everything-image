import LazyLoad from "vanilla-lazyload";

let weiLazyLoad;

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

window.addEventListener( 'load', () => {
  weiLazyLoad = new LazyLoad( {
    elements_selector: '.lazy',
    callback_reveal: callback_reveal,
    callback_loaded: callback_loaded // For background images
  } );
} );

// Listen for 'EverythingImage::Update' event for ajax
// generated images
document.addEventListener( 'EverythingImage::Update', () => {
  // Call event 'EverythingImage::Update' when adding dynamic images
  // `const event = new Event( 'EverythingImage::Update' );
  //  document.dispatchEvent( event );`
  if( weiLazyLoad ) {
    weiLazyLoad.update();
  }
} );

