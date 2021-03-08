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

		// $cart = Cart::getFromSession();

		$page = new Page();

		$page->setTpl( 'cart' );

	}
);
