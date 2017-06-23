<?php
namespace LTI;

/**
 * Class WimsQuestion
 *
 * @package LTI
 * @property WimsProvider $tool_provider
 */
class WimsQuestion extends Question {
	public function render(): string {
		$mustache = Router::$template;
		$tpl      = $mustache->loadTemplate( 'question-wims' );
		$args     = array(
			'+module' => 'adm/sheet',
			'+class'  => $this->id,
			'+sh'     => '1',
		);
		try {
			$args['session'] = $this->tool_provider->get_session( User::get_current() );
		} catch ( \Exception $e ) {
			// No session, nothing to do

		}

		return $tpl->render( array(
			'id'  => $this->id,
			'url' => $this->tool_provider->url . "/wims.cgi?" . http_build_query( $args ),
		) );
	}

	public static function load( $id ) {
		//Load from source
		$tp_id = '1';

		return new static( array(
			'id'            => $id,
			'type'          => 'wims',
			'tool_provider' => WimsProvider::load( $tp_id ),
		) );
	}

	public function get_mark( User $user ) {
		$start_session_column = 18;
		$session              = $this->tool_provider->get_session( $user );
		$session_length       = strlen( $session );
		$directory            = join( DIRECTORY_SEPARATOR, array(
			$this->tool_provider->data_path,
			$this->id,
			'score',
		) );
		$mark                 = false;
		$date                 = 0;

		if ( $handle = opendir( $directory ) ) {
			try {
				while ( false !== ( $entry = readdir( $handle ) ) ) {
					if ( $entry[0] != '.' ) {
						$score_file = join( DIRECTORY_SEPARATOR, array( $directory, $entry ) );
						if ( is_file( $score_file ) ) {
							if ( ! is_readable( $score_file ) ) {
								user_error( "$score_file is not readable", E_USER_WARNING );
								continue;
							}
							if ( $file = fopen( $score_file, "r" ) ) {
								try {
									while ( false !== ( $line = stream_get_line( $file, 4096, "\n" ) ) ) {
										if ( substr_compare( $line, $session, $start_session_column, $session_length ) == 0 ) {
											$current_date = \DateTime::createFromFormat( 'Ymd.H:i:s', substr( $line, 0, 17 ) );
											if ( $current_date > $date ) {
												$date = $current_date;
												$mark = intval( substr( $line, strpos( $line, 'score ' ) + strlen( 'score ' ) ) );
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
		}

		if ( false === $mark ) {
			throw new \Exception( "Session $session have no note for resource $this->id" );
		}

		return $mark;
	}
}
