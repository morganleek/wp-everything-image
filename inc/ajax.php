<?php
	// Ajax 
	if( !function_exists( 'wei_image_ajax' ) ) {
		function wei_image_ajax() {
			// This needs to be sanitzed
			$image_id = $_POST['image_id'];
			$sizes = $_POST['sizes']; // json_decode($_POST['sizes']);

			wp_send_json_success(wei_bulk_generate($image_id, $sizes), true);

			wp_die();
		}

		add_action( 'wp_ajax_wei_image', 'wei_image_ajax' );
	}