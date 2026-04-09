<?php
require_once 'includes/db.php';

// Удаляем старого админа
$pdo->exec("DELETE FROM users WHERE email = 'admin@dance.ru'");

// Создаем нового с паролем "admin123"
$name = 'Администратор';
$email = 'admin@dance.ru';
$password = password_hash('admin123', PASSWORD_DEFAULT);
$role = 'admin';

$stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())");
$stmt->execute([$name, $email, $password, $role]);

echo "✅ Админ создан!<br>";
echo "Email: $email<br>";
echo "Пароль: admin123<br>";
echo "Хеш в БД: $password<br>";
?>