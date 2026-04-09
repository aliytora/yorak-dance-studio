<?php
// includes/db.php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'dance_studio';

// 1. MySQLi подключение (для submit_review.php и старого кода)
$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    error_log("MySQLi Connection failed: " . mysqli_connect_error());
    // Не делаем die(), чтобы не ломать сайт полностью
    $conn = null;
} else {
    mysqli_set_charset($conn, "utf8");
}

// 2. PDO подключение (для админки и нового кода)
try {
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    error_log("PDO Connection failed: " . $e->getMessage());
    // Создаем пустой объект PDO, чтобы избежать ошибок
    $pdo = null;
}

// Для обратной совместимости
$link = $conn;
?>