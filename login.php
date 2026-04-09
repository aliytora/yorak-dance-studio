<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

if(isLoggedIn()) {
    redirect('index.php');
}

$error = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if(empty($email) || empty($password)) {
        $error = 'Заполните все поля';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            
            // Перенаправляем в зависимости от роли
           if($user['role'] === 'admin') {
    redirect('admin/index.php');
            } else {
                redirect('cabinet.php');
            }
        } else {
            $error = 'Неверный email или пароль';
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="form-container">
    <h1 style="text-align: center; margin-bottom: 30px;">Вход</h1>
    
    <?php if($error): ?>
        <div class="alert alert-error"><?= $error ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label>Пароль</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit" class="btn" style="width: 100%;">Войти</button>
    </form>
    
    <p style="text-align: center; margin-top: 20px;">
        Нет аккаунта? <a href="register.php" style="color: #04d9ff;">Зарегистрироваться</a>
    </p>
</div>

<?php include 'includes/footer.php'; ?>