<?php
require_once 'includes/auth.php';

// Очищаем все данные сессии
$_SESSION = array();

// Уничтожаем сессию
session_destroy();

// Перенаправляем
redirect('index.php');
?>