<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yorak Dance Studio — женская танцевальная студия </title>
    <meta name="title" content="Yorak Dance Studio — женская танцевальная студия | Неоновый стиль">
    <meta name="description" content="⚡ Yorak Dance Studio — женская танцевальная студия. High Heels, Jazz-Funk, Contemporary, Stretching, Girly Hip-Hop. 5+ лет опыта, 200+ учениц. Раскрой свою женственность в неоновой атмосфере!">
    <meta name="keywords" content="танцевальная студия, женские танцы, high heels, танцы на каблуках, jazz-funk, contemporary, stretching, растяжка, girly hip-hop, hip-hop для девушек, студия танцев, yorak dance, yorak dance studio, танцы для женщин, хореография, шоу-группа, неоновый стиль, танцы спб, танцевальная студия спб">
    <meta name="author" content="Yorak Dance Studio">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <meta name="revisit-after" content="7 days">
    <meta name="language" content="Russian">
    <link rel="stylesheet" href="/dance_studio2/css/style.css">
   <style>
.about-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 40px;
    margin: 60px 0;
}

.stat-item-custom {
    text-align: center;
    padding: 50px 25px;
    background: #111111;
    border: 2px solid #2a2a2a;
    position: relative;
    overflow: hidden;
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.7);
    transition: all 0.4s;
    border-left: 4px solid transparent;
}

.stat-item-custom:hover {
    border-left-color: #04d9ff;
    transform: translateY(-8px);
    box-shadow: 0 25px 50px rgba(4, 217, 255, 0.3);
}

.stat-item-custom::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, #04d9ff, #00f5ff);
    opacity: 0.8;
}

.stat-number-custom {
    font-size: 64px;
    font-weight: 900;
    color: #04d9ff;
    line-height: 1;
    margin-bottom: 12px;
    text-shadow: 
        0 0 20px rgba(4, 217, 255, 0.6),
        2px 2px 0 rgba(0, 0, 0, 0.8);
    font-family: 'Oswald', sans-serif;
    text-transform: uppercase;
    letter-spacing: 2px;
}

.stat-label-custom {
    font-size: 16px;
    color: #a0a0a0;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 3px;
}

/* Преимущества */
.advantages-list {
    list-style: none;
    margin-top: 40px;
    padding: 0;
}

.advantages-list li {
    margin-bottom: 20px;
    padding-left: 45px;
    position: relative;
    color: #e0e0e0;
    font-size: 16px;
    line-height: 1.7;
    font-weight: 500;
}

.advantages-list li::before {
    content: '⚡';
    position: absolute;
    left: 0;
    color: #04d9ff;
    font-size: 22px;
    font-weight: 900;
    text-shadow: 0 0 10px rgba(4, 217, 255, 0.6);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.7; transform: scale(1.1); }
}

/* Контент О студии */
.about-content {
    background: #111111;
    border: 2px solid #2a2a2a;
    padding: 60px;
    margin: 60px 0;
    position: relative;
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.7);
    border-left: 5px solid #04d9ff;
}

.about-content::after {
    content: '';
    position: absolute;
    bottom: -15px;
    right: -15px;
    width: 60px;
    height: 60px;
    border-bottom: 4px solid #04d9ff;
    border-right: 4px solid #04d9ff;
    box-shadow: 0 0 20px rgba(4, 217, 255, 0.4);
}

.about-content::before {
    content: '';
    position: absolute;
    top: -15px;
    left: -15px;
    width: 60px;
    height: 60px;
    border-top: 4px solid #04d9ff;
    border-left: 4px solid #04d9ff;
    box-shadow: 0 0 20px rgba(4, 217, 255, 0.4);
}

.about-content p {
    color: #e0e0e0 !important;
    font-size: 20px;
    line-height: 1.8;
    margin-bottom: 40px;
    font-weight: 400;
}

