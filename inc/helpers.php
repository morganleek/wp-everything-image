<?php
  // Debug Object Text Only (No rendered HTML)
  if( !function_exists( '____' ) ) {
    function ____( $obj ) {
      print '<pre>' . preg_replace( '/\r?\n|\r/', '', esc_html( print_r( $obj, true ) ) ) . '</pre>';
    }
  }

  // Debug Object
  if( !function_exists( '___', ) ) {
    function ___( $obj, $return = false ) {
			if( $return ) {
				return '<pre>' . print_r( $obj, true ) . '</pre>';
			}
			print '<pre>' . print_r( $obj, true ) . '</pre>';	
		}
  }

  // Debug Object Alias
	if( !function_exists( '_z' ) ) {
		function _z( $obj, $return = false ) {
			return ___( $obk, $return );
		}
	}