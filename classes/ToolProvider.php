<?php

namespace LTI;

abstract class ToolProvider implements Storable {
	private $values;

	public function __construct( $data ) {
		$this->values = $data;
	}

	public function __get( $key ) {
		if ( ! isset( $this->values[ $key ] ) ) {
			throw new \Exception( "$key not specified" );
		}

		return $this->values[ $key ];
	}

	public function insert() {
		// TODO: Implement insert() method.
	}
}