<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Определяем базовый путь для ссылок
$base_path = '';

// Проверяем, в админке ли мы
$is_admin_page = strpos($_SERVER['PHP_SELF'], '/admin/') !== false;

// Если мы в админке, то базовый путь должен вести на уровень выше
if ($is_admin_page) {
    $base_path = '../';
}

// Определяем путь к CSS
$css_path = $is_admin_page ? '../css/style.css' : './css/style.css';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yorak Dance Studio - Женская танцевальная студия</title>
    <link rel="stylesheet" href="<?= $css_path ?>">
    <?php if($is_admin_page): ?>
        <link rel="stylesheet" href="admin-style.css">
    <?php endif; ?>
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <a href="<?= $base_path ?>index.php">Yorak Dance Studio</a>
            </div>
            <nav>
                <ul>
                    <?php if($is_admin_page): ?>
                        <!-- Админ-панель: только выход -->
                        <li><a href="<?= $base_path ?>logout.php" style="color: #00d4ff;"> Выйти</a></li>
                    <?php else: ?>
                        <!-- Обычный сайт: полное меню -->
                        <li><a href="index.php">Главная</a></li>
                        <li><a href="schedule.php">Расписание</a></li>
                        <li><a href="trainers.php">Тренеры</a></li>
                        <li><a href="contacts.php">Контакты</a></li>
                        
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <li><a href="cabinet.php">Личный кабинет</a></li>
                            <li><a href="logout.php">Выйти</a></li>
                        <?php else: ?>
                            <li><a href="login.php">Войти</a></li>
                            <li><a href="register.php">Регистрация</a></li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    <main>