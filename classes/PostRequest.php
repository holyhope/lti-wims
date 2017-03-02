<?php
namespace LTI;


abstract class PostRequest extends Request {
	const message_type = false;

	public static function check_params( $get, $post ) {
		if ( empty( $post['lti_message_type'] ) || $post['lti_message_type'] != static::message_type ) {
			throw new \Exception( "Invalid parameters" );
		}
	}
}