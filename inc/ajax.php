<?php
	// Ajax 
	if( !function_exists( 'wei_image_ajax' ) ) {
		function wei_image_ajax() {
			// var data = {
			// 	'action': 'wei_image',
			// 	'image_id': 16,
			// 	'sizes': {
			// 		768: [768, 300, true],
			// 		1: [350, 200, true]  
			// 	}
			// }
			
			$image_id = $_POST['image_id'];
			$sizes = json_decode($_POST['sizes']);

			// _z('Image id: ' . $image_id);
			// _z($sizes);
			wp_send_json_success($sizes, true);

			// 16
			// wp_send_json_success(wei_bulk_generate($image_id, $sizes), true);

			wp_die();
		}

		add_action( 'wp_ajax_wei_image', 'wei_image_ajax' );
	}
