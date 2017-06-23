<?php

namespace LTI;

class PresentationTarget {
	const LABEL = 'launch_presentation_document_target';

	const EMBED   = 'embed';
	const FRAME   = 'frame';
	const POPUP   = 'popup';
	const IFRAME  = 'iframe';
	const WINDOW  = 'window';
	const OVERLAY = 'overlay';

	public $value = false;

	function __construct( $target ) {
		if ( ! in_array( $target, array(
			self::EMBED,
			self::FRAME,
			self::POPUP,
			self::IFRAME,
			self::WINDOW,
			self::OVERLAY,
		) )
		) {
			throw new \ErrorException( 'Invalid target presentation' );
		}
		$this->value = $target;
	}
}

class Presentation {
	public $width      = 'launch_presentation_width';
	public $height     = 'launch_presentation_height';
	public $locale     = 'launch_presentation_locale';
	public $css_url    = 'launch_presentation_css_url';
	public $return_url = 'launch_presentation_return_url';

	public function __construct( $get, $post ) {
		$this->width               = $post['launch_presentation_width'];
		$this->height              = $post['launch_presentation_height'];
		$this->locale              = $post['launch_presentation_locale'];
		$this->css_url             = $post['launch_presentation_css_url'];
		$this->return_url          = $post['launch_presentation_return_url'];
		$this->presentation_target = new PresentationTarget( $post[ PresentationTarget::LABEL ] );
	}

	public function render() {
		switch ( $this->presentation_target ) {
			case PresentationTarget::IFRAME:
				return include TEMPLATE_PATH . '/basic-launch.mustache';
			default:
				return "";
		}
	}
}
