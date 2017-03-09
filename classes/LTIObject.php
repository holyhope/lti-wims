<?php


namespace LTI;


abstract class LTIObject {
	const prefix = false;

	public function __construct( array $post ) {
		$this->values = self::filter_values( static::prefix, $post );
	}

	public static function filter_values( $prefix, $post ) {
		if ( ! $prefix ) {
			return $post;
		}
		
		$prefix_len = strlen( $prefix );
		$values     = array();

		foreach ( $post as $key => $value ) {
			if ( substr( $key, 0, $prefix_len ) == $prefix ) {
				$values[ substr( $key, $prefix_len ) ] = $value;
			}
		}

		return $values;
	}

	public function __get( $key ) {
		if ( ! isset( $this->values[ $key ] ) ) {
			throw new \Exception( static::prefix . "$key not specified" );
		}

		return $this->values[ $key ];
	}
}