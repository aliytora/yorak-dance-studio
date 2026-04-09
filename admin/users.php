<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

if(!isAdmin()) {
    redirect('../login.php');
}

// Повысить до админа
if(isset($_GET['make_admin'])) {
    $stmt = $pdo->prepare("UPDATE users SET role = 'admin' WHERE id = ?");
    $stmt->execute([$_GET['make_admin']]);
}

// Понизить до пользователя
if(isset($_GET['remove_admin'])) {
    $stmt = $pdo->prepare("UPDATE users SET role = 'user' WHERE id = ?");
    $stmt->execute([$_GET['remove_admin']]);
}

// Добавить абонемент
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_membership'])) {
    $user_id = $_POST['user_id'];
    $lessons = $_POST['lessons'];
    $months = $_POST['months'];
    $valid_until = date('Y-m-d', strtotime("+$months months"));
    
    // Проверяем, есть ли уже абонемент
    $stmt = $pdo->prepare("SELECT id FROM memberships WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $exists = $stmt->fetch();
    
    if($exists) {
        $stmt = $pdo->prepare("UPDATE memberships SET lessons_left = lessons_left + ?, valid_until = ? WHERE user_id = ?");
        $stmt->execute([$lessons, $valid_until, $user_id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO memberships (user_id, lessons_left, valid_until) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $lessons, $valid_until]);
    }
}

// Получаем всех пользователей
$users = $pdo->query("
    SELECT u.*, m.lessons_left, m.valid_until 
    FROM users u
    LEFT JOIN memberships m ON u.id = m.user_id
    ORDER BY u.created_at DESC
")->fetchAll();

// Получаем пользователей с ожиданием оплаты
$awaiting = $pdo->query("
    SELECT u.*, mp.name as plan_name, mp.lessons, mp.months, mp.price
    FROM users u
    LEFT JOIN membership_plans mp ON u.selected_plan_id = mp.id
    WHERE u.awaiting_payment = 1
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление пользователями - Yorak Dance Studio</title>
    <link rel="stylesheet" href="/dance_studio2/css/style.css">
    <link rel="stylesheet" href="admin-style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="admin-dashboard">
        <h1 class="section-title">Пользователи</h1>
        
        <!-- Блок ожидания оплаты -->
        <?php if(!empty($awaiting)): ?>
        <div class="awaiting-payments">
            <div class="awaiting-header">
                <h2>⏳ Ожидают оплаты в студии</h2>
                <span class="awaiting-badge"><?= count($awaiting) ?> заявок</span>
            </div>
            
            <table class="awaiting-table">
                <thead>
                    <tr>
                        <th>Клиент</th>
                        <th>Телефон</th>
                        <th>Абонемент</th>
                        <th>Занятий</th>
                        <th>Сумма</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($awaiting as $a): ?>
                    <tr>
                        <td data-label="Клиент"><?= htmlspecialchars($a['name']) ?></td>
                        <td data-label="Телефон"><?= htmlspecialchars($a['phone']) ?></td>
                        <td data-label="Абонемент"><?= htmlspecialchars($a['plan_name']) ?></td>
                        <td data-label="Занятий">
                            <?php if($a['lessons'] == 999): ?>
                                <span class="infinity-badge">∞</span>
                            <?php else: ?>
                                <?= $a['lessons'] ?>
                            <?php endif; ?>
                        </td>
                        <td data-label="Сумма">
                            <span class="price-highlight"><?= number_format($a['price'], 0, '', ' ') ?> ₽</span>
                        </td>
                        <td data-label="Действия">
                            <div class="btn-actions">
                                <a href="activate_membership.php?user_id=<?= $a['id'] ?>" 
                                   class="btn-approve" 
                                   onclick="return confirm('Подтвердить оплату?')">
                                    ✅ Оплатил
                                </a>
                                <a href="cancel_request.php?user_id=<?= $a['id'] ?>" 
                                   class="btn-cancel" 
                                   onclick="return confirm('Отменить заявку?')">
                                    ✗ Отмена
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
        
        <!-- Таблица пользователей -->
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Имя</th>
                    <th>Email</th>
                    <th>Телефон</th>
                    <th>Роль</th>
                    <th>Абонемент</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($users as $u): ?>
                <tr>
                    <td data-label="ID"><?= $u['id'] ?></td>
                    <td data-label="Имя"><?= htmlspecialchars($u['name']) ?></td>
                    <td data-label="Email"><?= htmlspecialchars($u['email']) ?></td>
                    <td data-label="Телефон"><?= htmlspecialchars($u['phone'] ?: '-') ?></td>
                    <td data-label="Роль">
                        <?php if($u['role'] == 'admin'): ?>
                            <span class="role-badge role-admin">Админ</span>
                        <?php else: ?>
                            <span class="role-badge role-user">Пользователь</span>
                        <?php endif; ?>
                    </td>
                    <td data-label="Абонемент" class="membership-info">
                        <?php if($u['lessons_left']): ?>
                            <?= $u['lessons_left'] == 999 ? '∞ безлимит' : $u['lessons_left'] . ' занятий' ?>
                            <br><small>до <?= date('d.m.Y', strtotime($u['valid_until'])) ?></small>
                        <?php else: ?>
                            <span style="color: #666666;">нет</span>
                        <?php endif; ?>
                    </td>
                    <td data-label="Действия">
                        <?php if($u['role'] == 'admin'): ?>
                            <a href="?remove_admin=<?= $u['id'] ?>" class="btn-small">📌 Снять админа</a>
                        <?php else: ?>
                            <a href="?make_admin=<?= $u['id'] ?>" class="btn-small">👑 Сделать админом</a>
                        <?php endif; ?>
                        <button onclick="showMembershipForm(<?= $u['id'] ?>, '<?= addslashes($u['name']) ?>')" class="btn-small">➕ Абонемент</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <a href="index.php" class="btn-back">← Назад в админ-панель</a>
    </div>

    <!-- Модальное окно для добавления абонемента -->
    <div id="membershipModal" class="modal">
        <div class="modal-content">
            <h3>Добавить абонемент для <span id="userName"></span></h3>
            <form method="POST" id="membershipForm">
                <input type="hidden" name="user_id" id="userId">
                <div class="admin-form-group">
                    <label>Количество занятий</label>
                    <input type="number" name="lessons" min="1" value="8" required>
                </div>
                <div class="admin-form-group">
                    <label>Срок действия (месяцев)</label>
                    <input type="number" name="months" min="1" value="1" required>
                </div>
                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit" name="add_membership" class="btn">Добавить</button>
                    <button type="button" onclick="hideModal()" class="btn-secondary">Отмена</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function showMembershipForm(userId, userName) {
        document.getElementById('userId').value = userId;
        document.getElementById('userName').textContent = userName;
        document.getElementById('membershipModal').style.display = 'flex';
    }

    function hideModal() {
        document.getElementById('membershipModal').style.display = 'none';
    }

    // Закрытие модального окна при клике вне его
    window.onclick = function(event) {
        let modal = document.getElementById('membershipModal');
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }
    </script>

    <?php include '../includes/footer.php'; ?>
</body>
</html>