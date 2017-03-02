<?php
namespace LTI;

require_once 'OAuthSignature.php';

/**
 * @property string signature
 * @property string signature_method
 * @property string consumer_key
 * @property string version
 */
class Oauth extends LTIObject {
	const prefix             = 'oauth_';
	const supported_versions = array( '1.0' );

	public $params;

	public function __construct( $headers, $get, $post ) {
		parent::__construct( $post );

		$parameters = array_merge( $get, $post );

		// We have a Authorization-header with OAuth data. Parse the header
		// and add those overriding any duplicates from GET or POST
		if ( @substr( $headers['Authorization'], 0, 6 ) == "OAuth " ) {
			$header_parameters = static::split_header( $headers['Authorization'] );
			$parameters        = array_merge( $parameters, $header_parameters );
		}

		$this->params = $parameters;
	}

	// Utility function for turning the Authorization: header into
	// parameters, has to do some unescaping
	// Can filter out any non-oauth parameters if needed (default behaviour)
	public static function split_header( $header, $only_allow_oauth_parameters = true ): array {
		$pattern = '/(([-_a-z]*)=("([^"]*)"|([^,]*)),?)/';
		$offset  = 0;
		$params  = array();
		while ( preg_match( $pattern, $header, $matches, PREG_OFFSET_CAPTURE, $offset ) > 0 ) {
			$match          = $matches[0];
			$header_name    = $matches[2][0];
			$header_content = ( isset( $matches[5] ) ) ? $matches[5][0] : $matches[4][0];
			if ( preg_match( '/^oauth_/', $header_name ) || ! $only_allow_oauth_parameters ) {
				$params[ $header_name ] = \OAuthSignature::urldecode_rfc3986( $header_content );
			}
			$offset = $match[1] + strlen( $match[0] );
		}

		if ( isset( $params['realm'] ) ) {
			unset( $params['realm'] );
		}

		return $params;
	}

	public function check() {
		if ( ! in_array( $this->version, static::supported_versions ) ) {
			throw new \Exception( "Invalid oauth version" );
		}

		$expected = \OAuthSignature::build_signature( $this->signature_method, $this->get_signature_base_string(), $this->consumer_key, 'p9R!rwR$' )->signature;

		if ( $expected != $this->signature ) {
			error_log( 'OAuth signature seems broken' );
			// throw new \Exception( "Invalid oauth signature expected $expected got " . $this->signature );
		}
	}

	/**
	 * The request parameters, sorted and concatenated into a normalized string.
	 *
	 * @return string
	 */
	public function get_signable_parameters() {
		// Grab all parameters
		$params = $this->params;

		// Remove oauth_signature if present
		// Ref: Spec: 9.1.1 ("The oauth_signature parameter MUST be excluded.")
		if ( isset( $params[ static::prefix . 'signature' ] ) ) {
			unset( $params[ static::prefix . 'signature' ] );
		}

		return \OAuthSignature::build_http_query( $params );
	}

	/**
	 * just uppercases the http method
	 */
	public function get_normalized_http_method() {
		return strtoupper( $_SERVER['REQUEST_METHOD'] );
	}

	/**
	 * parses the url and rebuilds it to be
	 * scheme://host/path
	 */
	public function get_normalized_http_url() {
		$scheme = empty( $_SERVER['HTTPS'] ) ? 'http' : 'https';

		$parts = parse_url(
			$scheme .
			'://' . $_SERVER['HTTP_HOST'] .
			$_SERVER['REQUEST_URI']
		);

		$port   = @$parts['port'];
		$scheme = $parts['scheme'];
		$host   = $parts['host'];
		$path   = @$parts['path'];

		$port or $port = ( $scheme == 'https' ) ? '443' : '80';

		if ( ( $scheme == 'https' && $port != '443' ) || ( $scheme == 'http' && $port != '80' ) ) {
			$host = "$host:$port";
		}

		return "$scheme://$host$path";
	}


	/**
	 * Returns the base string of this request
	 *
	 * The base string defined as the method, the url
	 * and the parameters (normalized), each urlencoded
	 * and the concated with &.
	 */
	public function get_signature_base_string() {
		$parts = \OAuthSignature::urlencode_rfc3986( array(
			$this->get_normalized_http_method(),
			$this->get_normalized_http_url(),
			$this->get_signable_parameters(),
		) );

		return implode( '&', $parts );
	}
}