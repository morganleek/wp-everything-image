# WP Everything Image - Image Generator Tool
Function to quickly generate responsive images and background divs.

## Description
Generate both HTML5 Picture tags and background image divs with full responsiveness and lazyloading out of the box. Can also be called via a post AJAX request when an admin.

## Requirements
[Fly Dynamic Image Resizer](https://wordpress.org/plugins/fly-dynamic-image-resizer/)

## Development
```bash
npm install
```

## Usage PHP
```php
wei_image($attachment_id, $args = array());
```

### Parameters
**$attachment_id** 

**$args**
  (array)(Optional)

* **'type'**
  (string) Either 'image' for HTML5 picture tag or 'background' for div element with background
* **'class'**
  (string) Override default div class name for 'background' type.
* **'sizes'**
  (array) Key value pair of 'min-width' breakpoint and array of (width, height, crop)
* **'content'**
  (string) HTML content to go over image
* **'return'**
  (bool) Whether to return or echo (default) the result

## Usage PHP (Gutenberg)
Ability to override Wordpress' default image resizings based on their block location using [jQuery](https://api.jquery.com/category/selectors/) like selectors.

```php
// Add filters with jQuery like selectors
add_filter( 'wei_wp_size_array', '_themename_wei_wp_size_array', 10, 1 );

// Custom Image Sizes
  function _themename_wei_wp_size_array( $size_queries = array() ) {
  // Cards
  $size_queries['.wp-block-media-text.has-media-on-the-top img'] = array(
    '1200' => array(702, 0, false), 
    '600' => array(453, 0, false), 
    '450' => array(550, 0, false),
    '1' => array(402, 0, false) 
  );
  // Covers
  $size_queries['.wp-block-cover.alignfull img'] = array(
    '1500' => array( 960, 0, false ),
    '1200' => array( 750, 0, false ),
    '992' => array( 600, 0, false ), 
    '768' => array( 496, 0, false ), 
    '1' => array( 450, 0, false ) 
  );
  // Slider
  $size_queries['.wp-block-gallery.is-style-gallery-slider img'] = array(
    '1500' => array( 1334, 0, false ),
    '1200' => array( 1300, 0, false ),
    '992' => array( 1000, 0, false ), 
    '768' => array( 792, 0, false ), 
    '1' => array( 450, 450, true ) 
  );
  // General column block
  $size_queries['.wp-block-column > figure > img'] = array(
    '1500' => array( 750, 0, false ),
    '1200' => array( 600, 0, false ),
    '992' => array( 496, 0, false ), 
    '768' => array( 362, 0, false ), 
    '1' => array( 450, 0, false ) 
);


  return $size_queries;
  }
```

## Usage JS
```js
wp.ajax.send('wei_image', {
  data: {
    "action": "wei_image",
    "image_id": image_id,
    "sizes": { sizes }
  },
  error: function(r) { /* Do something */ },
  success: function(r) { /* Do something */ },
  type: 'POST'
});
```

## Example

```php
// PHP Example

$image = wei_image( // Image as Picture tag
  $wp_image_id, 
  array(
    'type' => 'image',
    'class' => 'extra-classes', // Additional classes space seperated
    'sizes' => array(
      '1500' => array(1500, 300, true), // 1x 1500x300, 2x 3000x600 and crop
      '1200' => array(1200, 240, true), // ...
      '992' => array(992, 199, true), // ...
      '768' => array(768, 400, true), // ...
      '1' => array(375, 0, false) // Resize to 1x 375x{maintain-aspect} 2x 750x{maintain-aspect}
    ),
    'content' => '<h1>Some Content</h1>', // Optional
    'alt' => 'Alt tag text', // Optional
    'return' => true
  )
);
print $image;

wei_image( // Image as background
  $wp_image_id, 
  array(
    'type' => 'image', // "image" for a <picture> or "background" for <div> with background image
    'class' => 'override', // Use with "background". Overrides default div name. Requires you create the div
    'sizes' => array(
      '1500' => array(1500, 300, true),
      '1200' => array(1200, 240, true),
      '992' => array(992, 199, true),
      '765' => array(765, 400, true),
      '1' => array(375, 375, true)
    ),
    'content' => '<h1>Some Content</h1>', // Optional
    'alt' => 'Alt tag text', // Optional
    'return' => false // Return or echo
  )
);

```

```js
// JS Example

let image_id = 5;
wp.ajax.send('wei_image', {
  data: {
    "action":"wei_image",
    "image_id":image_id,
    "sizes":{
      "768": [768,300,true],
      "1": [350,200,true]
    }
  },
  error: function(r) { /* Do something */ },
  success: function(r) { /* Do something */ },
  type: 'POST'
});
```
