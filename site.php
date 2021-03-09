<?php

use \Hcode\Page;
use \Hcode\Model\Products;
use \Hcode\Model\Category;
use \Hcode\Model\User;
use \Hcode\Model\Cart;
use \Hcode\Model\Adress;

$app->get(
	'/',
	function() {

		$products = Products::listAll();

		$page = new Page();

		$page->setTpl(
			'index',
			array(
				'products' => Products::checklist( $products ),
			)
		);

	}
);

$app->get(
	'/categories/:idcategory',
	function( $idcategory ) {

		$page = ( isset( $_GET['page'] ) ) ? (int) $_GET['page'] : 1;

		$category = new Category();

		$category->get( (int) $idcategory );

		$pagination = $category->getProductsPage( $page );

		$pages = array();

		for ( $i = 1; $i <= $pagination['pages']; $i++ ) {
			array_push(
				$pages,
				array(
					'link' => '/categories/' . $category->getidcategory() . '?page=' . $i,
					'page' => $i,
				)
			);
		}

		$page = new Page();

		$page->setTpl(
			'category',
			array(
				'category' => $category->getValues(),
				'products' => $pagination['data'],
				'pages'    => $pages,
			)
		);

	}
);

$app->get(
	'/products/:desurl',
	function( $desurl ) {

		$product = new Products();

		$product->getFromURL( $desurl );

		$page = new Page();

		$page->setTpl(
			'product-detail',
			array(
				'product'    => $product->getValues(),
				'categories' => $product->getCategories(),
			)
		);

	}
);

$app->get(
	'/cart',
	function() {

		$cart = Cart::getFromSession();

		$page = new Page();

		$page->setTpl(
			'cart',
			array(
				'cart'     => $cart->getValues(),
				'products' => $cart->getProducts(),
				'error'    => Cart::getMsgError(),
			)
		);

	}
);

$app->get(
	'/cart/:idproduct/add',
	function( $idproduct ) {

		$product = new Products();

		$product->get( (int) $idproduct );

		$cart = Cart::getFromSession();

		$qtd = ( isset( $_GET['qtd'] ) ) ? (int) $_GET['qtd'] : 1;

		for ( $i = 0; $i < $qtd; $i++ ) {
			$cart->addProduct( $product );
		}

		header( 'Location: /cart' );
		exit;

	}
);


$app->get(
	'/cart/:idproduct/minus',
	function( $idproduct ) {

		$product = new Products();

		$product->get( (int) $idproduct );

		$cart = Cart::getFromSession();

		$cart->removeProduct( $product );

		header( 'Location: /cart' );
		exit;

	}
);


$app->get(
	'/cart/:idproduct/remove',
	function( $idproduct ) {

		$product = new Products();

		$product->get( (int) $idproduct );

		$cart = Cart::getFromSession();

		$cart->removeProduct( $product, true );

		header( 'Location: /cart' );
		exit;

	}
);

$app->post(
	'/cart/freight',
	function() {

		$cart = Cart::getFromSession();

		$cart->setFreight( $_POST['zipcode'] );

		header( 'Location: /cart' );
		exit;

	}
);

$app->get(
	'/checkout',
	function() {

		User::verifyLogin( false );

		$address = new Address();
		$cart    = Cart::getFromSession();

		if ( ! isset( $_GET['zipcode'] ) ) {

			$_GET['zipcode'] = $cart->getdeszipcode();

		}

		if ( isset( $_GET['zipcode'] ) ) {

			$address->loadFromCEP( $_GET['zipcode'] );

			$cart->setdeszipcode( $_GET['zipcode'] );

			$cart->save();

			$cart->getCalculateTotal();

		}

		if ( ! $address->getdesaddress() ) {
			$address->setdesaddress( '' );
		}
		if ( ! $address->getdesnumber() ) {
			$address->setdesnumber( '' );
		}
		if ( ! $address->getdescomplement() ) {
			$address->setdescomplement( '' );
		}
		if ( ! $address->getdesdistrict() ) {
			$address->setdesdistrict( '' );
		}
		if ( ! $address->getdescity() ) {
			$address->setdescity( '' );
		}
		if ( ! $address->getdesstate() ) {
			$address->setdesstate( '' );
		}
		if ( ! $address->getdescountry() ) {
			$address->setdescountry( '' );
		}
		if ( ! $address->getdeszipcode() ) {
			$address->setdeszipcode( '' );
		}

		$page = new Page();

		$page->setTpl(
			'checkout',
			array(
				'cart'     => $cart->getValues(),
				'address'  => $address->getValues(),
				'products' => $cart->getProducts(),
				'error'    => Address::getMsgError(),
			)
		);

	}
);

$app->get(
	'/login',
	function() {

		$page = new Page();

		$page->setTpl(
			'login',
			array(
				'error'          => User::getError(),
				'errorRegister'  => User::getErrorRegister(),
				'registerValues' => ( isset( $_SESSION['registerValues'] ) ) ? $_SESSION['registerValues'] : array(
					'name'  => '',
					'email' => '',
					'phone' => '',
				),
			)
		);

	}
);

$app->post(
	'/login',
	function() {

		try {

			User::login( $_POST['login'], $_POST['password'] );

		} catch ( Exception $e ) {

			User::setError( $e->getMessage() );

		}

		header( 'Location: /checkout' );
		exit;

	}
);

$app->get(
	'/logout',
	function() {

		User::logout();

		header( 'Location: /login' );
		exit;

	}
);
