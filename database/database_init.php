<?php

declare(strict_types=1);

$config = require __DIR__ . '/../config/database.php';
$pdo = new PDO("mysql:host={$config['host']};dbname={$config['dbname']}", $config['user'], $config['password']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try{
	$pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(50) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
    ");

	$pdo->exec("
        CREATE TABLE IF NOT EXISTS goods (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            image_path TEXT NOT NULL,
            title TEXT NOT NULL,
            author TEXT NOT NULL,
            is_published BOOLEAN NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        );
    ");

}catch(PDOException $e){}
