<?php
// Данные из твоих переменных Railway
$host = 'mysql.railway.internal';
$port = 3306;
$user = 'root';
$password = 'HJiAPYVJQPerzyJdvrHSOYYLkBBiBuRk';
$database = 'railway';

// Пробуем подключиться через PDO
try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4",
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT => 30
        ]
    );
    
    // MySQLi подключение
    $conn = mysqli_connect($host, $user, $password, $database, $port);
    
    if (!$conn) {
        throw new Exception("MySQLi failed: " . mysqli_connect_error());
    }
    
    mysqli_set_charset($conn, "utf8mb4");
    
    // Успех! (временно, потом удали)
    error_log("✅ Database connected successfully");
    
} catch(PDOException $e) {
    die("Ошибка БД: " . $e->getMessage());
}
?>