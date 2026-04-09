<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

// Получаем месяц и год (можно оставить навигацию)
$month = isset($_GET['month']) ? $_GET['month'] : date('n');
$year = isset($_GET['year']) ? $_GET['year'] : date('Y');

// Первый день месяца
$first_day = mktime(0, 0, 0, $month, 1, $year);
$days_in_month = date('t', $first_day);

// День недели первого числа (1 - понедельник, 7 - воскресенье)
$first_weekday = date('N', $first_day);

// Получаем все занятия
$schedules = $pdo->query("
    SELECT s.*, t.name as trainer_name 
    FROM schedules s 
    LEFT JOIN trainers t ON s.trainer_id = t.id 
    ORDER BY s.time
")->fetchAll();

// Цвета для разных направлений
$colors = [
    'Hip-Hop PRO 12+' => '#ffddc0',
    'Girly Hip-Hop' => '#c4d0ffda',
    'Contemporary' => '#f4baff',
    'High Heels' => '#ffb4b4',
    'Jazz-Funk' => '#ff9bc6',
    'Stretching' => '#c1ffdc',
];

// Функция получения цвета
function getDirectionColor($direction, $colors) {
    foreach($colors as $key => $color) {
        if(strpos($direction, $key) !== false) {
            return $color;
        }
    }
    return '#9e2a2b';
}

// Дни недели по-русски
$weekdays = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'];
$weekdays_full = [
    'monday' => 'Понедельник',
    'tuesday' => 'Вторник',
    'wednesday' => 'Среда',
    'thursday' => 'Четверг',
    'friday' => 'Пятница',
    'saturday' => 'Суббота',
    'sunday' => 'Воскресенье'
];

// Месяцы
$months = [
    1 => 'Январь', 2 => 'Февраль', 3 => 'Март', 4 => 'Апрель',
    5 => 'Май', 6 => 'Июнь', 7 => 'Июль', 8 => 'Август',
    9 => 'Сентябрь', 10 => 'Октябрь', 11 => 'Ноябрь', 12 => 'Декабрь'
];

// Текущая дата
$today = date('Y-m-d');

// Обработка записи
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book'])) {
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
            $stmt = $pdo->prepare("INSERT INTO bookings (schedule_id, booking_date, client_name, client_phone) VALUES (?, ?, ?, ?)");
            if($stmt->execute([$schedule_id, $booking_date, $name, $phone])) {
                $success = 'Вы успешно записаны!';
            } else {
                $error = 'Ошибка при записи';
            }
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<style>
/* =====================================================
   РАСПИСАНИЕ - Дерзкий ЧЕРНО-НЕОНОВЫЙ ГОЛУБОЙ стиль
   Черный + графит + #04d9ff + #00f5ff
   ===================================================== */

/* Общие стили */
body {
    background: #0c0c0c;
    font-family: 'Montserrat', sans-serif;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

/* Заголовок */

.section-title::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 0;
    width: 100px;
    height: 4px;
    background: #04d9ff;
    box-shadow: 0 0 15px rgba(4, 217, 255, 0.6);
}

/* Легенда цветов */
.color-legend {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    margin: 30px 0 40px;
    padding: 20px 25px;
    background: #111111;
    border: 1px solid #2a2a2a;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.7);
    border-left: 4px solid #04d9ff;
}

@media (max-width: 768px) {
    .color-legend {
        gap: 12px;
        padding: 15px;
    }
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: #e0e0e0;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.legend-color {
    width: 16px;
    height: 16px;
    border: 1px solid #04d9ff;
    box-shadow: 0 0 8px rgba(4, 217, 255, 0.4);
}

/* Календарь */
.calendar {
    background: #111111;
    padding: 30px;
    margin: 40px 0;
    border: 1px solid #2a2a2a;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.7);
    border-top: 3px solid #04d9ff;
}

.calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    flex-wrap: wrap;
    gap: 15px;
}

.calendar-header h2 {
    margin: 0;
    font-size: 28px;
    color: #ffffff;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 2px;
    text-shadow: 0 0 15px rgba(4, 217, 255, 0.3);
}

