<?php

declare(strict_types=1);

namespace app\dto;

readonly class GoodDTO{

	public function __construct(
		public int $id,
		public int $user_id,
		public string $image_path,
		public string $title,
		public string $author,
		public bool $isPublished,
		public string $created_at
	){
	}
}
