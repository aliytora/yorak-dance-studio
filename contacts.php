<?php include 'includes/header.php'; ?>

<style>
/* =====================================================
   КОНТАКТЫ - Дерзкий ЧЕРНО-НЕОНОВЫЙ ГОЛУБОЙ стиль
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
    background: linear-gradient(90deg, #04d9ff, #00f5ff);
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

.contacts-info {
    display: grid;
    grid-template-columns: 1fr 560px;
    gap: 60px;
    margin: 60px 0;
    align-items: start;
}

.contacts-list {
    display: flex;
    flex-direction: column;
    gap: 30px;
}

.contact-item {
    background: #1a1a1a;
    padding: 35px 30px;
    border-left: 4px solid #333333;
    position: relative;
    transition: all 0.3s;
    border: 1px solid #2a2a2a;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
}

.contact-item::before {
    content: '';
    position: absolute;
    left: -1px;
    top: 0;
    width: 5px;
    height: 0;
    background: linear-gradient(to bottom, #04d9ff, #00f5ff);
    transition: height 0.3s;
    z-index: 2;
}

.contact-item:hover::before {
    height: 100%;
}

.contact-item:hover {
    border-left-color: #04d9ff;
    transform: translateX(10px);
    box-shadow: 0 20px 40px rgba(4, 217, 255, 0.2);
}

.contact-item h3 {
    color: #04d9ff;
    font-size: 20px;
    font-weight: 800;
    margin: 0 0 20px 0;
    text-transform: uppercase;
    letter-spacing: 1px;
    text-shadow: 0 0 10px rgba(4, 217, 255, 0.5);
}

.contact-item p {
    color: #e0e0e0;
    margin: 0;
    font-size: 16px;
    line-height: 1.7;
    font-weight: 500;
}

.contact-item strong {
    color: #ffffff;
    font-weight: 700;
}

/* Карта */
.map {
    position: relative;
    background: #111111;
    border: 2px solid #2a2a2a;
    border-top: 4px solid #04d9ff;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.7);
    border-radius: 12px;
    overflow: hidden;
}

.map iframe {
    width: 100%;
    height: 400px;
    border: none;
    display: block;
}

.map::before {
    content: '🗺️';
    position: absolute;
    top: 20px;
    right: 20px;
    font-size: 32px;
    z-index: 10;
    opacity: 0.1;
    filter: drop-shadow(0 0 20px #04d9ff);
}

/* Адаптивность */
@media (max-width: 992px) {
    .contacts-info {
        grid-template-columns: 1fr;
        gap: 40px;
        text-align: center;
    }
    
    .map {
        order: -1;
    }
    
    .contact-item {
        padding: 25px 20px;
    }
}

@media (max-width: 768px) {
    .section-title {
        font-size: 36px;
    }
    
    .contacts-info {
        gap: 30px;
        margin: 40px 0;
    }
    
    .map iframe {
        height: 300px;
    }
    
    .contact-item {
        padding: 25px 20px;
    }
    
    .contact-item h3 {
        font-size: 18px;
    }
}

@media (max-width: 480px) {
    .section-title {
        font-size: 28px;
    }
    
    .contacts-info {
        gap: 25px;
    }
    
    .contact-item {
        padding: 20px 15px;
    }
    
    .contact-item h3 {
        font-size: 16px;
        margin-bottom: 15px;
    }
    
    .contact-item p {
        font-size: 15px;
    }
    
    .map iframe {
        height: 250px;
    }
}

/* Анимация появления */
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

.contact-item {
    animation: fadeInUp 0.6s ease-out forwards;
    opacity: 0;
}

.contact-item:nth-child(1) { animation-delay: 0.1s; }
.contact-item:nth-child(2) { animation-delay: 0.2s; }
.contact-item:nth-child(3) { animation-delay: 0.3s; }
.contact-item:nth-child(4) { animation-delay: 0.4s; }
.contact-item:nth-child(5) { animation-delay: 0.5s; }
</style>

<div class="container">
    <h1 class="section-title">Контакты</h1>
    
    <div class="contacts-info">
        <div class="contacts-list">
            <div class="contact-item">
                <h3>📍 Адрес</h3>
                <p>г. Ижевск, ул. Карла Маркса 191 <br><strong>ТЦ Аксион, -1 этаж</strong></p>
            </div>
            
            <div class="contact-item">
                <h3>📞 Телефон</h3>
                <p><strong>+7 (904) 248-07-36</strong></p>
            </div>
            
            <div class="contact-item">
                <h3>✉️ Email</h3>
                <p><strong>info@yorakdancestudio.ru</strong></p>
            </div>
            
            <div class="contact-item">
                <h3>🌐 Социальные сети</h3>
                <p>
                    <strong>Instagram:</strong> @yorak_studio<br>
                    <strong>Telegram:</strong> @yorak_studio_bot
                </p>
            </div>
            
            <div class="contact-item">
                <h3>⏰ Часы работы</h3>
                <p>
                    <strong>Пн-Пт:</strong> 10:00 - 22:00<br>
                    <strong>Сб-Вс:</strong> 11:00 - 20:00
                </p>
            </div>
        </div>
        
        <div class="map">
            <div style="position:relative;overflow:hidden;">
                <a href="https://yandex.ru/maps/44/izhevsk/?utm_medium=mapframe&utm_source=maps" style="color:#eee;font-size:12px;position:absolute;top:0px;">Ижевск</a>
                <a href="https://yandex.ru/maps/44/izhevsk/house/ulitsa_karla_marksa_191/YUoYdAdiQEcOQFtsfXR1dnlmZw==/?ll=53.202929%2C56.847553&utm_medium=mapframe&utm_source=maps&z=16" style="color:#eee;font-size:12px;position:absolute;top:14px;">Улица Карла Маркса, 191 на карте Ижевска — Яндекс Карты</a>
                <iframe src="https://yandex.ru/map-widget/v1/?ll=53.202929%2C56.847553&mode=whatshere&whatshere%5Bpoint%5D=53.202929%2C56.847553&whatshere%5Bzoom%5D=17&z=16" width="560" height="400" frameborder="1" allowfullscreen="true" style="position:relative;"></iframe>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
