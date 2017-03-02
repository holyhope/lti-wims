<?php
namespace LTI;

class Question extends LTIObject {

	public function __construct( $post ) {
		parent::__construct( $post );
		//Load data from DB
	}

	public function render(): string {
		$mustache = Router::$template;
		$tpl      = $mustache->loadTemplate( 'question' );

		return $tpl->render( array() );
	}
}