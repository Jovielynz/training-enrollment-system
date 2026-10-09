
<?php

require_once __DIR__ . '/../classes/Database.php';

$dsn = 'mysql:host=localhost;dbname=training_db;charset=utf8mb4';
$user = 'root';
$pass = '';

try {
    $db = Database::getInstance($dsn, $user, $pass);
} catch (PDOException $e) {
    exit('Database connection failed. Check your XAMPP MySQL service and database settings.');
}
