-- ════════════════════════════════════════════════════════════
-- NEXUS Esports — Full Database Schema + Seed Data
-- Import via phpMyAdmin: Database > Import > choose this file
-- ════════════════════════════════════════════════════════════

CREATE DATABASE IF NOT EXISTS `nexus_esports`
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `nexus_esports`;

-- ─────────────────────────────
-- Users
-- ─────────────────────────────
CREATE TABLE IF NOT EXISTS `users` (
    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `username`    VARCHAR(50)  NOT NULL,
    `email`       VARCHAR(120) NOT NULL UNIQUE,
    `password`    VARCHAR(255) NOT NULL COMMENT 'bcrypt hash',
    `country`     VARCHAR(10)  DEFAULT '',
    `bio`         TEXT         DEFAULT '',
    `skill_level` TINYINT UNSIGNED DEFAULT 0,
    `role`        ENUM('user','admin') DEFAULT 'user',
    `status`      ENUM('active','banned','pending') DEFAULT 'active',
    `joined_at`   DATETIME     DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (`email`),
    INDEX idx_role  (`role`)
) ENGINE=InnoDB;

-- ─────────────────────────────
-- Products / Merch
-- ─────────────────────────────
CREATE TABLE IF NOT EXISTS `products` (
    `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name`       VARCHAR(120)  NOT NULL,
    `category`   VARCHAR(60)   DEFAULT '',
    `price`      DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `stock`      INT UNSIGNED  DEFAULT 0,
    `image_url`  VARCHAR(255)  DEFAULT '',
    `created_at` DATETIME      DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ─────────────────────────────
-- Bookings / Tournament registrations
-- ─────────────────────────────
CREATE TABLE IF NOT EXISTS `bookings` (
    `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id`    INT UNSIGNED,
    `name`       VARCHAR(100) NOT NULL,
    `email`      VARCHAR(120) NOT NULL,
    `tournament` VARCHAR(100) DEFAULT '',
    `game`       VARCHAR(60)  DEFAULT '',
    `team`       VARCHAR(100) DEFAULT '',
    `booked_at`  DATETIME     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ─────────────────────────────
-- Orders
-- ─────────────────────────────
CREATE TABLE IF NOT EXISTS `orders` (
    `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_email` VARCHAR(120) NOT NULL,
    `product_id` INT UNSIGNED,
    `item`       VARCHAR(120) NOT NULL,
    `qty`        INT UNSIGNED  DEFAULT 1,
    `price`      DECIMAL(10,2) NOT NULL,
    `status`     ENUM('processing','shipped','delivered','cancelled') DEFAULT 'processing',
    `ordered_at` DATETIME      DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE SET NULL,
    INDEX idx_email (`user_email`)
) ENGINE=InnoDB;


-- ════════════════════════════════════════════════════════════
-- SEED DATA
-- ════════════════════════════════════════════════════════════

-- ─────────────────────────────
-- Admin + Demo Users
-- password = "admin123"  (bcrypt)
-- password = "nexus123"  (bcrypt)
-- ─────────────────────────────
INSERT IGNORE INTO `users` (`username`,`email`,`password`,`country`,`bio`,`skill_level`,`role`,`status`,`joined_at`) VALUES
('admin',       'admin@nexus.gg',       '$2y$12$EixZaYVK1fsbw1ZfbX3OXePaWxn96p36WQoeG6Lruj3vjPGga31lW', 'US', 'Platform administrator.', 10, 'admin', 'active', '2024-01-01 09:00:00'),
('donk_fan',    'demo@nexus.gg',        '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/VG.fyaWJyzf4wnfO2', 'RU', 'CS2 enthusiast. Big donk fan.', 7, 'user', 'active', '2024-03-15 14:22:00'),
('FakerMVP',    'faker@nexus.gg',       '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/VG.fyaWJyzf4wnfO2', 'KR', 'T1 supporter. Mid or feed.', 9, 'user', 'active', '2024-04-02 11:05:00'),
('AspruzX',     'aspruz@nexus.gg',      '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/VG.fyaWJyzf4wnfO2', 'BR', 'Valorant Radiant. MIBR forever.', 8, 'user', 'active', '2024-04-10 18:30:00'),
('MongraalLFT', 'mongraal@nexus.gg',    '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/VG.fyaWJyzf4wnfO2', 'GB', 'Fortnite pro player wannabe.', 8, 'user', 'active', '2024-05-01 09:15:00'),
('zywoo_2nd',   'zywoo@nexus.gg',       '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/VG.fyaWJyzf4wnfO2', 'FR', 'CS2 IGL. Vitality stan.', 7, 'user', 'active', '2024-05-20 16:45:00'),
('NightOwlGG',  'nightowl@nexus.gg',    '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/VG.fyaWJyzf4wnfO2', 'DE', 'G2 esports fan. League main.', 6, 'user', 'active', '2024-06-08 23:10:00'),
('TaysonStan',  'tayson@nexus.gg',      '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/VG.fyaWJyzf4wnfO2', 'SI', 'Fortnite world cup hopeful.', 9, 'user', 'active', '2024-06-22 13:00:00'),
('KeriaSUPP',   'keria@nexus.gg',       '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/VG.fyaWJyzf4wnfO2', 'KR', 'Support main. Worlds 3x winner.', 9, 'user', 'active', '2024-07-04 07:30:00'),
('FragKing99',  'fragking@nexus.gg',    '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/VG.fyaWJyzf4wnfO2', 'US', 'Top fragger in every game.', 8, 'user', 'active', '2024-07-18 20:00:00'),
('PixelHunter', 'pixel@nexus.gg',       '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/VG.fyaWJyzf4wnfO2', 'CA', 'Casual gamer turned competitive.', 5, 'user', 'active', '2024-08-01 11:00:00'),
('SentinelsHQ', 'sentinels@nexus.gg',   '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/VG.fyaWJyzf4wnfO2', 'US', 'Sentinels org fan account.', 6, 'user', 'active', '2024-08-14 15:30:00'),
('sh1roBot',    'sh1ro@nexus.gg',       '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/VG.fyaWJyzf4wnfO2', 'RU', 'Cloud9 Blue mega fan.', 8, 'user', 'active', '2024-09-05 09:45:00'),
('ProSpector',  'prospec@nexus.gg',     '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/VG.fyaWJyzf4wnfO2', 'NO', 'Scouts talent for fun.', 7, 'user', 'pending', '2024-09-19 17:20:00'),
('GhostAim',    'ghost@nexus.gg',       '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/VG.fyaWJyzf4wnfO2', 'PL', 'Insane aim, zero game sense.', 8, 'user', 'active', '2024-10-02 12:00:00');

-- ─────────────────────────────
-- Products
-- ─────────────────────────────
INSERT IGNORE INTO `products` (`name`,`category`,`price`,`stock`,`image_url`,`created_at`) VALUES
('NEXUS Pro Gaming Mouse',          'Peripherals',  59.99,  120, 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=400', '2024-02-01 10:00:00'),
('NEXUS Mechanical Keyboard TKL',   'Peripherals',  89.99,   85, 'https://images.unsplash.com/photo-1601445638532-1f42e25c9ae0?w=400', '2024-02-05 10:00:00'),
('NEXUS 240Hz Gaming Monitor 27"',  'Hardware',    299.99,   30, 'https://images.unsplash.com/photo-1593640408182-31c228b1de5b?w=400', '2024-02-10 10:00:00'),
('NEXUS Esports Jersey – Falcon',   'Apparel',      44.99,  200, 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=400', '2024-02-15 10:00:00'),
('NEXUS Esports Jersey – Sentinel', 'Apparel',      44.99,  180, 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=400', '2024-02-15 10:00:00'),
('NEXUS Logo Hoodie Black',         'Apparel',      64.99,  150, 'https://images.unsplash.com/photo-1556821840-3a63f15732ce?w=400', '2024-03-01 10:00:00'),
('NEXUS 7.1 Gaming Headset',        'Peripherals',  79.99,   60, 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400', '2024-03-10 10:00:00'),
('NEXUS XXL Desk Pad',              'Accessories',  29.99,  300, 'https://images.unsplash.com/photo-1593642632559-0c6d3fc62b89?w=400', '2024-03-15 10:00:00'),
('NEXUS Streamer Webcam 4K',        'Hardware',    119.99,   45, 'https://images.unsplash.com/photo-1579829366248-204fe8413f31?w=400', '2024-04-01 10:00:00'),
('NEXUS VIP Tournament Pass – S1',  'Passes',       24.99,  500, 'https://images.unsplash.com/photo-1542751371-adc38448a05e?w=400', '2024-04-10 10:00:00'),
('NEXUS Cap – Camo Edition',        'Apparel',      19.99,  250, 'https://images.unsplash.com/photo-1588850561407-ed78c282e89b?w=400', '2024-04-20 10:00:00'),
('NEXUS Pro Controller Stand',      'Accessories',  14.99,  400, 'https://images.unsplash.com/photo-1493711662062-fa541adb3fc8?w=400', '2024-05-01 10:00:00'),
('NEXUS Sticker Pack Vol.1',        'Accessories',   6.99, 1000, 'https://images.unsplash.com/photo-1572375992501-4b0892d50c69?w=400', '2024-05-10 10:00:00'),
('NEXUS Gaming Chair Pro',          'Hardware',    349.99,   15, 'https://images.unsplash.com/photo-1598550476439-6847785fcea6?w=400', '2024-05-20 10:00:00'),
('NEXUS Water Bottle 32oz',         'Accessories',  12.99,  600, 'https://images.unsplash.com/photo-1536939459926-301728717817?w=400', '2024-06-01 10:00:00');

-- ─────────────────────────────
-- Bookings
-- ─────────────────────────────
INSERT INTO `bookings` (`user_id`,`name`,`email`,`tournament`,`game`,`team`,`booked_at`) VALUES
(2,  'Alex Petrov',      'demo@nexus.gg',        'NEXUS Spring Cup 2024',         'CS2',       'Cloud9 Blue',    '2024-03-20 10:15:00'),
(3,  'Kim Sang-hyeok',   'faker@nexus.gg',        'NEXUS Spring Cup 2024',         'League of Legends', 'T1',  '2024-03-21 11:00:00'),
(4,  'Felipe Andrade',   'aspruz@nexus.gg',       'NEXUS Spring Cup 2024',         'Valorant',  'MIBR',           '2024-03-22 09:30:00'),
(5,  'Kyle Jackson',     'mongraal@nexus.gg',     'NEXUS Spring Cup 2024',         'Fortnite',  'Red Bull Esports','2024-03-22 14:00:00'),
(6,  'Marc Dumont',      'zywoo@nexus.gg',        'NEXUS Spring Cup 2024',         'CS2',       'Team Vitality',  '2024-03-23 16:20:00'),
(7,  'Leon Müller',      'nightowl@nexus.gg',     'NEXUS Spring Cup 2024',         'League of Legends', 'G2 Esports','2024-03-24 08:45:00'),
(8,  'Jonas Petersen',   'tayson@nexus.gg',       'NEXUS Summer Invitational',     'Fortnite',  'Falcons',        '2024-06-01 10:00:00'),
(9,  'Park Jun-sik',     'keria@nexus.gg',        'NEXUS Summer Invitational',     'League of Legends', 'T1',  '2024-06-02 12:00:00'),
(10, 'Jake Morrison',    'fragking@nexus.gg',     'NEXUS Summer Invitational',     'Valorant',  'Sentinels',      '2024-06-03 15:30:00'),
(11, 'Chris Lee',        'pixel@nexus.gg',        'NEXUS Summer Invitational',     'CS2',       'Solo',           '2024-06-04 09:00:00'),
(12, 'Tyler Rhodes',     'sentinels@nexus.gg',    'NEXUS Summer Invitational',     'Valorant',  'Sentinels',      '2024-06-04 11:45:00'),
(13, 'Ivan Sorokin',     'sh1ro@nexus.gg',        'NEXUS World Open 2024',         'CS2',       'Cloud9 Blue',    '2024-09-10 10:00:00'),
(14, 'Oscar Hansen',     'prospec@nexus.gg',      'NEXUS World Open 2024',         'Fortnite',  'Solo',           '2024-09-11 13:00:00'),
(15, 'Piotr Kowalski',   'ghost@nexus.gg',        'NEXUS World Open 2024',         'CS2',       'Falcons',        '2024-09-12 09:30:00'),
(2,  'Alex Petrov',      'demo@nexus.gg',         'NEXUS World Open 2024',         'CS2',       'Cloud9 Blue',    '2024-09-13 14:00:00'),
(4,  'Felipe Andrade',   'aspruz@nexus.gg',       'NEXUS World Open 2024',         'Valorant',  'MIBR',           '2024-09-14 16:00:00'),
(10, 'Jake Morrison',    'fragking@nexus.gg',     'NEXUS Fall Clash 2024',         'Valorant',  'Sentinels',      '2024-11-01 10:00:00'),
(3,  'Kim Sang-hyeok',   'faker@nexus.gg',        'NEXUS Fall Clash 2024',         'League of Legends', 'T1',  '2024-11-02 11:00:00'),
(6,  'Marc Dumont',      'zywoo@nexus.gg',        'NEXUS Fall Clash 2024',         'CS2',       'Team Vitality',  '2024-11-03 09:00:00'),
(8,  'Jonas Petersen',   'tayson@nexus.gg',       'NEXUS Fall Clash 2024',         'Fortnite',  'Falcons',        '2024-11-04 14:30:00'),
(NULL,'Sarah Connor',    'sarah@gamer.com',       'NEXUS Fall Clash 2024',         'Valorant',  'Solo',           '2024-11-05 17:00:00'),
(NULL,'Marcus Webb',     'marcus@gamer.com',      'NEXUS Winter Championship',     'CS2',       'Any',            '2025-01-10 08:00:00'),
(NULL,'Yuki Tanaka',     'yuki@gamer.com',        'NEXUS Winter Championship',     'League of Legends', 'Any', '2025-01-11 12:00:00'),
(NULL,'Ahmed Hassan',    'ahmed@gamer.com',       'NEXUS Winter Championship',     'Valorant',  'Any',            '2025-01-12 15:00:00'),
(NULL,'Emma Clarke',     'emma@gamer.com',        'NEXUS Winter Championship',     'Fortnite',  'Any',            '2025-01-13 10:30:00');

-- ─────────────────────────────
-- Orders
-- ─────────────────────────────
INSERT INTO `orders` (`user_email`,`product_id`,`item`,`qty`,`price`,`status`,`ordered_at`) VALUES
('faker@nexus.gg',      3,  'NEXUS 240Hz Gaming Monitor 27"',  1, 299.99, 'delivered',  '2024-04-05 10:00:00'),
('aspruz@nexus.gg',     1,  'NEXUS Pro Gaming Mouse',          1,  59.99, 'delivered',  '2024-04-06 11:00:00'),
('mongraal@nexus.gg',   4,  'NEXUS Esports Jersey – Falcon',   2,  44.99, 'delivered',  '2024-04-07 12:00:00'),
('zywoo@nexus.gg',      2,  'NEXUS Mechanical Keyboard TKL',   1,  89.99, 'delivered',  '2024-04-08 09:30:00'),
('demo@nexus.gg',       7,  'NEXUS 7.1 Gaming Headset',        1,  79.99, 'delivered',  '2024-04-10 14:00:00'),
('nightowl@nexus.gg',   8,  'NEXUS XXL Desk Pad',              1,  29.99, 'delivered',  '2024-04-12 16:00:00'),
('tayson@nexus.gg',     4,  'NEXUS Esports Jersey – Falcon',   1,  44.99, 'delivered',  '2024-05-03 10:00:00'),
('keria@nexus.gg',      6,  'NEXUS Logo Hoodie Black',         1,  64.99, 'delivered',  '2024-05-10 11:00:00'),
('fragking@nexus.gg',   9,  'NEXUS Streamer Webcam 4K',        1, 119.99, 'delivered',  '2024-05-15 13:00:00'),
('pixel@nexus.gg',      10, 'NEXUS VIP Tournament Pass – S1',  2,  24.99, 'delivered',  '2024-05-18 09:00:00'),
('sentinels@nexus.gg',  5,  'NEXUS Esports Jersey – Sentinel', 3,  44.99, 'delivered',  '2024-05-20 15:30:00'),
('sh1ro@nexus.gg',      1,  'NEXUS Pro Gaming Mouse',          1,  59.99, 'shipped',    '2024-06-01 10:00:00'),
('ghost@nexus.gg',      2,  'NEXUS Mechanical Keyboard TKL',   1,  89.99, 'shipped',    '2024-06-05 11:00:00'),
('faker@nexus.gg',      14, 'NEXUS Gaming Chair Pro',          1, 349.99, 'delivered',  '2024-06-10 14:00:00'),
('aspruz@nexus.gg',     11, 'NEXUS Cap – Camo Edition',        2,  19.99, 'delivered',  '2024-06-12 09:00:00'),
('demo@nexus.gg',       13, 'NEXUS Sticker Pack Vol.1',        3,   6.99, 'delivered',  '2024-06-15 10:00:00'),
('zywoo@nexus.gg',      12, 'NEXUS Pro Controller Stand',      1,  14.99, 'delivered',  '2024-07-01 12:00:00'),
('fragking@nexus.gg',   1,  'NEXUS Pro Gaming Mouse',          1,  59.99, 'delivered',  '2024-07-05 08:30:00'),
('nightowl@nexus.gg',   6,  'NEXUS Logo Hoodie Black',         2,  64.99, 'shipped',    '2024-07-10 17:00:00'),
('pixel@nexus.gg',      15, 'NEXUS Water Bottle 32oz',         2,  12.99, 'delivered',  '2024-07-12 09:00:00'),
('tayson@nexus.gg',     9,  'NEXUS Streamer Webcam 4K',        1, 119.99, 'processing', '2024-08-01 11:00:00'),
('keria@nexus.gg',      3,  'NEXUS 240Hz Gaming Monitor 27"',  1, 299.99, 'processing', '2024-08-05 14:00:00'),
('sentinels@nexus.gg',  10, 'NEXUS VIP Tournament Pass – S1',  5,  24.99, 'delivered',  '2024-08-10 10:00:00'),
('mongraal@nexus.gg',   7,  'NEXUS 7.1 Gaming Headset',        1,  79.99, 'shipped',    '2024-08-15 13:00:00'),
('sh1ro@nexus.gg',      8,  'NEXUS XXL Desk Pad',              1,  29.99, 'delivered',  '2024-08-20 09:30:00'),
('ghost@nexus.gg',      13, 'NEXUS Sticker Pack Vol.1',        5,   6.99, 'delivered',  '2024-09-01 10:00:00'),
('faker@nexus.gg',      6,  'NEXUS Logo Hoodie Black',         1,  64.99, 'delivered',  '2024-09-05 12:00:00'),
('aspruz@nexus.gg',     7,  'NEXUS 7.1 Gaming Headset',        1,  79.99, 'cancelled',  '2024-09-10 15:00:00'),
('demo@nexus.gg',       4,  'NEXUS Esports Jersey – Falcon',   1,  44.99, 'shipped',    '2024-09-15 11:00:00'),
('fragking@nexus.gg',   14, 'NEXUS Gaming Chair Pro',          1, 349.99, 'processing', '2024-09-20 09:00:00'),
('zywoo@nexus.gg',      1,  'NEXUS Pro Gaming Mouse',          2,  59.99, 'shipped',    '2024-10-01 10:00:00'),
('nightowl@nexus.gg',   11, 'NEXUS Cap – Camo Edition',        1,  19.99, 'delivered',  '2024-10-05 14:00:00'),
('pixel@nexus.gg',      5,  'NEXUS Esports Jersey – Sentinel', 2,  44.99, 'processing', '2024-10-10 16:00:00'),
('sarah@gamer.com',     10, 'NEXUS VIP Tournament Pass – S1',  1,  24.99, 'delivered',  '2024-10-15 10:00:00'),
('marcus@gamer.com',    2,  'NEXUS Mechanical Keyboard TKL',   1,  89.99, 'shipped',    '2024-10-20 11:00:00'),
('yuki@gamer.com',      1,  'NEXUS Pro Gaming Mouse',          1,  59.99, 'processing', '2024-10-25 09:00:00'),
('ahmed@gamer.com',     8,  'NEXUS XXL Desk Pad',              2,  29.99, 'delivered',  '2024-11-01 13:00:00'),
('emma@gamer.com',      13, 'NEXUS Sticker Pack Vol.1',        2,   6.99, 'delivered',  '2024-11-05 10:00:00'),
('keria@nexus.gg',      15, 'NEXUS Water Bottle 32oz',         1,  12.99, 'processing', '2024-11-10 08:00:00'),
('mongraal@nexus.gg',   6,  'NEXUS Logo Hoodie Black',         1,  64.99, 'processing', '2024-11-12 12:00:00');
