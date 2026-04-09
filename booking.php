<?php
require_once 'includes/db.php';
session_start();

$success = '';
$error = '';

// Получаем расписание на ближайшие 7 дней
$schedules = $pdo->query("
    SELECT s.*, t.name as trainer_name 
    FROM schedules s 
    LEFT JOIN trainers t ON s.trainer_id = t.id 
    ORDER BY FIELD(weekday, 'monday','tuesday','wednesday','thursday','friday','saturday','sunday'), time
")->fetchAll();

// Дни недели
$weekdays = [
    'monday' => 'Пн',
    'tuesday' => 'Вт',
    'wednesday' => 'Ср',
    'thursday' => 'Чт',
    'friday' => 'Пт',
    'saturday' => 'Сб',
    'sunday' => 'Вс'
];

// Ближайшие даты по дням недели
$dates = [];
for($i = 0; $i < 14; $i++) {
    $date = date('Y-m-d', strtotime("+$i days"));
    $day = strtolower(date('l', strtotime($date)));
    $dates[$day][] = $date;
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $schedule_id = $_POST['schedule_id'] ?? '';
    $booking_date = $_POST['booking_date'] ?? '';
    $name = $_POST['name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    
    if(empty($schedule_id) || empty($booking_date) || empty($name) || empty($phone)) {
        $error = 'Заполните все поля';
    } else {
        // Проверка мест
        $stmt = $pdo->prepare("
            SELECT s.max_participants, 
                   (SELECT COUNT(*) FROM bookings WHERE schedule_id = ? AND booking_date = ? AND status = 'active') as booked
            FROM schedules s
            WHERE s.id = ?
        ");
        $stmt->execute([$schedule_id, $booking_date, $schedule_id]);
        $slot = $stmt->fetch();
        
        if($slot['booked'] >= $slot['max_participants']) {
            $error = 'Мест нет';
        } else {
            // Начинаем транзакцию
            $pdo->beginTransaction();
            
            try {
                // Сначала записываем на занятие
                $stmt = $pdo->prepare("INSERT INTO bookings (schedule_id, booking_date, client_name, client_phone) VALUES (?, ?, ?, ?)");
                $stmt->execute([$schedule_id, $booking_date, $name, $phone]);
                $booking_id = $pdo->lastInsertId();
                
                // Если пользователь авторизован - списываем абонемент
                if(isset($_SESSION['user_id'])) {
                    $user_id = $_SESSION['user_id'];
                    
                    // Проверяем есть ли абонемент
                    $stmt = $pdo->prepare("
                        SELECT * FROM memberships 
                        WHERE user_id = ? AND lessons_left > 0 AND (valid_until IS NULL OR valid_until >= CURDATE())
                        ORDER BY created_at ASC
                    ");
                    $stmt->execute([$user_id]);
                    $membership = $stmt->fetch();
                    
                    if($membership) {
                        $lessons_before = $membership['lessons_left'];
                        
                        // Для безлимитного абонемента (999 занятий)
                        if($membership['lessons_left'] == 999) {
                            // Не списываем, но записываем в лог
                            $stmt = $pdo->prepare("
                                INSERT INTO membership_logs (user_id, action, lessons_before, lessons_after, lessons_changed, booking_id, description) 
                                VALUES (?, 'booking', ?, ?, 0, ?, 'Списание за занятие (безлимит)')
                            ");
                            $stmt->execute([$user_id, $lessons_before, $lessons_before, $booking_id]);
                        } else {
                            // Списываем одно занятие
                            $stmt = $pdo->prepare("UPDATE memberships SET lessons_left = lessons_left - 1 WHERE id = ?");
                            $stmt->execute([$membership['id']]);
                            
                            $lessons_after = $lessons_before - 1;
                            
                            // Записываем в историю
                            $stmt = $pdo->prepare("
                                INSERT INTO membership_logs (user_id, action, lessons_before, lessons_after, lessons_changed, booking_id, description) 
                                VALUES (?, 'booking', ?, ?, 1, ?, 'Списание за занятие')
                            ");
                            $stmt->execute([$user_id, $lessons_before, $lessons_after, $booking_id]);
                        }
                    }
                }
                
                $pdo->commit();
                $success = 'Записались!';
                
            } catch(Exception $e) {
                $pdo->rollBack();
                error_log("Booking error: " . $e->getMessage());
                $error = 'Ошибка при записи';
            }
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="container" style="max-width: 500px;">
    <h1 class="section-title">Запись</h1>
    
    <?php if($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    
    <?php if($error): ?>
        <div class="alert alert-error"><?= $error ?></div>
    <?php endif; ?>
    
    <form method="POST" style="background: white; padding: 20px; border-radius: 15px;">
        <div class="form-group">
            <label>Занятие</label>
            <select name="schedule_id" required style="width: 100%; padding: 10px;">
                <option value="">Выберите</option>
                <?php foreach($schedules as $s): ?>
                    <option value="<?= $s['id'] ?>">
                        <?= $s['direction'] ?> - <?= $s['trainer_name'] ?> 
                        (<?= $weekdays[$s['weekday']] ?> <?= date('H:i', strtotime($s['time'])) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label>Дата</label>
            <select name="booking_date" required style="width: 100%; padding: 10px;">
                <option value="">Выберите дату</option>
                <?php foreach($dates as $day => $day_dates): ?>
                    <?php foreach($day_dates as $date): ?>
                        <option value="<?= $date ?>"><?= date('d.m (D)', strtotime($date)) ?></option>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label>Имя</label>
            <input type="text" name="name" required style="width: 100%; padding: 10px;">
        </div>
        
        <div class="form-group">
            <label>Телефон</label>
            <input type="tel" name="phone" required style="width: 100%; padding: 10px;">
        </div>
        
        <button type="submit" class="btn" style="width: 100%;">Записаться</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>