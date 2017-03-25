<?php

namespace LTI;

class Router {
	/**
	 * @var DatabaseSession $db - Database connector
	 */
	public static $db;
	public static $template;

	private $requests = array();

	function register_request( $page, $request ) {
		$this->requests[ $page ] = $request;

		return $this;
	}

	function handle_request( $get, $post ) {
		static::$db       = DatabaseSession::from_config();
		static::$template = new \Mustache_Engine( array(
			'template_class_prefix'  => '__MyTemplates_',
			'cache'                  => '/tmp/cache/mustache',
			'cache_file_mode'        => 0666, // Please, configure your umask instead of doing this :)
			'cache_lambda_templates' => true,
			'loader'                 => new \Mustache_Loader_FilesystemLoader( TEMPLATE_PATH ),
			'partials_loader'        => new \Mustache_Loader_FilesystemLoader( implode( DIRECTORY_SEPARATOR, array(
				TEMPLATE_PATH,
				'partials',
			) ) ),
			'helpers'                => array(
				'i18n' => function ( $text ) {
					// do something translatey here...
				},
			),
			'escape'                 => function ( $value ) {
				return htmlspecialchars( $value, ENT_COMPAT, 'UTF-8' );
			},
			'charset'                => 'ISO-8859-1',
			'logger'                 => new \Mustache_Logger_StreamLogger( 'php://stderr' ),
			'strict_callables'       => true,
			'pragmas'                => [ \Mustache_Engine::PRAGMA_FILTERS ],
		) );

		session_start();

		$request = $this->get_request( $get, $post );

		try {
			$request->handle();
		} catch ( \Exception $e ) {
			$this->handle_error( $e );
		}

		session_commit();
	}

	public function setup( $options = array() ) {
		require_once 'config.php';
		require_once 'autoloader.php';

		$this->register_request( Requests\ProfileRequest::page, Requests\ProfileRequest::class );
		$this->register_request( Requests\BasicLaunchRequest::page, Requests\BasicLaunchRequest::class );
		$this->register_request( Requests\ResolveResourceRequest::page, Requests\ResolveResourceRequest::class );
		$this->register_request( Requests\ToolProxyRegistrationRequest::page, Requests\ToolProxyRegistrationRequest::class );

		require implode( DIRECTORY_SEPARATOR, array(
			ROOT_PATH,
			'vendor',
			'mustache',
			'mustache',
			'src',
			'Mustache',
			'Autoloader.php',
		) );
		\Mustache_Autoloader::register();
	}

	function get_request( $get, $post ) {
		$page = isset( $get['page'] ) ? $get['page'] : 'index';

		if ( empty( $this->requests[ $page ] ) ) {
			throw new \ErrorException( "Page not Found" );
		}

		$request = $this->requests[ $page ];

		try {
			$request::check_params( $get, $post );
		} catch ( \Exception $e ) {
			$this->invalid_parameters( $e );
		}

		return new $request( $get, $post );
	}

	function get_version( $get, $post ) {
		if ( empty( $post[ Version::label ] ) ) {
			throw new \ErrorException( "No version specified" );
		}

		return $post[ Version::label ];
	}

	private function handle_error( $e ) {
		header( "HTTP/1.0 " . $e->getCode() );
		echo $e->getMessage();
		die();
	}

	private function invalid_parameters( $e ) {
		header( "HTTP/1.0 400" );
		echo $e->getMessage();
		die();
	}
}
