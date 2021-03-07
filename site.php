<?php

use \Hcode\Page;
use \Hcode\Model\Products;
use \Hcode\Model\Category;

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
