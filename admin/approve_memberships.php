<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

if(!isLoggedIn() || $_SESSION['role'] !== 'admin') {
    redirect('../login.php');
}

// Получаем все ожидающие абонементы
$stmt = $pdo->query("
    SELECT um.*, u.name as user_name, u.email, u.phone, mp.name as plan_name, mp.price
    FROM user_memberships um
    JOIN users u ON um.user_id = u.id
    JOIN membership_plans mp ON um.plan_id = mp.id
    WHERE um.status = 'pending'
    ORDER BY um.created_at DESC
");
$pending = $stmt->fetchAll();

// Получаем все активные абонементы
$stmt = $pdo->query("
    SELECT um.*, u.name as user_name, mp.name as plan_name
    FROM user_memberships um
    JOIN users u ON um.user_id = u.id
    JOIN membership_plans mp ON um.plan_id = mp.id
    WHERE um.status = 'active'
    ORDER BY um.activated_at DESC
    LIMIT 20
");
$active = $stmt->fetchAll();

// Обработка активации
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['activate'])) {
    $membership_id = (int)$_POST['membership_id'];
    
    // Активируем абонемент
    $stmt = $pdo->prepare("
        UPDATE user_memberships 
        SET status = 'active', activated_at = NOW() 
        WHERE id = ? AND status = 'pending'
    ");
    $stmt->execute([$membership_id]);
    
    $_SESSION['message'] = 'Абонемент активирован';
    redirect('approve_memberships.php');
}

// Обработка отклонения
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reject'])) {
    $membership_id = (int)$_POST['membership_id'];
    
    $stmt = $pdo->prepare("DELETE FROM user_memberships WHERE id = ? AND status = 'pending'");
    $stmt->execute([$membership_id]);
    
    $_SESSION['message'] = 'Заявка отклонена';
    redirect('approve_memberships.php');
}

include 'includes/admin_header.php';
?>

<div class="container">
    <h1>Управление абонементами</h1>
    
    <?php if(isset($_SESSION['message'])): ?>
        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            <?= $_SESSION['message']; unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>
    
    <h2>Ожидают активации</h2>
    
    <?php if(empty($pending)): ?>
        <p>Нет заявок на активацию</p>
    <?php else: ?>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f4e1e6;">
                    <th style="padding: 10px; text-align: left;">Пользователь</th>
                    <th style="padding: 10px; text-align: left;">Абонемент</th>
                    <th style="padding: 10px; text-align: left;">Цена</th>
                    <th style="padding: 10px; text-align: left;">Дата заявки</th>
                    <th style="padding: 10px; text-align: left;">Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($pending as $p): ?>
                    <tr style="border-bottom: 1px solid #f0e0e5;">
                        <td style="padding: 10px;">
                            <?= htmlspecialchars($p['user_name']) ?><br>
                            <small><?= htmlspecialchars($p['phone']) ?></small>
                        </td>
                        <td style="padding: 10px;"><?= htmlspecialchars($p['plan_name']) ?></td>
                        <td style="padding: 10px;"><?= number_format($p['price'], 0, '', ' ') ?> ₽</td>
                        <td style="padding: 10px;"><?= date('d.m.Y H:i', strtotime($p['created_at'])) ?></td>
                        <td style="padding: 10px;">
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="membership_id" value="<?= $p['id'] ?>">
                                <button type="submit" name="activate" class="btn" style="background: #2e7d32; padding: 5px 15px;">Активировать</button>
                                <button type="submit" name="reject" class="btn" style="background: #c62828; padding: 5px 15px;" onclick="return confirm('Отклонить заявку?')">Отклонить</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    
    <h2 style="margin-top: 40px;">Активные абонементы</h2>
    
    <?php if(empty($active)): ?>
        <p>Нет активных абонементов</p>
    <?php else: ?>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f4e1e6;">
                    <th style="padding: 10px; text-align: left;">Пользователь</th>
                    <th style="padding: 10px; text-align: left;">Абонемент</th>
                    <th style="padding: 10px; text-align: left;">Осталось занятий</th>
                    <th style="padding: 10px; text-align: left;">Активирован</th>
                    <th style="padding: 10px; text-align: left;">Действует до</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($active as $a): ?>
                    <tr style="border-bottom: 1px solid #f0e0e5;">
                        <td style="padding: 10px;"><?= htmlspecialchars($a['user_name']) ?></td>
                        <td style="padding: 10px;"><?= htmlspecialchars($a['plan_name']) ?></td>
                        <td style="padding: 10px;"><?= $a['remaining_visits'] ?></td>
                        <td style="padding: 10px;"><?= date('d.m.Y', strtotime($a['activated_at'])) ?></td>
                        <td style="padding: 10px;"><?= $a['expiry_date'] ? date('d.m.Y', strtotime($a['expiry_date'])) : 'бессрочно' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include 'includes/admin_footer.php'; ?>