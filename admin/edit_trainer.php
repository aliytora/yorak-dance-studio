<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

if(!isAdmin()) {
    redirect('../login.php');
}

$id = $_GET['id'] ?? 0;
echo "<!-- DEBUG: ID из GET = $id -->";

$trainer = null;

if($id) {
    $stmt = $pdo->prepare("SELECT * FROM trainers WHERE id = ?");
    $stmt->execute([$id]);
    $trainer = $stmt->fetch();
}

// Обновление тренера
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<!-- DEBUG: FORM POSTED -->";
    
    $name = $_POST['name'];
    $bio = $_POST['bio'];
    $instagram = $_POST['instagram'];
    
    echo "<!-- DEBUG: name = $name -->";
    echo "<!-- DEBUG: bio = $bio -->";
    echo "<!-- DEBUG: instagram = $instagram -->";
    echo "<!-- DEBUG: id = $id -->";
    
    if($id) {
        // Обновляем существующего
        $sql = "UPDATE trainers SET name = ?, bio = ?, instagram = ?";
        $params = [$name, $bio, $instagram];
        
        // Если загрузили фото
        if(isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
            echo "<!-- DEBUG: Фото загружено -->";
            $upload_dir = '../image/trainers/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $filename = 'trainer_' . time() . '.' . $ext;
            $upload_file = $upload_dir . $filename;
            
            if(move_uploaded_file($_FILES['photo']['tmp_name'], $upload_file)) {
                echo "<!-- DEBUG: Фото сохранено как $filename -->";
                $sql .= ", photo = ?";
                $params[] = $filename;
            }
        }
        
        $sql .= " WHERE id = ?";
        $params[] = $id;
        
        echo "<!-- DEBUG: SQL = $sql -->";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute($params);
        
        echo "<!-- DEBUG: Результат = " . ($result ? 'ok' : 'fail') . " -->";
        
    } else {
        // Создаем нового
        $sql = "INSERT INTO trainers (name, bio, instagram) VALUES (?, ?, ?)";
        $params = [$name, $bio, $instagram];
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $id = $pdo->lastInsertId();
        
        echo "<!-- DEBUG: Новый ID = $id -->";
        
        // Если загрузили фото для нового
        if(isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
            $upload_dir = '../image/trainers/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $filename = 'trainer_' . time() . '.' . $ext;
            $upload_file = $upload_dir . $filename;
            
            if(move_uploaded_file($_FILES['photo']['tmp_name'], $upload_file)) {
                $stmt = $pdo->prepare("UPDATE trainers SET photo = ? WHERE id = ?");
                $stmt->execute([$filename, $id]);
            }
        }
    }
    
    // ВРЕМЕННО вместо редиректа показываем результат
    echo "<div style='background: #e3f7e3; padding: 20px; margin: 20px; border-radius: 10px;'>";
    echo "<h3>✅ Тренер сохранен!</h3>";
    echo "<p>ID: $id</p>";
    echo "<p>Имя: $name</p>";
    echo "<p><a href='edit_trainer.php?id=$id' class='btn'>Вернуться к тренеру</a></p>";
    echo "<p><a href='index.php' class='btn btn-outline'>В админку</a></p>";
    echo "</div>";
    exit;
}

// Получаем всех тренеров для списка
$all_trainers = $pdo->query("SELECT * FROM trainers ORDER BY name")->fetchAll();
?>

<?php include '../includes/header.php'; ?>
<link rel="stylesheet" href="/dance_studio2/css/style.css">
<link rel="stylesheet" href="admin-style.css">
<style>
.trainers-admin {
    max-width: 1200px;
    margin: 0 auto;
}

.trainer-form {
    background: white;
    padding: 30px;
    border-radius: 15px;
    margin-bottom: 40px;
}

.trainers-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 20px;
    margin-top: 30px;
}

.trainer-item {
    background: white;
    padding: 15px;
    border-radius: 10px;
    text-align: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.trainer-item img {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 10px;
}

.btn-edit {
    background: #04d9ff;
    color: white;
    padding: 5px 10px;
    border-radius: 5px;
    text-decoration: none;
    font-size: 12px;
    display: inline-block;
    margin-top: 10px;
}

.btn-edit:hover {
    background: #04d9ff;
}
</style>

<div class="container">
    <h1 class="section-title">Управление тренерами</h1>
    
    <div class="trainers-admin">
        <div class="trainer-form">
            <h2 style="color: #6b5b6b; margin-bottom: 20px;">
                <?= $id ? 'Редактировать' : 'Добавить' ?> тренера
            </h2>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Имя тренера</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($trainer['name'] ?? '') ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Описание / Биография</label>
                    <textarea name="bio" rows="5"><?= htmlspecialchars($trainer['bio'] ?? '') ?></textarea>
                </div>
                
                <div class="form-group">
                    <label>Instagram (без @)</label>
                    <input type="text" name="instagram" value="<?= htmlspecialchars($trainer['instagram'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label>Фото тренера</label>
                    <?php if($id && !empty($trainer['photo'])): ?>
                        <div style="margin-bottom: 10px;">
                            <img src="../image/trainers/<?= $trainer['photo'] ?>" style="max-width: 200px; border-radius: 10px;">
                        </div>
                    <?php endif; ?>
                    <input type="file" name="photo" accept="image/*">
                </div>
                
                <button type="submit" class="btn">Сохранить</button>
                <a href="index.php" class="btn btn-outline">Отмена</a>
            </form>
        </div>
        
        <h2 style="color: #6b5b6b; margin: 30px 0;">Список тренеров</h2>
        
        <div class="trainers-list">
            <?php foreach($all_trainers as $t): ?>
                <div class="trainer-item">
                    <?php if(!empty($t['photo']) && file_exists('../image/trainers/' . $t['photo'])): ?>
                        <img src="../image/trainers/<?= $t['photo'] ?>" alt="<?= htmlspecialchars($t['name']) ?>">
                    <?php else: ?>
                        <div style="width: 100px; height: 100px; border-radius: 50%; background: #f4e1e6; margin: 0 auto; display: flex; align-items: center; justify-content: center; font-size: 40px;">👤</div>
                    <?php endif; ?>
                    
                    <h4><?= htmlspecialchars($t['name']) ?></h4>
                    <p style="font-size: 12px; color: #7e6b7e;">@<?= htmlspecialchars($t['instagram'] ?? 'нет') ?></p>
                    
                    <a href="?id=<?= $t['id'] ?>" class="btn-edit">✎ Редактировать</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>