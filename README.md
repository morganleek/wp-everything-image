# WP Everything Image - Image Generator Tool
Function to quickly generate responsive images.

## Description
Generate both HTML5 Picture tags and background image divs with full responsiveness and lazyloading.

## Requirements
[Fly Dynamic Image Resizer](https://wordpress.org/plugins/fly-dynamic-image-resizer/)

## Usage 
wei_image($attachment_id, $args);

```php
wei_image($g['id'], array(
    'type' => 'image', // "image" for a <picture> or "background" for <div> with background image
    'class' => 'override', // Use with "background". Overrides default div name. Requires you create the div
    'sizes' => array(
      '1500' => array(1500, 300, true),
      '1200' => array(1200, 240, true),
      '992' => array(992, 199, true),
      '765' => array(765, 400, true),
      '1' => array(375, 375, true)
    ),
    'content' => '<h1>Some Content</h1>'
  )
);
```