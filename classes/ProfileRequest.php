<?php
namespace LTI;


class ProfileRequest extends Request {
	const page = 'profile';

	const content_type = 'application/vnd.ims.lti.v2.toolconsumerprofile+json';

	public static function check_params( $get, $post ) {
		return true;
	}

	public function handle() {
		header( 'Content-type: ' . self::content_type );

		$data = array(
			'@context'         => 'http://purl.imsglobal.org/ctx/lti/v2/ToolProviderProfile',
			'@type'            => 'ToolProviderProfile',

			// https://www.imsglobal.org/lti/model/mediatype/application/vnd/ims/lti/v2/toolproxy%2Bjson/index.html#ToolProfile
			'@id'              => $_SERVER['SERVER_ADDR'],
			'lti_version'      => Version::V_2,
			'product_instance' => array(
				'guid'          => '192.168.99.100',
				'product_info'  => array(
					'product_name'    => array(
						'default_value' => 'WIMS',
						'key'           => 'product.name',
					),
					'product_version' => '2016052303.06',
					'product_family'  => array(
						'code'   => 'wims',
						'vendor' => array(
							'code'        => 'wims',
							'vendor_name' => array(
								'default_value' => 'wims.fr',
								'key'           => 'product.vendor.name',
							),
							'timestamp'   => date( 'c' ),
						),
					),
				),
				'service_owner' => array(
					'@id'                => 'ServiceOwner',
					'service_owner_name' => array(
						'default_value' => 'LTI Tool Consumer',
						'key'           => 'service_owner.name',
					),
					'description'        => array(
						'default_value' => '',
						'key'           => 'service_owner.description',
					),
				),
			),
			'base_url_choice'  => array(
				// https://www.imsglobal.org/lti/model/mediatype/application/vnd/ims/lti/v2/toolproxy%2Bjson/index.html#BaseUrlChoice
				array( 'default_base_url' => $_SERVER['SERVER_ADDR'] ),
			),
			'resource_handler' => array(// https://www.imsglobal.org/lti/model/mediatype/application/vnd/ims/lti/v2/toolproxy%2Bjson/index.html#ResourceHandler
			),
			'message'          => array(
				// https://www.imsglobal.org/lti/model/mediatype/application/vnd/ims/lti/v2/toolproxy%2Bjson/index.html#MessageHandler
				array(
					'message_type' => 'basic-lti-launch-request',
					'path'         => '?page=basic-launch',
				),
			),
			'service_offered'  => array(
				// https://www.imsglobal.org/lti/model/mediatype/application/vnd/ims/lti/v2/toolproxy%2Bjson/index.html#RestService
				array(
					'@type' => 'RestService',

					'@id'      => 'tcp:ToolProxyBindingMemberships',
					'endpoint' => 'http://192.168.99.100:8082/mod/lti/services.php/{context_type}/{context_id}/bindings/{vendor_code}/{product_code}/{tool_code}/memberships',
					'format'   => [ 'application/vnd.ims.lis.v2.membershipcontainer+json' ],
					'action'   => [ 'GET' ],
				),
			),
		);
		echo json_encode( $data );
	}
}