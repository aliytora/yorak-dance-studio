<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

if(!isAdmin()) {
    redirect('../login.php');
}

$user_id = $_GET['user_id'];

// Получаем информацию о пользователе и выбранном плане
$stmt = $pdo->prepare("
    SELECT u.*, mp.lessons, mp.months, mp.name as plan_name, mp.price 
    FROM users u 
    LEFT JOIN membership_plans mp ON u.selected_plan_id = mp.id 
    WHERE u.id = ? AND u.awaiting_payment = 1
");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if($user) {
    $lessons = $user['lessons'];
    $months = $user['months'];
    $valid_until = date('Y-m-d', strtotime("+$months months"));
    
    // Начинаем транзакцию
    $pdo->beginTransaction();
    
    try {
        // Проверяем, есть ли уже абонемент
        $stmt = $pdo->prepare("SELECT * FROM memberships WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $existing = $stmt->fetch();
        
        $lessons_before = $existing ? $existing['lessons_left'] : 0;
        
        if($existing) {
            // Добавляем к существующему
            if($existing['lessons_left'] == 999 || $lessons == 999) {
                // Если был безлимит или новый безлимит - устанавливаем безлимит
                $stmt = $pdo->prepare("UPDATE memberships SET lessons_left = 999, valid_until = ? WHERE user_id = ?");
                $stmt->execute([$valid_until, $user_id]);
                $lessons_after = 999;
            } else {
                // Обычное сложение
                $stmt = $pdo->prepare("UPDATE memberships SET lessons_left = lessons_left + ?, valid_until = ? WHERE user_id = ?");
                $stmt->execute([$lessons, $valid_until, $user_id]);
                $lessons_after = $lessons_before + $lessons;
            }
        } else {
            // Создаем новый
            $stmt = $pdo->prepare("INSERT INTO memberships (user_id, lessons_left, valid_until) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $lessons, $valid_until]);
            $lessons_after = $lessons;
        }
        
        // Запись в историю
        $stmt = $pdo->prepare("
            INSERT INTO membership_logs (user_id, action, lessons_before, lessons_after, lessons_changed, description) 
            VALUES (?, 'purchase', ?, ?, ?, 'Оплачено в студии')
        ");
        $stmt->execute([$user_id, $lessons_before, $lessons_after, $lessons]);
        
        // Сбрасываем ожидание оплаты
        $stmt = $pdo->prepare("UPDATE users SET awaiting_payment = 0, selected_plan_id = NULL WHERE id = ?");
        $stmt->execute([$user_id]);
        
        $pdo->commit();
        
        $_SESSION['success'] = "Абонемент активирован для пользователя " . $user['name'];
        
    } catch(Exception $e) {
        $pdo->rollBack();
        error_log("Activation error: " . $e->getMessage());
        $_SESSION['error'] = "Ошибка при активации абонемента";
    }
}

redirect('users.php');
?>