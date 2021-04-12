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
		add_filter( 'the_content', 'wei_wp_image_filter_image_sizes', 9, 1 );
		add_filter( 'the_excerpt', 'wei_wp_image_filter_image_sizes', 9, 1 );
		add_filter( 'widget_text_content', 'wei_wp_image_filter_image_sizes', 9, 1 );
	}

	function wei_wp_image_filter_image_sizes( $content ) {
		// Create DOM from $content
		$dom = new Dom();
		$dom->loadStr( $content );

		// Get sizes via filter
		$size_queries = apply_filters( 'wei_wp_size_array', $size_queries );
		if( !empty( $size_queries ) ) {
			foreach( $size_queries as $selector => $size ) {
				// Search DOM by selectors
				$images = $dom->find( $selector );
				
				foreach( $images as $image ) {
					if( !$image->hasAttribute( "data-parsed" ) ) {
						// Grab attachment id
						if ( preg_match( '/wp-image-([0-9]+)/i', $image->__toString(), $class_id ) ) {
							// Grab classes no the attachemnt id
							$attachment_id = absint( $class_id[1] );

							// Migrate existing classes
							$migrate_classes = '';
							if( preg_match( '/class="(.+?)"/i', $image->__toString(), $classes ) ) {
								$migrate_classes = preg_replace( '/wp-image-([0-9]+)/i', '', $classes[1] );
							}

							// Build <picture> tag
							if ( $attachment_id ) {
								// Update
								$srcsets = wei_bulk_generate( $attachment_id, $size );
								// Image HTML
								$image_html = '';
								// Get srcset data
								$image_html .= '<picture>';
									foreach($srcsets as $srcset) {
										$image_html .= wei_generate_picture_source(array(
											'url' => $srcset[1], 
											'url_retina' => $srcset[2], 
											'min_width' => $srcset[0],
											'width' => $srcset[3],
											'height' => $srcset[4],
											'svg_placeholder' => $srcset[5]
										));	
									}
									$last = array_pop( $srcsets );
									$image_html .= '<img class="lazy wei-image ' . $final_classes . '" src="' . wei_generate_svg($last[3], $last[4]) . '" data-src="' . $last[1] . '" data-parsed="1" width="' . $last[3] . '" height="' . $last[4] . '">';
								$image_html .= '</picture>';
								// Create DOM and then grab picture elemnt
								$image_dom = new Dom();
								$image_dom->loadStr( $image_html );
								$image->getParent()->replaceChild( $image->id(), $image_dom->find('picture')[0] );
							}
						}
					}
				}
			}
		}

		return $dom->innerHtml;
	}

	// Return sizes to matched img tags
	// function wei_wp_image_filter_image_sizes( $content, $context = null ) {
	// 	// Matches - key is the attachment id, value is the size array
	// 	$image_matches = array();
	// 	// print '<div>' . $content . '</div>';

	// 	// Get user defined sizes
	// 	$size_queries = apply_filters( 'wei_wp_size_array', $size_queries );

	// 	if( !empty( $size_queries ) ) {
	// 		// XPath Checks
	// 		libxml_use_internal_errors( true ); // Supress invalid HTML messages
	// 		$doc = new DOMDocument( '1.0', 'utf-8' );
	// 		$doc->loadHTML( '<fragment>' . $content . '</fragment>', LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED ); // 
	// 		$xpath = new DOMXpath( $doc );

	// 		print $doc->saveHTML();
	// 		// For each size xPath check again the images
	// 		foreach( $size_queries as $k => $v ) {
	// 			$images = $xpath->query( $k );
	// 			// Match exists
	// 			if( $images ) {
	// 				// For each match grab the attachment id and log in the matches array
	// 				foreach( $images as $image ) {
	// 					$temp_filtered_image = '';
	// 					if ( preg_match( '/wp-image-([0-9]+)/i', $doc->saveHTML( $image ), $class_id ) ) {
	// 						// ___( $doc->saveHTML( $image ) );
	// 						$final_classes = '';
	// 						if( preg_match( '/class="(.+?)"/i', $doc->saveHTML( $image ), $classes ) ) {
	// 							$final_classes = preg_replace( '/wp-image-([0-9]+)/i', '', $classes[1] );
	// 						}
	// 						// Ensure not been updated already
	// 						$attachment_id = absint( $class_id[1] );
	// 						if ( $attachment_id ) {
	// 							// Update
	// 							$src_sized = wei_bulk_generate( $attachment_id, $v );
								
	// 							foreach($src_sized as $i) {
	// 								$temp_filtered_image .= wei_generate_picture_source(array(
	// 									'url' => $i[1], 
	// 									'url_retina' => $i[2], 
	// 									'min_width' => $i[0],
	// 									'width' => $i[3],
	// 									'height' => $i[4],
	// 									'svg_placeholder' => $i[5]
	// 								));	
	// 							}
	// 							$last = array_pop( $src_sized );
	// 							$temp_filtered_image .= '<img class="lazy wei-image ' . $final_classes . '" src="' . wei_generate_svg($last[3], $last[4]) . '" data-src="' . $last[1] . '" width="' . $last[3] . '" height="' . $last[4] . '">';
								
	// 						}
	// 					}
	// 					if( !empty( $temp_filtered_image ) ) {
	// 						wei_append_html_before( 
	// 							$image->parentNode, 
	// 							'<picture>' . $temp_filtered_image . '</picture>',
	// 							$image
	// 						);
	// 						$image->parentNode->removeChild( $image );
	// 					}
	// 				}
	// 			}
	// 		} 
			
	// 		// Remove wrapper
	// 		$content = str_replace(
	// 			array( '<fragment>', '</fragment>' ),
	// 			'',
	// 			$doc->saveHTML()
	// 		);
	// 	}

	// 	return $content;
	// }