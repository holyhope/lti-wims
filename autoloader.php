<?php
namespace LTI;

require_once 'config.php';

spl_autoload_register( function ( $class_name ) {
	$class = explode( '\\', $class_name );
	$root  = array_shift( $class );
	$path  = implode( DIRECTORY_SEPARATOR, $class );
	if ( $root == 'LTI' ) {
		@include_once implode( DIRECTORY_SEPARATOR, array(
			CLASS_PATH,
			"${path}.php",
		) );
	}
} );