<?php


namespace LTI;


interface Storable {

	public function insert();

	public static function load( $id );
}