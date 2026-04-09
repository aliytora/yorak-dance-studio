<?php
// Получаем MYSQL_URL из переменных Railway
$mysql_url = getenv('MYSQL_URL');

if (!$mysql_url) {
    die("MYSQL_URL not found. Check Railway variables.");
}

try {
    // PDO подключение
    $pdo = new PDO($mysql_url);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Для mysqli тоже подключаемся
    $parsed = parse_url($mysql_url);
    $host = $parsed['host'];
    $port = $parsed['port'] ?? 3306;
    $user = $parsed['user'];
    $password = $parsed['pass'];
    $database = ltrim($parsed['path'], '/');
    
    $conn = mysqli_connect($host, $user, $password, $database, $port);
    
    if (!$conn) {
        die("MySQLi Connection failed: " . mysqli_connect_error());
    }
    
    mysqli_set_charset($conn, "utf8mb4");
    
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>