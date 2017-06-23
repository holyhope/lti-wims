<?php
namespace LTI\Requests;

use LTI\WimsQuestion;
use LTI\User;
use LTI\LISPerson;
use LTI\Context;
use LTI\Custom;
use LTI\ToolConsumer;
use LTI\LaunchPresentation;
use LTI\ResourceLink;

use LTI\Router;
use LTI\Oauth;

require_once 'Request.php';

class BasicLaunchRequest extends PostRequest {
	const page         = 'basic-launch';
	const message_type = 'basic-lti-launch-request';

	public $resource_link;

	public $roles; //!< Array of roles (Recommended)

	public $user;
	public $context; //!< Context of the request
	public $launch_presentation;
	public $tool_consumer;
	public $lis_person; //!< LIS Profil (Recommended)

	public $question;

	public $customs;

	public function __construct( $get, $post ) {
		parent::__construct( $get, $post );

		$this->user = new User( $post );
		foreach ( new LISPerson( $post ) as $item => $value ) {
			$this->user->$item = $value;
		}
		$this->context             = new Context( $post );
		$this->customs             = Custom::get_all( $post );
		$this->tool_consumer       = new ToolConsumer( $post );
		$this->launch_presentation = new LaunchPresentation( $post );
		$this->resource_link       = new ResourceLink( $post );
		$this->roles               = explode( ',', $post['roles'] );

		$this->question = WimsQuestion::load( $this->customs['id'] );
	}

	public static function check_params( $get, $post ) {
		parent::check_params( $get, $post );
		$oauth = new Oauth( static::get_headers(), $get, $post );
		//$oauth->check();
		//assert( isset( $post[ Custom::prefix . 'id' ] ) );
	}

	public function handle() {
		User::set_current( $this->user );

		echo $this->render();
	}

	public function support( $version ) {
		return true;
	}

	/**
	 * @return string
	 */
	public function render(): string {
		$mustache = Router::$template;
		$tpl      = $mustache->loadTemplate( 'basic-launch-' . $this->launch_presentation->document_target );

		$data = array(
			'return_url'  => $this->launch_presentation->return_url,
			'resource'    => $this->question->render(),
			'resource_id' => $this->question->id,
			'title'       => $this->context->title . ' - ' . $this->resource_link->title,
			'description' => $this->resource_link->description,
		);

		if ( isset( $this->launch_presentation->css_url ) ) {
			$data['css_url'] = $this->launch_presentation->css_url;
		}

		return $tpl->render( $data );
	}


	// helper to try to sort out headers for people who aren't running apache
	public static function get_headers() {
		if ( function_exists( 'apache_request_headers' ) ) {
			// we need this to get the actual Authorization: header
			// because apache tends to tell us it doesn't exist
			$in  = apache_request_headers();
			$out = array();
			foreach ( $in as $key => $value ) {
				$key         = str_replace( " ", "-", ucwords( strtolower( str_replace( "-", " ", $key ) ) ) );
				$out[ $key ] = $value;
			}

			return $out;
		}
		// otherwise we don't have apache and are just going to have to hope
		// that $_SERVER actually contains what we need
		$out = array();
		foreach ( $_SERVER as $key => $value ) {
			if ( substr( $key, 0, 5 ) == "HTTP_" ) {
				// this is chaos, basically it is just there to capitalize the first
				// letter of every word that is not an initial HTTP and strip HTTP
				// code from przemek
				$key         = str_replace( " ", "-", ucwords( strtolower( str_replace( "_", " ", substr( $key, 5 ) ) ) ) );
				$out[ $key ] = $value;
			}
		}

		return $out;
	}
}

