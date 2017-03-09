<?php
namespace LTI;

class Question extends LTIObject implements Storable {
	public static function load( $id ): Question {
		//Load from source
		return new static( array(
			'id' => $id,
		) );
	}

	public function render(): string {
		$mustache = Router::$template;
		$tpl      = $mustache->loadTemplate( 'question' );

		return $tpl->render( array( 'id' => $this->id ) );
	}

	public function insert() {
		// TODO: Implement insert() method.
	}
}
