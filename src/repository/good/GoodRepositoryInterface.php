<?php

declare(strict_types=1);

namespace app\repository\good;

use app\dto\GoodDTO;

interface GoodRepositoryInterface{

	public function createGood(int $userId, string $title, string $imgPath = 'default.jpg') : GoodDTO;

	public function getGoods() : array;

	public function getGoodById(int $goodId) : ?GoodDTO;

	public function updateGood(int $goodId, string $title, string $imagePath) : bool;

	public function deleteGood(int $goodId) : bool;
}
