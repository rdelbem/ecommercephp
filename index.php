<?php

session_start();

require_once 'vendor/autoload.php';

use \Slim\Slim;
use \Hcode\Page;
use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\Category;

$app = new Slim();

$app->config( 'debug', true );

$app->get(
	'/',
	function() {

		$page = new Page();

		$page->setTpl( 'index' );

	}
);

$app->get(
	'/admin',
	function() {

		User::verifyLogin();

		$page = new PageAdmin();

		$page->setTpl( 'index' );

	}
);

$app->get(
	'/admin/login',
	function() {

		$page = new PageAdmin(
			array(
				'header' => false,
				'footer' => false,
			)
		);

		$page->setTpl( 'login' );

	}
);


$app->post(
	'/admin/login',
	function() {

		User::login( $_POST['login'], $_POST['password'] );
		header( 'Location: /admin' );

		exit;

	}
);

$app->get(
	'/admin/logout',
	function() {

		User::logout();

		header( 'Location: /admin/login' );

		exit;

	}
);

$app->get(
	'/admin/users',
	function() {

		User::verifyLogin();

		$users = User::listAll();

		$page = new PageAdmin();

		$page->setTpl(
			'users',
			array(
				'users' => $users,
			)
		);

	}
);

$app->get(
	'/admin/users/create',
	function() {

		User::verifyLogin();

		$page = new PageAdmin();

		$page->setTpl( 'users-create' );

	}
);

$app->get(
	'/admin/users/:iduser/delete',
	function( $iduser ) {

		User::verifyLogin();

		$user = new User();

		$user->get( (int) $iduser );

		$user->delete();

		header( 'Location: /admin/users' );
		exit;

	}
);

$app->get(
	'/admin/users/:iduser',
	function( $iduser ) {

		User::verifyLogin();

		$user = new User();

		$user->get( (int) $iduser );

		$page = new PageAdmin();

		$page->setTpl(
			'users-update',
			array(
				'user' => $user->getValues(),
			)
		);

	}
);

$app->post(
	'/admin/users/create',
	function() {

		User::verifyLogin();

		$user = new User();

		$_POST['inadmin'] = ( isset( $_POST['inadmin'] ) ) ? 1 : 0;

		$user->setData( $_POST );

		$user->save();

		header( 'Location: /admin/users' );
		exit;
	}
);


$app->post(
	'/admin/users/:iduser',
	function( $iduser ) {
		User::verifyLogin();

		$user = new User();

		$_POST['inadmin'] = ( isset( $_POST['inadmin'] ) ) ? 1 : 0;

		$user->get( (int) $iduser );

		$user->setData( $_POST );

		$user->update();

		header( 'Location: /admin/users' );
		exit;

	}
);

$app->get(
	'/admin/forgot',
	function() {

		$page = new PageAdmin(
			array(
				'header' => false,
				'footer' => false,
			)
		);

		$page->setTpl( 'forgot' );

	}
);

$app->post(
	'/admin/forgot',
	function() {

		$user = User::getForgot( $_POST['email'] );

		header( 'Location: /admin/forgot/sent' );
		exit;

	}
);

$app->get(
	'/admin/forgot/sent',
	function() {

		$page = new PageAdmin(
			array(
				'header' => false,
				'footer' => false,
			)
		);

		$page->setTpl( 'forgot-sent' );

	}
);

$app->get(
	'/admin/forgot/reset',
	function() {

		$user = User::validForgotDecrypt( $_GET['code'] );

		$page = new PageAdmin(
			array(
				'header' => false,
				'footer' => false,
			)
		);

		$page->setTpl(
			'forgot-reset',
			array(
				'name' => $user['desperson'],
				'code' => $_GET['code'],
			)
		);

	}
);

$app->post(
	'/admin/forgot/reset',
	function() {

		$forgot = User::validForgotDecrypt( $_POST['code'] );

		User::setFogotUsed( $forgot['idrecovery'] );

		$user = new User();

		$user->get( (int) $forgot['iduser'] );

		$password = User::getPasswordHash( $_POST['password'] );

		$user->setPassword( $password );

		$page = new PageAdmin(
			array(
				'header' => false,
				'footer' => false,
			)
		);

		$page->setTpl( 'forgot-reset-success' );

	}
);

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

$app->run();
