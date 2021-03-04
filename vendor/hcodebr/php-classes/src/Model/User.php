<?php

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;

class User extends Model {

	const SESSION = 'User';

	public static function login( $login, $password ) {

		$sql = new Sql();

		$result = $sql->select(
			'SELECT * FROM tb_users WHERE deslogin = :LOGIN',
			array(

				':LOGIN' => $login,

			)
		);

		if ( count( $result ) === 0 ) {
			throw new \Exception( 'Usuário inexistente ou senha inválida', 1 );
		}

		$data = $result[0];

		if ( password_verify( $password, $data['despassword'] ) === true ) {

			$user = new User();

			$user->setData( $data );

			$_SESSION[ self::SESSION ] = $user->getValues();

			return $user;

		} else {

			throw new \Exception( 'Usuário inexistente ou senha inválida', 1 );

		}

	}

	public static function verifyLogin( $inadmin = true ) {
		if ( ! isset( $_SESSION[ self::SESSION ] ) ||
			! $_SESSION[ self::SESSION ] ||
			! (int) $_SESSION[ self::SESSION ]['iduser'] > 0 ||
			(bool) $_SESSION[ self::SESSION ]['inadmin'] !== $inadmin
			) {

			header( 'Location: /admin/longin' );

			exit;
		}
	}

	public static function logout() {
		$_SESSION[ self::SESSION ] = null;
	}

}
