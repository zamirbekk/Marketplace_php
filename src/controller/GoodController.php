<?php

declare(strict_types=1);

namespace app\controller;

use app\service\GoodService;
use app\service\UserService;
use app\core\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use function is_dir;
use function md5;
use function mkdir;
use function uniqid;

class GoodController{

	private Session $session;

	public function __construct(
		private GoodService $goodService,
		private UserService $userService,
		Session $session
	){
		$this->session = $session;
	}

	public function showGoods(Request $request) : void{
		$userId = $this->session->get('user_id');

		if(!$userId){
			View::redirect('/login');
			return;
		}

		$user = $this->userService->findById($userId);
		$userName = $user->name;

		$goods = $this->goodService->getGoods();

		View::render('wall', [
			'userName' => $userName,
			'userId' => $userId,
			'goods' => $goods
		]);
	}

	public function createGood(Request $request) : void{
		$userId = $this->session->get('user_id');

		if(!$userId){
			View::redirect('/login');
			return;
		}

		$title = $request->request->get('title');
		$image = $request->files->get('image');

		$uploadDir = '/../uploads/';
		if(!is_dir($uploadDir)){
			mkdir($uploadDir, 0777, true);
		}

		if($image && $image->isValid()){
			$uniqueName = md5(uniqid()) . '.' . $image->guessExtension();
			$image->move($uploadDir, $uniqueName);
			$imgPath = $uploadDir . $uniqueName;
		}else{
			$imgPath = $uploadDir . 'img.png';
		}

		$this->goodService->createGood($userId, $title, $imgPath);
		View::redirect('/wall');
	}

	public function editGood(Request $request, int $goodId) : void{
		$userId = $this->session->get('user_id');

		if(!$userId){
			View::redirect('/login');
			return;
		}

		$title = $request->request->get('title');
		$image = $request->files->get('image');

		$uploadDir = __DIR__ . '/../uploads/';
		if(!is_dir($uploadDir)){
			mkdir($uploadDir, 0777, true);
		}

		if($image && $image->isValid()){
			$uniqueName = md5(uniqid()) . '.' . $image->guessExtension();
			$image->move($uploadDir, $uniqueName);
			$imgPath = $uploadDir . $uniqueName;
		}else{
			$imgPath = $request->request->get('existingImgPath', $uploadDir . 'img.png');
		}

		$this->goodService->updateGood($goodId, $userId, $title, $imgPath);
		View::redirect('/wall');
	}

	public function deleteGood(int $goodId) : void{
		$userId = $this->session->get('user_id');

		if(!$userId){
			View::redirect('/login');
			return;
		}

		$this->goodService->deleteGood($goodId, $userId);
		View::redirect('/wall');
	}
}
