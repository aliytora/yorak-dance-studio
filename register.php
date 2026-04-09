<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

if(isLoggedIn()) {
    redirect('index.php');
}

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    
    if($password !== $confirm) {
        $error = 'Пароли не совпадают';
    } else {
        // Проверка, существует ли email
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if($stmt->fetch()) {
            $error = 'Email уже зарегистрирован';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)");
            if($stmt->execute([$name, $email, $phone, $hash])) {
                $success = 'Регистрация успешна! Теперь можно войти.';
            } else {
                $error = 'Ошибка регистрации';
            }
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="form-container">
    <h1 style="text-align: center; margin-bottom: 30px;">Регистрация</h1>
    
    <?php if($error): ?>
        <div class="alert alert-error"><?= $error ?></div>
    <?php endif; ?>
    
    <?php if($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <div class="form-group">
            <label>Имя</label>
            <input type="text" name="name" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required>
        </div>
        <div class="form-group">
            <label>Телефон</label>
            <input type="tel" name="phone">
        </div>
        <div class="form-group">
            <label>Пароль</label>
            <input type="password" name="password" required>
        </div>
        <div class="form-group">
            <label>Подтвердите пароль</label>
            <input type="password" name="confirm_password" required>
        </div>
        <button type="submit" class="btn" style="width: 100%;">Зарегистрироваться</button>
    </form>
    
    <p style="text-align: center; margin-top: 20px;">
        Уже есть аккаунт? <a href="login.php" style="color: #04d9ff;">Войти</a>
    </p>
</div>

<?php include 'includes/footer.php'; ?>