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

	public static function listAll() {
		$sql = new Sql();
		 return $sql->select( 'SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) ORDER BY b.desperson' );

	}

	public function save() {
		$sql = new Sql();

		$results = $sql->select(
			'CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)',
			array(
				':desperson'   => utf8_decode( $this->getdesperson() ),
				':deslogin'    => $this->getdeslogin(),
				':despassword' => $this->getdespassword(),
				':desemail'    => $this->getdesemail(),
				':nrphone'     => $this->getnrphone(),
				':inadmin'     => $this->getinadmin(),
			)
		);

		$this->setData( $results[0] );

	}

	public function get( $iduser ) {
		$sql = new Sql();

		$results = $sql->select(
			'SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser = :iduser',
			array(
				':iduser' => $iduser,
			)
		);

		$data = $results[0];

		$this->setData( $data );

	}

	public function update() {
		$sql = new Sql();

		$results = $sql->select(
			'CALL sp_usersupdate_save(:iduser, :desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)',
			array(
				':iduser'      => $this->getiduser(),
				':desperson'   => utf8_decode( $this->getdesperson() ),
				':deslogin'    => $this->getdeslogin(),
				':despassword' => $this->getdespassword(),
				':desemail'    => $this->getdesemail(),
				':nrphone'     => $this->getnrphone(),
				':inadmin'     => $this->getinadmin(),
			)
		);

		$this->setData( $results[0] );

	}

	public function delete() {
		$sql = new Sql();

		$sql->query(
			'CALL sp_users_delete(:iduser)',
			array(
				':iduser' => $this->getiduser(),
			)
		);

	}

}
