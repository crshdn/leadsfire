-- LeadsFire Click Tracker - Clean Schema
-- Version: 1.0.0
-- Designed for simplicity, performance, and ML-readiness

SET FOREIGN_KEY_CHECKS=0;
SET NAMES utf8mb4;

-- ============================================
-- CORE TABLES
-- ============================================

-- Users table
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(100) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'user', 'viewer') NOT NULL DEFAULT 'user',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `last_login` DATETIME NULL,
    `login_attempts` TINYINT UNSIGNED NOT NULL DEFAULT 0,
    `locked_until` DATETIME NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_username` (`username`),
    UNIQUE KEY `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Traffic Sources (where traffic comes from)
DROP TABLE IF EXISTS `traffic_sources`;
CREATE TABLE `traffic_sources` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(50) NOT NULL,
    `postback_url` VARCHAR(500) NULL,
    `cost_param` VARCHAR(50) NULL DEFAULT 'cost',
    `external_id_param` VARCHAR(50) NULL DEFAULT 'clickid',
    `tokens` JSON NULL COMMENT 'Available URL tokens for this source',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Affiliate Networks (where offers come from)
DROP TABLE IF EXISTS `affiliate_networks`;
CREATE TABLE `affiliate_networks` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(50) NOT NULL,
    `postback_url` VARCHAR(500) NULL,
    `revenue_param` VARCHAR(50) NULL DEFAULT 'payout',
    `subid_param` VARCHAR(50) NULL DEFAULT 'subid',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Landing Pages (predefined landing pages)
DROP TABLE IF EXISTS `landing_pages`;
CREATE TABLE `landing_pages` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(200) NOT NULL,
    `url` VARCHAR(500) NOT NULL,
    `group_id` INT UNSIGNED NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `notes` TEXT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_group` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Offers (predefined offers)
DROP TABLE IF EXISTS `offers`;
CREATE TABLE `offers` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(200) NOT NULL,
    `url` VARCHAR(500) NOT NULL,
    `affiliate_network_id` INT UNSIGNED NULL,
    `payout` DECIMAL(10,4) NOT NULL DEFAULT 0,
    `payout_type` ENUM('fixed', 'percent', 'dynamic') NOT NULL DEFAULT 'fixed',
    `group_id` INT UNSIGNED NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `notes` TEXT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_network` (`affiliate_network_id`),
    KEY `idx_group` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Campaigns
DROP TABLE IF EXISTS `campaigns`;
CREATE TABLE `campaigns` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(200) NOT NULL,
    `key_code` VARCHAR(32) NOT NULL COMMENT 'Unique tracking key',
    `traffic_source_id` INT UNSIGNED NULL,
    `cost_model` ENUM('none', 'cpc', 'cpm', 'cpa', 'revshare', 'auto') NOT NULL DEFAULT 'none',
    `cost_value` DECIMAL(10,4) NOT NULL DEFAULT 0,
    `redirect_type` SMALLINT NOT NULL DEFAULT 302,
    `rotation_type` ENUM('probabilistic', 'sequential') NOT NULL DEFAULT 'probabilistic',
    `engage_seconds` SMALLINT UNSIGNED NOT NULL DEFAULT 3 COMMENT 'Seconds before counting as engaged',
    `tracking_domain` VARCHAR(255) NULL,
    `group_id` INT UNSIGNED NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `notes` TEXT NULL,
    `created_by` INT UNSIGNED NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_key_code` (`key_code`),
    KEY `idx_traffic_source` (`traffic_source_id`),
    KEY `idx_group` (`group_id`),
    KEY `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Campaign Paths (landing pages and offers assigned to campaigns)
DROP TABLE IF EXISTS `campaign_paths`;
CREATE TABLE `campaign_paths` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `campaign_id` INT UNSIGNED NOT NULL,
    `path_type` ENUM('landing', 'offer') NOT NULL,
    `landing_page_id` INT UNSIGNED NULL,
    `offer_id` INT UNSIGNED NULL,
    `direct_url` VARCHAR(500) NULL COMMENT 'Direct URL if not using predefined LP/offer',
    `weight` SMALLINT UNSIGNED NOT NULL DEFAULT 100 COMMENT 'Rotation weight (0-100)',
    `position` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_campaign` (`campaign_id`),
    KEY `idx_type` (`path_type`),
    KEY `idx_active` (`campaign_id`, `is_active`, `path_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TRACKING TABLES (Optimized for high volume)
-- ============================================

