<?php
require_once 'includes/db.php';
$trainers = $pdo->query("SELECT * FROM trainers")->fetchAll();
?>
<?php include 'includes/header.php'; ?>

<style>
/* =====================================================
   НАШИ ТРЕНЕРЫ - Дерзкий ЧЕРНО-НЕОНОВЫЙ ГОЛУБОЙ стиль
   Черный + графит + #04d9ff + #00f5ff
   ===================================================== */

body {
    background: #0c0c0c;
    font-family: 'Montserrat', sans-serif;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}


.section-title::before {
    content: '';
    position: absolute;
    bottom: -15px;
    left: 0;
    width: 80px;
    height: 5px;
    background: #04d9ff;
    box-shadow: 0 0 15px rgba(4, 217, 255, 0.6);
}

.section-title::after {
    content: '';
    position: absolute;
    bottom: -15px;
    left: 90px;
    width: 30px;
    height: 5px;
    background: #ffffff;
}

.trainers-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
    margin: 50px 0;
    position: relative;
}

@media (max-width: 992px) {
    .trainers-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 576px) {
    .trainers-grid {
        grid-template-columns: 1fr;
    }
}

.trainer-card {
    background: #1a1a1a;
    border: 2px solid #333333;
    transition: all 0.3s;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.trainer-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 5px;
    height: 0;
    background: linear-gradient(to bottom, #04d9ff, #00f5ff);
    transition: height 0.3s;
    z-index: 2;
    box-shadow: 0 0 10px rgba(4, 217, 255, 0.5);
}

.trainer-card:hover::before {
    height: 100%;
}

.trainer-card:hover {
    border-color: #04d9ff;
    transform: translateY(-10px);
    box-shadow: 0 20px 30px rgba(4, 217, 255, 0.3);
}

.trainer-card::after {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(4, 217, 255, 0.1) 0%, transparent 70%);
    opacity: 0;
    transition: opacity 0.3s;
    pointer-events: none;
}

.trainer-card:hover::after {
    opacity: 1;
}

.trainer-card img {
    width: 100%;
    height: 380px;
    object-fit: cover;
    object-position: center top;
    transition: transform 0.5s;
    border-bottom: 3px solid transparent;
}

.trainer-card:hover img {
    transform: scale(1.05);
    border-bottom-color: #04d9ff;
}

.trainer-card h3 {
    margin: 25px 0 10px;
    color: #ffffff;
    font-size: 24px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
    position: relative;
    padding-bottom: 10px;
}

.trainer-card h3::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 50px;
    height: 3px;
    background: linear-gradient(90deg, #04d9ff, #00f5ff);
    transition: width 0.3s;
    box-shadow: 0 0 10px rgba(4, 217, 255, 0.6);
}

.trainer-card:hover h3::after {
    width: 80px;
}

.trainer-card p {
    color: #cccccc;
    padding: 0 25px;
    margin: 15px 0;
    line-height: 1.8;
    font-size: 15px;
}

.instagram-link {
    display: inline-block;
    margin: 15px 0 30px;
    color: #04d9ff;
    text-decoration: none;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-size: 14px;
    padding: 8px 20px;
    border: 2px solid #04d9ff;
    transition: all 0.3s;
    position: relative;
    overflow: hidden;
    z-index: 1;
    text-shadow: 0 0 10px rgba(4, 217, 255, 0.5);
}

.instagram-link::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: #04d9ff;
    transition: left 0.3s;
    z-index: -1;
}

.instagram-link:hover::before {
    left: 0;
}

.instagram-link:hover {
    color: #000000;
    border-color: #04d9ff;
    transform: scale(1.05);
}

