// Плавная прокрутка к якорям
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if(target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Мобильное меню (бургер)
const createMobileMenu = () => {
    const header = document.querySelector('header .container');
    const nav = document.querySelector('nav ul');
    
    if(window.innerWidth <= 768 && !document.querySelector('.burger')) {
        const burger = document.createElement('div');
        burger.className = 'burger';
        burger.innerHTML = '☰';
        burger.style.cssText = `
            font-size: 30px;
            cursor: pointer;
            color: #6b5b6b;
            display: block;
        `;
        
        burger.addEventListener('click', () => {
            nav.style.display = nav.style.display === 'flex' ? 'none' : 'flex';
            nav.style.flexDirection = 'column';
            nav.style.position = 'absolute';
            nav.style.top = '80px';
            nav.style.left = '0';
            nav.style.width = '100%';
            nav.style.background = 'white';
            nav.style.padding = '20px';
            nav.style.boxShadow = '0 5px 10px rgba(0,0,0,0.1)';
        });
        
        header.appendChild(burger);
        nav.style.display = 'none';
    } else if(window.innerWidth > 768) {
        nav.style.display = 'flex';
        nav.style.position = 'static';
        nav.style.padding = '0';
        nav.style.boxShadow = 'none';
        const burger = document.querySelector('.burger');
        if(burger) burger.remove();
    }
};

window.addEventListener('load', createMobileMenu);
window.addEventListener('resize', createMobileMenu);

// Валидация форм на клиенте
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function(e) {
        const email = this.querySelector('input[type="email"]');
        const phone = this.querySelector('input[type="tel"]');
        const password = this.querySelector('input[type="password"]');
        const confirm = this.querySelector('input[name="confirm_password"]');
        
        if(email && !validateEmail(email.value)) {
            e.preventDefault();
            showNotification('Введите корректный email', 'error');
        }
        
        if(phone && phone.value && !validatePhone(phone.value)) {
            e.preventDefault();
            showNotification('Введите корректный телефон (11 цифр)', 'error');
        }
        
        if(password && confirm && password.value !== confirm.value) {
            e.preventDefault();
            showNotification('Пароли не совпадают', 'error');
        }
        
        if(password && password.value.length < 6 && password.value.length > 0) {
            e.preventDefault();
            showNotification('Пароль должен быть минимум 6 символов', 'error');
        }
    });
});

// Валидация email
function validateEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

// Валидация телефона (российский формат)
function validatePhone(phone) {
    const cleaned = phone.replace(/\D/g, '');
    return cleaned.length === 11;
}

// Форматирование телефона при вводе
document.querySelectorAll('input[type="tel"]').forEach(input => {
    input.addEventListener('input', function(e) {
        let value = this.value.replace(/\D/g, '');
        if(value.length > 11) value = value.slice(0, 11);
        
        if(value.length > 0) {
            if(value.length <= 1) {
                this.value = '+7';
            } else if(value.length <= 4) {
                this.value = '+7 (' + value.slice(1);
            } else if(value.length <= 7) {
                this.value = '+7 (' + value.slice(1, 4) + ') ' + value.slice(4);
            } else if(value.length <= 9) {
                this.value = '+7 (' + value.slice(1, 4) + ') ' + value.slice(4, 7) + '-' + value.slice(7);
            } else {
                this.value = '+7 (' + value.slice(1, 4) + ') ' + value.slice(4, 7) + '-' + value.slice(7, 9) + '-' + value.slice(9, 11);
            }
        } else {
            this.value = '';
        }
    });
});

