<?php
namespace LTI;

include_once '../config.php';

if ( isset( $_GET['php'] ) ) {
	echo phpinfo();
} else {
	header( 'Content-type: application/json' );

	echo json_encode( array(
		'ROOT_PATH'          => ROOT_PATH,
		'CLASS_PATH'         => CLASS_PATH,
		'ACTIVATED_REQUESTS' => ACTIVATED_REQUESTS,
		'DB_NAME'            => DB_NAME,
		'DB_USER'            => DB_USER,
		'DB_PASSWORD'        => DB_PASSWORD,
		'DB_HOST'            => DB_HOST,
		'DB_PORT'            => DB_PORT,
	) );
}