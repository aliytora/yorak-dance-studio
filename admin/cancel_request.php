<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

if(!isAdmin()) {
    redirect('../login.php');
}

$user_id = $_GET['user_id'];

// Просто сбрасываем ожидание оплаты
$stmt = $pdo->prepare("UPDATE users SET awaiting_payment = 0, selected_plan_id = NULL WHERE id = ?");
$stmt->execute([$user_id]);

$_SESSION['success'] = "Заявка отменена";

redirect('users.php');
?>