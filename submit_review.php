<?php
require_once 'includes/db.php';
session_start();

// Проверяем, есть ли хоть одно подключение
if (!$conn && !$pdo) {
    $_SESSION['review_message'] = 'Ошибка подключения к базе данных';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Валидация
    if (empty($_POST['name']) || empty($_POST['review'])) {
        $_SESSION['review_message'] = 'Заполните все поля';
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }
    
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $review = mysqli_real_escape_string($conn, $_POST['review']);
    $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 5;
    
    // Загрузка фото
    $photo_path = NULL;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        
        $upload_dir = 'uploads/reviews/';
        
        // Создаем папку, если её нет
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $filename = time() . '_' . uniqid() . '.' . $extension;
        $target_path = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_path)) {
            $photo_path = $target_path;
        }
    }
    
    // Пробуем вставить через MySQLi
    if ($conn) {
        if ($photo_path) {
            $query = "INSERT INTO reviews (name, review_text, rating, photo, status) 
                      VALUES ('$name', '$review', $rating, '$photo_path', 'pending')";
        } else {
            $query = "INSERT INTO reviews (name, review_text, rating, status) 
                      VALUES ('$name', '$review', $rating, 'pending')";
        }
        
        if (mysqli_query($conn, $query)) {
            $_SESSION['review_message'] = 'Спасибо за отзыв! Он будет опубликован после проверки администратором.';
        } else {
            error_log("MySQL Error: " . mysqli_error($conn));
            $_SESSION['review_message'] = 'Ошибка при сохранении отзыва. Попробуйте позже.';
        }
    } 
    // Если MySQLi не работает, пробуем через PDO
    elseif ($pdo) {
        try {
            if ($photo_path) {
                $stmt = $pdo->prepare("INSERT INTO reviews (name, review_text, rating, photo, status) VALUES (?, ?, ?, ?, 'pending')");
                $stmt->execute([$name, $review, $rating, $photo_path]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO reviews (name, review_text, rating, status) VALUES (?, ?, ?, 'pending')");
                $stmt->execute([$name, $review, $rating]);
            }
            $_SESSION['review_message'] = 'Спасибо за отзыв! Он будет опубликован после проверки администратором.';
        } catch(PDOException $e) {
            error_log("PDO Error: " . $e->getMessage());
            $_SESSION['review_message'] = 'Ошибка при сохранении отзыва. Попробуйте позже.';
        }
    }
    
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}
?>