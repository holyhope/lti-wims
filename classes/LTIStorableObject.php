<?php
namespace LTI;

abstract class LTIStorableObject extends LTIObject implements Storable {
	const table_name = null;

	public $values = array();

	protected static function map_values( $values ) {
		return array_combine( array_map( function ( $key ) {
			return ":$key";
		}, array_keys( $values ) ), $values );
	}

	public function insert() {
		$db         = Router::$db;
		$values     = $this->map_values( $this->values );
		$keys       = implode( ',', array_keys( $values ) );
		$table_name = $db->prefix . static::table_name;
		$sth        = $db->connexion->prepare( <<<SQL
INSERT INTO $table_name VALUES ($keys);
SQL
		);

		if ( ! $sth->execute( $values ) ) {
			throw new \Exception( $sth->errorInfo()[2] );
		}
	}

	public static function search( $filters ): LTIStorableObject {
		$db         = Router::$db;
		$table_name = $db->prefix . static::table_name;

		$cond = array();
		foreach ( $filters as $column => $filter ) {
			$cond[] = "$column = :$column";
		}
		$filters = array_combine( array_map( function ( $column ) {
			return ":$column";
		}, array_keys( $filters ) ), $filters );
		$cond    = implode( ' AND ', $cond );
		$sql     = <<<SQL
SELECT * FROM $table_name WHERE $cond
SQL;

		$sth = $db->connexion->prepare( $sql );

		$sth->execute( $filters );

		$result = $sth->fetch( \PDO::FETCH_ASSOC );

		if ( ! $result ) {
			throw new \Exception( $sth->errorInfo()[2] );
		}

		$data = array();

		foreach ( $result as $key => $value ) {
			$data[ static::prefix . $key ] = $value;
		}

		return new static( $data );
	}

	public static function load( $id ) {
		return new static( static::search( array( 'id' => $id ) ) );
	}
}