-- Clicks (main tracking table)
DROP TABLE IF EXISTS `clicks`;
CREATE TABLE `clicks` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `campaign_id` INT UNSIGNED NOT NULL,
    `click_id` VARCHAR(32) NOT NULL COMMENT 'Public click ID (base62 encoded)',
    `subid` VARCHAR(100) NULL COMMENT 'SubID/keyword from traffic source',
    `landing_page_id` INT UNSIGNED NULL,
    `offer_id` INT UNSIGNED NULL,
    `ip` VARBINARY(16) NOT NULL COMMENT 'IPv4/IPv6 in binary',
    `user_agent` VARCHAR(500) NULL,
    `referrer` VARCHAR(500) NULL,
    `country` CHAR(2) NULL,
    `region` VARCHAR(100) NULL,
    `city` VARCHAR(100) NULL,
    `device_type` ENUM('desktop', 'mobile', 'tablet', 'unknown') NULL,
    `browser` VARCHAR(50) NULL,
    `os` VARCHAR(50) NULL,
    `cost` DECIMAL(10,4) NULL,
    `revenue` DECIMAL(10,4) NULL,
    `is_unique` TINYINT(1) NOT NULL DEFAULT 1,
    `is_bot` TINYINT(1) NOT NULL DEFAULT 0,
    `view_time` DATETIME NOT NULL COMMENT 'When click was received',
    `engage_time` DATETIME NULL COMMENT 'When visitor engaged (stayed X seconds)',
    `action_time` DATETIME NULL COMMENT 'When visitor clicked through to offer',
    `conversion_time` DATETIME NULL COMMENT 'When conversion was recorded',
    `extra1` VARCHAR(100) NULL,
    `extra2` VARCHAR(100) NULL,
    `extra3` VARCHAR(100) NULL,
    `extra4` VARCHAR(100) NULL,
    `extra5` VARCHAR(100) NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_click_id` (`click_id`),
    KEY `idx_campaign_time` (`campaign_id`, `view_time`),
    KEY `idx_conversion` (`campaign_id`, `conversion_time`),
    KEY `idx_subid` (`campaign_id`, `subid`),
    KEY `idx_ip` (`ip`, `campaign_id`, `view_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Conversions (detailed conversion data)
DROP TABLE IF EXISTS `conversions`;
CREATE TABLE `conversions` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `click_id` BIGINT UNSIGNED NOT NULL,
    `campaign_id` INT UNSIGNED NOT NULL,
    `transaction_id` VARCHAR(100) NULL COMMENT 'External transaction ID',
    `revenue` DECIMAL(10,4) NOT NULL DEFAULT 0,
    `status` ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'approved',
    `payout_type` VARCHAR(50) NULL,
    `ip` VARBINARY(16) NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_click` (`click_id`),
    KEY `idx_campaign_time` (`campaign_id`, `created_at`),
    KEY `idx_transaction` (`transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- CACHE TABLES (For fast dashboard/reports)
-- ============================================

-- Daily stats cache
DROP TABLE IF EXISTS `stats_daily`;
CREATE TABLE `stats_daily` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `campaign_id` INT UNSIGNED NOT NULL,
    `date` DATE NOT NULL,
    `views` INT UNSIGNED NOT NULL DEFAULT 0,
    `unique_views` INT UNSIGNED NOT NULL DEFAULT 0,
    `engagements` INT UNSIGNED NOT NULL DEFAULT 0,
    `actions` INT UNSIGNED NOT NULL DEFAULT 0,
    `conversions` INT UNSIGNED NOT NULL DEFAULT 0,
    `cost` DECIMAL(12,4) NOT NULL DEFAULT 0,
    `revenue` DECIMAL(12,4) NOT NULL DEFAULT 0,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_campaign_date` (`campaign_id`, `date`),
    KEY `idx_date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SubID stats cache
DROP TABLE IF EXISTS `stats_subid`;
CREATE TABLE `stats_subid` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `campaign_id` INT UNSIGNED NOT NULL,
    `subid` VARCHAR(100) NOT NULL,
    `date` DATE NOT NULL,
    `views` INT UNSIGNED NOT NULL DEFAULT 0,
    `conversions` INT UNSIGNED NOT NULL DEFAULT 0,
    `cost` DECIMAL(12,4) NOT NULL DEFAULT 0,
    `revenue` DECIMAL(12,4) NOT NULL DEFAULT 0,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_campaign_subid_date` (`campaign_id`, `subid`, `date`),
    KEY `idx_date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- CONFIGURATION TABLES
-- ============================================

-- App configuration
DROP TABLE IF EXISTS `config`;
CREATE TABLE `config` (
    `key` VARCHAR(100) NOT NULL,
    `value` TEXT NULL,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Groups (for organizing campaigns, LPs, offers)
DROP TABLE IF EXISTS `groups`;
CREATE TABLE `groups` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `type` ENUM('campaign', 'landing_page', 'offer') NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Custom domains
DROP TABLE IF EXISTS `custom_domains`;
CREATE TABLE `custom_domains` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `domain` VARCHAR(255) NOT NULL,
    `is_verified` TINYINT(1) NOT NULL DEFAULT 0,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_domain` (`domain`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- INITIAL DATA
-- ============================================

-- Default config
INSERT INTO `config` (`key`, `value`) VALUES
('app_name', 'LeadsFire Click Tracker'),
('timezone', 'America/New_York'),
('dedup_seconds', '0'),
('cookie_timeout', '2592000'),
('conversion_window_days', '30'),
('data_retention_days', '730');

SET FOREIGN_KEY_CHECKS=1;
