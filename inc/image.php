<?php
	require WEI__PLUGIN_DIR . 'vendor/autoload.php';
	use PHPHtmlParser\Dom;
	
	// Override WP srcset image method with custom sizings

	// Actions
	add_action( 'init', 'wei_wp_image_init' );

	// Init
	function wei_wp_image_init() {
		// Disable Wordpress' img and iframe filters
		// remove_filter( 'the_content', 'wp_filter_content_tags' );
		// remove_filter( 'the_excerpt', 'wp_filter_content_tags' );
		// remove_filter( 'widget_text_content', 'wp_filter_content_tags' );

		// Enable own img and iframe filters
		// add_filter( 'the_content', 'wei_wp_image_filter_image_sizes', 9, 1 );
		// add_filter( 'the_excerpt', 'wei_wp_image_filter_image_sizes', 9, 1 );
		// add_filter( 'widget_text_content', 'wei_wp_image_filter_image_sizes', 9, 1 );

		// Testing
		add_filter( 'the_content', 'wei_test', 8, 1 );
	}

	function wei_test( $content ) {
		// print WEI__PLUGIN_DIR . 'vendor/autoload.php';
		$dom = new Dom();
		$dom->loadStr( $content );

		$finders = array(
			'.wp-block-media-text__media img',
			'.wp-block-columns img',
			'img'
		);

		foreach( $finders as $find ) {
			$found = $dom->find( $find );
			// ___( $found->count() );
			$i = 0;
			foreach( $found as $img ) {
				if( !$img->hasAttribute( "data-parsed" ) ) {
					$i++;
					$img->setAttribute( "data-parsed", "1" );
				}
			}
			___( $i );
		}



		// ___( get_class_methods( $dom->find( '.wp-block-media-text__media img' )[0] ) );
		// PHPHtmlParser\Dom\Node\HtmlNode
		// Array
		// (
		// 		[0] => __construct
		// 		[1] => setHtmlSpecialCharsDecode
		// 		[2] => innerHtml
		// 		[3] => innerText
		// 		[4] => outerHtml
		// 		[5] => text
		// 		[6] => propagateEncoding
		// 		[7] => hasChildren
		// 		[8] => getChild
		// 		[9] => getChildren
		// 		[10] => countChildren
		// 		[11] => addChild
		// 		[12] => insertBefore
		// 		[13] => insertAfter
		// 		[14] => removeChild
		// 		[15] => hasNextChild
		// 		[16] => nextChild
		// 		[17] => previousChild
		// 		[18] => isChild
		// 		[19] => replaceChild
		// 		[20] => firstChild
		// 		[21] => lastChild
		// 		[22] => isDescendant
		// 		[23] => setParent
		// 		[24] => getIterator
		// 		[25] => count
		// 		[26] => __destruct
		// 		[27] => __get
		// 		[28] => __toString
		// 		[29] => id
		// 		[30] => getParent
		// 		[31] => delete
		// 		[32] => isAncestor
		// 		[33] => getAncestor
		// 		[34] => hasNextSibling
		// 		[35] => nextSibling
		// 		[36] => previousSibling
		// 		[37] => getTag
		// 		[38] => setTag
		// 		[39] => getAttributes
		// 		[40] => getAttribute
		// 		[41] => hasAttribute
		// 		[42] => setAttribute
		// 		[43] => removeAttribute
		// 		[44] => removeAllAttributes
		// 		[45] => ancestorByTag
		// 		[46] => find
		// 		[47] => findById
		// 		[48] => isTextNode
		// )

		// ___( $dom->find( '.wp-block-media-text__media img' )->count()  );
		// PHPHtmlParser\Dom\Node\Collection
		// Array
		// (
		// 		[0] => __call
		// 		[1] => __get
		// 		[2] => __toString
		// 		[3] => count
		// 		[4] => getIterator
		// 		[5] => offsetSet
		// 		[6] => offsetExists
		// 		[7] => offsetUnset
		// 		[8] => offsetGet
		// 		[9] => toArray
		// 		[10] => each
		// )
		
		// $img = $dom->find( 'img' )[0]; // Returns Node\Collection
		// echo $img->getAttribute( 'src' );
		print $dom->__toString();
		// ___( get_class_methods( new Dom() ) );
		// Array
		// (
		// 		[0] => __construct
		// 		[1] => __toString
		// 		[2] => loadFromFile
		// 		[3] => loadFromUrl
		// 		[4] => loadStr
		// 		[5] => setOptions
		// 		[6] => find
		// 		[7] => getElementById
		// 		[8] => getElementsByTag
		// 		[9] => getElementsByClass
		// 		[10] => __get
		// 		[11] => firstChild
		// 		[12] => lastChild
		// 		[13] => countChildren
		// 		[14] => getChildren
		// 		[15] => hasChildren
		// )



		// print 'hello';
		// if( preg_match_all( '/<(img|iframe)\s[^>]+>/', $content, $matches, PREG_SET_ORDER ) ) {
		// 	foreach( $matches as $match ) {
		// 		____( $match );
		// 	}
		// }

		die();
	}


	// Use same method to find and replace images
	// function wei_wp_image_filter_content_tags( $content, $context = null ) {
	// 	if ( null === $context ) {
	// 		$context = current_filter();
	// 	}
	
	// 	$add_img_loading_attr    = wp_lazy_loading_enabled( 'img', $context );
	// 	$add_iframe_loading_attr = wp_lazy_loading_enabled( 'iframe', $context );

	// 	// Check for images
	// 	if ( ! preg_match_all( '/<(img|iframe)\s[^>]+>/', $content, $matches, PREG_SET_ORDER ) ) {
	// 		return $content;
	// 	}
	// 	// ___( $matches ); die();

	// 	// Pass the content and return array with attachment ids and sizes based on path
	// 	$image_matches = wei_wp_image_filter_image_sizes( $content );
	// 	___( $image_matches ); die();
	
	// 	// List of the unique `img` tags found in $content.
	// 	$images = array();
	
	// 	// List of the unique `iframe` tags found in $content.
	// 	$iframes = array();


	// 	foreach ( $matches as $match ) {
	// 		list( $tag, $tag_name ) = $match;
			
	// 		switch ( $tag_name ) {
	// 			case 'img':
	// 				if ( preg_match( '/wp-image-([0-9]+)/i', $tag, $class_id ) ) {
	// 					$attachment_id = absint( $class_id[1] );
	
	// 					if ( $attachment_id ) {
	// 						// If exactly the same image tag is used more than once, overwrite it.
	// 						// All identical tags will be replaced later with 'str_replace()'.
	// 						$images[ $tag ] = $attachment_id;
	// 						break;
	// 					}
	// 				}
	// 				$images[ $tag ] = 0;
	// 				break;
	// 			case 'iframe':
	// 				$iframes[ $tag ] = 0;
	// 				break;
	// 		}
	// 	}
	// 	___( $images ); die();
	
	// // 	// Reduce the array to unique attachment IDs.
	// // 	// ___( $images);
	// // 	// $attachment_ids = array_unique( array_filter( array_values( $images ) ) );
	// // 	$attachment_ids = array_filter( array_values( $images ) );
	// // 	// ___( $attachment_ids); die();
	
	// // 	if ( count( $attachment_ids ) > 1 ) {
	// // 		/*
	// // 		 * Warm the object cache with post and meta information for all found
	// // 		 * images to avoid making individual database calls.
	// // 		 */
	// // 		_prime_post_caches( $attachment_ids, false, true );
	// // 	}
	
	// // 	foreach ( $images as $image => $attachment_id ) {
	// // 		$filtered_image = $image;
	
	// // 		// Add 'width' and 'height' attributes if applicable.
	// // 		if ( $attachment_id > 0 && false === strpos( $filtered_image, ' width=' ) && false === strpos( $filtered_image, ' height=' ) ) {
	// // 			$filtered_image = wp_img_tag_add_width_and_height_attr( $filtered_image, $context, $attachment_id );
	// // 		}
	
	// // 		if( array_key_exists( $attachment_id, $image_matches ) ) {
	// // 			// Custom image sizes with lazyload			
	// // 			$temp_filtered_image = '';
	// // 			$src_sized = wei_bulk_generate( $attachment_id, $image_matches[$attachment_id] );
				
	// // 			$temp_filtered_image .= '<picture>';
	// // 				foreach($src_sized as $i) {
	// // 					$temp_filtered_image .= wei_generate_picture_source(array(
	// // 						'url' => $i[1], 
	// // 						'url_retina' => $i[2], 
	// // 						'min_width' => $i[0],
	// // 						'width' => $i[3],
	// // 						'height' => $i[4],
	// // 						'svg_placeholder' => $i[5]
	// // 					));	
	// // 				}
					
	// // 				$last = array_pop( $src_sized );
					
	// // 				// $temp_filtered_image .= '<!--' . $filtered_image . '-->';
	// // 				$temp_filtered_image .= '<img class="lazy" src="' . wei_generate_svg($last[3], $last[4]) . '" data-src="' . $last[1] . '" width="' . $last[3] . '" height="' . $last[4] . '">';
	// // 			$temp_filtered_image .= '</picture>';
				
	// // 			$filtered_image = $temp_filtered_image;
	// // 		}
	// // 		else {
	// // 			// Do the terrible Wordpress default
	// // 			if ( $attachment_id > 0 && false === strpos( $filtered_image, ' srcset=' ) ) {
	// // 				$filtered_image = wp_img_tag_add_srcset_and_sizes_attr( $filtered_image, $context, $attachment_id );
	// // 			}

	// // 			// Add 'loading' attribute if applicable.
	// // 			if ( $add_img_loading_attr && false === strpos( $filtered_image, ' loading=' ) ) {
	// // 				$filtered_image = wp_img_tag_add_loading_attr( $filtered_image, $context );
	// // 			}
	// // 		}
	
	// // 		if ( $filtered_image !== $image ) {
	// // 			$content = str_replace( $image, $filtered_image, $content );
	// // 		}
	// // 	}
	
	// // 	foreach ( $iframes as $iframe => $attachment_id ) {
	// // 		$filtered_iframe = $iframe;
	
	// // 		// Add 'loading' attribute if applicable.
	// // 		if ( $add_iframe_loading_attr && false === strpos( $filtered_iframe, ' loading=' ) ) {
	// // 			$filtered_iframe = wp_iframe_tag_add_loading_attr( $filtered_iframe, $context );
	// // 		}
	
	// // 		if ( $filtered_iframe !== $iframe ) {
	// // 			$content = str_replace( $iframe, $filtered_iframe, $content );
	// // 		}
	// // 	}
	
	// // 	return $content;
	// }

	// Return sizes to matched img tags
	function wei_wp_image_filter_image_sizes( $content, $context = null ) {
		// Matches - key is the attachment id, value is the size array
		$image_matches = array();
		// print '<div>' . $content . '</div>';

		// Get user defined sizes
		$size_queries = apply_filters( 'wei_wp_size_array', $size_queries );

		if( !empty( $size_queries ) ) {
			// XPath Checks
			libxml_use_internal_errors( true ); // Supress invalid HTML messages
			$doc = new DOMDocument( '1.0', 'utf-8' );
			$doc->loadHTML( '<fragment>' . $content . '</fragment>', LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED ); // 
			$xpath = new DOMXpath( $doc );

			print $doc->saveHTML();
			// For each size xPath check again the images
			foreach( $size_queries as $k => $v ) {
				$images = $xpath->query( $k );
				// Match exists
				if( $images ) {
					// For each match grab the attachment id and log in the matches array
					foreach( $images as $image ) {
						$temp_filtered_image = '';
						if ( preg_match( '/wp-image-([0-9]+)/i', $doc->saveHTML( $image ), $class_id ) ) {
							// ___( $doc->saveHTML( $image ) );
							$final_classes = '';
							if( preg_match( '/class="(.+?)"/i', $doc->saveHTML( $image ), $classes ) ) {
								$final_classes = preg_replace( '/wp-image-([0-9]+)/i', '', $classes[1] );
							}
							// Ensure not been updated already
							$attachment_id = absint( $class_id[1] );
							if ( $attachment_id ) {
								// Update
								$src_sized = wei_bulk_generate( $attachment_id, $v );
								
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
								$temp_filtered_image .= '<img class="lazy wei-image ' . $final_classes . '" src="' . wei_generate_svg($last[3], $last[4]) . '" data-src="' . $last[1] . '" width="' . $last[3] . '" height="' . $last[4] . '">';
								
							}
						}
						if( !empty( $temp_filtered_image ) ) {
							wei_append_html_before( 
								$image->parentNode, 
								'<picture>' . $temp_filtered_image . '</picture>',
								$image
							);
							$image->parentNode->removeChild( $image );
						}
					}
				}
			} 
			
			// Remove wrapper
			$content = str_replace(
				array( '<fragment>', '</fragment>' ),
				'',
				$doc->saveHTML()
			);
		}

		return $content;
	}