.no-photo {
    width: 100%;
    height: 380px;
    background: linear-gradient(135deg, #1a1a1a, #0a0a0a);
    border-bottom: 3px solid #333333;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #04d9ff;
    font-size: 64px;
    font-weight: 900;
    transition: all 0.3s;
    text-shadow: 0 0 20px rgba(4, 217, 255, 0.8);
}

.trainer-card:hover .no-photo {
    border-bottom-color: #04d9ff;
    background: linear-gradient(135deg, #04d9ff, #00f5ff);
    color: #000000;
}

/* Дополнительные стили для био */
.trainer-card .bio {
    max-height: 100px;
    overflow-y: auto;
    padding-right: 10px;
    margin: 15px 25px;
    color: #cccccc;
    line-height: 1.8;
    font-size: 14px;
    text-align: left;
    border-left: 2px solid #333333;
    padding-left: 15px;
}

.trainer-card .bio::-webkit-scrollbar {
    width: 4px;
}

.trainer-card .bio::-webkit-scrollbar-track {
    background: #333333;
}

.trainer-card .bio::-webkit-scrollbar-thumb {
    background: #04d9ff;
}

/* Анимация появления карточек */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.trainer-card {
    animation: fadeInUp 0.6s ease-out forwards;
    opacity: 0;
}

.trainer-card:nth-child(1) { animation-delay: 0.1s; }
.trainer-card:nth-child(2) { animation-delay: 0.2s; }
.trainer-card:nth-child(3) { animation-delay: 0.3s; }
.trainer-card:nth-child(4) { animation-delay: 0.4s; }
.trainer-card:nth-child(5) { animation-delay: 0.5s; }
.trainer-card:nth-child(6) { animation-delay: 0.6s; }

/* Специализация тренера (если добавить в БД) */
.trainer-specialization {
    display: inline-block;
    padding: 5px 15px;
    background: linear-gradient(135deg, #04d9ff, #00f5ff);
    color: #000000;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 10px;
    box-shadow: 0 0 10px rgba(4, 217, 255, 0.5);
}

/* Статистика тренера (если добавить в БД) */
.trainer-stats {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin: 15px 0;
    padding: 10px 0;
    border-top: 1px solid #333333;
    border-bottom: 1px solid #333333;
}

.trainer-stat {
    text-align: center;
}

.trainer-stat-value {
    font-size: 20px;
    font-weight: 900;
    color: #04d9ff;
    text-shadow: 0 0 10px rgba(4, 217, 255, 0.5);
}

.trainer-stat-label {
    font-size: 11px;
    color: #cccccc;
    text-transform: uppercase;
}

/* Иконка Instagram */
.instagram-link i {
    margin-right: 5px;
    font-size: 16px;
}

/* Адаптивность для мобильных */
@media (max-width: 768px) {
    .section-title {
        font-size: 36px;
    }
    
    .trainer-card img,
    .no-photo {
        height: 320px;
    }
    
    .trainer-card h3 {
        font-size: 20px;
    }
    
    .trainer-card p {
        padding: 0 15px;
        font-size: 14px;
    }
    
    .trainer-card .bio {
        margin: 15px 15px;
    }
}

@media (max-width: 480px) {
    .section-title {
        font-size: 28px;
    }
    
    .trainer-card img,
    .no-photo {
        height: 280px;
    }
    
    .trainer-card h3 {
        font-size: 18px;
        margin: 20px 0 5px;
    }
}

/* Эффект параллакса для фото (опционально) */
.trainer-card {
    perspective: 1000px;
}

.trainer-card img {
    transform-style: preserve-3d;
    transition: transform 0.5s;
}

.trainer-card:hover img {
    transform: scale(1.05) translateZ(20px);
}
</style>


<div class="container">
    <h1 class="section-title">Наши тренеры</h1>
    
    <div class="trainers-grid">
        <?php foreach($trainers as $index => $t): 
            // Путь к фото
            $photo_path = 'image/trainers/' . $t['photo'];
            
            // Разбиваем био на строки для лучшего отображения
            $bio_lines = explode("\n", $t['bio']);
        ?>
            <div class="trainer-card">
                <?php if(!empty($t['photo']) && file_exists($photo_path)): ?>
                    <img src="<?= $photo_path ?>" alt="<?= htmlspecialchars($t['name']) ?>">
                <?php else: ?>
                    <div class="no-photo">
                        <?= mb_substr(htmlspecialchars($t['name']), 0, 1) ?>
                    </div>
                <?php endif; ?>
                
                <h3><?= htmlspecialchars($t['name']) ?></h3>
                
                <?php if($t['specialization'] ?? false): ?>
                    <div class="trainer-specialization">
                        <?= htmlspecialchars($t['specialization']) ?>
                    </div>
                <?php endif; ?>
                
                <?php if($t['bio']): ?>
                    <div class="bio">
                        <?php foreach($bio_lines as $line): ?>
                            <?= trim($line) ?><br>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <?php if($t['instagram']): ?>
                    <a href="https://instagram.com/<?= $t['instagram'] ?>" class="instagram-link" target="_blank">
                        📷 @<?= $t['instagram'] ?>
                    </a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>