<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

if(!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];

// Получаем активный абонемент пользователя
$stmt = $pdo->prepare("
    SELECT * FROM memberships 
    WHERE user_id = ? AND lessons_left > 0 AND (valid_until IS NULL OR valid_until >= CURDATE())
");
$stmt->execute([$user_id]);
$active_membership = $stmt->fetch();

// Получаем доступные тарифы
$plans = $pdo->query("SELECT * FROM membership_plans WHERE is_active = 1 ORDER BY price")->fetchAll();

// Обработка покупки
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buy_plan'])) {
    $plan_id = $_POST['plan_id'];
    
    // Сохраняем выбранный план в ожидание оплаты
    $stmt = $pdo->prepare("UPDATE users SET awaiting_payment = 1, selected_plan_id = ? WHERE id = ?");
    $stmt->execute([$plan_id, $user_id]);
    
    $success = "Заявка на абонемент отправлена! Оплатите в студии и администратор активирует абонемент.";
}
?>

<?php include 'includes/header.php'; ?>

<style>
/* Стили остаются те же */
.plans-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin: 40px 0;
}

.plan-card {
    background: white;
    border-radius: 15px;
    padding: 30px 20px;
    text-align: center;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    transition: transform 0.3s;
    position: relative;
    border: 2px solid transparent;
}

.plan-card:hover {
    transform: translateY(-5px);
    border-color: #04d9ff;
}

.plan-card.popular {
    border-color: #04d9ff;
    transform: scale(1.02);
}

.popular-badge {
    position: absolute;
    top: -10px;
    right: 20px;
    background: #04d9ff;
    color: white;
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.plan-name {
    font-size: 24px;
    color: #6b5b6b;
    margin-bottom: 10px;
}

.plan-price {
    font-size: 36px;
    color: #04d9ff;
    font-weight: 600;
    margin: 20px 0;
}

.plan-price small {
    font-size: 14px;
    color: #b5a5b5;
}

.plan-lessons {
    font-size: 18px;
    color: #7e6b7e;
    margin: 15px 0;
}

.plan-description {
    color: #7e6b7e;
    font-size: 14px;
    margin: 15px 0;
    min-height: 60px;
}

.btn-buy {
    background: #04d9ff;
    color: white;
    border: none;
    padding: 12px 30px;
    border-radius: 30px;
    cursor: pointer;
    font-size: 16px;
    width: 100%;
    transition: all 0.3s;
}

.btn-buy:hover {
    background: #04d9ff;
    transform: scale(1.05);
}

.btn-buy:disabled {
    background: #ccc;
    cursor: not-allowed;
    transform: none;
}

.success-message {
    background: #e3f7e3;
    color: #2e7d32;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    text-align: center;
}

.info-message {
    background: #fff3cd;
    color: #856404;
    padding: 15px;
    border-radius: 8px;
    margin: 20px 0;
    text-align: center;
}

.active-membership {
    background: #e3f7e3;
    border: 2px solid #2e7d32;
    padding: 20px;
    border-radius: 15px;
    margin-bottom: 30px;
    text-align: center;
}

.active-membership h3 {
    color: #2e7d32;
    margin-bottom: 10px;
}

.active-membership .lessons-count {
    font-size: 36px;
    font-weight: 600;
    color: #2e7d32;
}

.active-membership .valid-until {
    color: #666;
    margin-top: 10px;
}
</style>

<div class="container">
    <h1 class="section-title">Выберите абонемент</h1>
    
    <?php if(isset($success)): ?>
        <div class="success-message"><?= $success ?></div>
        <div class="info-message">
            <h3>💳 Как оплатить:</h3>
            <p>1. Приходите в студию по адресу: г. Ижевск, ул. Карла Маркса 191</p>
            <p>2. Скажите администратору, что хотите оплатить абонемент</p>
            <p>3. После оплаты администратор активирует абонемент в вашем кабинете</p>
            <p><strong>Статус:</strong> ⏳ Ожидаем подтверждения оплаты</p>
        </div>
    <?php endif; ?>
    
    <?php if($active_membership): ?>
        <div class="active-membership">
            <h3>✅ У вас есть активный абонемент</h3>
            <div class="lessons-count"><?= $active_membership['lessons_left'] ?> занятий осталось</div>
            <div class="valid-until">Действует до: <?= date('d.m.Y', strtotime($active_membership['valid_until'])) ?></div>
            <p style="margin-top: 15px;">Вы можете купить новый абонемент, он добавится к текущему</p>
        </div>
    <?php endif; ?>
    
    <div class="plans-grid">
        <?php foreach($plans as $index => $plan): ?>
            <div class="plan-card <?= $index == 2 ? 'popular' : '' ?>">
                <?php if($index == 2): ?>
                    <div class="popular-badge">Популярный</div>
                <?php endif; ?>
                
                <div class="plan-name"><?= htmlspecialchars($plan['name'])?></div>
                <div class="plan-price">
                    <?= number_format($plan['price'], 0, '', ' ') ?> ₽
                    <small>/ <?= $plan['months'] ?> <?= $plan['months'] == 1 ? 'месяц' : 'месяца' ?></small>
                </div>
                <div class="plan-lessons"><?= $plan['lessons'] == 999 ? '∞' : $plan['lessons'] ?> занятий</div>
                <div class="plan-description"><?= htmlspecialchars($plan['description'])?></div>
                
                <form method="POST">
                    <input type="hidden" name="plan_id" value="<?= $plan['id'] ?>">
                    <button type="submit" name="buy_plan" class="btn-buy">Выбрать</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
    
    <p style="text-align: center; margin: 30px 0;">
        <a href="cabinet.php" class="btn btn-outline">← Вернуться в кабинет</a>
    </p>
</div>

<?php include 'includes/footer.php'; ?>