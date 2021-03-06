<?php

use \Hcode\Page;
use \Hcode\Model\Products;

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
