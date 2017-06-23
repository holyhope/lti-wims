<?php
namespace LTI;

/**
 * Class Question
 *
 * @property integer      id
 * @property string       type
 * @property ToolProvider tool_provider
 */
abstract class Question implements Storable {
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

	public abstract function render(): string;

	public function insert() {
		// TODO: Implement insert() method.
	}

	public abstract function get_mark(User $user);
}