/* Десктопная версия - сетка */
@media (min-width: 769px) {
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 8px;
    }
    
    .calendar-day-header {
        text-align: center;
        padding: 15px 10px;
        font-weight: 700;
        color: #04d9ff;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 1px;
        background: #1a1a1a;
        border: 1px solid #2a2a2a;
        box-shadow: 0 0 10px rgba(4, 217, 255, 0.2);
    }
    
    .calendar-day {
        min-height: 130px;
        background: #0a0a0a;
        border: 1px solid #2a2a2a;
        padding: 12px;
        position: relative;
        transition: all 0.3s;
    }
    
    .calendar-day:hover {
        border-color: #04d9ff;
        box-shadow: 0 0 20px rgba(4, 217, 255, 0.3);
        transform: translateY(-2px);
    }
    
    .calendar-day.empty {
        background: #0a0a0a;
        border: 1px dashed #2a2a2a;
        opacity: 0.5;
    }
    
    .day-number {
        font-size: 16px;
        font-weight: 700;
        color: #ffffff;
        margin-bottom: 10px;
        display: inline-block;
        background: #1a1a1a;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #04d9ff;
        box-shadow: 0 0 10px rgba(4, 217, 255, 0.4);
    }
}

/* Мобильная версия - вертикальный список */
@media (max-width: 768px) {
    .calendar-grid {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    
    .calendar-day-header {
        display: none;
    }
    
    .calendar-day {
        background: #0a0a0a;
        border: 1px solid #2a2a2a;
        padding: 16px;
        position: relative;
    }
    
    .calendar-day.empty {
        display: none;
    }
    
    .day-number {
        font-size: 18px;
        font-weight: 700;
        color: #ffffff;
        margin-bottom: 12px;
        padding-bottom: 8px;
        border-bottom: 1px solid #04d9ff;
        display: flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 0 10px rgba(4, 217, 255, 0.2);
    }
    
    .calendar-day .day-number::before {
        content: attr(data-weekday);
        color: #04d9ff;
        font-weight: 600;
        font-size: 14px;
        background: #1a1a1a;
        padding: 4px 10px;
        border: 1px solid #2a2a2a;
        box-shadow: 0 0 8px rgba(4, 217, 255, 0.3);
    }
}

.calendar-day.past {
    opacity: 0.4;
    background: #050505;
}

.calendar-event {
    background: #04d9ff;
    color: #000000;
    padding: 8px 10px;
    margin-bottom: 6px;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s;
    border: 1px solid #04d9ff;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 0 10px rgba(4, 217, 255, 0.4);
}

.calendar-event::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.2);
    transition: left 0.3s;
}

.calendar-event:hover::before {
    left: 0;
}

.calendar-event:hover {
    background: #ffffff;
    color: #000000;
    border-color: #ffffff;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(4, 217, 255, 0.6);
}

.calendar-event.full {
    opacity: 0.4;
    background: #2a2a2a;
    border-color: #2a2a2a;
    color: #a0a0a0;
    box-shadow: none;
}

.calendar-event.past-event {
    opacity: 0.2;
    pointer-events: none;
    background: #1a1a1a;
    border-color: #1a1a1a;
}

/* Модальное окно */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.9);
    backdrop-filter: blur(5px);
    z-index: 1000;
    overflow-y: auto;
    padding: 20px;
    box-sizing: border-box;
    align-items: center;
    justify-content: center;
}

.modal-content {
    background: #111111;
    max-width: 500px;
    width: 100%;
    margin: auto;
    padding: 35px;
    position: relative;
    border: 2px solid #04d9ff;
    box-shadow: 0 20px 40px rgba(4, 217, 255, 0.3);
    animation: modalFadeIn 0.3s ease;
}

