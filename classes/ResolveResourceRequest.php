<?php

namespace LTI;

require_once 'Request.php';

class ResolveResourceRequest extends Request {
	const page = 'resolve-resource';

	public $resource;

	public $return_url; //!< Array of roles (Recommended)

	public $user;
	public $context; //!< Context of the request
	public $launch_presentation;
	public $tool_consumer;
	public $lis_person; //!< LIS Profil (Recommended)

	public  $customs;
	private $mark;
	private $max = 10;

	public function __construct( $get, $post ) {
		parent::__construct( $get, $post );

		$this->user       = User::get_current();
		$this->mark       = rand( 0, $this->max );
		$this->return_url = static::sanitize_returl_url( $post['return_url'] );
	}

	private static function sanitize_returl_url( $url ) {
		$url    = parse_url( $url );
		$scheme = isset( $url['scheme'] ) ? $url['scheme'] : ( $url['port'] == '443' ? 'https' : 'http' );
		$host   = $url['host'];
		$port   = isset( $url['port'] ) ? $url['port'] : ( $scheme == 'http' ? '80' : '443' );
		$path   = ltrim( @$url['path'], '/' );
		$query  = isset( $url['query'] ) ? $url['query'] . '&' : '';

		return "${scheme}://${host}:${port}/${path}?${query}";
	}

	public static function check_params( $get, $post ) {
		$return_url = parse_url( $post['return_url'] );
		if ( empty( $return_url['host'] ) ) {
			throw new \HttpInvalidParamException( '', 400 );
		}

		return true;
	}


	public function handle() {
		header( 'Location: ' . $this->return_url . 'lti_msg=' . urlencode( $this->render() ) );
		echo "Redirection to " . $this->return_url;
	}

	public function support( $version ) {
		return true;
	}

	private function render() {
		$mustache = Router::$template;
		$tpl      = $mustache->loadTemplate( 'resolve-resource' );

		$data = array(
			'name' => $this->user->id,
			'mark' => $this->mark,
			'max'  => $this->max,
		);

		return $tpl->render( $data );
	}
}

