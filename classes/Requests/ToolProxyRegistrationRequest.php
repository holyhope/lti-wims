<?php
namespace LTI\Requests;

use LTI\Registration;
use LTI\ToolConsumerProfile;

use LTI\DatabaseSession;


class ToolProxyRegistrationRequest extends PostRequest {
	const page         = 'register';
	const message_type = 'ToolProxyRegistrationRequest';

	const content_type = 'application/vnd.ims.lti.v2.toolproxy+json';

	public $registration;
	public $profile;
	public $return_url;

	public function __construct( array $get, array $post ) {
		parent::__construct( $get, $post );

		$this->registration = new Registration( $post );
		$this->profile      = $post['tc_profile_url'];
		$this->return_url   = $post['launch_presentation_return_url'];
	}

	private function request_profile() {
		$oauth       = new \OAuth( $this->registration->values['key'], $this->registration->values['password'] );
		$headers_raw = explode( ',', ltrim( $oauth->getRequestHeader( 'GET', $this->profile ), 'OAuth ' ) );
		$headers     = array();
		foreach ( $headers_raw as $header ) {
			$header                = explode( '=', $header );
			$headers[ $header[0] ] = substr( $header[1], 1, strlen( $header[1] ) - 2 );
		}

		$request = curl_init();
		curl_setopt( $request, CURLOPT_URL, $this->profile );
		curl_setopt( $request, CURLOPT_RETURNTRANSFER, 1 );

		$data = curl_exec( $request );
		if ( curl_errno( $request ) ) {
			throw new \Exception( "Error: " . curl_error( $request ) );
		}
		curl_close( $request );

		return new ToolConsumerProfile( json_decode( $data ) );
	}

	private function find_registration_service( $profile ) {
		foreach ( $profile->values->service_offered as $service ) {
			if ( in_array( self::content_type, $service->format ) ) {
				return $service;
			}
		}

		throw new \Exception( "Service not found" );
	}

	public function redirect() {
		header(
			'Location: ' .
			$this->return_url . '?' .
			http_build_query( array(
				'status'          => 'failure',
				'tool_proxy_guid' => $this->guid,
			) )
		);
		die();
	}

	public function handle() {
		$profile = $this->request_profile();

		$service = $this->find_registration_service( $profile );

		$this->register_tool( $service );

		$db = DatabaseSession::from_config();
		$profile->insert( $db );

		$this->redirect();
	}

	public function support( $version ) {
		return in_array( strtolower( $version ), array( 'lti-2p0' ) );
	}

	public function __get( $key ) {
		switch ( $key ) {
			case 'guid':
				return $this->registration->key;
			default:
				return $this->$key;
		}
	}

	private function register_tool( $service ) {
		$data = array(
			"@context"              => "http://purl.imsglobal.org/ctx/lti/v2/ToolProxy",
			"@type"                 => "ToolProxy",
			"@id"                   => $_SERVER['SERVER_NAME'],
			"lti_version"           => "LTI-2p0",
			"tool_proxy_guid"       => $this->guid,
			"tool_consumer_profile" => $this->profile,
			"tool_profile"          => ( $_SERVER['HTTPS'] ? 'https' : 'http' ) . '://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . '/?' . http_build_query( array( 'page' => ProfileRequest::page ) ),
			'security_contract'     => array(
				// https://www.imsglobal.org/lti/model/mediatype/application/vnd/ims/lti/v2/toolproxy%2Bjson/index.html#SecurityContract
				'shared_secret' => $this->registration->values['key'],
			),
			'enabled_capability'    => array( '' ),
		);

		$oauth       = new \OAuth( $this->registration->values['key'], $this->registration->values['password'] );
		$headers_raw = explode( ',', ltrim( $oauth->getRequestHeader( 'POST', $this->profile, $data ), 'OAuth ' ) );
		$headers     = array();
		foreach ( $headers_raw as $header ) {
			$header                = explode( '=', $header );
			$headers[ $header[0] ] = substr( $header[1], 1, strlen( $header[1] ) - 2 );
		}

		$headers['Content-Type']    = self::content_type;
		$headers['oauth_body_hash'] = hash_hmac( 'sha1', http_build_query( $data ), $this->registration->values['key'] );

		echo '<pre>';
		var_dump( $headers );
		var_dump( $data );
		echo '</pre>';

		$request = curl_init();
		curl_setopt( $request, CURLOPT_URL, $service->endpoint );
		curl_setopt( $request, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $request, CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $request, CURLOPT_POST, 1 );
		curl_setopt( $request, CURLOPT_POSTFIELDS, $data );

		$data = curl_exec( $request );

		$http_code = curl_getinfo( $request, CURLINFO_HTTP_CODE );
		if ( $http_code != 201 ) {
			if ( curl_errno( $request ) ) {
				throw new \Exception( "Error: " . curl_error( $request ) );
			}
			throw new \Exception( "($http_code) Cannot register Tool Proxy" );
		}

		curl_close( $request );

		echo '<pre>';
		var_dump( $data );
		echo '</pre>';
		die();
	}
}