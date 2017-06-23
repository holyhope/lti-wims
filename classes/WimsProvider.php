<?php

namespace lti;

use LTI\ToolProvider;

/**
 * Class WimsProvider
 *
 * @package lti
 * @property string $id
 * @property string $url
 * @property string $data_path
 * @property string $sessions_path
 */
class WimsProvider extends ToolProvider {
	const type = 'wims';

	public static function load( $id ) {
		return new static( array(
			'id'            => $id,
			'url'           => 'http://192.168.99.100:32773/wims',
			'data_path'     => '/var/wims/classes',
			'sessions_path' => '/var/wims/sessions',
		) );
	}

	public function get_session( User $user ) {
		if ( $handle = opendir( $this->sessions_path ) ) {
			try {
				$attribute        = "wims_email=";
				$attribute_length = strlen( $attribute );
				/* This is the correct way to loop over the directory. */
				while ( false !== ( $entry = readdir( $handle ) ) ) {
					if ( $entry[0] != '.' ) {
						$session_path = join( DIRECTORY_SEPARATOR, array( $this->sessions_path, $entry ) );
						if ( is_dir( $session_path ) ) {
							$filename = join( DIRECTORY_SEPARATOR, array( $session_path, 'var.stat' ) );
							if ( ! is_readable( $filename ) ) {
								user_error( "$filename is not readable", E_USER_WARNING );
								continue;
							}
							if ( $file = fopen( $filename, "r" ) ) {
								try {
									while ( false !== ( $line = stream_get_line( $file, 4096, "\n" ) ) ) {
										if ( strncmp( $line, $attribute, $attribute_length ) == 0 ) {
											if ( substr( $line, $attribute_length ) == $user->contact_email_primary ) {
												return basename( $session_path );
											}
										}
									}
								} finally {
									fclose( $file );
								}
							}
						}
					}
				}
			} finally {
				closedir( $handle );
			}
			throw new \Exception( "User $user->id have no sessions on tool_provider $this->id" );
		}
	}
}

