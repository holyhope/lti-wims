<?php


namespace LTI;


class ToolConsumerProfile extends LTIStorableObject {
	public $table_name = 'tool_consumer_profile';

	public $values = array();

	/**
	 * ToolConsumerProfile constructor.
	 */
	public function __construct( $data ) {
		$this->values = $data;
	}
}