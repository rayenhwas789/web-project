-- NEXUS Esports — Database Schema
-- Import via phpMyAdmin: Database > Import > choose this file

CREATE DATABASE IF NOT EXISTS `nexus_esports`
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `nexus_esports`;

-- ─────────────────────────────
-- Users
-- ─────────────────────────────
CREATE TABLE IF NOT EXISTS `users` (
    `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `username`   VARCHAR(50)  NOT NULL,
    `email`      VARCHAR(120) NOT NULL UNIQUE,
    `password`   VARCHAR(255) NOT NULL COMMENT 'bcrypt hash',
    `country`    VARCHAR(10)  DEFAULT '',
    `bio`        TEXT         DEFAULT '',
    `skill_level` TINYINT UNSIGNED DEFAULT 0,
    `role`       ENUM('user','admin') DEFAULT 'user',
    `status`     ENUM('active','banned','pending') DEFAULT 'active',
    `joined_at`  DATETIME     DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (`email`),
    INDEX idx_role  (`role`)
) ENGINE=InnoDB;

-- ─────────────────────────────
-- Products / Merch
-- ─────────────────────────────
CREATE TABLE IF NOT EXISTS `products` (
    `id`        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name`      VARCHAR(120) NOT NULL,
    `category`  VARCHAR(60)  DEFAULT '',
    `price`     DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `stock`     INT UNSIGNED  DEFAULT 0,
    `image_url` VARCHAR(255)  DEFAULT '',
    `created_at` DATETIME     DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ─────────────────────────────
-- Bookings / Tournament registrations
-- ─────────────────────────────
CREATE TABLE IF NOT EXISTS `bookings` (
    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id`     INT UNSIGNED,
    `name`        VARCHAR(100) NOT NULL,
    `email`       VARCHAR(120) NOT NULL,
    `tournament`  VARCHAR(100) DEFAULT '',
    `game`        VARCHAR(60)  DEFAULT '',
    `team`        VARCHAR(100) DEFAULT '',
    `booked_at`   DATETIME     DEFAULT CURRENT_TIMESTAMP,
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
    `qty`        INT UNSIGNED DEFAULT 1,
    `price`      DECIMAL(10,2) NOT NULL,
    `status`     ENUM('processing','shipped','delivered','cancelled') DEFAULT 'processing',
    `ordered_at` DATETIME     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE SET NULL,
    INDEX idx_email (`user_email`)
) ENGINE=InnoDB;

-- ─────────────────────────────
-- Seed: default admin account
-- password = "admin123" (bcrypt)
-- ─────────────────────────────
INSERT IGNORE INTO `users` (`username`,`email`,`password`,`role`,`status`)
VALUES (
    'admin',
    'admin@nexus.gg',
    '$2y$12$EixZaYVK1fsbw1ZfbX3OXePaWxn96p36WQoeG6Lruj3vjPGga31lW',
    'admin',
    'active'
);
