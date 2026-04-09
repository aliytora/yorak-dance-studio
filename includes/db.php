<?php
// Подключение к БД на Railway
$host = getenv('DB_HOST') ?: 'localhost';
$port = getenv('DB_PORT') ?: '3306';
$user = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';
$database = getenv('DB_NAME') ?: 'dance_studio';

// Подключение
$conn = mysqli_connect($host, $user, $password, $database, $port);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");

// PDO подключение
try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("PDO Connection failed: " . $e->getMessage());
}
?>