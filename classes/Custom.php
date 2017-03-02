<?php


namespace LTI;


class Custom extends LTIObject {
	const prefix = 'custom_';

	public static function get_all( $post ) {
		return self::filter_values( 'custom_', $post );
	}
}