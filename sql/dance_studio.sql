-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Мар 05 2026 г., 15:48
-- Версия сервера: 8.0.30
-- Версия PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `dance_studio`
--

-- --------------------------------------------------------

--
-- Структура таблицы `bookings`
--

CREATE TABLE `bookings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `schedule_id` int NOT NULL,
  `booking_date` date NOT NULL,
  `status` enum('active','cancelled','visited') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `client_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `user_membership_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `schedule_id` (`schedule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `bookings`
--

INSERT INTO `bookings` (`id`, `schedule_id`, `booking_date`, `status`, `client_name`, `client_phone`, `created_at`, `user_membership_id`) VALUES
(6, 15, '2026-02-05', 'active', 'Алия', '+7 (950) 176-48-24', '2026-02-24 09:21:17', NULL),
(7, 13, '2026-02-06', 'visited', 'Алия', '+7 (950) 176-48-24', '2026-02-24 09:21:39', NULL),
(8, 7, '2026-03-10', 'cancelled', 'Алия', '+7 (950) 176-48-24', '2026-02-24 09:22:36', NULL),
(9, 18, '2026-03-13', 'cancelled', 'Алия', '+7 (950) 176-48-24', '2026-02-24 09:26:49', NULL),
(10, 11, '2026-03-06', 'visited', 'Алия', '+7 (950) 176-48-24', '2026-03-03 19:48:41', NULL),
(11, 1, '2026-03-07', 'active', 'София', '+7 (983) 456-72-34', '2026-03-03 20:29:17', NULL),
(12, 10, '2026-03-11', 'visited', 'Елизавета', '+7 (952) 405-28-07', '2026-03-04 12:00:47', NULL),
(13, 4, '2026-03-12', 'visited', 'Дарья', '+7 (952) 405-28-07', '2026-03-05 08:41:39', NULL),
(14, 12, '2026-03-07', 'visited', 'Дарья', '+7 (952) 405-28-07', '2026-03-05 09:29:47', NULL),
(15, 4, '2026-03-05', 'visited', 'Дарья', '+7 (950) 823-29-03', '2026-03-05 09:32:17', NULL),
(16, 1, '2026-03-14', 'visited', 'Алия', '+7 (950) 176-48-24', '2026-03-05 10:08:16', NULL),
(17, 18, '2026-03-13', 'visited', 'Алия', '+7 (950) 176-48-24', '2026-03-05 10:11:48', NULL),
(18, 15, '2026-03-05', 'visited', 'Алиса', '+7 (950) 154-17-58', '2026-03-05 12:11:44', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `memberships`
--

CREATE TABLE `memberships` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `lessons_left` int DEFAULT '0',
  `valid_until` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `memberships`
--

INSERT INTO `memberships` (`id`, `user_id`, `lessons_left`, `valid_until`, `created_at`) VALUES
(1, 10, 12, '2026-04-05', '2026-03-03 19:32:55'),
(5, 12, 4, '2026-04-05', '2026-03-05 09:08:26'),
(6, 14, 8, '2026-04-05', '2026-03-05 10:03:20'),
(7, 15, 999, '2026-04-05', '2026-03-05 12:11:06');

-- --------------------------------------------------------

--
-- Структура таблицы `membership_logs`
--

CREATE TABLE `membership_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `action` varchar(50) NOT NULL,
  `lessons_before` int DEFAULT NULL,
  `lessons_after` int DEFAULT NULL,
  `lessons_changed` int DEFAULT NULL,
  `booking_id` int DEFAULT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `booking_id` (`booking_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `membership_logs`
--

INSERT INTO `membership_logs` (`id`, `user_id`, `action`, `lessons_before`, `lessons_after`, `lessons_changed`, `booking_id`, `description`, `created_at`) VALUES
(2, 11, 'purchase', 0, 4, 4, NULL, 'Оплачено в студии', '2026-03-03 20:30:12'),
(3, 12, 'purchase', 0, 4, 4, NULL, 'Оплачено в студии', '2026-03-04 12:00:15'),
(8, 12, 'purchase', 0, 4, 4, NULL, 'Оплачено в студии', '2026-03-05 09:08:26'),
(9, 10, 'purchase', 8, 12, 4, NULL, 'Оплачено в студии', '2026-03-05 09:31:21'),
(10, 14, 'purchase', 0, 8, 8, NULL, 'Оплачено в студии', '2026-03-05 10:03:20'),
(11, 15, 'purchase', 0, 999, 999, NULL, 'Оплачено в студии', '2026-03-05 12:11:06');

-- --------------------------------------------------------

--
-- Структура таблицы `membership_plans`
--

CREATE TABLE `membership_plans` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `lessons` int NOT NULL,
  `months` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text,
  `is_active` tinyint DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `membership_plans`
--

INSERT INTO `membership_plans` (`id`, `name`, `lessons`, `months`, `price`, `description`, `is_active`, `created_at`) VALUES
(2, 'Легкий старт', 4, 1, '2000.00', '4 занятия в течение месяца', 1, '2026-03-03 19:48:00'),
(3, 'Базовый', 8, 1, '3800.00', '8 занятий в течение месяца', 1, '2026-03-03 19:48:00'),
(4, 'Оптимальный', 12, 1, '5400.00', '12 занятий в течение месяца', 1, '2026-03-03 19:48:00'),
(6, 'Пробный', 1, 1, '350.00', 'Разовое занятие', 1, '2026-03-05 09:15:53'),
(9, 'Безлимит', 999, 1, '4500.00', 'Неограниченное количество занятий', 1, '2026-03-05 09:15:53');

-- --------------------------------------------------------

--
-- Структура таблицы `reviews`
--

CREATE TABLE `reviews` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `review_text` text NOT NULL,
  `rating` int DEFAULT '5',
  `photo` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `approved_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `reviews`
--

INSERT INTO `reviews` (`id`, `name`, `review_text`, `rating`, `photo`, `status`, `created_at`, `approved_at`) VALUES
(2, 'Дарья', 'Вообще все четенько, люблю Алию ', 5, NULL, 'approved', '2026-03-04 12:50:07', '2026-03-04 12:50:17');

-- --------------------------------------------------------

--
-- Структура таблицы `schedules`
--

CREATE TABLE `schedules` (
  `id` int NOT NULL AUTO_INCREMENT,
  `direction` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `level` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `room` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `requirements` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancel_hours` int DEFAULT '6',
  `trainer_id` int DEFAULT NULL,
  `weekday` enum('monday','tuesday','wednesday','thursday','friday','saturday','sunday') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `time` time NOT NULL,
  `duration` int DEFAULT '60',
  `max_participants` int DEFAULT '15',
  PRIMARY KEY (`id`),
  KEY `trainer_id` (`trainer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `schedules`
--

INSERT INTO `schedules` (`id`, `direction`, `level`, `description`, `room`, `requirements`, `cancel_hours`, `trainer_id`, `weekday`, `time`, `duration`, `max_participants`) VALUES
(1, 'Hip-Hop PRO 12+', 'Шоу-группа', NULL, NULL, NULL, 6, 1, 'saturday', '15:00:00', 90, 12),
(2, 'Hip-Hop PRO 12+', 'Шоу-группа', NULL, NULL, NULL, 6, 1, 'sunday', '14:00:00', 90, 12),
(3, 'Girly Hip-Hop', 'Начинающие', NULL, NULL, NULL, 6, 1, 'tuesday', '18:00:00', 60, 10),
(4, 'Girly Hip-Hop', 'Средний', NULL, NULL, NULL, 6, 1, 'thursday', '19:00:00', 60, 10),
(5, 'Contemporary', 'Начинающие', NULL, NULL, NULL, 6, 2, 'monday', '17:00:00', 60, 8),
(6, 'Contemporary', 'Средний', NULL, NULL, NULL, 6, 2, 'wednesday', '19:00:00', 60, 8),
(7, 'Stretching', 'Утро', NULL, NULL, NULL, 6, 2, 'tuesday', '09:00:00', 55, 15),
(8, 'Stretching', 'Вечер', NULL, NULL, NULL, 6, 2, 'thursday', '20:00:00', 55, 15),
(9, 'High Heels', 'Начинающие', NULL, NULL, NULL, 6, 3, 'monday', '19:00:00', 60, 10),
(10, 'High Heels', 'Средний', NULL, NULL, NULL, 6, 3, 'wednesday', '20:00:00', 60, 10),
(11, 'Jazz-Funk', 'Начинающие', NULL, NULL, NULL, 6, 3, 'friday', '18:00:00', 60, 8),
(12, 'Jazz-Funk', 'Средний', NULL, NULL, NULL, 6, 3, 'saturday', '11:00:00', 60, 8),
(13, 'Jazz-Funk', 'Продвинутые', NULL, NULL, NULL, 6, 4, 'friday', '19:30:00', 90, 6),
(14, 'Jazz-Funk', 'Продвинутые', NULL, NULL, NULL, 6, 4, 'sunday', '16:00:00', 90, 6),
(15, 'Contemporary', 'Продвинутые', NULL, NULL, NULL, 6, 4, 'thursday', '18:30:00', 90, 6),
(16, 'Stretching', 'Утро', NULL, NULL, NULL, 6, 5, 'monday', '10:00:00', 55, 15),
(17, 'Stretching', 'День', NULL, NULL, NULL, 6, 5, 'wednesday', '14:00:00', 55, 15),
(18, 'Stretching', 'Вечер', NULL, NULL, NULL, 6, 5, 'friday', '19:00:00', 55, 15);

-- --------------------------------------------------------

--
-- Структура таблицы `trainers`
--

CREATE TABLE `trainers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bio` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `instagram` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `trainers`
--

INSERT INTO `trainers` (`id`, `name`, `photo`, `bio`, `instagram`) VALUES
(1, 'Алия Залалутдинова', 'trainer_1772569411.jpg', 'Основательница студии. Hip-Hop PRO 12+ и Girly Hip-Hop. Стаж 10 лет. Руководитель шоу-группы.', 'liaaliy'),
(2, 'Екатерина Волкова', 'trainer_1772569027.jpg', 'Contemporary и Stretching. Хореограф, работа с эмоциями и телом. Стаж 7 лет.', 'katya_volkova'),
(3, 'Алина Сафина', 'trainer_1772568861.jpg', 'High Heels и Jazz-Funk. Танцует 8 лет, училась в Москве и Европе.', 'alina_safina'),
(4, 'Диана Хисамова', 'trainer_1772568712.jpg', 'Jazz-Funk (продвинутый уровень) и Contemporary. Артистка, участница шоу "Танцы на ТНТ".', 'diana_his'),
(5, 'Рената Хакимова', 'trainer_1772569583.jpg', 'Только Stretching. Мастер спорта по художественной гимнастике.', 'renata_hakimova');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` enum('user','admin') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `awaiting_payment` tinyint DEFAULT '0',
  `selected_plan_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `role`, `created_at`, `awaiting_payment`, `selected_plan_id`) VALUES
(8, 'Алия', 'koleso@mail.ru', '$2y$10$fn6RhLUl1GieuNXSkv0.fe2ezJRM5K4S.c2qzy5outj.Qnnf11jWe', '+7 (950) 176-48-24', 'user', '2026-02-24 09:07:23', 0, NULL),
(10, 'Администратор', 'admin@dance.ru', '$2y$10$4NjNhUM.mDKS1lLPlxD1d.KeMj1.7gxyWWTvgfaqPVN2RnD.b.wl.', NULL, 'admin', '2026-03-03 18:46:36', 0, NULL),
(11, 'София', 'gendalflovebilbo@mail.ru', '$2y$10$M.2cRUZmoKwhGBEZzgwFAOA8c3XeCLKWQ.JsmwfTEzuMxZz3V2S4.', '+7 (983) 456-72-34', 'user', '2026-03-03 20:28:46', 0, NULL),
(12, 'Желдубкина Елизавета', 'darya@inbox.ru', '$2y$10$w6/dsYcMKvIX9i46RP1s0.J0VZe7I4Ok8c38zDeoKEIjjPrguXVq.', '+7 (952) 405-28-07', 'user', '2026-03-04 11:59:07', 0, NULL),
(13, 'Алия', 'aliytora@inbox.ru', '$2y$10$DTISSnGDeN9SLbuJscpi2.gSAR3hMT43AupRW/YwuluWfmE77uKja', '+7 (950) 176-48-24', 'user', '2026-03-05 10:01:24', 0, NULL),
(14, 'Алия', 'aliytora23@inbox.ru', '$2y$10$pvqSvJOZliLsKWDUW4fRpOCgZULIiD2Ki1DzWS/qVkVs/LXuEk9u2', '+7 (950) 176-48-24', 'user', '2026-03-05 10:02:43', 0, NULL),
(15, 'Алиса', 'aff1978@inbox.ru', '$2y$10$Cd96Hklnvqxw/9haTkhe9.ojlhPfsPYAtsoNauyQSDHNLeP1rsohO', '+7 (950) 154-17-58', 'user', '2026-03-05 12:10:39', 0, NULL);

--
-- Ограничения внешнего ключа
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`schedule_id`) REFERENCES `schedules` (`id`) ON DELETE CASCADE;

ALTER TABLE `memberships`
  ADD CONSTRAINT `memberships_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `membership_logs`
  ADD CONSTRAINT `membership_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `membership_logs_ibfk_2` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE SET NULL;

ALTER TABLE `schedules`
  ADD CONSTRAINT `schedules_ibfk_1` FOREIGN KEY (`trainer_id`) REFERENCES `trainers` (`id`) ON DELETE SET NULL;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;