.about-content h3 {
    color: #ffffff !important;
    font-size: 32px;
    font-weight: 900;
    margin: 60px 0 40px;
    text-transform: uppercase;
    letter-spacing: 3px;
    font-family: 'Oswald', sans-serif;
    position: relative;
}

.about-content h3::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 0;
    width: 80px;
    height: 3px;
    background: #04d9ff;
}

/* Отзывы - Дерзкие карточки */
.review-card {
    background: #111111;
    border: 2px solid #2a2a2a;
    padding: 40px 30px;
    transition: all 0.4s;
    height: 100%;
    min-width: 320px;
    margin: 0 20px;
    position: relative;
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.7);
    overflow: hidden;
}

.review-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, #04d9ff, #00f5ff);
}

.review-card:hover {
    border-color: #04d9ff;
    transform: translateY(-12px) scale(1.02);
    box-shadow: 0 30px 60px rgba(4, 217, 255, 0.4);
}

.review-author {
    display: flex;
    align-items: center;
    margin-bottom: 25px;
}

.review-avatar {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    background: linear-gradient(135deg, #04d9ff, #00f5ff);
    color: #000000;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    font-weight: 900;
    margin-right: 20px;
    border: 3px solid #ffffff;
    box-shadow: 0 8px 25px rgba(4, 217, 255, 0.6);
    position: relative;
    overflow: hidden;
}

.review-avatar::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    background: rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    transition: all 0.4s;
    transform: translate(-50%, -50%);
}

.review-card:hover .review-avatar::after {
    width: 200%;
    height: 200%;
}

.review-avatar img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
}

.review-author h4 {
    font-size: 22px;
    font-weight: 800;
    margin-bottom: 6px;
    color: #ffffff;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.review-rating {
    color: #ffd700;
    font-size: 18px;
    letter-spacing: 2px;
    text-shadow: 0 0 10px rgba(255, 215, 0, 0.5);
}

.review-text {
    color: #e0e0e0;
    line-height: 1.8;
    font-size: 15px;
    font-style: italic;
    font-weight: 400;
    position: relative;
}

.review-text::before {
    content: '"';
    font-size: 60px;
    color: #04d9ff;
    position: absolute;
    top: -10px;
    left: -20px;
    font-family: 'Oswald', sans-serif;
    opacity: 0.3;
    line-height: 1;
}

/* Форма отзыва */
.review-form-container {
    background: #111111;
    padding: 60px;
    border: 2px solid #2a2a2a;
    margin: 60px 0;
    position: relative;
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.7);
    border-top: 5px solid #04d9ff;
}

.review-form-container h3 {
    font-size: 36px;
    font-weight: 900;
    margin-bottom: 40px;
    color: #ffffff;
    text-transform: uppercase;
    letter-spacing: 4px;
    font-family: 'Oswald', sans-serif;
    text-align: center;
    position: relative;
}

.review-form input,
.review-form select,
.review-form textarea {
    width: 100%;
    padding: 18px 20px;
    background: #1a1a1a;
    border: 2px solid #2a2a2a;
    font-size: 16px;
    color: #ffffff;
    transition: all 0.4s;
    border-radius: 0;
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
}

.review-form input:focus,
.review-form select:focus,
.review-form textarea:focus {
    outline: none;
    border-color: #04d9ff;
    background: #222222;
    box-shadow: 0 0 20px rgba(4, 217, 255, 0.2);
    transform: translateY(-2px);
}

.review-form input::placeholder,
.review-form textarea::placeholder {
    color: #a0a0a0;
}

.review-form label {
    display: block;
    margin-bottom: 12px;
    font-weight: 700;
    font-size: 14px;
    color: #04d9ff;
    text-transform: uppercase;
    letter-spacing: 2px;
}

.review-form button {
    background: transparent;
    color: #ffffff;
    border: 3px solid #04d9ff;
    padding: 20px 50px;
    font-weight: 800;
    font-size: 16px;
    cursor: pointer;
    transition: all 0.4s;
    text-transform: uppercase;
    letter-spacing: 3px;
    position: relative;
    overflow: hidden;
    z-index: 1;
    display: block;
    margin: 30px auto 0;
    font-family: 'Oswald', sans-serif;
}

