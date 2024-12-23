<?php

declare(strict_types=1);

namespace app\repository\good;

use app\repository\user\UserRepositoryInterface;
use DateTime;
use PDO;
use app\dto\GoodDTO;
use function date;

class MySQLGoodRepository implements GoodRepositoryInterface{

	public function __construct(
		private PDO $pdo,
		private UserRepositoryInterface $userRepository
	){
	}

	public function createGood(int $userId, string $title, string $imgPath = '') : GoodDTO{
		$stmt = $this->pdo->prepare(
			"INSERT INTO goods (user_id, image_path, title, author, is_published, created_at) 
            VALUES (:user_id, :image_path, :title, :author, :is_published, NOW())"
		);
		$stmt->execute([
			':user_id' => $userId,
			':title' => $title,
			':image_path' => $imgPath,
			':author' => $this->userRepository->findUserById($userId)->name,
			':is_published' => 0,
		]);

		$goodId = $this->pdo->lastInsertId();

		return new GoodDTO(
			(int) $goodId,
			$userId,
			$imgPath,
			$title,
			$this->userRepository->findUserById($userId)->name,
			false,
			date("d-m-Y H:i:s")
		);
	}

	public function getGoods() : array{
		$stmt = $this->pdo->query(
			"SELECT g.id, g.title, g.image_path, g.created_at, g.user_id, g.author, g.is_published 
            FROM goods g 
            ORDER BY g.created_at DESC"
		);

		$goods = [];
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			$goods[] = new GoodDTO(
				(int) $row['id'],
				(int) $row['user_id'],
				$row['image_path'],
				$row['title'],
				$row['author'],
				(bool) $row['is_published'],
				DateTime::createFromFormat("Y-m-d H:i:s", $row['created_at'])->format('d-m-Y H:i:s')
			);
		}

		return $goods;
	}

	public function getGoodById(int $goodId) : ?GoodDTO{
		$stmt = $this->pdo->prepare(
			"SELECT g.id, g.title, g.image_path, g.created_at, g.user_id, g.author, g.is_published 
            FROM goods g 
            WHERE g.id = :id"
		);
		$stmt->execute([':id' => $goodId]);

		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if($row){
			return new GoodDTO(
				(int) $row['id'],
				(int) $row['user_id'],
				$row['image_path'],
				$row['title'],
				$row['author'],
				(bool) $row['is_published'],
				DateTime::createFromFormat("Y-m-d H:i:s", $row['created_at'])->format('d-m-Y H:i:s')
			);
		}
		return null;
	}

	public function updateGood(int $goodId, string $title, string $imagePath) : bool{
		$stmt = $this->pdo->prepare(
			"UPDATE goods 
            SET title = :title, image_path = :image_path 
            WHERE id = :id"
		);
		return $stmt->execute([
			':title' => $title,
			':image_path' => $imagePath,
			':id' => $goodId,
		]);
	}

	public function deleteGood(int $goodId) : bool{
		$stmt = $this->pdo->prepare(
			"DELETE FROM goods 
            WHERE id = :id"
		);
		return $stmt->execute([':id' => $goodId]);
	}
}
