<?php
	function ____( $obj ) {
		print '<pre>' . preg_replace( '/\r?\n|\r/', '', esc_html( print_r( $obj, true ) ) ) . '</pre>';
	}
	
	function filter_content_images(  $content ) {
		// Check for Gutenberg
		if( empty( $GLOBALS['wp']->query_vars['rest_route'] ) ) { 
			$blocks = parse_blocks( $content );

			foreach( $blocks as $block ) {
				if( strlen( $block['innerHTML'] ) > 5 ) {
					____( $block['innerHTML'] );
					// print '<pre>' . esc_html( print_r( $block['innerHTML'], true ) ) . '</pre>';
				}
			}
		}
		return $content;
	}

	function filter_stop() {
		die();
	}

	// add_filter( 'the_content', 'filter_content_images', 1 );
	// add_filter( 'the_content', 'filter_content_images', 9 );
	// add_filter( 'the_content', 'filter_content_images', 10 );
	// add_filter( 'the_content', 'filter_stop', 20 );

	function _themename_wp_calculate_image_srcset_meta( $image_meta, $size_array, $image_src, $attachment_id ) {
		//  ___($image_meta);
		// ___($size_array);
		// ___($image_src);
		// ___($attachment_id);
		return $image_meta;
	}

	// add_filter( 'wp_calculate_image_srcset_meta', '_themename_wp_calculate_image_srcset_meta', 1, 4 );

	function image_init() {
		// Disable Wordpress' img and iframe filters
		remove_filter( 'the_content', 'wp_filter_content_tags' );
		remove_filter( 'the_excerpt', 'wp_filter_content_tags' );
		remove_filter( 'widget_text_content', 'wp_filter_content_tags' );

		// Enable own img and iframe filters
		add_filter( 'the_content', 'image_filter_content_tags' );
		add_filter( 'the_excerpt', 'image_filter_content_tags' );
		add_filter( 'widget_text_content', 'image_filter_content_tags' );
	}

	add_action( 'init', 'image_init' );

	// Stop WP adding srcset and sizes
	// add_filter( 'wp_img_tag_add_srcset_and_sizes_attr', '__return_false' );

	// Use same method to find and replace images
	function image_filter_content_tags( $content, $context = null ) {
		if ( null === $context ) {
			$context = current_filter();
		}
	
		$add_img_loading_attr    = wp_lazy_loading_enabled( 'img', $context );
		$add_iframe_loading_attr = wp_lazy_loading_enabled( 'iframe', $context );
	
		libxml_use_internal_errors( true );
		$doc = new DOMDocument();
		$doc->loadHTML( $content );
		$xpath = new DOMXpath( $doc );

		// User defined sizes
		// Defined least to most specific as some will appear more than once
		$size_queries = array(
			'//*/img' => array(
				'992' => array(992, 0, false), 
				'768' => array(768, 0, false), 
				'1' => array(450, 0, false) 
			),
			'//*/div[@class="wp-block-column"]/*/img' => array(
				'992' => array(496, 0, false), 
				'768' => array(384, 0, false), 
				'1' => array(450, 0, false) 
			)
		);

		// Matches against attachment id
		$image_matches = array();

		// For each size xPath check again the images
		foreach( $size_queries as $k => $v ) {
			$images = $xpath->query( $k );
			// Match exists
			if( $images ) {
				// For each match grap the attachment id and log in the matches array
				foreach( $images as $image ) {
					if ( preg_match( '/wp-image-([0-9]+)/i', $doc->saveHTML( $image ), $class_id ) ) {
						$attachment_id = absint( $class_id[1] );
						if ( $attachment_id ) {
							$image_matches[ $attachment_id ] = $v;
						}
					}
				}
			}
		} 
		

		if ( ! preg_match_all( '/<(img|iframe)\s[^>]+>/', $content, $matches, PREG_SET_ORDER ) ) {
			return $content;
		}
	
		// List of the unique `img` tags found in $content.
		$images = array();
	
		// List of the unique `iframe` tags found in $content.
		$iframes = array();
	
		foreach ( $matches as $match ) {
			list( $tag, $tag_name ) = $match;
	
			switch ( $tag_name ) {
				case 'img':
					if ( preg_match( '/wp-image-([0-9]+)/i', $tag, $class_id ) ) {
						$attachment_id = absint( $class_id[1] );
	
						if ( $attachment_id ) {
							// If exactly the same image tag is used more than once, overwrite it.
							// All identical tags will be replaced later with 'str_replace()'.
							$images[ $tag ] = $attachment_id;
							break;
						}
					}
					$images[ $tag ] = 0;
					break;
				case 'iframe':
					$iframes[ $tag ] = 0;
					break;
			}
		}
	
		// Reduce the array to unique attachment IDs.
		$attachment_ids = array_unique( array_filter( array_values( $images ) ) );
	
		if ( count( $attachment_ids ) > 1 ) {
			/*
			 * Warm the object cache with post and meta information for all found
			 * images to avoid making individual database calls.
			 */
			_prime_post_caches( $attachment_ids, false, true );
		}
	
		foreach ( $images as $image => $attachment_id ) {
			$filtered_image = $image;
	
			// Add 'width' and 'height' attributes if applicable.
			if ( $attachment_id > 0 && false === strpos( $filtered_image, ' width=' ) && false === strpos( $filtered_image, ' height=' ) ) {
				$filtered_image = wp_img_tag_add_width_and_height_attr( $filtered_image, $context, $attachment_id );
			}
	
			// Custom image sizes with lazyload
			if( array_key_exists( $attachment_id, $image_matches ) ) {
				
				$temp_filtered_image = '';
				$src_sized = wei_bulk_generate( $attachment_id, $image_matches[$attachment_id] );
				
				$temp_filtered_image .= '<picture>';
					foreach($src_sized as $i) {
						$temp_filtered_image .= wei_generate_picture_source(array(
							'url' => $i[1], 
							'url_retina' => $i[2], 
							'min_width' => $i[0],
							'width' => $i[3],
							'height' => $i[4],
							'svg_placeholder' => $i[5]
						));	
					}
					
					$last = array_pop( $src_sized );
					
					$temp_filtered_image .= '<img class="lazy" src="' . wei_generate_svg($last[3], $last[4]) . '" data-src="' . $last[1] . '" width="' . $last[3] . '" height="' . $last[4] . '">';
				$temp_filtered_image .= '</picture>';
				
				$filtered_image = $temp_filtered_image;
			}
			else {
				// Do the terrible Wordpress default
				if ( $attachment_id > 0 && false === strpos( $filtered_image, ' srcset=' ) ) {
					$filtered_image = wp_img_tag_add_srcset_and_sizes_attr( $filtered_image, $context, $attachment_id );
				}

				// Add 'loading' attribute if applicable.
				if ( $add_img_loading_attr && false === strpos( $filtered_image, ' loading=' ) ) {
					$filtered_image = wp_img_tag_add_loading_attr( $filtered_image, $context );
				}
			}
	
			if ( $filtered_image !== $image ) {
				$content = str_replace( $image, $filtered_image, $content );
			}
		}
	
		foreach ( $iframes as $iframe => $attachment_id ) {
			$filtered_iframe = $iframe;
	
			// Add 'loading' attribute if applicable.
			if ( $add_iframe_loading_attr && false === strpos( $filtered_iframe, ' loading=' ) ) {
				$filtered_iframe = wp_iframe_tag_add_loading_attr( $filtered_iframe, $context );
			}
	
			if ( $filtered_iframe !== $iframe ) {
				$content = str_replace( $iframe, $filtered_iframe, $content );
			}
		}
	
		return $content;
	}