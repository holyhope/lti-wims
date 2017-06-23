<?php


namespace LTI;


abstract class LTIObject implements \Iterator {
	const prefix = false;

	public $values = array();

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

	public function __set( $key, $value ) {
		return $this->values[ $key ] = $value;
	}


	public function current() {
		return current( $this->values );
	}

	public function next() {
		return next( $this->values );
	}

	public function key() {
		return key( $this->values );
	}

	public function valid() {
		return false !== current( $this->values );
	}

	public function rewind() {
		return reset( $this->values );
	}
}