.review-form button::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: #04d9ff;
    transition: left 0.4s ease;
    z-index: -1;
}

.review-form button:hover {
    color: #000000;
    border-color: #04d9ff;
    transform: translateY(-3px);
    box-shadow: 0 15px 40px rgba(4, 217, 255, 0.5);
}

.review-form button:hover::before {
    left: 0;
}

/* Слайдер */
.reviews-slider-container {
    position: relative;
    overflow: hidden;
    margin: 60px 0;
    padding: 30px 0;
}

.reviews-slider {
    display: flex;
    transition: transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

.slider-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: #111111;
    color: #04d9ff;
    border: 3px solid #04d9ff;
    width: 60px;
    height: 60px;
    border-radius: 0;
    cursor: pointer;
    font-size: 24px;
    transition: all 0.4s;
    z-index: 10;
    font-weight: 900;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(10px);
}

.slider-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: #04d9ff;
    transition: left 0.4s ease;
    z-index: -1;
}

.slider-btn:hover {
    color: #000000;
    transform: translateY(-50%) scale(1.1);
    box-shadow: 0 20px 50px rgba(4, 217, 255, 0.6);
}

.slider-btn:hover::before {
    left: 0;
}

.slider-btn.prev {
    left: 20px;
}

.slider-btn.next {
    right: 20px;
}

/* Остальные стили без изменений */
.message-success {
    background: #1a2a1a;
    color: #9fc5a0;
    padding: 25px 30px;
    border: 3px solid #9fc5a0;
    margin: 30px 0;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    text-align: center;
    box-shadow: 0 10px 30px rgba(159, 197, 160, 0.3);
    font-size: 16px;
}

.empty-reviews {
    width: 100%;
    text-align: center;
    padding: 80px 40px;
    background: #1a1a1a;
    border: 2px solid #333333;
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.7);
    margin: 20px 0;
}

.empty-reviews p {
    color: #a0a0a0;
    font-size: 20px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 2px;
    margin: 0;
}

/* Адаптивность */
@media (max-width: 768px) {
    .about-stats {
        grid-template-columns: 1fr;
        gap: 30px;
    }
    
    .review-card {
        min-width: 280px;
        margin: 0 10px;
    }
    
    .about-content {
        padding: 40px 30px;
    }
    
    .review-form-container {
        padding: 40px 30px;
    }
    
    .review-form-container h3 {
        font-size: 28px;
    }
    
    .slider-btn {
        width: 50px;
        height: 50px;
        font-size: 20px;
    }
    
    .slider-btn.prev {
        left: 10px;
    }
    
    .slider-btn.next {
        right: 10px;
    }
}

@media (max-width: 480px) {
    .about-stats {
        grid-template-columns: 1fr;
    }
    
    .stat-number-custom {
        font-size: 48px;
    }
    
    .review-card {
        min-width: 100%;
        margin: 0 0 30px 0;
    }
}

.reviews-section {
    background: #0a0a0a !important;
    padding: 100px 0 !important;
}
</style>

