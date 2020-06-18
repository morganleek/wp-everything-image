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

## Parameters
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

## Usage JS
```js
wp.ajax.send('wei_image', {
  data: {
    "action":"wei_image","image_id":image_id,"sizes":{sizes}
  },
  error: function() {},
  success: function(r) { console.log(r); },
  type: 'POST'
});
```

## Example

```php
// Image as Picture tag
$image = wei_image(
  $wp_image_id, 
  array(
    'type' => 'image',
    'class' => 'override',
    'sizes' => array(
      '1500' => array(1500, 300, true),
      '1200' => array(1200, 240, true),
      '992' => array(992, 199, true),
      '765' => array(765, 400, true),
      '1' => array(375, 375, true)
    ),
    'content' => '<h1>Some Content</h1>', // Optional
    'alt' => 'Alt tag text', // Optional
    'return' => true
  )
);
print $image;

// Image as background
wei_image(
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
  error: function(r) { // Do something },
  success: function(r) { // Do something },
  type: 'POST'
});
```
