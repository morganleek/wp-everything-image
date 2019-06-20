<?php
	/*
	Plugin Name: Everything Image
	Plugin URI: http://morganleek.me/wordpress-2/everything-image
	Description: Generate sized, lazy loaded, responsive HTML images and CSS background divs
	Version: 0.1.0
	Author: Morgan Leek
	Author URI: https://morganleek.me/
	Text Domain: everything-image
	Domain Path: /languages
	*/

	// Security
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

	// Define WC_PLUGIN_FILE.
	if ( ! defined( 'WEI_PLUGIN_FILE' ) ) {
		define( 'WEI_PLUGIN_FILE', __FILE__ );
	}

	require_once 'inc/shim.php';

	// Scripts
	function wei_enqueue_scripts() {
		wp_enqueue_script('jquery');

		$url = plugin_dir_url( __FILE__ );
		wp_register_script('vanilla-lazyload', $url . 'bower_components/vanilla-lazyload/dist/lazyload.min.js', array(), '11.0.5');

		wp_register_script('everything-image', $url . 'wp-everything-image.js', array('jquery', 'vanilla-lazyload'), '1.0.1');
		wp_enqueue_script('everything-image');	

		wp_register_style('everything-image', $url . 'wp-everything-image.css', array(), '1.0.2');
		wp_enqueue_style('everything-image');
	}
	add_action( 'wp_enqueue_scripts', 'wei_enqueue_scripts' );

	// Add fly image resizer dependency

	// Change parameters to single array to avoid future conflicts

	$svg = '<svg id="clear" data-name="layer-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {width} {height}"></svg>';

	// Debug
	if(!function_exists('_z')) {
		function _z($obj, $return = false) {
			if($return) {
				return '<pre>' . print_r($obj, true) . '</pre>';
			}
			print '<pre>' . print_r($obj, true) . '</pre>';	
		}
	}

	if(!function_exists('wei_get_attachment')) {
		function wei_get_attachment( $attachment_id ) {
	
			$attachment = get_post( $attachment_id );
			return array(
				'alt' => get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true ),
				'caption' => $attachment->post_excerpt,
				'description' => $attachment->post_content,
				'href' => get_permalink( $attachment->ID ),
				'src' => $attachment->guid,
				'title' => $attachment->post_title
			);
		}
	}

	// Return maximum sized ratio 
	// If either value is zero if will automatically 
	// choose the best non-cropped size
	if(!function_exists('wei_opt_ratio')) {
		function wei_opt_ratio($target, $dimensions) {
			$imageWidth = $target[0];
			$imageHeight = $target[1];

			if($target[0] === 0 || $target[1] === 0) { // Automatic Width or Height
				$ratio = $dimensions[0] / $dimensions[1];
				if($target[0] === 0) {
					$imageWidth = $target[1] * $ratio;
				}
				else {
					$imageHeight = $target[0] / $ratio;
				}
			}
			else { // Best sized dimensions
				$ratio = $target[0] / $target[1];

				if($dimensions[0] < $target[0] || $dimensions[1] < $target[1]) {				
					if($dimensions[0] / $ratio <= $dimensions[1]) {
						$imageWidth = $dimensions[0];
						$imageHeight = $dimensions[0] / $ratio;
					}
					else {
						$imageWidth = $dimensions[1] * $ratio;
						$imageHeight = $dimensions[1];
					}
				}
			}

			return array(floor($imageWidth), floor($imageHeight));
		}
	}

	if(!function_exists('wei_generate_picture_source')) {
		function wei_generate_picture_source($args) {
			$defaults = array(
				'url' => '',
				'url_retina' => '',
				'min_width' => 0,
				'width' => 0,
				'height' => 0,
				'return' => true
			);

			$_args = wp_parse_args($args, $defaults);

			if(!empty($_args['url']) && !empty($_args['url_retina']) && !empty($_args['min_width'])) {
				$return = '<source media="(min-width: ' . $_args['min_width'] . 'px)" data-srcset="' . $_args['url'] . ' 1x, ' . $_args['url_retina'] . ' 2x" width="' . $_args['width'] . '" height="' . $_args['height'] . '">';
				
				if($return) {
					return $return;
				}
				print $return;
			}
		} 
	}

	if(!function_exists('wei_genereate_picture')) {
		function wei_genereate_picture($image_id, $args = array()) {
			$defaults = array(
				'images' => array(),
				'content' => '', 
				'return' => true
			);

			$_args = wp_parse_args($args, $defaults);

			$html = '';
			$attachment = wei_get_attachment($image_id);
			$html .= '<div class="wei-picture-wrapper">';
				$html .= '<picture>';
					foreach($_args['images'] as $i) {
						$html .= wei_generate_picture_source(array(
							'url' => $i[1], 
							'url_retina' => $i[2], 
							'min_width' => $i[0],
							'width' => $i[3],
							'height' => $i[4]
						));	
					}
					$last = array_shift($_args['images']);

					global $svg;
					$svg_final = str_replace('{width}', $last[3], $svg);
					$svg_final = str_replace('{height}', $last[4], $svg_final);
					$svg_encoded = base64_encode($svg_final);

					$html .= '<img class="lazy" src="data:image/svg+xml;base64,' . $svg_encoded . '" data-src="' . $last[1] . '" alt="' . $attachment['caption'] . ' ' . $attachment['alt'] . ' ' . $attachment['description'] . '" width="' . $last[3] . '" height="' . $last[4] . '">';
				$html .= '</picture>';
				if(!empty($_args['content'])) {
					$html .= '<div class="content"><div class="content-align">' . $_args['content'] . '</div></div>';
				}
			$html .= '</div>';

			return $html;
		}
	}

	if(!function_exists('wei_media_query')) {
		function wei_media_query($image, $class, $padding, $min_width, $retina = false) {
			$html = '';
			
			if($min_width > 0) {
				if($retina) {
					$html .= '@media (-webkit-min-device-pixel-ratio: 2) and (min-width:' . $min_width . 'px), (min-resolution: 192dpi) and (min-width:' . $min_width . 'px) {';
				}
				else {
					$html .= '@media(min-width:' . $min_width . 'px) {';	
				}
			}

				$html .= ' .' . $class . ' { ';
					$html .= 'width: 100%;';
					$html .= 'height: 0;';
					$html .= 'padding-bottom: ' . $padding . '%;';
					$html .= 'background-repeat: no-repeat;';
					$html .= 'background-size: cover;';
					$html .= 'opacity: 0;';
					$html .= 'transition: opacity 0.5s;';
					$html .= 'background-position: 50% 50%;';
				$html .= '}';

				$html .= ' .' . $class . '.loading { ';
					$html .= 'background-image: url(' . $image . ');';
				$html .= '}';			

				$html .= ' .' . $class . '.loaded { ';
					$html .= 'background-image: url(' . $image . ');';
					$html .= 'opacity: 1;';
				$html .= '}';

			if($min_width > 0) {
				$html .= '}';
			}

			return $html;
		}
	}

	if(!function_exists('wei_genereate_background')) {
		function wei_genereate_background($image_id, $args = array()) {
			$defaults = array(
				'images' => array(), 
				'sizes' => array(), 
				// 'content' => '',
				'return' => true
			);

			$_args = wp_parse_args($args, $defaults);
			$html = '';

			$attachment = wei_get_attachment($image_id);
			$images = array_reverse($_args['images']);

			$empty_class = false;

			$class_wei = $_args['class'];
			if(empty($_args['class'])) {
				$class = 'wrapper-' . rand(1000000, 9999999) . '-' . $image_id;
				$class_wei = $class . ' .wei-background';
				$empty_class = true;
			}
			
			$html .= '<style>';
				$padding = floor($images[0][4] / $images[0][3] * 100);
				$html .= wei_media_query($images[0][1], $class_wei, $padding, 0, false);
				foreach($images as $k => $i) {
					$padding = floor($i[4] / $i[3] * 100);

					$html .= wei_media_query($i[1], $class_wei, $padding, $i[0], false); 
					$html .= wei_media_query($i[2], $class_wei, $padding, $i[0], true); 
				}
			$html .= '</style>';
			
			$inner = '<img style="display: none;" src="' . $images[0][1] . '" alt="' . $attachment['caption'] . ' ' . $attachment['alt'] . ' ' . $attachment['description'] . '">';
			$inner .= (!empty($_args['content'])) ? '<div class="content">' . $_args['content'] . '</div>' : '';
			
			if($empty_class) {
				$html .= '<div class="wei-background-wrapper ' . $class . '"><div class="wei-background lazy">';
					$html .= '<div>';
						$html .= $inner;
					$html .= '</div>';
				$html .= '</div></div>';
			}
			else {
				$html .= $inner;
			}

			return $html;
		}
	}

	if(!function_exists('wei_bulk_generate')) {
		function wei_bulk_generate($image_id, $sizes = array()) {
			// // Resize Images
			$styles = [];

			if($image_id !== 0 && $image_id !== NULL && !empty($sizes)) {
				$meta = wp_get_attachment_metadata($image_id);
				$width = $meta['width'];
				$height = $meta['height'];

				$output_sizes = array();

				foreach($sizes as $k => $s) {
					$output_sizes[$k]['dimensions'] = wei_opt_ratio(array($s[0], $s[1]), array($width, $height));
					$output_sizes[$k]['dimensions_retina'] = wei_opt_ratio(array($s[0] * 2, $s[1] * 2), array($width, $height));
					$output_sizes[$k]['crop'] = $s[2];
				}

				// Generate Images
				foreach($output_sizes as $k => $o) {
					$img = ''; $img_retina = '';
					$resized = array();
					if($o['dimensions'][0] == $width && $o['dimensions'][1] == $height) { // Use image if it's already sized
						$resized['src'] = wp_get_attachment_image_url($image_id, 'full');
					}
					else {
						$resized = fly_get_attachment_image_src($image_id, $o['dimensions'], $o['crop']);  
					}
					$img = $resized['src'];

					// Retina
					$resized = array();
					$rWidth = $o['dimensions_retina'][0];
					$rHeight = $o['dimensions_retina'][1]; 
					if($rWidth == $width && $rHeight == $height) { // Use image if it's already sized
						$resized['src'] = wp_get_attachment_image_url($image_id, 'full');
					}
					else {
						$resized = fly_get_attachment_image_src($image_id, array($rWidth, $rHeight), $o['crop']);  
					}
					$img_retina = $resized['src'];

					$styles[] = array($k, $img, $img_retina, $o['dimensions'][0], $o['dimensions'][1]);
				}
			}
			return $styles;
		}
	}

	if(!function_exists('wei_image')) {
		function wei_image($image_id = 0, $args = array()) {
			$defaults = array(
				'type' => 'background',
				'sizes' => array(), 
				'content' => '',
				'class' => '',
				'return' => false
			);

			$_args = wp_parse_args($args, $defaults);

			if($image_id !== 0 && $image_id !== NULL && !empty($_args['sizes'])) {
				$styles = wei_bulk_generate($image_id, $_args['sizes']);

				$html = '';
				if($_args['type'] == 'background') {
					$html = wei_genereate_background($image_id, array(
						'images' => $styles, 
						'sizes' => $_args['sizes'],
						'content' => $_args['content'],
						'class' => $_args['class']
					));
				}
				else {
					$html = wei_genereate_picture($image_id, array(
						'images' => $styles,
						'content' => $_args['content']
					));
				}

				if($_args['return']) {
					return $html;
				}
				print $html;
			}
		}
	}