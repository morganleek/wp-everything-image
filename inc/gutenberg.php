<?php
	function wei_gutenberg_enabled() {
		// Gutenberg plugin is installed and activated.
		$gutenberg = ! ( false === has_filter( 'replace_editor', 'gutenberg_init' ) );

		// Block editor since 5.0.
		$block_editor = version_compare( $GLOBALS['wp_version'], '5.0-beta', '>' );

		if ( ! $gutenberg && ! $block_editor ) {
				return false;
		}

		if ( wei_is_classic_editor_plugin_active() ) {
				$editor_option       = get_option( 'classic-editor-replace' );
				$block_editor_active = array( 'no-replace', 'block' );

				return in_array( $editor_option, $block_editor_active, true );
		}

		return true;
	}

	function wei_is_classic_editor_plugin_active() {
    if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    if ( is_plugin_active( 'classic-editor/classic-editor.php' ) ) {
			return true;
    }

    return false;
	}

	function wei_image_walker_resizer( &$block, $args ) {
		$defaults = array( 
			'parent' => '',
			'siblings' => 0,
			'depth' => 0,
			'path' => array()
		);
		$_args = wp_parse_args( $args, $defaults );
		
		if( $block ) {
			if( !empty( $block['blockName'] ) ) {
				if( !empty( $_args['parent'] ) ) {
					$_args['path'][] = $_args['parent'] . ':' . $_args['siblings'];
				}
				// ___( $_args['parent'] . ':' . $_args['siblings'] . ',' . $block['blockName'] );
				if( $block['blockName'] == 'core/image' && isset( $block['attrs']['id'] ) ) {
					$classes = array( 'wp-block-image' );
					if( isset( $block['attrs']['align'] ) ) {
						$classes[] = 'align' . $block['attrs']['align'];
					}
					if( isset( $block['attrs']['sizeSlug'] ) ) {
						$classes[] = 'size-' . $block['attrs']['sizeSlug'];
					}

					$sizes = wei_block_image_sizes( $block['id'], $_args['path'] );
					$image = wei_image( // Image as Picture tag
						$block['attrs']['id'], 
						array(
							'type' => 'image',
							'sizes' => $sizes,
							'class' => implode( ' ', $classes ),
							'return' => true
						)
					);
					$block['innerHTML'] = $image;
					$block['innerContent'][0] = $image;
				}
				if( count( $block['innerBlocks'] ) > 0 ) {
					$_args['parent'] = $block['blockName'];
					foreach( $block['innerBlocks'] as $key => $child ) {
						wei_image_walker_resizer( $block['innerBlocks'][$key], array(
							'parent' => $_args['parent'], 
							'siblings' => count( $block['innerBlocks'] ),
							'depth' => $_args['depth'] + 1,
							'path' => $_args['path']
						) );
					}
				}
			}
		}
		return;
	}

	function wei_block_image_sizes( $id = 0, $path = 0 ) {
		// Move these sizes somewhere easy to update
		$default_sizes = array(
			'1500' => array(1500, 0, false), 
			'1200' => array(1200, 0, false), 
			'992' => array(992, 0, false), 
			'768' => array(768, 0, false), 
			'1' => array(375, 0, false) 
		);

		$default_sizes = apply_filters( 'wei_default_gutenberg_size', $default_sizes );

		// Example
		// $sizes_array = array(
		// 	'core/columns:2' => array(
		// 		'1500' => array(750, 0, false), 
		// 		'1200' => array(600, 0, false), 
		// 		'992' => array(496, 0, false), 
		// 		'768' => array(384, 0, false), 
		// 		'1' => array(375, 0, false) 
		// 	),
		// 	'core/columns:3' => array(
		// 		'1500' => array(500, 0, false), 
		// 		'1200' => array(400, 0, false), 
		// 		'992' => array(330, 0, false), 
		// 		'768' => array(256, 0, false), 
		// 		'1' => array(375, 0, false) 
		// 	)
		// );

		// sizes filter
		$sizes_array = apply_filters( 'wei_add_gutenberg_size', $sizes_array );
		
		foreach( $sizes_array as $k => $r ) {
			if( in_array( $k, $path ) ) {
				return $sizes_array[$k];
			}
		}
		return $default_sizes;
	}

	function wei_filter_content_images( $content ) {
		if( apply_filters( 'wei_activate_gutenberg_resize', false ) ) {
			// Check for Gutenberg
			if( wei_gutenberg_enabled() ) {			
				// Avoid admin JSON posts
				if( empty( $GLOBALS['wp']->query_vars['rest_route'] ) ) { 
					// Get blocks
					$blocks = parse_blocks( $content );
	
					// Walk each block to find images and their path for appropriate resize
					foreach( $blocks as $block_key => $block ) {
						$args = array( 
							'parent' => '',
							'siblings' => count( $blocks ),
							'depth' => 0
						);
						wei_image_walker_resizer( $blocks[$block_key], $args );
					}
				
					// Turn back into HTML
					$return = '';
					foreach ( $blocks as $block ) {
						// Render block
						$return .= render_block( $block );
					}
					return $return;
				}
			}
		}
		return $content;
	}
	
	add_filter( 'the_content', 'wei_filter_content_images', 1 );