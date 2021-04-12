<?php 
  if( !function_exists( 'array_key_last' ) ) {
    function array_key_last( $array ) {
      if( !is_array( $array ) || empty( $array ) ) {
        return NULL;
      }

      return array_keys( $array )[count( $array ) - 1];
    }
  }


