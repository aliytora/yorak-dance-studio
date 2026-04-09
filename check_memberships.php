<?php
require_once 'includes/db.php';

// Находим пользователей у которых осталось 1-2 занятия
$stmt = $pdo->query("
    SELECT u.*, m.lessons_left, m.valid_until 
    FROM users u
    JOIN memberships m ON u.id = m.user_id
    WHERE m.lessons_left IN (1, 2) 
    AND m.valid_until >= CURDATE()
");

$users_to_notify = $stmt->fetchAll();

foreach($users_to_notify as $user) {
    // Тут можно добавить отправку email или SMS
    // Пока просто запишем в отдельную таблицу
    
    // Создаем таблицу для уведомлений если её нет
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `notifications` (
            `id` INT PRIMARY KEY AUTO_INCREMENT,
            `user_id` INT NOT NULL,
            `message` TEXT,
            `is_read` TINYINT DEFAULT 0,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
        )
    ");
    
    // Добавляем уведомление
    $message = "У вас осталось {$user['lessons_left']} занятий. Пополните абонемент!";
    $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
    $stmt->execute([$user['id'], $message]);
}

echo "Уведомления созданы для " . count($users_to_notify) . " пользователей";
?>