# WP Everything Image - Image Generator Tool
Function to quickly generate responsive images and background divs.

## Description
Generate both HTML5 Picture tags and background image divs with full responsiveness and lazyloading out of the box.

## Requirements
[Fly Dynamic Image Resizer](https://wordpress.org/plugins/fly-dynamic-image-resizer/)

## Usage 
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

## Example

```php

$image = wei_image(
    $image_id, 
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
        'content' => '<h1>Some Content</h1>',
        'return' => true
    )
);

print $image;

```
