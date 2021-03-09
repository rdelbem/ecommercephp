<?php

use \Hcode\Page;
use \Hcode\Model\Products;
use \Hcode\Model\Category;
use \Hcode\Model\User;
use \Hcode\Model\Cart;

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
