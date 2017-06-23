<?php

namespace LTI;

/**
 * Class User
 *
 * @package LTI
 * @property $id
 */
class User extends LTIStorableObject {
	const table_name = 'users';
	const prefix     = 'user_';

	public static function get_current(): User {
		$id = $_SESSION['current_user'];

		return static::load( $id );
	}

	public static function set_current( User $user ) {
		try {
			$user->values['id'] = $user->insert();
		} catch ( \Exception $e ) {
			// Already stored in the database.
		}

		return $_SESSION['current_user'] = $user->values['id'];
	}
}
