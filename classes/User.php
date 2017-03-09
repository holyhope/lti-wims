<?php

namespace LTI;

class User extends LTIStorableObject {
	const table_name = 'users';
	const prefix     = 'user_';

	public static function get_current(): User {
		return static::load( $_SESSION['current_user'] );
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
