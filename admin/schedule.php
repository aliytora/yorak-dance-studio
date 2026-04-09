<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

if(!isAdmin()) {
    redirect('../login.php');
}

// Добавление занятия
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $direction = $_POST['direction'];
    $level = $_POST['level'];
    $trainer_id = $_POST['trainer_id'];
    $weekday = $_POST['weekday'];
    $time = $_POST['time'];
    $duration = $_POST['duration'];
    $max_participants = $_POST['max_participants'];
    
    $stmt = $pdo->prepare("INSERT INTO schedules (direction, level, trainer_id, weekday, time, duration, max_participants) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$direction, $level, $trainer_id, $weekday, $time, $duration, $max_participants]);
}

// Удаление занятия
if(isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM schedules WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
}

// Получаем список тренеров
$trainers = $pdo->query("SELECT * FROM trainers")->fetchAll();

// Получаем расписание
$schedules = $pdo->query("
    SELECT s.*, t.name as trainer_name 
    FROM schedules s 
    LEFT JOIN trainers t ON s.trainer_id = t.id 
    ORDER BY FIELD(weekday, 'monday','tuesday','wednesday','thursday','friday','saturday','sunday'), time
")->fetchAll();

$days = [
    'monday' => 'Понедельник',
    'tuesday' => 'Вторник',
    'wednesday' => 'Среда',
    'thursday' => 'Четверг',
    'friday' => 'Пятница',
    'saturday' => 'Суббота',
    'sunday' => 'Воскресенье'
];
?>

<?php include '../includes/header.php'; ?>
<link rel="stylesheet" href="/dance_studio2/css/style.css">
<link rel="stylesheet" href="admin-style.css">
<div class="container">
    <h1 class="section-title">Управление расписанием</h1>
    
    <div style="background: white; padding: 30px; border-radius: 15px; margin-bottom: 40px;">
        <h2 style="color: #6b5b6b; margin-bottom: 20px;">Добавить занятие</h2>
        <form method="POST" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <div class="form-group">
                <label>Направление</label>
                <input type="text" name="direction" required>
            </div>
            <div class="form-group">
                <label>Уровень</label>
                <select name="level">
                    <option>Начинающие</option>
                    <option>Средний</option>
                    <option>Продвинутые</option>
                    <option>Любой</option>
                    <option>14+</option>
                </select>
            </div>
            <div class="form-group">
                <label>Тренер</label>
                <select name="trainer_id">
                    <?php foreach($trainers as $t): ?>
                        <option value="<?= $t['id'] ?>"><?= $t['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>День недели</label>
                <select name="weekday">
                    <?php foreach($days as $key => $value): ?>
                        <option value="<?= $key ?>"><?= $value ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Время</label>
                <input type="time" name="time" required>
            </div>
            <div class="form-group">
                <label>Длительность (мин)</label>
                <input type="number" name="duration" value="60">
            </div>
            <div class="form-group">
                <label>Макс. участников</label>
                <input type="number" name="max_participants" value="15">
            </div>
            <div style="grid-column: 1/-1;">
                <button type="submit" name="add" class="btn">Добавить</button>
            </div>
        </form>
    </div>
    
    <h2 style="color: #6b5b6b; margin: 30px 0 20px;">Текущее расписание</h2>
    <table class="schedule-table">
        <thead>
            <tr>
                <th>День</th>
                <th>Время</th>
                <th>Направление</th>
                <th>Уровень</th>
                <th>Тренер</th>
                <th>Длит.</th>
                <th>Макс.</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($schedules as $s): ?>
                <tr>
                    <td><?= $days[$s['weekday']] ?></td>
                    <td><?= date('H:i', strtotime($s['time'])) ?></td>
                    <td><?= htmlspecialchars($s['direction']) ?></td>
                    <td><?= htmlspecialchars($s['level']) ?></td>
                    <td><?= htmlspecialchars($s['trainer_name']) ?></td>
                    <td><?= $s['duration'] ?></td>
                    <td><?= $s['max_participants'] ?></td>
                    <td>
                        <a href="?delete=<?= $s['id'] ?>" class="btn" style="background: #04d9ff;" onclick="return confirm('Удалить?')">Удалить</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <p style="margin-top: 20px;">
        <a href="index.php" class="btn btn-outline">Назад</a>
    </p>
</div>

<?php include '../includes/footer.php'; ?>