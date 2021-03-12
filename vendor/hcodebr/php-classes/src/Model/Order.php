<?php

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Model\Cart;

class Order extends Model {

	const SUCCESS = 'Order-Success';
	const ERROR   = 'Order-Error';

	public function save() {
		$sql = new Sql();

		$results = $sql->select(
			'CALL sp_orders_save(:idorder, :idcart, :iduser, :idstatus, :idaddress, :vltotal)',
			array(
				':idorder'   => $this->getidorder(),
				':idcart'    => $this->getidcart(),
				':iduser'    => $this->getiduser(),
				':idstatus'  => $this->getidstatus(),
				':idaddress' => $this->getidaddress(),
				':vltotal'   => $this->getvltotal(),
			)
		);

		if ( count( $results ) > 0 ) {
			$this->setData( $results[0] );
		}

	}

	public function get( $idorder ) {
		$sql = new Sql();

		$results = $sql->select(
			'
			SELECT * 
			FROM tb_orders a 
			INNER JOIN tb_ordersstatus b USING(idstatus) 
			INNER JOIN tb_carts c USING(idcart)
			INNER JOIN tb_users d ON d.iduser = a.iduser
			INNER JOIN tb_addresses e USING(idaddress)
			INNER JOIN tb_persons f ON f.idperson = d.idperson
			WHERE a.idorder = :idorder
		',
			array(
				':idorder' => $idorder,
			)
		);

		if ( count( $results ) > 0 ) {
			$this->setData( $results[0] );
		}

	}

	public static function listAll() {
		$sql = new Sql();

		return $sql->select(
			'
			SELECT * 
			FROM tb_orders a 
			INNER JOIN tb_ordersstatus b USING(idstatus) 
			INNER JOIN tb_carts c USING(idcart)
			INNER JOIN tb_users d ON d.iduser = a.iduser
			INNER JOIN tb_addresses e USING(idaddress)
			INNER JOIN tb_persons f ON f.idperson = d.idperson
			ORDER BY a.dtregister DESC
		'
		);

	}

	public function delete() {
		$sql = new Sql();

		$sql->query(
			'DELETE FROM tb_orders WHERE idorder = :idorder',
			array(
				':idorder' => $this->getidorder(),
			)
		);

	}

	public function getCart():Cart {

		$cart = new Cart();

		$cart->get( (int) $this->getidcart() );

		return $cart;

	}

	public static function setError( $msg ) {
		$_SESSION[ self::ERROR ] = $msg;

	}

	public static function getError() {
		$msg = ( isset( $_SESSION[ self::ERROR ] ) && $_SESSION[ self::ERROR ] ) ? $_SESSION[ self::ERROR ] : '';

		self::clearError();

		return $msg;

	}

	public static function clearError() {
		$_SESSION[ self::ERROR ] = null;

	}

	public static function setSuccess( $msg ) {
		$_SESSION[ self::SUCCESS ] = $msg;

	}

	public static function getSuccess() {
		$msg = ( isset( $_SESSION[ self::SUCCESS ] ) && $_SESSION[ self::SUCCESS ] ) ? $_SESSION[ self::SUCCESS ] : '';

		self::clearSuccess();

		return $msg;

	}

	public static function clearSuccess() {
		$_SESSION[ self::SUCCESS ] = null;

	}

	public static function getPage( $page = 1, $itemsPerPage = 10 ) {
		$start = ( $page - 1 ) * $itemsPerPage;

		$sql = new Sql();

		$results = $sql->select(
			"
			SELECT SQL_CALC_FOUND_ROWS *
			FROM tb_orders a 
			INNER JOIN tb_ordersstatus b USING(idstatus) 
			INNER JOIN tb_carts c USING(idcart)
			INNER JOIN tb_users d ON d.iduser = a.iduser
			INNER JOIN tb_addresses e USING(idaddress)
			INNER JOIN tb_persons f ON f.idperson = d.idperson
			ORDER BY a.dtregister DESC
			LIMIT $start, $itemsPerPage;
		"
		);

		$resultTotal = $sql->select( 'SELECT FOUND_ROWS() AS nrtotal;' );

		return array(
			'data'  => $results,
			'total' => (int) $resultTotal[0]['nrtotal'],
			'pages' => ceil( $resultTotal[0]['nrtotal'] / $itemsPerPage ),
		);

	}

	public static function getPageSearch( $search, $page = 1, $itemsPerPage = 10 ) {
		$start = ( $page - 1 ) * $itemsPerPage;

		$sql = new Sql();

		$results = $sql->select(
			"
			SELECT SQL_CALC_FOUND_ROWS *
			FROM tb_orders a 
			INNER JOIN tb_ordersstatus b USING(idstatus) 
			INNER JOIN tb_carts c USING(idcart)
			INNER JOIN tb_users d ON d.iduser = a.iduser
			INNER JOIN tb_addresses e USING(idaddress)
			INNER JOIN tb_persons f ON f.idperson = d.idperson
			WHERE a.idorder = :id OR f.desperson LIKE :search
			ORDER BY a.dtregister DESC
			LIMIT $start, $itemsPerPage;
		",
			array(
				':search' => '%' . $search . '%',
				':id'     => $search,
			)
		);

		$resultTotal = $sql->select( 'SELECT FOUND_ROWS() AS nrtotal;' );

		return array(
			'data'  => $results,
			'total' => (int) $resultTotal[0]['nrtotal'],
			'pages' => ceil( $resultTotal[0]['nrtotal'] / $itemsPerPage ),
		);

	}

}


