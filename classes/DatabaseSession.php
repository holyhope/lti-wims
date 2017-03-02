<?php
namespace LTI;

class DatabaseSession {
	public $connexion;

	public $prefix;

	public function __construct( $driver, $host, $port, $dbname, $user = null, $password = null, $prefix = '' ) {
		$this->connexion = new \PDO( "$driver:host=$host;port=$port;dbname=$dbname", $user, $password );
		$this->prefix    = $prefix;
	}

	public static function from_config() {
		return new DatabaseSession(
			DB_DRIVER,
			DB_HOST,
			DB_PORT,
			DB_NAME,
			DB_USER,
			DB_PASSWORD,
			DB_PREFIX
		);
	}
}
