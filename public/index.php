<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use app\controller\AuthController;
use app\controller\GoodController;
use app\repository\user\MySQLUserRepository;
use app\repository\good\MySQLGoodRepository;
use app\service\UserService;
use app\service\GoodService;
use app\core\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

$config = require __DIR__ . '/../config/database.php';
$pdo = new PDO("mysql:host={$config['host']};dbname={$config['dbname']}", $config['user'], $config['password']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$session = new Session();
$session->start();

$userRepository = new MySQLUserRepository($pdo);
$goodRepository = new MySQLGoodRepository($pdo, $userRepository);

$userService = new UserService($userRepository);
$goodService = new GoodService($goodRepository);

$authController = new AuthController($userService, $session);
$goodController = new GoodController($goodService, $userService, $session);

$request = Request::createFromGlobals();

$path = $request->getPathInfo();
$method = $request->getMethod();

switch($path){
	case '/register':
		if($method === 'POST'){
			$authController->register($request);
		}else{
			if($session->get('user_id')){
				View::redirect('/wall');
				return;
			}
			View::render('register');
		}
		break;

	case '/login':
		if($method === 'POST'){
			$authController->login($request);
		}else{
			if($session->get('user_id') !== null){
				View::redirect('/wall');
				return;
			}
			View::render('login');
		}
		break;

	case '/logout':
		$authController->logout();
		break;

	case '/wall':
		$goodController->showGoods($request);
		break;

	case '/good/create':
		if($session->get('user_id')){
			if($method === 'POST'){
				$goodController->createGood($request);
			}else{
				View::render('create_good');
			}
		}else{
			header('Location: /login');
			exit;
		}
		break;

	case '/good/edit':
		if($session->get('user_id')){
			$goodId = $request->request->get('id');
			$goodController->editGood($request, (int) $goodId);
		}else{
			header('Location: /login');
			exit;
		}
		break;

	case '/good/delete':
		if($session->get('user_id')){
			$goodId = $request->query->get('id');
			$goodController->deleteGood((int) $goodId);
		}else{
			header('Location: /login');
			exit;
		}
		break;

	default:
		echo "404 Not Found";
		break;
}