// Уведомления
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        padding: 15px 25px;
        background: ${type === 'success' ? '#e3f7e3' : '#fde0e0'};
        color: ${type === 'success' ? '#2e7d32' : '#c62828'};
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        z-index: 1000;
        animation: slideIn 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Добавляем анимации в head
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    .fade-in {
        animation: fadeIn 0.5s ease;
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
`;
document.head.appendChild(style);

// Фильтрация расписания (если есть таблица)
const scheduleTable = document.querySelector('.schedule-table');
if(scheduleTable) {
    // Создаем фильтры
    const filterContainer = document.createElement('div');
    filterContainer.className = 'schedule-filters';
    filterContainer.style.cssText = `
        margin-bottom: 20px;
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    `;
    
    // Фильтр по направлению
    const directionFilter = document.createElement('select');
    directionFilter.className = 'filter-select';
    directionFilter.style.cssText = `
        padding: 10px;
        border: 1px solid #f0e0e5;
        border-radius: 8px;
        background: white;
    `;
    
    // Собираем уникальные направления
    const directions = new Set();
    scheduleTable.querySelectorAll('tbody tr td:nth-child(2)').forEach(cell => {
        directions.add(cell.textContent.trim());
    });
    
    directionFilter.innerHTML = '<option value="">Все направления</option>' + 
        Array.from(directions).map(d => `<option value="${d}">${d}</option>`).join('');
    
    // Фильтр по тренеру
    const trainerFilter = document.createElement('select');
    trainerFilter.className = 'filter-select';
    trainerFilter.style.cssText = directionFilter.style.cssText;
    
    const trainers = new Set();
    scheduleTable.querySelectorAll('tbody tr td:nth-child(4)').forEach(cell => {
        trainers.add(cell.textContent.trim());
    });
    
    trainerFilter.innerHTML = '<option value="">Все тренеры</option>' + 
        Array.from(trainers).map(t => `<option value="${t}">${t}</option>`).join('');
    
    filterContainer.appendChild(directionFilter);
    filterContainer.appendChild(trainerFilter);
    
    scheduleTable.parentNode.insertBefore(filterContainer, scheduleTable);
    
    // Функция фильтрации
    function filterSchedule() {
        const direction = directionFilter.value;
        const trainer = trainerFilter.value;
        
        scheduleTable.querySelectorAll('tbody tr').forEach(row => {
            const rowDirection = row.querySelector('td:nth-child(2)').textContent.trim();
            const rowTrainer = row.querySelector('td:nth-child(4)').textContent.trim();
            
            const directionMatch = !direction || rowDirection === direction;
            const trainerMatch = !trainer || rowTrainer === trainer;
            
            row.style.display = directionMatch && trainerMatch ? '' : 'none';
        });
    }
    
    directionFilter.addEventListener('change', filterSchedule);
    trainerFilter.addEventListener('change', filterSchedule);
}

// Подтверждение удаления
document.querySelectorAll('.delete-btn, [onclick*="confirm"]').forEach(btn => {
    btn.addEventListener('click', function(e) {
        if(!confirm('Вы уверены?')) {
            e.preventDefault();
        }
    });
});

// Счетчик занятий в корзине (если нужно)
function updateCartCount() {
    // Можно реализовать позже
}

// Ленивая загрузка изображений
if('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if(entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.add('fade-in');
                observer.unobserve(img);
            }
        });
    });
    
    document.querySelectorAll('img[data-src]').forEach(img => {
        imageObserver.observe(img);
    });
}

// Таймер для акций (если нужен)
function startTimer(duration, display) {
    let timer = duration, minutes, seconds;
    setInterval(() => {
        minutes = parseInt(timer / 60, 10);
        seconds = parseInt(timer % 60, 10);
        
        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;
        
        display.textContent = minutes + ":" + seconds;
        
        if (--timer < 0) {
            timer = duration;
        }
    }, 1000);
}

// Инициализация при загрузке
document.addEventListener('DOMContentLoaded', () => {
    // Добавляем класс для анимаций
    document.body.classList.add('loaded');
    
    // Плавное появление элементов
    document.querySelectorAll('.direction-card, .trainer-card').forEach((el, index) => {
        el.style.animation = `fadeIn 0.5s ease ${index * 0.1}s forwards`;
        el.style.opacity = '0';
    });
    
    // Обработка ховера для карточек
    document.querySelectorAll('.direction-card, .trainer-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});