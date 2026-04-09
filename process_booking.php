<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

if(!isLoggedIn()) {
    redirect('login.php');
}

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('schedule.php');
}

$user_id = $_SESSION['user_id'];
$schedule_id = $_POST['schedule_id'] ?? 0;
$booking_date = $_POST['booking_date'] ?? '';

// Получаем данные пользователя
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if(!$user || empty($user['phone'])) {
    $_SESSION['error'] = 'Укажите телефон в профиле';
    redirect('profile.php');
}

// Проверяем расписание
$stmt = $pdo->prepare("SELECT * FROM schedules WHERE id = ? AND status = 'active'");
$stmt->execute([$schedule_id]);
$schedule = $stmt->fetch();

if(!$schedule) {
    $_SESSION['error'] = 'Занятие не найдено';
    redirect('schedule.php');
}

// Проверяем, есть ли активный абонемент
$stmt = $pdo->prepare("
    SELECT * FROM user_memberships 
    WHERE user_id = ? AND status = 'active' 
    AND (expiry_date IS NULL OR expiry_date >= CURDATE())
    AND remaining_visits > 0
    ORDER BY created_at DESC
    LIMIT 1
");
$stmt->execute([$user_id]);
$membership = $stmt->fetch();

// Проверяем, не записан ли уже пользователь
$stmt = $pdo->prepare("
    SELECT * FROM bookings 
    WHERE client_phone = ? AND schedule_id = ? 
    AND booking_date = ? AND status = 'active'
");
$stmt->execute([$user['phone'], $schedule_id, $booking_date]);

if($stmt->fetch()) {
    $_SESSION['error'] = 'Вы уже записаны на это занятие';
    redirect('schedule.php');
}

// Начинаем транзакцию
$pdo->beginTransaction();

try {
    // Создаем запись
    if($membership) {
        // Запись по абонементу
        $stmt = $pdo->prepare("
            INSERT INTO bookings (schedule_id, user_membership_id, client_name, client_phone, booking_date, status, used_membership)
            VALUES (?, ?, ?, ?, ?, 'active', 1)
        ");
        $stmt->execute([$schedule_id, $membership['id'], $user['name'], $user['phone'], $booking_date]);
        
        // Обновляем количество оставшихся занятий в абонементе
        $new_used = $membership['used_visits'] + 1;
        $new_remaining = $membership['remaining_visits'] - 1;
        
        $stmt = $pdo->prepare("
            UPDATE user_memberships 
            SET used_visits = ?, remaining_visits = ? 
            WHERE id = ?
        ");
        $stmt->execute([$new_used, $new_remaining, $membership['id']]);
        
        $_SESSION['success'] = 'Вы записаны! Списано 1 занятие с абонемента. Осталось: ' . $new_remaining;
    } else {
        // Обычная запись (разовая)
        $stmt = $pdo->prepare("
            INSERT INTO bookings (schedule_id, client_name, client_phone, booking_date, status, used_membership)
            VALUES (?, ?, ?, ?, 'active', 0)
        ");
        $stmt->execute([$schedule_id, $user['name'], $user['phone'], $booking_date]);
        
        $_SESSION['success'] = 'Вы записаны! Оплата при входе в студию.';
    }
    
    $pdo->commit();
    redirect('cabinet.php');
    
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['error'] = 'Ошибка при записи: ' . $e->getMessage();
    redirect('schedule.php');
}