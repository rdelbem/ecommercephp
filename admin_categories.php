<?php

use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\Category;
use \Hcode\Model\Product;

$app->get(
	'/admin/categories',
	function() {

		User::verifyLogin();

		$search = ( isset( $_GET['search'] ) ) ? $_GET['search'] : '';
		$page   = ( isset( $_GET['page'] ) ) ? (int) $_GET['page'] : 1;

		if ( $search != '' ) {

			$pagination = Category::getPageSearch( $search, $page );

		} else {

			$pagination = Category::getPage( $page );

		}

		$pages = array();

		for ( $x = 0; $x < $pagination['pages']; $x++ ) {

			array_push(
				$pages,
				array(
					'href' => '/admin/categories?' . http_build_query(
						array(
							'page'   => $x + 1,
							'search' => $search,
						)
					),
					'text' => $x + 1,
				)
			);

		}

		$page = new PageAdmin();

		$page->setTpl(
			'categories',
			array(
				'categories' => $pagination['data'],
				'search'     => $search,
				'pages'      => $pages,
			)
		);

	}
);

$app->get(
	'/admin/categories/create',
	function() {

		User::verifyLogin();

		$page = new PageAdmin();

		$page->setTpl( 'categories-create' );

	}
);

$app->post(
	'/admin/categories/create',
	function() {

		User::verifyLogin();

		$category = new Category();

		$category->setData( $_POST );

		$category->save();

		header( 'Location: /admin/categories' );
		exit;

	}
);

$app->get(
	'/admin/categories/:idcategory/delete',
	function( $idcategory ) {

		User::verifyLogin();

		$category = new Category();

		$category->get( (int) $idcategory );

		$category->delete();

		header( 'Location: /admin/categories' );
		exit;

	}
);

$app->get(
	'/admin/categories/:idcategory',
	function( $idcategory ) {

		User::verifyLogin();

		$category = new Category();

		$category->get( (int) $idcategory );

		$page = new PageAdmin();

		$page->setTpl(
			'categories-update',
			array(
				'category' => $category->getValues(),
			)
		);

	}
);

$app->post(
	'/admin/categories/:idcategory',
	function( $idcategory ) {

		User::verifyLogin();

		$category = new Category();

		$category->get( (int) $idcategory );

		$category->setData( $_POST );

		$category->save();

		header( 'Location: /admin/categories' );
		exit;

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
