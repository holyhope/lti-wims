<?php

class OAuthSignature {
	public $signature;
	public $timestamp_threshold;

	const methods = array(
		'HMAC-SHA1' => array( __CLASS__, 'build_signature_HMAC_SHA1' ),
	);

	public function __construct( string $signature, int $timestamp_threshold = 300 ) {
		$this->signature           = $signature;
		$this->timestamp_threshold = $timestamp_threshold;
	}


	public static function build_http_query( $params ) {
		if ( ! $params ) {
			return '';
		}

		// Urlencode both keys and values
		$keys   = static::urlencode_rfc3986( array_keys( $params ) );
		$values = static::urlencode_rfc3986( array_values( $params ) );
		$params = array_combine( $keys, $values );

		// Parameters are sorted by name, using lexicographical byte value ordering.
		// Ref: Spec: 9.1.1 (1)
		uksort( $params, 'strcmp' );

		$pairs = array();
		foreach ( $params as $parameter => $value ) {
			if ( is_array( $value ) ) {
				// If two or more parameters share the same name, they are sorted by their value
				// Ref: Spec: 9.1.1 (1)
				natsort( $value );
				foreach ( $value as $duplicate_value ) {
					$pairs[] = $parameter . '=' . $duplicate_value;
				}
			} else {
				$pairs[] = $parameter . '=' . $value;
			}
		}
		// For each parameter, the name is separated from the corresponding value by an '=' character (ASCII code 61)
		// Each name-value pair is separated by an '&' character (ASCII code 38)
		return implode( '&', $pairs );
	}

	public static function build_signature( string $method, string $base_string, string $identifier, string $secret = '' ): OAuthSignature {
		return call_user_func( static::methods[ $method ], $base_string, $identifier, $secret );
	}

	public static function build_signature_HMAC_SHA1( string $base_string, string $identifier, string $secret ): OAuthSignature {
		$key = implode( '&', static::urlencode_rfc3986( array(
			$identifier,
			$secret,
		) ) );

		$computed_signature = base64_encode( hash_hmac( 'sha1', $base_string, $key, true ) );

		return new static ( $computed_signature );
	}

	/**
	 * check that the timestamp is new enough
	 */
	public function check_timestamp( $timestamp ): Void {
		// verify that timestamp is recentish
		$now = time();
		if ( $now - $timestamp > $this->timestamp_threshold ) {
			throw new \OAuthException( "Expired timestamp, yours $timestamp, ours $now" );
		}
	}

	public static function urlencode_rfc3986( $input ) {
		if ( is_array( $input ) ) {
			return array_map( array( __NAMESPACE__ . '\\' . __CLASS__, __METHOD__ ), $input );
		}

		return str_replace( '+', ' ', str_replace( '%7E', '~', rawurlencode( $input ) ) );
	}

	// This decode function isn't taking into consideration the above
	// modifications to the encoding process. However, this method doesn't
	// seem to be used anywhere so leaving it as is.
	public static function urldecode_rfc3986( string $string ): string {
		return urldecode( $string );
	}
}
