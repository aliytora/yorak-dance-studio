<?php
// Берем строку подключения напрямую (Railway её передает, но getenv может не работать)
$mysql_url = "mysql://root:HJiAPYVJQPerzyJdvrHSOYYLkBBiBuRk@mysql.railway.internal:3306/railway";

// ИЛИ пробуем через getenv
$env_url = getenv('MYSQL_URL');
if ($env_url) {
    $mysql_url = $env_url;
}

try {
    // PDO подключение
    $pdo = new PDO($mysql_url);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Парсим URL для mysqli
    $parsed = parse_url($mysql_url);
    $host = $parsed['host'];
    $port = $parsed['port'] ?? 3306;
    $user = $parsed['user'];
    $password = $parsed['pass'];
    $database = ltrim($parsed['path'], '/');
    
    // MySQLi подключение
    $conn = mysqli_connect($host, $user, $password, $database, $port);
    
    if (!$conn) {
        die("MySQLi Connection failed: " . mysqli_connect_error());
    }
    
    mysqli_set_charset($conn, "utf8mb4");
    
    echo "✅ База данных подключена успешно!"; // Временно, потом удалишь
    
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>