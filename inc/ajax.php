<?php
	// // How to use
	// wp.ajax.send('wei_image', {
	//   data: {
	//     "action":"wei_image","image_id":5,"sizes":{"768":[768,300,true],"1":[350,200,true]}
	//   },
	//   error: function() {},
	//   success: function(r) { console.log(r); },
	//   type: 'POST'
	// });
	//
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