@keyframes modalFadeIn {
    from {
        opacity: 0;
        transform: scale(0.95) translateY(20px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

@media (max-width: 768px) {
    .modal-content {
        margin: auto 0 0;
        animation: slideUp 0.3s ease;
    }
    
    @keyframes slideUp {
        from {
            transform: translateY(100%);
        }
        to {
            transform: translateY(0);
        }
    }
}

.close {
    position: absolute;
    top: 20px;
    right: 20px;
    font-size: 28px;
    cursor: pointer;
    color: #04d9ff;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s;
    border: 1px solid #04d9ff;
    font-weight: 300;
    box-shadow: 0 0 15px rgba(4, 217, 255, 0.4);
}

.close:hover {
    background: #04d9ff;
    color: #000000;
    transform: rotate(90deg);
}

.event-title {
    font-size: 28px;
    color: #ffffff;
    margin-bottom: 8px;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 2px;
    padding-right: 30px;
    border-left: 4px solid #04d9ff;
    padding-left: 15px;
    text-shadow: 0 0 15px rgba(4, 217, 255, 0.5);
}

.event-info {
    color: #04d9ff;
    margin-bottom: 25px;
    font-weight: 600;
    font-size: 16px;
    background: #1a1a1a;
    display: inline-block;
    padding: 6px 16px;
    border: 1px solid #2a2a2a;
    text-transform: uppercase;
    letter-spacing: 1px;
    box-shadow: 0 0 10px rgba(4, 217, 255, 0.2);
}

.event-detail {
    margin-bottom: 16px;
    padding: 16px 18px;
    background: #0a0a0a;
    border: 1px solid #2a2a2a;
    border-left: 3px solid #04d9ff;
    box-shadow: 0 0 10px rgba(4, 217, 255, 0.1);
}

.event-detail strong {
    color: #04d9ff;
    display: block;
    margin-bottom: 8px;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.event-detail p {
    color: #e0e0e0;
    margin: 0;
    line-height: 1.6;
    font-size: 15px;
}

.places {
    color: #04d9ff;
    font-weight: 800;
    font-size: 24px;
    text-shadow: 0 0 10px rgba(4, 217, 255, 0.5);
}

.places.full {
    color: #666;
}

.booking-form {
    margin-top: 25px;
    padding-top: 25px;
    border-top: 2px solid #2a2a2a;
}

.booking-form h3 {
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 20px;
    color: #ffffff;
    text-transform: uppercase;
    letter-spacing: 2px;
}

/* Формы */
.form-group {
    margin-bottom: 16px;
}

.form-group label {
    display: block;
    margin-bottom: 6px;
    color: #04d9ff;
    font-weight: 600;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.form-group input {
    width: 100%;
    padding: 14px 16px;
    background: #1a1a1a;
    border: 1px solid #2a2a2a;
    font-size: 15px;
    color: #ffffff;
    transition: all 0.3s;
    box-shadow: 0 0 5px rgba(4, 217, 255, 0.1);
}

.form-group input:focus {
    outline: none;
    border-color: #04d9ff;
    background: #222222;
    box-shadow: 0 0 20px rgba(4, 217, 255, 0.3);
}

/* Кнопки */
.btn, .btn-outline {
    padding: 14px 28px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s;
    text-decoration: none;
    display: inline-block;
    border: none;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 2px;
}

@media (max-width: 480px) {
    .btn, .btn-outline {
        width: 100%;
        text-align: center;
    }
}

.btn {
    background: #04d9ff;
    color: #000000;
    border: 2px solid #04d9ff;
    position: relative;
    overflow: hidden;
    z-index: 1;
    box-shadow: 0 5px 15px rgba(4, 217, 255, 0.4);
}

.btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: #000000;
    transition: left 0.3s;
    z-index: -1;
}

.btn:hover {
    color: #04d9ff;
    border-color: #04d9ff;
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(4, 217, 255, 0.6);
}

.btn:hover::before {
    left: 0;
}

.btn-outline {
    background: transparent;
    color: #ffffff;
    border: 2px solid #ffffff;
}

.btn-outline:hover {
    border-color: #04d9ff;
    color: #04d9ff;
    background: transparent;
    box-shadow: 0 0 20px rgba(4, 217, 255, 0.4);
}

/* Навигация */
.calendar-header a {
    min-width: 44px;
    height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    font-weight: 700;
    padding: 0;
    box-shadow: 0 0 15px rgba(4, 217, 255, 0.3);
}

/* Сообщения */
.success-message,
.error-message {
    padding: 16px 24px;
    margin-bottom: 30px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    border-left: 4px solid;
}

.success-message {
    background: #1a2a1a;
    color: #9fc5a0;
    border-left-color: #9fc5a0;
}

.error-message {
    background: #2a1a1a;
    color: #04d9ff;
    border-left-color: #04d9ff;
    box-shadow: 0 0 15px rgba(4, 217, 255, 0.3);
}

/* Индикатор свободных мест на мобильных */
@media (max-width: 768px) {
    .calendar-event {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 16px;
        font-size: 13px;
    }
    
    .event-name {
        font-weight: 700;
        color: #000000;
    }
    
    .event-time {
        background: rgba(0, 0, 0, 0.3);
        padding: 4px 10px;
        font-size: 12px;
        font-weight: 700;
        color: #000000;
    }
    
    .event-free-badge {
        background: #000000;
        color: #04d9ff;
        padding: 4px 10px;
        font-size: 12px;
        font-weight: 700;
        border: 1px solid #04d9ff;
        box-shadow: 0 0 8px rgba(4, 217, 255, 0.4);
    }
    
    .calendar-event.full .event-free-badge {
        background: #1a1a1a;
        color: #666;
        border-color: #2a2a2a;
    }
    
    .calendar-day .day-number::before {
        color: #04d9ff;
    }
}
</style>


<div class="container">
    <h1 class="section-title">Расписание занятий</h1>
    
    <?php if(isset($success)): ?>
        <div class="success-message"><?= $success ?></div>
    <?php endif; ?>
    
    <?php if(isset($error)): ?>
        <div class="error-message"><?= $error ?></div>
    <?php endif; ?>
    
    <!-- Легенда цветов -->
    <div class="color-legend">
        <?php foreach($colors as $name => $color): ?>
            <div class="legend-item">
                <div class="legend-color" style="background: <?= $color ?>;"></div>
                <span><?= $name ?></span>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div class="calendar">
        <div class="calendar-header">
            <a href="?month=<?= $month-1 ?>&year=<?= $year ?>" class="btn-outline" <?= $month <= date('n') && $year <= date('Y') ? 'style="visibility: hidden;"' : '' ?>>←</a>
            <h2><?= $months[$month] ?> <?= $year ?></h2>
            <a href="?month=<?= $month+1 ?>&year=<?= $year ?>" class="btn-outline">→</a>
        </div>
        
        <div class="calendar-grid">
            <?php foreach($weekdays as $day): ?>
                <div class="calendar-day-header"><?= $day ?></div>
            <?php endforeach; ?>
            
            <?php
            // Пустые ячейки до первого дня
            for($i = 1; $i < $first_weekday; $i++): ?>
                <div class="calendar-day empty"></div>
            <?php endfor; ?>
            
            <?php for($day = 1; $day <= $days_in_month; $day++): 
                $current_date = "$year-$month-".str_pad($day, 2, '0', STR_PAD_LEFT);
                $current_weekday = strtolower(date('l', strtotime($current_date)));
                $weekday_name = $weekdays_full[$current_weekday];
                
                // Проверяем, прошлая ли это дата
                $is_past = strtotime($current_date) < strtotime($today);
                
                // Собираем события для этого дня
                $day_events = [];
                foreach($schedules as $schedule) {
                    if($schedule['weekday'] == $current_weekday) {
                        // Получаем количество свободных мест
                        $stmt = $pdo->prepare("
                            SELECT COUNT(*) as booked 
                            FROM bookings 
                            WHERE schedule_id = ? AND booking_date = ? AND status = 'active'
                        ");
                        $stmt->execute([$schedule['id'], $current_date]);
                        $booked = $stmt->fetch()['booked'];
                        $free = $schedule['max_participants'] - $booked;
                        
                        $day_events[] = [
                            'schedule' => $schedule,
                            'free' => $free,
                            'color' => getDirectionColor($schedule['direction'], $colors)
                        ];
                    }
                }
            ?>
                <div class="calendar-day <?= $is_past ? 'past' : '' ?>" data-date="<?= $current_date ?>">
                    <div class="day-number" data-weekday="<?= $weekday_name ?>"><?= $day ?></div>
                    
                    <?php foreach($day_events as $event): 
                        $schedule = $event['schedule'];
                        $free = $event['free'];
                        $color = $event['color'];
                    ?>
                        <div class="calendar-event 
                                    <?= $free == 0 ? 'full' : '' ?> 
                                    <?= $is_past ? 'past-event' : '' ?>" 
                             style="background: <?= $color ?>;"
                             <?php if(!$is_past): ?>
                             onclick='showEvent(<?= json_encode([
                                'id' => $schedule['id'],
                                'direction' => $schedule['direction'],
                                'level' => $schedule['level'],
                                'description' => $schedule['description'] ?? 'Описание отсутствует',
                                'room' => $schedule['room'] ?? 'Зал не указан',
                                'trainer' => $schedule['trainer_name'],
                                'time' => date('H:i', strtotime($schedule['time'])),
                                'date' => $current_date,
                                'date_formatted' => date('d.m', strtotime($current_date)) . ' (' . $weekday_name . ')',
                                'free' => $free,
                                'max' => $schedule['max_participants'],
                                'requirements' => $schedule['requirements'] ?? 'нет специальных требований',
                                'cancel_hours' => $schedule['cancel_hours'] ?? 6
                            ], JSON_UNESCAPED_UNICODE) ?>)'
                             <?php endif; ?>>
                            
                            <?php if(isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/Mobile|Android|iPhone/i', $_SERVER['HTTP_USER_AGENT'])): ?>
                                <!-- Мобильная версия события -->
                                <span class="event-name"><?= $schedule['direction'] ?></span>
                                <span class="event-time"><?= date('H:i', strtotime($schedule['time'])) ?></span>
                                <?php if($free == 0): ?>
                                    <span class="event-free-badge">нет мест</span>
                                <?php elseif($free <= 3): ?>
                                    <span class="event-free-badge">осталось <?= $free ?></span>
                                <?php endif; ?>
                            <?php else: ?>
                                <!-- Десктопная версия события -->
                                <strong><?= $schedule['direction'] ?></strong><br>
                                <?= date('H:i', strtotime($schedule['time'])) ?>
                                <?php if($free == 0): ?> <span style="color: #ffffff; background: rgba(0,0,0,0.5); padding: 2px 5px; margin-left: 5px;">нет мест</span><?php endif; ?>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                    
                    <?php if(empty($day_events) && isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/Mobile|Android|iPhone/i', $_SERVER['HTTP_USER_AGENT'])): ?>
                        <div style="color: #666666; text-align: center; padding: 15px; font-size: 14px; border: 2px solid #333333;">
                            Нет занятий
                        </div>
                    <?php endif; ?>
                </div>
            <?php endfor; ?>
        </div>
    </div>
</div>

<!-- Модальное окно -->
<div id="eventModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <div id="modalContent"></div>
    </div>
</div>

<script>
function showEvent(event) {
    // Проверяем, авторизован ли пользователь
    let isLoggedIn = <?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>;
    
    let html = `
        <h2 class="event-title">${event.direction}</h2>
        <div class="event-info">${event.level ? event.level : ''} | ${event.trainer}</div>
        
        <div class="event-detail">
            <strong>Время и дата</strong>
            <p>${event.time} ${event.date_formatted}</p>
        </div>
        
        <div class="event-detail">
            <strong>Зал</strong>
            <p>${event.room}</p>
        </div>
        
        <div class="event-detail">
            <strong>Описание</strong>
            <p>${event.description}</p>
        </div>
        
        <div class="event-detail">
            <strong>Что нужно с собой</strong>
            <p>${event.requirements}</p>
        </div>
        
        <div class="event-detail">
            <strong>Правила отмены</strong>
            <p>Отменить запись можно за ${event.cancel_hours} и более часов до начала</p>
        </div>
        
        <div class="event-detail">
            <strong>Свободных мест</strong>
            <p class="places ${event.free == 0 ? 'full' : ''}">${event.free} из ${event.max}</p>
        </div>
    `;
    
    if(event.free > 0) {
        html += `<div class="booking-form">`;
        html += `<h3>Записаться</h3>`;
        
        if(isLoggedIn) {
            html += `
                <form method="POST">
                    <input type="hidden" name="schedule_id" value="${event.id}">
                    <input type="hidden" name="booking_date" value="${event.date}">
                    
                    <div class="form-group">
                        <label>Ваше имя</label>
                        <input type="text" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Телефон</label>
                        <input type="tel" name="phone" id="modalPhone" required>
                    </div>
                    
                    <button type="submit" name="book" class="btn">Записаться</button>
                </form>
            `;
        } else {
            html += `
                <div style="text-align: center; padding: 30px; background: #000000; border: 2px solid #333333;">
                    <p style="color: #ffffff; margin-bottom: 20px; font-weight: 700;">Чтобы записаться, войдите в личный кабинет</p>
                    <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                        <a href="login.php" class="btn">Войти</a>
                        <a href="register.php" class="btn-outline">Регистрация</a>
                    </div>
                </div>
            `;
        }
        html += `</div>`;
    } else {
        html += '<div style="text-align: center; padding: 30px;"><p style="color: #9e2a2b; font-size: 20px; font-weight: 900; text-transform: uppercase;">Мест нет</p></div>';
    }
    
    document.getElementById('modalContent').innerHTML = html;
    document.getElementById('eventModal').style.display = 'flex';
    
    if(isLoggedIn && event.free > 0) {
        setTimeout(() => {
            let phoneInput = document.getElementById('modalPhone');
            if(phoneInput) {
                phoneInput.addEventListener('input', function(e) {
                    let value = this.value.replace(/\D/g, '');
                    if(value.length > 11) value = value.slice(0, 11);
                    
                    if(value.length > 0) {
                        if(value.length <= 1) {
                            this.value = '+7';
                        } else if(value.length <= 4) {
                            this.value = '+7 (' + value.slice(1);
                        } else if(value.length <= 7) {
                            this.value = '+7 (' + value.slice(1, 4) + ') ' + value.slice(4);
                        } else if(value.length <= 9) {
                            this.value = '+7 (' + value.slice(1, 4) + ') ' + value.slice(4, 7) + '-' + value.slice(7);
                        } else {
                            this.value = '+7 (' + value.slice(1, 4) + ') ' + value.slice(4, 7) + '-' + value.slice(7, 9) + '-' + value.slice(9, 11);
                        }
                    } else {
                        this.value = '';
                    }
                });
            }
        }, 100);
    }
}

function closeModal() {
    document.getElementById('eventModal').style.display = 'none';
}

window.onclick = function(event) {
    let modal = document.getElementById('eventModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}

// Закрытие модального окна по свайпу вниз на мобильных
let touchstartY = 0;
let touchstartTime = 0;
let modal = document.getElementById('eventModal');

modal.addEventListener('touchstart', function(e) {
    let modalContent = document.querySelector('.modal-content');
    if (e.target === modal || e.target === modalContent) {
        touchstartY = e.touches[0].clientY;
        touchstartTime = Date.now();
    }
});

modal.addEventListener('touchmove', function(e) {
    if (touchstartY === 0) return;
    
    let touchY = e.touches[0].clientY;
    let diff = touchY - touchstartY;
    
    // Если свайп вниз и модальное окно открыто
    if (diff > 50) {
        e.preventDefault();
        let modalContent = document.querySelector('.modal-content');
        modalContent.style.transform = `translateY(${diff}px)`;
    }
});

modal.addEventListener('touchend', function(e) {
    if (touchstartY === 0) return;
    
    let touchY = e.changedTouches[0].clientY;
    let diff = touchY - touchstartY;
    let timeDiff = Date.now() - touchstartTime;
    let modalContent = document.querySelector('.modal-content');
    
    // Если быстрый свайп или достаточно большое смещение
    if (diff > 100 || (diff > 50 && timeDiff < 300)) {
        closeModal();
    }
    
    modalContent.style.transform = '';
    touchstartY = 0;
});
</script>

<?php include 'includes/footer.php'; ?>