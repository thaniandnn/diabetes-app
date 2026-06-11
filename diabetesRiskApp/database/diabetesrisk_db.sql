-- ============================================================
-- DiabetesRisk Database Schema
-- Database: diabetesrisk_db
-- ============================================================

CREATE DATABASE IF NOT EXISTS `diabetesrisk_db`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `diabetesrisk_db`;

-- ============================================================
-- Table: users
-- ============================================================
CREATE TABLE IF NOT EXISTS `users` (
  `id`         INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `nama`       VARCHAR(100)     NOT NULL,
  `email`      VARCHAR(150)     NOT NULL UNIQUE,
  `password`   VARCHAR(255)     NOT NULL,
  `created_at` TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: predictions
-- ============================================================
CREATE TABLE IF NOT EXISTS `predictions` (
  `id`                        INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `user_id`                   INT UNSIGNED    NOT NULL,
  `pregnancies`               TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `glucose`                   SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `blood_pressure`            SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `skin_thickness`            SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `insulin`                   SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `bmi`                       DECIMAL(5,2)    NOT NULL DEFAULT 0.00,
  `diabetes_pedigree_function` DECIMAL(6,4)   NOT NULL DEFAULT 0.0000,
  `age`                       TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `prediction_result`         ENUM('Diabetes','No Diabetes') NOT NULL,
  `risk_level`                ENUM('Tinggi','Rendah')        NOT NULL,
  `created_at`                TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_predictions_user`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Demo user (password: admin123)
-- ============================================================
INSERT INTO `users` (`nama`, `email`, `password`) VALUES
('Administrator', 'admin@diabetesrisk.local',
 '$2y$12$eW5.l5a1IiZ6HkBp0nK.BO7EfEQJK7NQb4E3LiEuHCzB7s9YxN5pC');
