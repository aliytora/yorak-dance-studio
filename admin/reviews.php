<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

// Проверка прав администратора
if (!isAdmin()) {
    header('Location: ../login.php');
    exit();
}

// Обработка действий с отзывами
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['review_id'], $_POST['action'])) {
        $review_id = (int)$_POST['review_id'];
        $action = $_POST['action'];
        
        if ($action === 'approve') {
            $stmt = $pdo->prepare("UPDATE reviews SET status = 'approved', approved_at = NOW() WHERE id = ?");
            $stmt->execute([$review_id]);
            $message = "Отзыв одобрен";
        } elseif ($action === 'reject') {
            $stmt = $pdo->prepare("UPDATE reviews SET status = 'rejected' WHERE id = ?");
            $stmt->execute([$review_id]);
            $message = "Отзыв отклонен";
        } elseif ($action === 'delete') {
            $stmt = $pdo->prepare("SELECT photo FROM reviews WHERE id = ?");
            $stmt->execute([$review_id]);
            $review = $stmt->fetch();
            
            if ($review && $review['photo'] && file_exists('../' . $review['photo'])) {
                unlink('../' . $review['photo']);
            }
            
            $stmt = $pdo->prepare("DELETE FROM reviews WHERE id = ?");
            $stmt->execute([$review_id]);
            $message = "Отзыв удален";
        }
    }
}

// Получение фильтра
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// Базовый запрос
if ($filter === 'pending') {
    $query = "SELECT * FROM reviews WHERE status = 'pending' ORDER BY created_at DESC";
} elseif ($filter === 'approved') {
    $query = "SELECT * FROM reviews WHERE status = 'approved' ORDER BY approved_at DESC";
} elseif ($filter === 'rejected') {
    $query = "SELECT * FROM reviews WHERE status = 'rejected' ORDER BY created_at DESC";
} else {
    $query = "SELECT * FROM reviews ORDER BY created_at DESC";
}

$reviews = $pdo->query($query)->fetchAll();

// Статистика
$stats = [
    'all' => $pdo->query("SELECT COUNT(*) FROM reviews")->fetchColumn(),
    'pending' => $pdo->query("SELECT COUNT(*) FROM reviews WHERE status = 'pending'")->fetchColumn(),
    'approved' => $pdo->query("SELECT COUNT(*) FROM reviews WHERE status = 'approved'")->fetchColumn(),
    'rejected' => $pdo->query("SELECT COUNT(*) FROM reviews WHERE status = 'rejected'")->fetchColumn()
];
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление отзывами - Yorak Dance Studio</title>
    <link rel="stylesheet" href="/dance_studio2/css/style.css">
    <link rel="stylesheet" href="admin-style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="admin-dashboard">
        <h1 class="section-title">Управление отзывами</h1>
        
        <a href="index.php" class="btn-back" style="display: inline-block; margin-bottom: 20px;">← Назад в панель</a>
        
        <!-- Статистика -->
        <div class="reviews-stats">
            <div class="reviews-stat-card">
                <div class="reviews-stat-number"><?= $stats['all'] ?></div>
                <div class="reviews-stat-label">Всего отзывов</div>
            </div>
            <div class="reviews-stat-card">
                <div class="reviews-stat-number"><?= $stats['pending'] ?></div>
                <div class="reviews-stat-label">Ожидают проверки</div>
            </div>
            <div class="reviews-stat-card">
                <div class="reviews-stat-number"><?= $stats['approved'] ?></div>
                <div class="reviews-stat-label">Одобрено</div>
            </div>
            <div class="reviews-stat-card">
                <div class="reviews-stat-number"><?= $stats['rejected'] ?></div>
                <div class="reviews-stat-label">Отклонено</div>
            </div>
        </div>
        
        <!-- Фильтры -->
        <div class="reviews-filters">
            <a href="?filter=all" class="filter-btn <?= $filter === 'all' ? 'active' : '' ?>">Все</a>
            <a href="?filter=pending" class="filter-btn <?= $filter === 'pending' ? 'active' : '' ?>">На проверке</a>
            <a href="?filter=approved" class="filter-btn <?= $filter === 'approved' ? 'active' : '' ?>">Одобренные</a>
            <a href="?filter=rejected" class="filter-btn <?= $filter === 'rejected' ? 'active' : '' ?>">Отклоненные</a>
        </div>
        
        <!-- Сообщение -->
        <?php if (isset($message)): ?>
            <div class="message-success">✓ <?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        
        <!-- Список отзывов -->
        <div class="reviews-list">
            <?php if (empty($reviews)): ?>
                <div class="empty-reviews">
                    <p>Нет отзывов в этой категории</p>
                </div>
            <?php else: ?>
                <?php foreach ($reviews as $review): ?>
                    <div class="review-card <?= $review['status'] ?>">
                        <div class="review-header">
                            <div class="reviewer-info">
                                <div class="reviewer-avatar">
                                    <?php if ($review['photo'] && file_exists('../' . $review['photo'])): ?>
                                        <img src="../<?= htmlspecialchars($review['photo']) ?>" alt="Фото">
                                    <?php else: ?>
                                        <?= mb_substr(htmlspecialchars($review['name']), 0, 1) ?>
                                    <?php endif; ?>
                                </div>
                                <div class="reviewer-details">
                                    <h3><?= htmlspecialchars($review['name']) ?></h3>
                                    <div class="rating">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <?= $i <= $review['rating'] ? '★' : '☆' ?>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                            </div>
                            <span class="review-status-badge <?= $review['status'] ?>">
                                <?php 
                                if ($review['status'] === 'pending') echo '⏳ Ожидает';
                                elseif ($review['status'] === 'approved') echo '✓ Одобрен';
                                else echo '✗ Отклонен';
                                ?>
                            </span>
                        </div>
                        
                        <div class="review-text">
                            "<?= nl2br(htmlspecialchars($review['review_text'])) ?>"
                        </div>
                        
                        <?php if ($review['photo'] && file_exists('../' . $review['photo'])): ?>
                            <div class="review-photo">
                                <img src="../<?= htmlspecialchars($review['photo']) ?>" alt="Фото отзыва">
                            </div>
                        <?php endif; ?>
                        
                        <div class="review-meta">
                            📅 Дата: <?= date('d.m.Y H:i', strtotime($review['created_at'])) ?>
                            <?php if ($review['approved_at']): ?>
                                | ✅ Одобрен: <?= date('d.m.Y H:i', strtotime($review['approved_at'])) ?>
                            <?php endif; ?>
                        </div>
                        
                        <form method="POST" class="review-actions">
                            <input type="hidden" name="review_id" value="<?= $review['id'] ?>">
                            
                            <?php if ($review['status'] !== 'approved'): ?>
                                <button type="submit" name="action" value="approve" class="btn-review btn-review-approve">✓ Одобрить</button>
                            <?php endif; ?>
                            
                            <?php if ($review['status'] !== 'rejected'): ?>
                                <button type="submit" name="action" value="reject" class="btn-review btn-review-reject">✗ Отклонить</button>
                            <?php endif; ?>
                            
                            <button type="submit" name="action" value="delete" class="btn-review btn-review-delete" onclick="return confirm('Удалить отзыв? Это действие нельзя отменить.')">🗑 Удалить</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>