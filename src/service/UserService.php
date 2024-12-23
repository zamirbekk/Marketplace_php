<?php

declare(strict_types=1);

namespace app\service;

use app\repository\user\UserRepositoryInterface;
use app\dto\UserDTO;
use function password_verify;
use function session_destroy;

class UserService{

	public function __construct(
		private UserRepositoryInterface $userRepository
	){
	}

	public function register($name, $email, $password) : UserDTO{
		return $this->userRepository->createUser($name, $email, $password);
	}

	public function login($email, $password) : ?UserDTO{
		$user = $this->userRepository->findUserByEmail($email);
		if($user !== null && password_verify($password, $user->password)){
			$_SESSION['user_id'] = $user->id;
			return $user;
		}
		return null;
	}

	public function findById(int $id) : ?UserDTO{
		return $this->userRepository->findUserById($id);
	}

	public function logout() : void{
		session_destroy();
	}
}