</head>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>Танцевальная студия <span>Yorak</span></h1>
            <p>Раскрой свою женственность и творческий потенциал в безопасной и поддерживающей среде</p>
            <a href="schedule.php" class="btn">Смотреть расписание</a>
            <a href="schedule.php" class="btn btn-outline">Записаться</a>
        </div>
    </section>

    <!-- Directions Section -->
    <section class="directions">
        <div class="container">
            <h2 class="section-title">Направления</h2>
            <div class="directions-grid">
                <div class="direction-card">
                    <img src="./image/high-heels.webp" alt="High Heels">
                    <h3>High Heels</h3>
                    <p>Танцы на каблуках. Раскрепощение, нежность, уверенность</p>
                </div>
                <div class="direction-card">
                    <img src="./image/jazz-funk.jpg" alt="Jazz-Funk">
                    <h3>Jazz-Funk</h3>
                    <p>Современное танцевальное направление, сочетающее технику джаза и энергию фанка. Танцуй в кайф!</p>
                </div>
                <div class="direction-card">
                    <img src="./image/Contemporary.webp" alt="Contemporary">
                    <h3>Contemporary</h3>
                    <p>Современная хореография, работа с эмоциями и пространством</p>
                </div>
                <div class="direction-card">
                    <img src="./image/girly-hiphop.jpg" alt="Hip-Hop">
                    <h3>Girly Hip-Hop</h3>
                    <p>Энергия, драйв, уличные движения и стиль</p>
                </div>
                <div class="direction-card">
                    <img src="./image/Stretching.jpg" alt="Stretching">
                    <h3>Stretching</h3>
                    <p>Растяжка, гибкость, здоровье спины и суставов</p>
                </div>
                <div class="direction-card">
                    <img src="./image/bars.jpg" alt="Hip-hop 12+">
                    <h3>Hip-hop pro 12+</h3>
                    <p>Команда под руководством основательницы студии. Выступления на городских мероприятиях, фестивалях, съёмки клипов.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="about" style="background: #0a0a0a; padding: 100px 0;">
        <div class="container">
            <h2 class="section-title">О студии</h2>
            
            <div class="about-content">
                <p style="font-size: 20px; line-height: 1.8; margin-bottom: 40px;">
                    Наша студия создана для женщин, которые хотят не просто поддерживать форму, 
                    а раскрыть свою женственность, снять стресс и найти единомышленниц.
                </p>
                
                <div class="about-stats">
                    <div class="stat-item-custom">
                        <div class="stat-number-custom">5+</div>
                        <div class="stat-label-custom">лет опыта</div>
                    </div>
                    <div class="stat-item-custom">
                        <div class="stat-number-custom">200+</div>
                        <div class="stat-label-custom">учениц</div>
                    </div>
                    <div class="stat-item-custom">
                        <div class="stat-number-custom">6</div>
                        <div class="stat-label-custom">направлений</div>
                    </div>
                </div>
                
                <h3>Почему выбирают нас</h3>
                
                <ul class="advantages-list">
                    <li>Только женщины-тренеры — доверительная атмосфера</li>
                    <li>Группы по уровням подготовки</li>
                    <li>Шоу-группа для выступлений</li>
                    <li>Уютная студия в центре города</li>
                </ul>
            </div>
        </div>
    </section>

    <?php
    // Получаем одобренные отзывы для слайдера
    $reviews = [];

    // Проверяем, есть ли соединение с БД (mysqli)
    if (isset($conn) && $conn) {
        $reviews_query = "SELECT * FROM reviews WHERE status = 'approved' ORDER BY approved_at DESC";
        $reviews_result = mysqli_query($conn, $reviews_query);
        
        if ($reviews_result && mysqli_num_rows($reviews_result) > 0) {
            $reviews = mysqli_fetch_all($reviews_result, MYSQLI_ASSOC);
        }
    } 
    // Если нет mysqli, пробуем через PDO
    elseif (isset($pdo) && $pdo) {
        try {
            $stmt = $pdo->query("SELECT * FROM reviews WHERE status = 'approved' ORDER BY approved_at DESC");
            $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $reviews = [];
        }
    }
    ?>

    <!-- Reviews Section -->
    <section class="reviews-section">
        <div class="container">
            <h2 class="section-title">Отзывы наших учениц</h2>
            
            <?php if (isset($_SESSION['review_message'])): ?>
                <div class="message-success">
                    <?php 
                    echo $_SESSION['review_message'];
                    unset($_SESSION['review_message']);
                    ?>
                </div>
            <?php endif; ?>
            
            <!-- Слайдер отзывов -->
            <div class="reviews-slider-container">
                <div class="reviews-slider" id="reviewsSlider">
                    <?php if (empty($reviews)): ?>
                        <div class="empty-reviews">
                            <p>Пока нет отзывов. Будьте первой!</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($reviews as $review): ?>
                            <div class="review-card">
                                <div class="review-author">
                                    <?php if (!empty($review['photo']) && file_exists($review['photo'])): ?>
                                        <div class="review-avatar">
                                            <img src="<?php echo htmlspecialchars($review['photo']); ?>" alt="<?php echo htmlspecialchars($review['name']); ?>">
                                        </div>
                                    <?php else: ?>
                                        <div class="review-avatar">
                                            <?php echo mb_substr(htmlspecialchars($review['name']), 0, 1); ?>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <h4><?php echo htmlspecialchars($review['name']); ?></h4>
                                        <div class="review-rating">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <?php echo $i <= $review['rating'] ? '★' : '☆'; ?>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                </div>
                                <p class="review-text"><?php echo nl2br(htmlspecialchars($review['review_text'])); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <!-- Кнопки слайдера -->
                <?php if (!empty($reviews) && count($reviews) > 1): ?>
                <button class="slider-btn prev" onclick="slideReviews(-1)">❮</button>
                <button class="slider-btn next" onclick="slideReviews(1)">❯</button>
                <?php endif; ?>
            </div>
            
            <!-- Форма добавления отзыва -->
            <div class="review-form-container">
                <h3>Оставить отзыв</h3>
                
                <form action="submit_review.php" method="POST" enctype="multipart/form-data" class="review-form">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px;">
                        <div>
                            <label>Ваше имя</label>
                            <input type="text" name="name" required placeholder="Введите ваше имя">
                        </div>
                        
                        <div>
                            <label>Оценка</label>
                            <select name="rating">
                                <option value="5">5 ★ Превосходно</option>
                                <option value="4">4 ★ Отлично</option>
                                <option value="3">3 ★ Хорошо</option>
                                <option value="2">2 ★ Удовлетворительно</option>
                                <option value="1">1 ★ Нужны улучшения</option>
                            </select>
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 30px;">
                        <label>Ваш отзыв</label>
                        <textarea name="review" rows="6" required placeholder="Расскажите о вашем опыте в Yorak Dance Studio..."></textarea>
                    </div>
                    
                    <div style="margin-bottom: 40px;">
                        <label>Фото (необязательно)</label>
                        <input type="file" name="photo" accept="image/*">
                    </div>
                    
                    <button type="submit">Отправить отзыв</button>
                    <p style="margin-top: 25px; color: #a0a0a0; font-size: 14px; font-style: italic; text-align: center;">
                        * Отзыв появится после проверки администратором
                    </p>
                </form>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <!-- JavaScript для слайдера - улучшенная версия -->
    <script>
    let currentSlide = 0;
    const slider = document.getElementById('reviewsSlider');
    const cards = document.querySelectorAll('.review-card');
    
    function slideReviews(direction) {
        if (cards.length === 0 || cards.length === 1) return;
        
        const cardStyle = window.getComputedStyle(cards[0]);
        const cardWidth = cards[0].offsetWidth + 
                         parseInt(cardStyle.marginLeft) + 
                         parseInt(cardStyle.marginRight) + 40; // margin между карточками
        
        const containerWidth = slider.parentElement.offsetWidth;
        const visibleCards = Math.floor(containerWidth / cardWidth);
        const maxSlide = Math.max(0, cards.length - visibleCards);
        
        currentSlide += direction;
        
        if (currentSlide < 0) currentSlide = 0;
        if (currentSlide > maxSlide) currentSlide = maxSlide;
        
        slider.style.transform = `translateX(-${currentSlide * cardWidth}px)`;
    }

    // Автоадаптация при ресайзе
    window.addEventListener('resize', () => {
        if (cards.length > 0) {
            currentSlide = 0;
            slider.style.transform = 'translateX(0)';
        }
    });

    // Автоскролл каждые 8 секунд
    setInterval(() => {
        if (cards.length > 1) {
            slideReviews(1);
        }
    }, 8000);
    </script>
</body>
</html>
