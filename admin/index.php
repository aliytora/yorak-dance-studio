<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

// Проверка прав администратора
if (!isAdmin()) {
    header('Location: ../login.php');
    exit();
}

// Получаем статистику для отображения
$users_count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$today_bookings = $pdo->query("SELECT COUNT(*) FROM bookings WHERE DATE(booking_date) = CURDATE()")->fetchColumn();
$active_schedules = $pdo->query("SELECT COUNT(*) FROM schedules")->fetchColumn();

// Получаем статистику отзывов
$pending_reviews = $pdo->query("SELECT COUNT(*) FROM reviews WHERE status = 'pending'")->fetchColumn();
$total_reviews = $pdo->query("SELECT COUNT(*) FROM reviews")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ панель - Yorak Dance Studio</title>
    <link rel="stylesheet" href="/dance_studio2/css/style.css">
<link rel="stylesheet" href="admin-style.css">
  
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="admin-dashboard">
        <h1 class="section-title">Панель администратора</h1>
        
        <div class="dashboard-stats">
            <div class="stat-box">
                <div class="stat-number"><?= htmlspecialchars($users_count) ?></div>
                <div class="stat-label">Всего пользователей</div>
            </div>
            <div class="stat-box">
                <div class="stat-number"><?= htmlspecialchars($today_bookings) ?></div>
                <div class="stat-label">Записей на сегодня</div>
            </div>
            <div class="stat-box">
                <div class="stat-number"><?= htmlspecialchars($active_schedules) ?></div>
                <div class="stat-label">Занятий в неделю</div>
            </div>
            <div class="stat-box">
                <div class="stat-number"><?= htmlspecialchars($total_reviews) ?></div>
                <div class="stat-label">Всего отзывов</div>
            </div>
        </div>
        
        <div class="dashboard-grid">
            <div class="dashboard-card">
                <div class="card-icon"></div>
                <h3>Пользователи</h3>
                <p>Управление пользователями, назначение администраторов, добавление абонементов</p>
                <a href="users.php" class="btn">Управлять пользователями</a>
            </div>
            
            <div class="dashboard-card">
                <div class="card-icon"></div>
                <h3>Расписание</h3>
                <p>Добавление и удаление занятий, настройка времени и тренеров</p>
                <a href="schedule.php" class="btn">Управлять расписанием</a>
            </div>
            
            <div class="dashboard-card">
                <div class="card-icon"></div>
                <h3>Записи</h3>
                <p>Просмотр всех записей, отметка о посещении, отмена</p>
                <a href="bookings.php" class="btn">Смотреть записи</a>
            </div>

            <div class="dashboard-card">
                <?php if ($pending_reviews > 0): ?>
                    <div class="badge badge-warning"><?= $pending_reviews ?> новых</div>
                <?php endif; ?>
                <div class="card-icon"></div>
                <h3>Отзывы</h3>
                <p>Модерация отзывов от учениц. <?= $pending_reviews ?> ожидают проверки</p>
                <a href="reviews.php" class="btn <?= $pending_reviews > 0 ? 'btn-warning' : '' ?>">
                    Управлять отзывами
                    <?php if ($pending_reviews > 0): ?>
                        (<?= $pending_reviews ?>)
                    <?php endif; ?>
                </a>
            </div>
        </div>

        <div class="trainer-section">
            <a href="edit_trainer.php" class="btn btn-secondary"> Управление тренерами</a>
            <a href="reviews.php" class="btn btn-secondary"> Все отзывы</a>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>