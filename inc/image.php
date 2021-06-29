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
		$size_queries = apply_filters( 'wei_wp_size_array', array() );
		if( !empty( $size_queries ) ) {
			foreach( $size_queries as $selector => $size ) {
				// Search DOM by selectors
				$images = $dom->find( $selector );
				
				// Each image found
				foreach( $images as $k => $image ) {
					if( !$image->hasAttribute( "data-parsed" ) ) {
						// Grab attachment id
						if ( preg_match( '/wp-image-([0-9]+)/i', $image->__toString(), $class_id ) ) {
							// Grab classes no the attachemnt id
							$attachment_id = absint( $class_id[1] );

							// Build <picture> tag
							if ( $attachment_id ) {
								// Migrate existing classes
								$migrate_classes = '';
								if( preg_match( '/class="(.+?)"/i', $image->__toString(), $classes ) ) {
									$migrate_classes = preg_replace( '/wp-image-([0-9]+)/i', '', $classes[1] );
								}
								
								// Build srcset
								if( $image->getAttribute( 'width' ) || $image->getAttribute( 'height' ) ) {
									// Has inline height or width
									$alt_size = array(
										'1' => array(
											$image->getAttribute( 'width' ) ?: 0, 
											$image->getAttribute( 'height' ) ?: 0, 
											false) 
									);
									$srcsets = wei_bulk_generate( $attachment_id, $alt_size );
								}
								else {
									// No inline height or width use user defined
									$srcsets = wei_bulk_generate( $attachment_id, $size );
								}
							
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
									$width = $last[3];
									$height = $last[4];
									$image_html .= '<img class="lazy wei-image ' . $migrate_classes . '" src="' . wei_generate_svg($last[3], $last[4]) . '" data-src="' . $last[1] . '" data-parsed="1" width="' . $width . '" height="' . $height . '" data-item-processed="' . $k . '">';
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