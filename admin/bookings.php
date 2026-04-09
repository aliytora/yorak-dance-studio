<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

if(!isAdmin()) {
    redirect('../login.php');
}

// Отметить как посещено и списать занятие
if(isset($_GET['visited'])) {
    $booking_id = $_GET['visited'];
    
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("
            SELECT b.*, u.id as user_id 
            FROM bookings b
            LEFT JOIN users u ON b.client_phone = u.phone
            WHERE b.id = ?
        ");
        $stmt->execute([$booking_id]);
        $booking = $stmt->fetch();
        
        if($booking && $booking['user_id']) {
            $stmt = $pdo->prepare("
                SELECT * FROM memberships 
                WHERE user_id = ? AND lessons_left > 0 AND (valid_until IS NULL OR valid_until >= CURDATE())
            ");
            $stmt->execute([$booking['user_id']]);
            $membership = $stmt->fetch();
            
            if($membership && $membership['lessons_left'] != 999) {
                $lessons_before = $membership['lessons_left'];
                $stmt = $pdo->prepare("UPDATE memberships SET lessons_left = lessons_left - 1 WHERE id = ?");
                $stmt->execute([$membership['id']]);
                $lessons_after = $lessons_before - 1;
                
                $stmt = $pdo->prepare("
                    INSERT INTO membership_logs (user_id, action, lessons_before, lessons_after, lessons_changed, booking_id, description) 
                    VALUES (?, 'booking', ?, ?, 1, ?, 'Списание за посещенное занятие')
                ");
                $stmt->execute([$booking['user_id'], $lessons_before, $lessons_after, $booking_id]);
            }
        }
        
        $stmt = $pdo->prepare("UPDATE bookings SET status = 'visited' WHERE id = ?");
        $stmt->execute([$booking_id]);
        
        $pdo->commit();
    } catch(Exception $e) {
        $pdo->rollBack();
        error_log("Visit error: " . $e->getMessage());
    }
    
    redirect('bookings.php');
}

// Отменить запись
if(isset($_GET['cancel'])) {
    $stmt = $pdo->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ?");
    $stmt->execute([$_GET['cancel']]);
    redirect('bookings.php');
}

// Получаем все записи
$bookings = $pdo->query("
    SELECT b.*, 
           s.direction, 
           s.time, 
           s.weekday, 
           t.name as trainer_name,
           u.id as user_id,
           u.name as user_name, 
           u.email, 
           u.phone
    FROM bookings b
    JOIN schedules s ON b.schedule_id = s.id
    LEFT JOIN trainers t ON s.trainer_id = t.id
    LEFT JOIN users u ON b.client_phone = u.phone
    ORDER BY b.booking_date DESC, s.time
")->fetchAll();

$days = ['monday'=>'Пн','tuesday'=>'Вт','wednesday'=>'Ср','thursday'=>'Чт','friday'=>'Пт','saturday'=>'Сб','sunday'=>'Вс'];
$statuses = ['active' => 'Активна', 'cancelled' => 'Отменена', 'visited' => 'Посещено'];
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление записями - Yorak Dance Studio</title>
    <link rel="stylesheet" href="/dance_studio2/css/style.css">
    <link rel="stylesheet" href="admin-style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container">
        <h1 class="section-title">Все записи</h1>
        
        <?php if(empty($bookings)): ?>
            <div class="empty-state">
                <p>Пока нет ни одной записи</p>
            </div>
        <?php else: ?>
            <table class="bookings-table">
                <thead>
                    <tr>
                        <th>Дата</th>
                        <th>Клиент</th>
                        <th>Телефон</th>
                        <th>Занятие</th>
                        <th>Тренер</th>
                        <th>Статус</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($bookings as $b): ?>
                        <tr>
                            <td data-label="Дата" class="date-info">
                                <?= date('d.m.Y', strtotime($b['booking_date'])) ?><br>
                                <small><?= $days[$b['weekday']] ?> <?= date('H:i', strtotime($b['time'])) ?></small>
                            </td>
                            <td data-label="Клиент">
                                <strong><?= htmlspecialchars($b['client_name']) ?></strong><br>
                                <?php if($b['email']): ?>
                                    <small style="color: #a0a0a0;"><?= htmlspecialchars($b['email']) ?></small>
                                <?php endif; ?>
                                <?php if($b['user_id']): ?>
                                    <div class="membership-info">✅ Есть аккаунт</div>
                                <?php endif; ?>
                            </td>
                            <td data-label="Телефон"><?= htmlspecialchars($b['client_phone'] ?: '-') ?></td>
                            <td data-label="Занятие"><?= htmlspecialchars($b['direction']) ?></td>
                            <td data-label="Тренер"><?= htmlspecialchars($b['trainer_name'] ?: 'Не указан') ?></td>
                            <td data-label="Статус">
                                <span class="status-badge status-<?= $b['status'] ?>">
                                    <?= $statuses[$b['status']] ?>
                                </span>
                            </td>
                            <td data-label="Действия">
                                <?php if($b['status'] == 'active'): ?>
                                    <a href="?visited=<?= $b['id'] ?>" class="btn-small" onclick="return confirm('Отметить как посещено и списать занятие?')">✓ Посетила</a>
                                    <a href="?cancel=<?= $b['id'] ?>" class="btn-small btn-cancel" onclick="return confirm('Отменить запись?')">✗ Отменить</a>
                                <?php elseif($b['status'] == 'visited'): ?>
                                    <span class="status-text" style="color: #81c784;">✓ Посещено</span>
                                <?php else: ?>
                                    <span class="status-text" style="color: #ef5350;">✗ Отменено</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        
        <p style="text-align: center; margin-top: 40px;">
            <a href="index.php" class="btn-outline">← Назад в админ-панель</a>
        </p>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>