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
	}
	add_action( 'wp_enqueue_scripts', 'wei_enqueue_scripts' );

	// Add fly image resizer dependency

	// Change parameters to single array to avoid future conflicts

	// Debug
	if(!function_exists('_d')) {
		function _d($obj, $return = false) {
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


	// Returns maximum size for image whilst maintaining an aspect ratio
	if(!function_exists('wei_opt_ratio')) {
		function wei_opt_ratio($target, $dimensions) {
			$width = $dimensions[0];
			$height = $dimensions[1];

			$optWidth = $target[0];
			$optHeight = $target[1];

			$ratio = $optWidth / $optHeight;

			$imageWidth = $optWidth;
			$imageHeight = $optHeight;

			if($width < $optWidth || $height < $optHeight) {
				if($width / $ratio <= $height) {
					$imageWidth = $width;
					$imageHeight = $width / $ratio;
				}
				else {
					$imageWidth = $height * $ratio;
					$imageHeight = $height;
				}
			}
			
			return array(floor($imageWidth), floor($imageHeight));
		}
	}

	if(!function_exists('wei_generate_picture_source')) {
		function wei_generate_picture_source($url, $url_retina, $min_width, $return = true) {
			if(!empty($url) && !empty($url_retina) && !empty($min_width)) {
				$return = '<source media="(min-width: ' . $min_width . 'px)" data-srcset="' . $url . ' 1x, ' . $url_retina . ' 2x">';
				
				if($return) {
					return $return;
				}
				print $return;
			}
		} 
	}

	if(!function_exists('wei_genereate_picture')) {
		function wei_genereate_picture($image_id, $images, $return = true) {
			$html = '';
			$attachment = wei_get_attachment($image_id);
			$html .= '<picture>';
				foreach($images as $i) {
					$html .= wei_generate_picture_source($i[1], $i[2], $i[0]);	
				}
				$last = array_pop($images);
				$html .= '<img class="lazy" data-src="' . $last[1] . '" alt="' . $attachment['caption'] . ' ' . $attachment['alt'] . ' ' . $attachment['description'] . '">'; // ' . get_stylesheet_directory() . '/img/loading.gif
			$html .= '</picture>';	

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

			$class = 'wrapper-' . rand(1000000, 9999999) . '-' . $image_id;
			
			$html .= '<style>';

				// $last = array_key_last($images);
				$padding = $_args['sizes'][$images[0][0]][0] / $_args['sizes'][$images[0][0]][1] * 100;
				$html .= wei_media_query($images[0][1], $class . ' .scm-bg', $padding, 0, false);
				foreach($images as $k => $i) {
					$padding = $_args['sizes'][$i[0]][1] / $_args['sizes'][$i[0]][0] * 100;

					$html .= wei_media_query($i[1], $class . ' .scm-bg', $padding, $i[0], false); 
					$html .= wei_media_query($i[2], $class . ' .scm-bg', $padding, $i[0], true); 
				}
			$html .= '</style>';

			$html .= '<div class="scm-bg-wrapper ' . $class . '"><div class="scm-bg lazy">';
				$html .= '<div>';
					$html .= '<img style="display: none;" src="' . $images[0][1] . '" alt="' . $attachment['caption'] . ' ' . $attachment['alt'] . ' ' . $attachment['description'] . '">';
					$html .= (!empty($_args['content'])) ? '<div class="content">' . $_args['content'] . '</div>' : '';
				$html .= '</div>';
			$html .= '</div></div>';

			return $html;
		}
	}

	if(!function_exists('wei_bulk_generate')) {
		function wei_bulk_generate($image_id, $sizes = array()) {
			// // Resize Images
			$styles = [];

			if($image_id !== 0 && !empty($sizes)) {
				$meta = wp_get_attachment_metadata($image_id);
				$width = $meta['width'];
				$height = $meta['height'];

				$output_sizes = array();

				foreach($sizes as $k => $s) {
					$output_sizes[$k]['dimensions'] = wei_opt_ratio(array($s[0], $s[1]), array($width, $height));
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
					$rWidth = $o['dimensions'][0] * 2;
					$rHeight = $o['dimensions'][1] * 2;
					if($rWidth == $width && $rHeight == $height) { // Use image if it's already sized
						$resized['src'] = wp_get_attachment_image_url($image_id, 'full');
					}
					else {
						$resized = fly_get_attachment_image_src($image_id, array($rWidth, $rHeight), $o['crop']);  
					}
					$img_retina = $resized['src'];

					$styles[] = array($k, $img, $img_retina);
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
				'return' => false
			);

			$_args = wp_parse_args($args, $defaults);

			if($image_id !== 0 && !empty($_args['sizes'])) {
				$styles = wei_bulk_generate($image_id, $_args['sizes']);

				$html = '';
				if($_args['type'] == 'background') {
					$html = wei_genereate_background($image_id, array(
						'images' => $styles, 
						'sizes' => $_args['sizes'],
						'content' => $_args['content']
					));
				}
				else {
					$html = wei_genereate_picture($image_id, $styles);
				}

				if($_args['return']) {
					return $html;
				}
				print $html;
			}
		}
	}