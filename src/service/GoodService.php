<?php

declare(strict_types=1);

namespace app\service;

use app\repository\good\GoodRepositoryInterface;
use app\dto\GoodDTO;

class GoodService{

	public function __construct(
		private GoodRepositoryInterface $goodRepository
	){
	}

	public function createGood(int $userId, string $title, string $imagePath) : GoodDTO{
		return $this->goodRepository->createGood($userId, $title, $imagePath);
	}

	public function getGoods() : array{
		return $this->goodRepository->getGoods();
	}

	public function getGoodById(int $goodId) : ?GoodDTO{
		return $this->goodRepository->getGoodById($goodId);
	}

	public function updateGood(int $goodId, int $userId, string $title, string $imagePath) : bool{
		$good = $this->goodRepository->getGoodById($goodId);
		if($good && $good->user_id === $userId){
			return $this->goodRepository->updateGood($goodId, $title, $imagePath);
		}
		return false;
	}

	public function deleteGood(int $goodId, int $userId) : bool{
		$good = $this->goodRepository->getGoodById($goodId);
		if($good && $good->user_id === $userId){
			return $this->goodRepository->deleteGood($goodId);
		}
		return false;
	}
}
