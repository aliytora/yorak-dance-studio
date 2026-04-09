<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

if(!isAdmin()) {
    redirect('../login.php');
}
?>
<?php include '../includes/header.php'; ?>

<div class="container">
    <h1 class="section-title">Панель администратора</h1>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 30px;">
        <div class="admin-card" style="background: white; padding: 30px; border-radius: 15px; text-align: center;">
            <h3>Пользователи</h3>
            <p>Управление пользователями и ролями</p>
            <a href="users.php" class="btn">Перейти</a>
        </div>
        
        <div class="admin-card" style="background: white; padding: 30px; border-radius: 15px; text-align: center;">
            <h3>Расписание</h3>
            <p>Управление занятиями</p>
            <a href="schedule.php" class="btn">Перейти</a>
        </div>
        
        <div class="admin-card" style="background: white; padding: 30px; border-radius: 15px; text-align: center;">
            <h3>Записи</h3>
            <p>Просмотр всех записей</p>
            <a href="bookings.php" class="btn">Перейти</a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>