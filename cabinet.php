<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

if(!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];

// Получаем данные пользователя
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    redirect('logout.php');
}

// Получаем активный абонемент пользователя
$stmt = $pdo->prepare("
    SELECT * FROM memberships 
    WHERE user_id = ? AND lessons_left > 0 AND (valid_until IS NULL OR valid_until >= CURDATE())
");
$stmt->execute([$user_id]);
$membership = $stmt->fetch();

// Получаем историю списаний
$stmt = $pdo->prepare("
    SELECT * FROM membership_logs 
    WHERE user_id = ? 
    ORDER BY created_at DESC 
    LIMIT 10
");
$stmt->execute([$user_id]);
$membership_logs = $stmt->fetchAll();

// Получаем записи пользователя
$stmt = $pdo->prepare("
    SELECT b.*, s.direction, s.level, s.time, s.weekday, t.name as trainer_name
    FROM bookings b
    JOIN schedules s ON b.schedule_id = s.id
    LEFT JOIN trainers t ON s.trainer_id = t.id
    WHERE b.client_phone = ? AND b.status = 'active' AND b.booking_date >= CURDATE()
    ORDER BY b.booking_date, s.time
");
$stmt->execute([$user['phone']]);
$bookings = $stmt->fetchAll();

// Получаем прошлые записи
$stmt = $pdo->prepare("
    SELECT b.*, s.direction, s.time, s.weekday, t.name as trainer_name
    FROM bookings b
    JOIN schedules s ON b.schedule_id = s.id
    LEFT JOIN trainers t ON s.trainer_id = t.id
    WHERE b.client_phone = ? AND (b.status != 'active' OR b.booking_date < CURDATE())
    ORDER BY b.booking_date DESC
    LIMIT 10
");
$stmt->execute([$user['phone']]);
$history = $stmt->fetchAll();

$weekdays = [
    'monday' => 'Пн',
    'tuesday' => 'Вт',
    'wednesday' => 'Ср',
    'thursday' => 'Чт',
    'friday' => 'Пт',
    'saturday' => 'Сб',
    'sunday' => 'Вс'
];

$months = [
    'January' => 'января',
    'February' => 'февраля',
    'March' => 'марта',
    'April' => 'апреля',
    'May' => 'мая',
    'June' => 'июня',
    'July' => 'июля',
    'August' => 'августа',
    'September' => 'сентября',
    'October' => 'октября',
    'November' => 'ноября',
    'December' => 'декабря'
];
?>

<?php include 'includes/header.php'; ?>

<?php
// Проверяем, есть ли ожидание оплаты
$stmt = $pdo->prepare("SELECT awaiting_payment, selected_plan_id FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user_status = $stmt->fetch();

if($user_status && $user_status['awaiting_payment'] == 1):
    // Получаем название плана
    $stmt = $pdo->prepare("SELECT name FROM membership_plans WHERE id = ?");
    $stmt->execute([$user_status['selected_plan_id']]);
    $plan = $stmt->fetch();
?>
<div class="payment-warning">
    <p>
        <strong>⏳ Ожидает подтверждения оплаты</strong><br>
        Вы выбрали абонемент "<?= htmlspecialchars($plan['name'] ?? '') ?>". 
        Оплатите в студии, и администратор активирует его.
    </p>
</div>
<?php endif; ?>

<style>
/* =====================================================
   СТИЛЬНЫЙ КАБИНЕТ — красный/черный/белый (посветлее)
   ===================================================== */

body {
    background: #0a0a0a;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

/* Заголовок */
h1.section-title {
    color: #ffffff;
    font-size: 48px;
    font-weight: 900;
    margin-bottom: 40px;
    text-align: center;
    text-transform: uppercase;
    letter-spacing: 2px;
    position: relative;
    padding-bottom: 20px;
}

h1.section-title::before {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 150px;
    height: 5px;
    background: #04d9ff;
}

h1.section-title::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 50px;
    height: 5px;
    background: #ffffff;
    z-index: 1;
}

/* Сетка кабинета - обновленная структура */
.cabinet-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 30px;
    margin: 30px 0;
}

/* Верхний блок с профилем и абонементом */
.profile-header {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    margin-bottom: 30px;
}

/* Карточка профиля */
.profile-card {
    background: #1f1f1f;
    border: 1px solid #333333;
    padding: 25px;
    display: flex;
    align-items: center;
    gap: 25px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
}

.profile-avatar {
    width: 80px;
    height: 80px;
    background: #04d9ff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 36px;
    font-weight: 900;
    color: #ffffff;
    border: 3px solid #ffffff;
}

.profile-info h3 {
    color: #ffffff;
    font-size: 24px;
    font-weight: 800;
    margin-bottom: 10px;
}

.profile-info p {
    color: #b0b0b0;
    margin-bottom: 5px;
    font-size: 14px;
}

.profile-info p strong {
    color: #04d9ff;
    font-weight: 700;
    min-width: 70px;
    display: inline-block;
}

/* Карточка абонемента - главная */
.membership-main-card {
    background: linear-gradient(135deg, #1f1f1f 0%, #2a2a2a 100%);
    border: 1px solid #333333;
    padding: 25px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
}

.membership-main-card::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 150px;
    height: 150px;
    background: rgba(158, 42, 43, 0.1);
    border-radius: 50%;
    transform: translate(50px, -50px);
}

.membership-main-card h4 {
    color: #04d9ff;
    font-size: 14px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 2px;
    margin-bottom: 10px;
}

.membership-main-content {
    display: flex;
    align-items: center;
    gap: 30px;
    flex-wrap: wrap;
}

.membership-main-count {
    text-align: center;
    min-width: 120px;
}

.membership-main-count .lessons-count {
    font-size: 64px;
    font-weight: 900;
    color: #ffffff;
    line-height: 1;
    margin-bottom: 5px;
}

.membership-main-count .lessons-label {
    color: #04d9ff;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.membership-main-stats {
    flex: 1;
    display: flex;
    gap: 30px;
    flex-wrap: wrap;
}

.membership-stat-item {
    flex: 1;
    min-width: 120px;
}

.membership-stat-label {
    color: #b0b0b0;
    font-size: 13px;
    text-transform: uppercase;
    margin-bottom: 5px;
}

.membership-stat-value {
    color: #ffffff;
    font-size: 24px;
    font-weight: 800;
}

.membership-stat-value small {
    color: #04d9ff;
    font-size: 14px;
    font-weight: 400;
    margin-left: 5px;
}

/* Прогресс-бар */
.progress-bar-container {
    margin-top: 20px;
    background: #333333;
    height: 8px;
    border-radius: 4px;
    overflow: hidden;
}

.progress-bar-fill {
    background: #04d9ff;
    height: 100%;
    width: 0%;
    transition: width 0.3s;
    border-radius: 4px;
}

.progress-labels {
    display: flex;
    justify-content: space-between;
    margin-top: 8px;
    color: #b0b0b0;
    font-size: 12px;
}

/* Основная сетка контента */
.content-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
}

/* Левая колонка с предстоящими занятиями */
.bookings-section {
    background: #1f1f1f;
    border: 1px solid #333333;
    padding: 25px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
}

.bookings-section h3 {
    color: #ffffff;
    font-size: 22px;
    font-weight: 800;
    margin-bottom: 20px;
    text-transform: uppercase;
    letter-spacing: 1px;
    border-bottom: 2px solid #04d9ff;
    padding-bottom: 10px;
}

/* Карточка занятия */
.booking-item {
    padding: 20px;
    border: 1px solid #333333;
    margin-bottom: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    transition: all 0.3s;
    background: #2a2a2a;
}

.booking-item:hover {
    border-color: #04d9ff;
    transform: translateX(-5px);
    box-shadow: 5px 5px 20px rgba(158, 42, 43, 0.2);
}

.booking-item strong {
    color: #ffffff;
    font-size: 20px;
    font-weight: 800;
    display: block;
    margin-bottom: 8px;
}

.booking-item .details {
    color: #b0b0b0;
    font-size: 14px;
    line-height: 1.6;
    margin-bottom: 5px;
}

.booking-item .trainer {
    color: #04d9ff;
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
}

/* Правая колонка с двумя блоками */
.right-column {
    display: flex;
    flex-direction: column;
    gap: 30px;
}

/* Блок статистики */
.stats-grid {
    background: #1f1f1f;
    border: 1px solid #333333;
    padding: 25px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
}

.stats-grid h3 {
    color: #ffffff;
    font-size: 22px;
    font-weight: 800;
    margin-bottom: 20px;
    text-transform: uppercase;
    letter-spacing: 1px;
    border-bottom: 2px solid #04d9ff;
    padding-bottom: 10px;
}

.stats-cards {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
}

.stat-card {
    background: #2a2a2a;
    padding: 20px;
    text-align: center;
    border: 1px solid #333333;
}

.stat-card .stat-number {
    font-size: 36px;
    font-weight: 900;
    color: #04d9ff;
    line-height: 1;
    margin-bottom: 8px;
}

.stat-card .stat-label {
    color: #b0b0b0;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Блок операций */
.operations-section {
    background: #1f1f1f;
    border: 1px solid #333333;
    padding: 25px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
}

.operations-section h3 {
    color: #ffffff;
    font-size: 22px;
    font-weight: 800;
    margin-bottom: 20px;
    text-transform: uppercase;
    letter-spacing: 1px;
    border-bottom: 2px solid #04d9ff;
    padding-bottom: 10px;
}

.operation-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 15px;
    margin-bottom: 8px;
    background: #2a2a2a;
    border-left: 4px solid;
    transition: all 0.3s;
}

.operation-item:hover {
    transform: translateX(5px);
    background: #333333;
}

.operation-booking {
    border-left-color: #04d9ff;
}

.operation-purchase {
    border-left-color: #ffffff;
}

.operation-refund {
    border-left-color: #666666;
}

.operation-item .operation-info span {
    color: #ffffff;
    font-weight: 700;
    display: block;
    margin-bottom: 3px;
}

.operation-item .operation-info small {
    color: #b0b0b0;
    font-size: 11px;
}

.operation-item .operation-value {
    font-weight: 900;
    font-size: 18px;
}

.operation-item .operation-value.negative {
    color: #04d9ff;
}

.operation-item .operation-value.positive {
    color: #ffffff;
}

/* История посещений */
.history-section {
    background: #1f1f1f;
    border: 1px solid #333333;
    padding: 25px;
    margin-top: 30px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
}

.history-section h3 {
    color: #ffffff;
    font-size: 22px;
    font-weight: 800;
    margin-bottom: 20px;
    text-transform: uppercase;
    letter-spacing: 1px;
    border-bottom: 2px solid #04d9ff;
    padding-bottom: 10px;
}

.history-item {
    padding: 12px 15px;
    background: #2a2a2a;
    margin-bottom: 8px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    border: 1px solid #333333;
}

.history-item .direction {
    color: #ffffff;
    font-weight: 700;
    font-size: 15px;
}

.history-item .date {
    color: #b0b0b0;
    font-size: 13px;
}

.history-item .status-visited {
    color: #04d9ff;
    font-weight: 800;
    text-transform: uppercase;
    font-size: 12px;
}

.history-item .status-cancelled {
    color: #666666;
    font-weight: 800;
    text-transform: uppercase;
    font-size: 12px;
}

/* Кнопки */
.btn {
    display: inline-block;
    padding: 12px 25px;
    background: transparent;
    color: #ffffff;
    text-decoration: none;
    transition: all 0.3s;
    border: 2px solid #04d9ff;
    font-size: 13px;
    cursor: pointer;
    text-align: center;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    position: relative;
    overflow: hidden;
    z-index: 1;
}

.btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: #04d9ff;
    transition: left 0.3s;
    z-index: -1;
}

.btn:hover::before {
    left: 0;
}

.btn:hover {
    color: #ffffff;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(158, 42, 119, 0.3);
}

.btn-outline {
    border: 2px solid #ffffff;
}

.btn-outline::before {
    background: #ffffff;
}

.btn-outline:hover {
    color: #000000;
}

.btn-small {
    padding: 8px 15px;
    font-size: 12px;
}

.actions-bar {
    display: flex;
    gap: 10px;
    margin-top: 20px;
    flex-wrap: wrap;
}

/* Уведомление об ожидании оплаты */
.payment-warning {
    background: #2a1a27;
    padding: 20px;
    margin-bottom: 30px;
    border-left: 5px solid #04d9ff;
    border: 1px solid #333333;
}

.payment-warning p {
    color: #ffffff;
    font-size: 15px;
    line-height: 1.6;
}

.payment-warning strong {
    color: #04d9ff;
    font-weight: 800;
}

/* Пустое состояние */
.empty-state {
    text-align: center;
    padding: 40px 20px;
    background: #2a2a2a;
    border: 1px solid #333333;
}

.empty-state p {
    color: #b0b0b0;
    margin-bottom: 20px;
    font-size: 16px;
}

/* =====================================================
   АДАПТИВНОСТЬ
   ===================================================== */
@media screen and (max-width: 992px) {
    .profile-header {
        grid-template-columns: 1fr;
    }
    
    .content-grid {
        grid-template-columns: 1fr;
    }
    
    .stats-cards {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media screen and (max-width: 768px) {
    h1.section-title {
        font-size: 36px;
    }
    
    .profile-card {
        flex-direction: column;
        text-align: center;
    }
    
    .membership-main-content {
        flex-direction: column;
        text-align: center;
    }
    
    .membership-main-stats {
        justify-content: center;
    }
    
    .stats-cards {
        grid-template-columns: 1fr;
    }
    
    .booking-item {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .booking-item .btn {
        width: 100%;
    }
}

@media screen and (max-width: 480px) {
    h1.section-title {
        font-size: 28px;
    }
    
    .profile-card {
        padding: 20px;
    }
    
    .profile-avatar {
        width: 60px;
        height: 60px;
        font-size: 28px;
    }
    
    .profile-info h3 {
        font-size: 20px;
    }
    
    .membership-main-count .lessons-count {
        font-size: 48px;
    }
    
    .membership-main-stats {
        flex-direction: column;
        gap: 15px;
    }
    
    .actions-bar {
        flex-direction: column;
    }
    
    .actions-bar .btn {
        width: 100%;
    }
}
</style>

<div class="container">
    <h1 class="section-title">Личный кабинет</h1>
    
    <!-- Верхний блок с профилем и абонементом -->
    <div class="profile-header">
        <!-- Карточка профиля -->
        <div class="profile-card">
            <div class="profile-avatar">
                <?= mb_substr(htmlspecialchars($user['name']), 0, 1) ?>
            </div>
            <div class="profile-info">
                <h3><?= htmlspecialchars($user['name']) ?></h3>
                <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                <p><strong>Телефон:</strong> <?= htmlspecialchars($user['phone'] ?: 'не указан') ?></p>
                <p><strong>С нами с:</strong> <?= date('d.m.Y', strtotime($user['created_at'])) ?></p>
            </div>
        </div>
        
        <!-- Карточка абонемента -->
        <div class="membership-main-card">
            <h4>Абонемент</h4>
            <?php if($membership): 
                $current_month_start = date('Y-m-01');
                $current_month_end = date('Y-m-t');

                $stmt = $pdo->prepare("
                    SELECT COUNT(*) as used_this_month 
                    FROM bookings b
                    WHERE b.client_phone = ? 
                    AND b.booking_date BETWEEN ? AND ?
                    AND b.status = 'visited'
                ");
                $stmt->execute([$user['phone'], $current_month_start, $current_month_end]);
                $monthly_usage = $stmt->fetch();

                $used_this_month = $monthly_usage['used_this_month'] ?? 0;
                $is_unlimited = ($membership['lessons_left'] == 999);
            ?>
                <div class="membership-main-content">
                    <div class="membership-main-count">
                        <?php if($is_unlimited): ?>
                            <div class="lessons-count">∞</div>
                            <div class="lessons-label">безлимит</div>
                        <?php else: ?>
                            <div class="lessons-count"><?= $membership['lessons_left'] ?></div>
                            <div class="lessons-label">занятий осталось</div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="membership-main-stats">
                        <div class="membership-stat-item">
                            <div class="membership-stat-label">Использовано</div>
                            <div class="membership-stat-value"><?= $used_this_month ?><small>в этом месяце</small></div>
                        </div>
                        
                        <?php if($membership['valid_until']): ?>
                            <div class="membership-stat-item">
                                <div class="membership-stat-label">Действует до</div>
                                <div class="membership-stat-value"><?= date('d.m', strtotime($membership['valid_until'])) ?></div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="membership-stat-item">
                            <div class="membership-stat-label">До конца месяца</div>
                            <div class="membership-stat-value"><?= date('t') - date('j') ?><small>дней</small></div>
                        </div>
                    </div>
                </div>
                
                <?php if(!$is_unlimited): ?>
                    <?php 
                    $avg_per_month = 8;
                    $percent = min(100, ($used_this_month / $avg_per_month) * 100);
                    ?>
                    <div class="progress-bar-container">
                        <div class="progress-bar-fill" style="width: <?= $percent ?>%;"></div>
                    </div>
                    <div class="progress-labels">
                        <span>Начало месяца</span>
                        <span>Конец месяца</span>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div style="text-align: center; padding: 20px;">
                    <p style="color: #ffffff; margin-bottom: 15px;">Нет активного абонемента</p>
                    <a href="buy_membership.php" class="btn">Купить абонемент</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Основная сетка контента -->
    <div class="content-grid">
        <!-- Левая колонка - Предстоящие занятия -->
        <div class="bookings-section">
            <h3>Мои предстоящие занятия</h3>
            
            <?php if(empty($bookings)): ?>
                <div class="empty-state">
                    <p>У вас пока нет записей</p>
                    <a href="schedule.php" class="btn">Выбрать занятие</a>
                </div>
            <?php else: ?>
                <?php foreach($bookings as $booking): 
                    $timestamp = strtotime($booking['booking_date']);
                    $day = date('j', $timestamp);
                    $month = $months[date('F', $timestamp)] ?? date('F', $timestamp);
                ?>
                    <div class="booking-item">
                        <div>
                            <strong><?= htmlspecialchars($booking['direction']) ?></strong>
                            <?php if(!empty($booking['level'])): ?>
                                <span style="font-size: 13px; color: #9e2a2b; margin-left: 10px;"><?= htmlspecialchars($booking['level']) ?></span>
                            <?php endif; ?>
                            
                            <div class="details">
                                🗓 <?= $day ?> <?= $month ?> (<?= $weekdays[$booking['weekday']] ?? '?' ?>) 
                                в <?= date('H:i', strtotime($booking['time'])) ?>
                            </div>
                            
                            <div class="trainer">👤 <?= htmlspecialchars($booking['trainer_name'] ?? 'Тренер не указан') ?></div>
                        </div>
                        
                        <a href="cancel_booking.php?id=<?= $booking['id'] ?>" 
                           class="btn btn-small" 
                           onclick="return confirm('Отменить запись?')">
                            Отменить
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            
            <div class="actions-bar">
                <a href="schedule.php" class="btn">Записаться</a>
                <a href="buy_membership.php" class="btn btn-outline">Купить абонемент</a>
            </div>
        </div>
        
        <!-- Правая колонка -->
        <div class="right-column">
            <!-- Блок статистики -->
            <div class="stats-grid">
                <h3>Статистика</h3>
                <div class="stats-cards">
                    <div class="stat-card">
                        <div class="stat-number"><?= count($bookings) ?></div>
                        <div class="stat-label">активных записей</div>
                    </div>
                    
                    <?php if($membership && !$is_unlimited): ?>
                        <div class="stat-card">
                            <div class="stat-number"><?= $membership['lessons_left'] ?></div>
                            <div class="stat-label">осталось занятий</div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="stat-card">
                        <div class="stat-number"><?= count($history) ?></div>
                        <div class="stat-label">всего посещений</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-number"><?= date('t') - date('j') ?></div>
                        <div class="stat-label">дней в месяце</div>
                    </div>
                </div>
            </div>
            
            <!-- Блок последних операций -->
            <?php if(!empty($membership_logs)): ?>
                <div class="operations-section">
                    <h3>Последние операции</h3>
                    
                    <?php foreach(array_slice($membership_logs, 0, 5) as $log): ?>
                        <div class="operation-item operation-<?= $log['action'] ?>">
                            <div class="operation-info">
                                <span>
                                    <?php if($log['action'] == 'booking'): ?>
                                        Списание занятия
                                    <?php elseif($log['action'] == 'purchase'): ?>
                                        Покупка абонемента
                                    <?php elseif($log['action'] == 'refund'): ?>
                                        Возврат
                                    <?php endif; ?>
                                </span>
                                <small><?= date('d.m Y', strtotime($log['created_at'])) ?></small>
                            </div>
                            <div class="operation-value <?= $log['action'] == 'booking' ? 'negative' : 'positive' ?>">
                                <?= $log['action'] == 'booking' ? '−' : '+' ?><?= $log['lessons_changed'] ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- История посещений -->
    <?php if(!empty($history)): ?>
        <div class="history-section">
            <h3>История посещений</h3>
            <?php foreach(array_slice($history, 0, 5) as $h): ?>
                <div class="history-item">
                    <span class="direction"><?= htmlspecialchars($h['direction']) ?></span>
                    <span class="date"><?= date('d.m.Y', strtotime($h['booking_date'])) ?></span>
                    <span class="<?= ($h['status'] ?? '') == 'visited' ? 'status-visited' : 'status-cancelled' ?>">
                        <?= ($h['status'] ?? '') == 'visited' ? '✓ Посещено' : '✗ Отменено' ?>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>