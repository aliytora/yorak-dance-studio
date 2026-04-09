<?php
$host = 'mysql.railway.internal';
$port = 3306;
$user = 'root';
$password = 'HJiAPYVJQPerzyJdvrHSOYYLkBBiBuRk';
$database = 'railway';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Установка базы данных</title>
    <meta charset="UTF-8">
    <style>
        body { background: #0a0a0a; color: #fff; font-family: monospace; padding: 20px; }
        textarea { width: 100%; height: 500px; background: #1a1a1a; color: #0f0; border: 1px solid #00d4ff; padding: 10px; font-size: 12px; }
        button { background: #00d4ff; color: #000; padding: 12px 30px; border: none; cursor: pointer; font-weight: bold; margin-top: 10px; }
        pre { background: #1a1a1a; padding: 15px; overflow: auto; border-left: 3px solid #00d4ff; margin-top: 20px; max-height: 400px; overflow-y: auto; }
        .success { color: #0f0; }
        .error { color: #f00; }
        h1 { color: #00d4ff; }
    </style>
</head>
<body>
    <h1>🚀 Установка базы данных Yorak Dance Studio</h1>
    <p>Вставь SQL код ниже и нажми "Выполнить"</p>
    
    <form method="POST">
        <textarea name="sql" placeholder="Вставь сюда SQL код..."><?= htmlspecialchars($_POST['sql'] ?? '') ?></textarea><br>
        <button type="submit">▶ Выполнить SQL</button>
    </form>
    
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['sql'])) {
        try {
            $pdo = new PDO("mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4", $user, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
            
            // Выполняем каждый запрос отдельно
            $sql = $_POST['sql'];
            $sql = preg_replace('/\/\*.*?\*\//s', '', $sql); // Убираем комментарии
            $sql = preg_replace('/^--.*$/m', '', $sql); // Убираем строки с --
            $queries = explode(";\n", $sql);
            
            $success = 0;
            $errors = [];
            
            foreach ($queries as $query) {
                $query = trim($query);
                if (empty($query)) continue;
                
                try {
                    $pdo->exec($query);
                    $success++;
                    echo "<pre class='success'>✅ Выполнено: " . substr($query, 0, 80) . "...</pre>";
                } catch(PDOException $e) {
                    $errors[] = $e->getMessage();
                    echo "<pre class='error'>❌ Ошибка: " . $e->getMessage() . "</pre>";
                }
            }
            
            $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
            
            echo "<hr><pre class='success'>✅ Всего выполнено: $success запросов</pre>";
            
            if (!empty($errors)) {
                echo "<pre class='error'>⚠️ Ошибок: " . count($errors) . "</pre>";
            }
            
        } catch(PDOException $e) {
            echo "<pre class='error'>❌ Ошибка подключения: " . $e->getMessage() . "</pre>";
        }
    }
    ?>
</body>
</html>