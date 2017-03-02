<?php

namespace LTI;

const LTI_TYPE_BASIC_LAUNCH      = 'basic-lti-launch-request';
const LTI_TYPE_TOOL_REGISTRATION = 'ToolProxyRegistrationRequest';


abstract class Request {
	const page = false;

	/**
	 * Handle the request
	 *
	 * @param array $get  - GET parameters
	 * @param array $post - POST parameters
	 */
	public function __construct( $get, $post ) {
	}

	public abstract static function check_params( $get, $post );

	public abstract function handle();
}
