-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 07, 2025 at 05:38 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mdagang`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_name` varchar(191) DEFAULT NULL,
  `user_role` varchar(191) DEFAULT NULL,
  `action_type` enum('create','update','delete','login','logout','view','export','import') NOT NULL,
  `module` varchar(191) NOT NULL,
  `model_type` varchar(191) DEFAULT NULL,
  `model_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description` text NOT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `changes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`changes`)),
  `ip_address` varchar(191) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `risk_level` enum('low','medium','high','critical') NOT NULL DEFAULT 'low',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `user_name`, `user_role`, `action_type`, `module`, `model_type`, `model_id`, `description`, `old_values`, `new_values`, `changes`, `ip_address`, `user_agent`, `risk_level`, `created_at`, `updated_at`) VALUES
(1, 1, 'Administrator', 'administrator', 'login', 'users', 'App\\Models\\User', 1, 'User logged in', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'low', '2025-08-30 02:01:54', '2025-08-30 02:01:54'),
(2, 1, 'Administrator', 'administrator', 'login', 'users', 'App\\Models\\User', 1, 'User logged in', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'low', '2025-08-30 02:01:54', '2025-08-30 02:01:54'),
(3, 1, 'Administrator', 'administrator', 'login', 'users', 'App\\Models\\User', 1, 'User logged in', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'low', '2025-08-30 09:03:53', '2025-08-30 09:03:53'),
(4, 1, 'Administrator', 'administrator', 'login', 'users', 'App\\Models\\User', 1, 'User logged in', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'low', '2025-08-30 09:03:53', '2025-08-30 09:03:53'),
(5, 1, 'Administrator', 'administrator', 'create', 'manufacturing', 'App\\Models\\RawMaterial', 1, 'Created new raw material: BB', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'medium', '2025-08-30 09:07:32', '2025-08-30 09:07:32'),
(6, 1, 'Administrator', 'administrator', 'create', 'manufacturing', 'App\\Models\\Supplier', 6, 'Created new supplier: Wali', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'medium', '2025-08-30 09:41:58', '2025-08-30 09:41:58'),
(7, 1, 'Administrator', 'administrator', 'login', 'users', 'App\\Models\\User', 1, 'User logged in', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'low', '2025-09-02 16:06:29', '2025-09-02 16:06:29'),
(8, 1, 'Administrator', 'administrator', 'login', 'users', 'App\\Models\\User', 1, 'User logged in', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'low', '2025-09-02 16:06:29', '2025-09-02 16:06:29'),
(9, 1, 'Administrator', 'administrator', 'update', 'manufacturing', 'App\\Models\\RawMaterial', 1, 'Updated raw material: BB', NULL, NULL, '{\"last_purchase_price\":\"100\",\"average_price\":\"120\",\"minimum_stock\":5,\"maximum_stock\":200,\"expiry_date\":\"2025-08-30\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'medium', '2025-09-02 16:07:41', '2025-09-02 16:07:41'),
(10, 1, 'Administrator', 'administrator', 'login', 'users', 'App\\Models\\User', 1, 'User logged in', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'low', '2025-09-04 09:41:03', '2025-09-04 09:41:03'),
(11, 1, 'Administrator', 'administrator', 'login', 'users', 'App\\Models\\User', 1, 'User logged in', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'low', '2025-09-04 09:41:03', '2025-09-04 09:41:03'),
(12, 1, 'Administrator', 'administrator', 'create', 'manufacturing', 'App\\Models\\RawMaterial', 2, 'Created new raw material: asd', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'medium', '2025-09-04 09:41:40', '2025-09-04 09:41:40'),
(13, NULL, NULL, NULL, 'create', 'purchase_orders', 'PurchaseOrder', 1, 'Created purchase order #PUR-202509-00001 for supplier CV. Kemasan Jaya', NULL, NULL, '\"{\\\"supplier_id\\\":\\\"3\\\",\\\"order_date\\\":\\\"2025-09-04T00:00:00.000000Z\\\",\\\"expected_date\\\":\\\"2025-09-04T00:00:00.000000Z\\\",\\\"status\\\":\\\"draft\\\",\\\"payment_status\\\":\\\"unpaid\\\",\\\"payment_terms\\\":\\\"Net 30\\\",\\\"shipping_cost\\\":\\\"0.00\\\",\\\"reference\\\":null,\\\"notes\\\":null,\\\"subtotal\\\":\\\"100.00\\\",\\\"tax_amount\\\":\\\"0.00\\\",\\\"total_amount\\\":\\\"100.00\\\",\\\"created_by\\\":null,\\\"po_number\\\":\\\"PUR-202509-00001\\\",\\\"updated_at\\\":\\\"2025-09-04T16:43:18.000000Z\\\",\\\"created_at\\\":\\\"2025-09-04T16:43:18.000000Z\\\",\\\"id\\\":1}\"', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'low', '2025-09-04 09:43:18', '2025-09-04 09:43:18'),
(14, 1, NULL, NULL, 'update', 'purchase_orders', 'PurchaseOrder', 1, 'Sent purchase order #PUR-202509-00001 to supplier', NULL, NULL, '\"{\\\"status\\\":\\\"confirmed\\\"}\"', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'low', '2025-09-04 09:44:51', '2025-09-04 09:44:51'),
(15, 1, 'Administrator', 'administrator', 'create', 'manufacturing', 'App\\Models\\Supplier', 7, 'Created new supplier: qq', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'medium', '2025-09-04 09:45:14', '2025-09-04 09:45:14'),
(16, NULL, NULL, NULL, 'create', 'manufacturing', 'App\\Models\\Recipe', 2, 'Created new recipe: asd', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'medium', '2025-09-04 10:04:54', '2025-09-04 10:04:54'),
(17, NULL, NULL, NULL, 'create', 'manufacturing', 'App\\Models\\Recipe', 3, 'Created new recipe: asd', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'medium', '2025-09-04 10:06:14', '2025-09-04 10:06:14'),
(18, NULL, NULL, NULL, 'create', 'manufacturing', 'App\\Models\\Recipe', 4, 'Created new recipe: asd', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'medium', '2025-09-04 10:07:22', '2025-09-04 10:07:22'),
(19, NULL, NULL, NULL, 'create', 'manufacturing', 'App\\Models\\Recipe', 5, 'Created new recipe: asd', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'medium', '2025-09-04 10:10:25', '2025-09-04 10:10:25'),
(20, NULL, NULL, NULL, 'create', 'manufacturing', 'App\\Models\\Recipe', 6, 'Created new recipe: asd', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'medium', '2025-09-04 10:10:50', '2025-09-04 10:10:50'),
(21, NULL, NULL, NULL, 'create', 'manufacturing', 'App\\Models\\Recipe', 7, 'Created new recipe: asd', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'medium', '2025-09-04 10:12:44', '2025-09-04 10:12:44'),
(22, NULL, NULL, NULL, 'create', 'manufacturing', 'App\\Models\\Recipe', 8, 'Created new recipe: asd', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'medium', '2025-09-04 10:14:49', '2025-09-04 10:14:49'),
(23, NULL, NULL, NULL, 'create', 'manufacturing', 'App\\Models\\Recipe', 9, 'Created new recipe: asd', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'medium', '2025-09-04 10:19:04', '2025-09-04 10:19:04'),
(24, NULL, NULL, NULL, 'create', 'manufacturing', 'App\\Models\\Recipe', 10, 'Created new recipe: asd', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'medium', '2025-09-04 10:28:32', '2025-09-04 10:28:32'),
(25, NULL, NULL, NULL, 'create', 'manufacturing', 'App\\Models\\Recipe', 11, 'Created new recipe: asd', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'medium', '2025-09-04 10:29:05', '2025-09-04 10:29:05'),
(26, NULL, NULL, NULL, 'create', 'manufacturing', 'App\\Models\\Recipe', 12, 'Created new recipe: asd', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'medium', '2025-09-04 10:32:02', '2025-09-04 10:32:02'),
(27, NULL, NULL, NULL, 'create', 'manufacturing', 'App\\Models\\ProductionOrder', 1, 'Created production order: PRD-20250904-001', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'medium', '2025-09-04 12:07:23', '2025-09-04 12:07:23'),
(28, 1, 'Administrator', 'administrator', 'login', 'users', 'App\\Models\\User', 1, 'User logged in', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'low', '2025-09-04 16:19:41', '2025-09-04 16:19:41'),
(29, 1, 'Administrator', 'administrator', 'login', 'users', 'App\\Models\\User', 1, 'User logged in', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'low', '2025-09-04 16:19:41', '2025-09-04 16:19:41'),
(30, 1, 'Administrator', 'administrator', 'login', 'users', 'App\\Models\\User', 1, 'User logged in', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'low', '2025-09-04 23:14:28', '2025-09-04 23:14:28'),
(31, 1, 'Administrator', 'administrator', 'login', 'users', 'App\\Models\\User', 1, 'User logged in', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'low', '2025-09-04 23:14:28', '2025-09-04 23:14:28'),
(32, 1, 'Administrator', 'administrator', 'login', 'users', 'App\\Models\\User', 1, 'User logged in', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'low', '2025-09-05 05:15:44', '2025-09-05 05:15:44'),
(33, 1, 'Administrator', 'administrator', 'login', 'users', 'App\\Models\\User', 1, 'User logged in', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'low', '2025-09-05 05:15:44', '2025-09-05 05:15:44'),
(34, 1, 'Administrator', 'administrator', 'login', 'users', 'App\\Models\\User', 1, 'User logged in', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'low', '2025-09-05 07:25:22', '2025-09-05 07:25:22'),
(35, 1, 'Administrator', 'administrator', 'login', 'users', 'App\\Models\\User', 1, 'User logged in', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'low', '2025-09-05 07:25:22', '2025-09-05 07:25:22'),
(36, 1, 'Administrator', 'administrator', 'login', 'users', 'App\\Models\\User', 1, 'User logged in', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'low', '2025-09-05 19:55:55', '2025-09-05 19:55:55'),
(37, 1, 'Administrator', 'administrator', 'login', 'users', 'App\\Models\\User', 1, 'User logged in', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'low', '2025-09-05 19:55:55', '2025-09-05 19:55:55'),
(38, 1, 'Administrator', 'administrator', 'login', 'users', 'App\\Models\\User', 1, 'User logged in', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'low', '2025-09-05 22:02:07', '2025-09-05 22:02:07'),
(39, 1, 'Administrator', 'administrator', 'login', 'users', 'App\\Models\\User', 1, 'User logged in', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'low', '2025-09-05 22:02:07', '2025-09-05 22:02:07'),
(40, 1, 'Administrator', 'administrator', 'login', 'users', 'App\\Models\\User', 1, 'User logged in', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'low', '2025-09-06 10:01:05', '2025-09-06 10:01:05'),
(41, 1, 'Administrator', 'administrator', 'login', 'users', 'App\\Models\\User', 1, 'User logged in', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'low', '2025-09-06 10:01:05', '2025-09-06 10:01:05'),
(42, 1, 'Administrator', 'administrator', 'login', 'users', 'App\\Models\\User', 1, 'User logged in', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'low', '2025-09-07 08:03:53', '2025-09-07 08:03:53'),
(43, 1, 'Administrator', 'administrator', 'login', 'users', 'App\\Models\\User', 1, 'User logged in', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'low', '2025-09-07 08:03:53', '2025-09-07 08:03:53');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(191) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-5c785c036466adea360111aa28563bfd556b5fba', 'i:1;', 1757258779),
('laravel-cache-5c785c036466adea360111aa28563bfd556b5fba:timer', 'i:1757258779;', 1757258779);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(191) NOT NULL,
  `owner` varchar(191) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(191) DEFAULT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `icon`, `parent_id`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Makanan', 'makanan', 'Kategori untuk produk makanan', NULL, NULL, 0, 1, '2025-08-30 02:01:31', '2025-08-30 02:01:31'),
(2, 'Roti & Kue', 'roti-kue', 'Produk roti dan kue', NULL, 1, 0, 1, '2025-08-30 02:01:31', '2025-08-30 02:01:31'),
(3, 'Snack', 'snack', 'Makanan ringan', NULL, 1, 0, 1, '2025-08-30 02:01:31', '2025-08-30 02:01:31'),
(4, 'Makanan Berat', 'makanan-berat', 'Makanan utama', NULL, 1, 0, 1, '2025-08-30 02:01:31', '2025-08-30 02:01:31'),
(5, 'Minuman', 'minuman', 'Kategori untuk produk minuman', NULL, NULL, 0, 1, '2025-08-30 02:01:31', '2025-08-30 02:01:31'),
(6, 'Jus', 'jus', 'Jus buah dan sayur', NULL, 5, 0, 1, '2025-08-30 02:01:31', '2025-08-30 02:01:31'),
(7, 'Kopi & Teh', 'kopi-teh', 'Minuman kopi dan teh', NULL, 5, 0, 1, '2025-08-30 02:01:31', '2025-08-30 02:01:31'),
(8, 'Minuman Kemasan', 'minuman-kemasan', 'Minuman dalam kemasan', NULL, 5, 0, 1, '2025-08-30 02:01:31', '2025-08-30 02:01:31'),
(9, 'Bahan Baku', 'bahan-baku', 'Kategori untuk bahan baku produksi', NULL, NULL, 0, 1, '2025-08-30 02:01:31', '2025-08-30 02:01:31'),
(10, 'Tepung', 'tepung', 'Berbagai jenis tepung', NULL, 9, 0, 1, '2025-08-30 02:01:31', '2025-08-30 02:01:31'),
(11, 'Gula & Pemanis', 'gula-pemanis', 'Gula dan pemanis lainnya', NULL, 9, 0, 1, '2025-08-30 02:01:31', '2025-08-30 02:01:31'),
(12, 'Minyak & Lemak', 'minyak-lemak', 'Minyak goreng dan lemak', NULL, 9, 0, 1, '2025-08-30 02:01:31', '2025-08-30 02:01:31'),
(13, 'Bumbu & Rempah', 'bumbu-rempah', 'Bumbu dan rempah-rempah', NULL, 9, 0, 1, '2025-08-30 02:01:31', '2025-08-30 02:01:31');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  `email` varchar(191) DEFAULT NULL,
  `phone` varchar(191) NOT NULL,
  `address` text NOT NULL,
  `city` varchar(191) DEFAULT NULL,
  `postal_code` varchar(191) DEFAULT NULL,
  `type` enum('individual','business') NOT NULL DEFAULT 'individual',
  `tax_id` varchar(191) DEFAULT NULL,
  `credit_limit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `outstanding_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `code`, `name`, `email`, `phone`, `address`, `city`, `postal_code`, `type`, `tax_id`, `credit_limit`, `outstanding_balance`, `notes`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'CUST00001', 'PT. Maju Jaya', 'info@majujaya.com', '021-5551234', 'Jl. Sudirman No. 123, Jakarta Pusat, DKI Jakarta', 'Jakarta', '10110', 'business', '01.234.567.8-901.000', 50000000.00, 0.00, 'Customer premium dengan pembayaran tepat waktu. Payment terms: Net 30, Discount: 5%', 1, '2025-08-30 02:01:31', '2025-08-30 02:01:31'),
(2, 'CUST00002', 'CV. Berkah Abadi', 'purchasing@berkah.co.id', '022-6667890', 'Jl. Asia Afrika No. 45, Bandung, Jawa Barat', 'Bandung', '40111', 'business', '02.345.678.9-012.000', 30000000.00, 0.00, 'Customer reguler. Payment terms: Net 15, Discount: 3%', 1, '2025-08-30 02:01:31', '2025-08-30 02:01:31'),
(3, 'CUST00003', 'Toko Sumber Rezeki', 'sumberrezeki@gmail.com', '031-8881234', 'Jl. Tunjungan No. 78, Surabaya, Jawa Timur', 'Surabaya', '60275', 'business', '03.456.789.0-123.000', 20000000.00, 0.00, 'Pembayaran cash on delivery. Payment terms: COD, Discount: 2%', 1, '2025-08-30 02:01:31', '2025-08-30 02:01:31'),
(4, 'CUST00004', 'UD. Sejahtera', 'udsejahtera@yahoo.com', '024-3334567', 'Jl. Pemuda No. 90, Semarang, Jawa Tengah', 'Semarang', '50132', 'business', '04.567.890.1-234.000', 15000000.00, 0.00, 'Customer baru. Payment terms: Net 7', 1, '2025-08-30 02:01:31', '2025-08-30 02:01:31'),
(5, 'CUST00005', 'PT. Global Niaga', 'procurement@globalniaga.com', '061-4445678', 'Jl. Gatot Subroto No. 56, Medan, Sumatera Utara', 'Medan', '20235', 'business', '05.678.901.2-345.000', 75000000.00, 0.00, 'Customer korporat dengan volume besar. Payment terms: Net 45, Discount: 7.5%', 1, '2025-08-30 02:01:31', '2025-08-30 02:01:31'),
(6, 'CUST00006', 'Minimarket Bahagia', 'bahagia.mart@gmail.com', '0274-555789', 'Jl. Malioboro No. 123, Yogyakarta, DI Yogyakarta', 'Yogyakarta', '55271', 'business', '06.789.012.3-456.000', 10000000.00, 0.00, 'Jaringan minimarket lokal. Payment terms: Net 14, Discount: 2.5%', 1, '2025-08-30 02:01:31', '2025-08-30 02:01:31'),
(7, 'CUST00007', 'Hotel Mewah Sentosa', 'purchasing@hotelmewah.com', '0361-777888', 'Jl. Sunset Road No. 88, Kuta, Bali', 'Denpasar', '80361', 'business', '07.890.123.4-567.000', 40000000.00, 0.00, 'Hotel bintang 5, order rutin bulanan. Payment terms: Net 30, Discount: 5%', 1, '2025-08-30 02:01:31', '2025-08-30 02:01:31'),
(8, 'CUST00008', 'Restoran Nusantara', 'nusantara.resto@outlook.com', '021-8889999', 'Jl. Kemang Raya No. 45, Jakarta Selatan, DKI Jakarta', 'Jakarta', '12730', 'business', '08.901.234.5-678.000', 25000000.00, 0.00, 'Restoran chain dengan 5 cabang. Payment terms: Net 21, Discount: 3%', 1, '2025-08-30 02:01:31', '2025-08-30 02:01:31'),
(9, 'CUST00009', 'Koperasi Karyawan Sejahtera', 'koperasi@sejahtera.co.id', '021-4441111', 'Jl. HR Rasuna Said Kav. B-12, Jakarta, DKI Jakarta', 'Jakarta', '12940', 'business', '09.012.345.6-789.000', 35000000.00, 0.00, 'Koperasi dengan 1000+ anggota. Payment terms: Net 30, Discount: 4%', 1, '2025-08-30 02:01:31', '2025-08-30 02:01:31'),
(10, 'CUST00010', 'Supermarket Hemat', 'admin@superhemat.com', '0251-333444', 'Jl. Pajajaran No. 100, Bogor, Jawa Barat', 'Bogor', '16143', 'business', '10.123.456.7-890.000', 60000000.00, 15000000.00, 'Temporarily inactive - pending payment. Payment terms: Net 60, Discount: 8%', 0, '2025-08-30 02:01:31', '2025-08-30 02:01:31'),
(11, 'CUST00011', 'Budi Santoso1', 'budi.santoso@gmail.com', '0812-3456-7890', 'Jl. Melati No. 15, Tangerang', 'Tangerang', '15117', 'individual', NULL, 5000000.00, 0.00, 'Customer perorangan, pembayaran tunai', 1, '2025-08-30 02:01:31', '2025-09-05 23:46:58'),
(12, 'CUST00012', 'Ibu Siti Nurhaliza', 'siti.nurhaliza@yahoo.com', '0821-9876-5432', 'Perumahan Griya Indah Blok C-12, Bekasi', 'Bekasi', '17121', 'individual', NULL, 3000000.00, 0.00, 'Customer tetap, order mingguan', 1, '2025-08-30 02:01:31', '2025-08-30 02:01:31');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(191) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_number` varchar(191) NOT NULL,
  `order_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `invoice_date` date NOT NULL,
  `due_date` date NOT NULL,
  `status` enum('draft','sent','paid','partial','overdue','cancelled') NOT NULL DEFAULT 'draft',
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `shipping_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `paid_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `balance_due` decimal(15,2) NOT NULL DEFAULT 0.00,
  `payment_terms` varchar(191) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `terms_conditions` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(191) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(191) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2024_01_01_000001_create_categories_table', 1),
(5, '2024_01_01_000002_create_customers_table', 1),
(6, '2024_01_01_000003_create_suppliers_table', 1),
(7, '2024_01_01_000004_create_raw_materials_table', 1),
(8, '2024_01_01_000005_create_products_table', 1),
(9, '2024_01_01_000006_create_recipes_table', 1),
(10, '2024_01_01_000007_create_recipe_ingredients_table', 1),
(11, '2024_01_01_000008_create_production_orders_table', 1),
(12, '2024_01_01_000009_create_quality_inspections_table', 1),
(13, '2024_01_01_000010_create_purchase_orders_table', 1),
(14, '2024_01_01_000011_create_purchase_order_items_table', 1),
(15, '2024_01_01_000012_create_orders_table', 1),
(16, '2024_01_01_000013_create_order_items_table', 1),
(17, '2024_01_01_000014_create_transactions_table', 1),
(18, '2024_01_01_000015_create_invoices_table', 1),
(19, '2024_01_01_000016_create_payments_table', 1),
(20, '2024_01_01_000017_create_stock_movements_table', 1),
(21, '2024_01_01_000018_create_activity_logs_table', 1),
(22, '2024_01_01_000019_update_users_table', 1),
(23, '2025_01_23_000001_fix_raw_materials_table', 1),
(24, '2025_01_23_000002_update_stock_movements_type_enum', 1),
(25, '2025_08_18_032912_create_personal_access_tokens_table', 1),
(26, '2025_08_18_033549_add_fields_to_users_table', 1),
(27, '2025_08_18_fix_suppliers_table_columns', 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_number` varchar(191) NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `order_date` date NOT NULL,
  `delivery_date` date DEFAULT NULL,
  `status` enum('draft','pending','confirmed','processing','ready','shipped','delivered','completed','cancelled') NOT NULL DEFAULT 'draft',
  `payment_status` enum('unpaid','partial','paid') NOT NULL DEFAULT 'unpaid',
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `shipping_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `payment_method` varchar(191) DEFAULT NULL,
  `shipping_address` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(15,2) NOT NULL,
  `production_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_price` decimal(15,2) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(191) NOT NULL,
  `token` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `payment_number` varchar(191) NOT NULL,
  `type` enum('received','paid') NOT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `payment_method` varchar(191) NOT NULL,
  `reference_type` varchar(191) DEFAULT NULL,
  `reference_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `supplier_id` bigint(20) UNSIGNED DEFAULT NULL,
  `bank_account` varchar(191) DEFAULT NULL,
  `transaction_id` varchar(191) DEFAULT NULL,
  `status` enum('pending','completed','failed','cancelled') NOT NULL DEFAULT 'pending',
  `description` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `attachment` varchar(191) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(191) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `production_orders`
--

CREATE TABLE `production_orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_number` varchar(191) NOT NULL,
  `recipe_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED DEFAULT NULL,
  `batch_count` int(11) NOT NULL DEFAULT 1,
  `quantity_planned` decimal(10,3) NOT NULL DEFAULT 1.000,
  `quantity_produced` decimal(10,3) NOT NULL DEFAULT 0.000,
  `start_date` date NOT NULL,
  `target_date` date DEFAULT NULL,
  `completed_date` date DEFAULT NULL,
  `batch_number` varchar(255) DEFAULT NULL,
  `priority` varchar(255) DEFAULT NULL,
  `status` enum('pending','in_progress','completed','cancelled','on_hold') NOT NULL DEFAULT 'pending',
  `progress` int(11) NOT NULL DEFAULT 0,
  `operator_name` varchar(191) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `estimated_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `actual_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `estimated_duration_hours` int(11) NOT NULL DEFAULT 0,
  `actual_duration_hours` decimal(8,2) NOT NULL DEFAULT 0.00,
  `efficiency_percentage` int(11) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `production_orders`
--

INSERT INTO `production_orders` (`id`, `order_number`, `recipe_id`, `product_id`, `batch_count`, `quantity_planned`, `quantity_produced`, `start_date`, `target_date`, `completed_date`, `batch_number`, `priority`, `status`, `progress`, `operator_name`, `created_by`, `estimated_cost`, `actual_cost`, `estimated_duration_hours`, `actual_duration_hours`, `efficiency_percentage`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'PRD-20250904-001', 2, NULL, 1, 1.000, 0.000, '2025-09-04', NULL, NULL, 'BATCH-20250904-0D78E6', 'normal', 'pending', 0, NULL, NULL, 0.00, 0.00, 0, 0.00, 0, 'asd', '2025-09-04 12:07:23', '2025-09-04 12:07:23');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sku` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` enum('food','beverage','other') NOT NULL DEFAULT 'other',
  `unit` varchar(191) NOT NULL,
  `image` varchar(191) DEFAULT NULL,
  `base_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `selling_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `min_stock` decimal(10,3) NOT NULL DEFAULT 0.000,
  `current_stock` decimal(10,3) NOT NULL DEFAULT 0.000,
  `reserved_stock` decimal(10,3) NOT NULL DEFAULT 0.000,
  `production_time_hours` int(11) NOT NULL DEFAULT 0,
  `expiry_date` date DEFAULT NULL,
  `is_manufactured` tinyint(1) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `sku`, `name`, `description`, `category_id`, `type`, `unit`, `image`, `base_price`, `selling_price`, `min_stock`, `current_stock`, `reserved_stock`, `production_time_hours`, `expiry_date`, `is_manufactured`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'asd', 'asd', NULL, NULL, 'other', '', NULL, 100.00, 200.00, 300.000, 400.000, 500.000, 0, NULL, 1, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `purchase_orders`
--

CREATE TABLE `purchase_orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `po_number` varchar(191) NOT NULL,
  `supplier_id` bigint(20) UNSIGNED NOT NULL,
  `order_date` date NOT NULL,
  `expected_date` date DEFAULT NULL,
  `received_date` date DEFAULT NULL,
  `status` enum('draft','sent','confirmed','received','cancelled') NOT NULL DEFAULT 'draft',
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `shipping_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `payment_terms` varchar(191) DEFAULT NULL,
  `payment_status` enum('unpaid','partial','paid') NOT NULL DEFAULT 'unpaid',
  `reference` varchar(191) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_orders`
--

INSERT INTO `purchase_orders` (`id`, `po_number`, `supplier_id`, `order_date`, `expected_date`, `received_date`, `status`, `subtotal`, `tax_amount`, `shipping_cost`, `total_amount`, `payment_terms`, `payment_status`, `reference`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'PUR-202509-00001', 3, '2025-09-04', '2025-09-04', NULL, 'sent', 100.00, 0.00, 0.00, 100.00, 'Net 30', 'unpaid', NULL, NULL, NULL, '2025-09-04 09:43:18', '2025-09-04 09:44:51');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_items`
--

CREATE TABLE `purchase_order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `purchase_order_id` bigint(20) UNSIGNED NOT NULL,
  `raw_material_id` bigint(20) UNSIGNED DEFAULT NULL,
  `item_name` varchar(191) NOT NULL,
  `item_code` varchar(191) DEFAULT NULL,
  `quantity` decimal(10,3) NOT NULL,
  `unit` varchar(191) NOT NULL,
  `unit_price` decimal(15,2) NOT NULL,
  `total_price` decimal(15,2) NOT NULL,
  `received_quantity` decimal(10,3) NOT NULL DEFAULT 0.000,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_order_items`
--

INSERT INTO `purchase_order_items` (`id`, `purchase_order_id`, `raw_material_id`, `item_name`, `item_code`, `quantity`, `unit`, `unit_price`, `total_price`, `received_quantity`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'BB', 'RM000001', 1.000, 'kg', 100.00, 100.00, 0.000, NULL, '2025-09-04 09:43:18', '2025-09-04 09:43:18');

-- --------------------------------------------------------

--
-- Table structure for table `quality_inspections`
--

CREATE TABLE `quality_inspections` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `inspection_id` varchar(191) NOT NULL,
  `production_order_id` bigint(20) UNSIGNED NOT NULL,
  `batch_number` varchar(191) NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `batch_size` int(11) NOT NULL,
  `inspector_name` varchar(191) NOT NULL,
  `inspection_date` date NOT NULL,
  `status` enum('pending','passed','failed','conditional') NOT NULL DEFAULT 'pending',
  `score` int(11) NOT NULL DEFAULT 0,
  `checklist` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`checklist`)),
  `defects` text DEFAULT NULL,
  `corrective_actions` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `raw_materials`
--

CREATE TABLE `raw_materials` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `unit` varchar(191) NOT NULL,
  `current_stock` decimal(10,3) NOT NULL DEFAULT 0.000,
  `minimum_stock` decimal(10,3) NOT NULL DEFAULT 0.000,
  `maximum_stock` decimal(10,3) NOT NULL DEFAULT 0.000,
  `reorder_point` decimal(10,3) NOT NULL DEFAULT 0.000,
  `reorder_quantity` decimal(10,3) NOT NULL DEFAULT 0.000,
  `average_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `last_purchase_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `supplier_id` bigint(20) UNSIGNED DEFAULT NULL,
  `category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `last_purchase_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `status` enum('good','low_stock','critical','out_of_stock') NOT NULL DEFAULT 'good',
  `storage_location` varchar(191) DEFAULT NULL,
  `lead_time_days` int(11) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `raw_materials`
--

INSERT INTO `raw_materials` (`id`, `code`, `name`, `description`, `unit`, `current_stock`, `minimum_stock`, `maximum_stock`, `reorder_point`, `reorder_quantity`, `average_price`, `last_purchase_price`, `supplier_id`, `category_id`, `last_purchase_date`, `expiry_date`, `status`, `storage_location`, `lead_time_days`, `notes`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'RM000001', 'BB', 'asd', 'kg', 30.000, 5.000, 200.000, 5.000, 195.000, 120.00, 100.00, 1, 13, NULL, '2025-08-30', 'good', 'Gedung A', 3, NULL, 1, '2025-08-30 09:07:32', '2025-09-02 16:07:41'),
(2, 'RM000002', 'asd', 'asd', 'liter', 0.000, 0.000, 0.000, 0.000, 0.000, 0.00, 0.00, 1, 9, NULL, NULL, 'out_of_stock', 'asd', 3, NULL, 1, '2025-09-04 09:41:40', '2025-09-04 09:41:40');

-- --------------------------------------------------------

--
-- Table structure for table `recipes`
--

CREATE TABLE `recipes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `category` varchar(191) DEFAULT NULL,
  `batch_size` decimal(10,3) NOT NULL DEFAULT 1.000,
  `batch_unit` varchar(191) DEFAULT NULL,
  `total_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `cost_per_unit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `selling_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `profit_margin` decimal(5,2) NOT NULL DEFAULT 0.00,
  `production_time_minutes` int(11) NOT NULL DEFAULT 0,
  `instructions` text DEFAULT NULL,
  `production_time` int(11) DEFAULT NULL,
  `preparation_time` int(11) DEFAULT NULL,
  `status` enum('active','inactive','draft') NOT NULL DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `version` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `recipes`
--

INSERT INTO `recipes` (`id`, `code`, `name`, `description`, `product_id`, `category`, `batch_size`, `batch_unit`, `total_cost`, `cost_per_unit`, `selling_price`, `profit_margin`, `production_time_minutes`, `instructions`, `production_time`, `preparation_time`, `status`, `notes`, `version`, `created_at`, `updated_at`) VALUES
(2, '12', 'asd', NULL, 1, NULL, 1.000, NULL, 100.00, 0.00, 0.00, 0.00, 0, '\"asd\"', 60, NULL, 'active', 'asd', '1.0', '2025-09-04 10:04:54', '2025-09-04 11:26:33'),
(3, '122', 'asd', NULL, 1, NULL, 1.000, NULL, 120.00, 0.00, 0.00, 0.00, 0, '\"asd\"', 60, NULL, 'active', 'asd', '1.0', '2025-09-04 10:06:14', '2025-09-04 10:06:14'),
(4, '1', 'asd', NULL, 1, NULL, 1.000, NULL, 100.00, 0.00, 0.00, 0.00, 0, '\"asd\"', 60, NULL, 'active', 'asd', '1.0', '2025-09-04 10:07:22', '2025-09-04 10:07:22'),
(5, '2', 'asd', 'asd', 1, NULL, 1.000, NULL, 100.00, 0.00, 0.00, 0.00, 0, '\"ds\"', 60, NULL, 'active', 'sd', '1.0', '2025-09-04 10:10:25', '2025-09-04 10:10:25'),
(6, '3', 'asd', 'asd', 1, NULL, 1.000, NULL, 100.00, 0.00, 0.00, 0.00, 0, NULL, 60, NULL, 'active', '12', '1.0', '2025-09-04 10:10:50', '2025-09-04 10:10:50'),
(7, '4', 'asd', 'asd', 1, NULL, 1.000, NULL, 100.00, 0.00, 0.00, 0.00, 0, NULL, 60, NULL, 'active', '12', '1.0', '2025-09-04 10:12:44', '2025-09-04 10:12:44'),
(8, '5', 'asd', 'asd', 1, NULL, 1.000, NULL, 100.00, 0.00, 0.00, 0.00, 0, NULL, 60, NULL, 'active', '12', '1.0', '2025-09-04 10:14:49', '2025-09-04 10:14:49'),
(9, '6', 'asd', 'asd', 1, NULL, 1.000, NULL, 100.00, 0.00, 0.00, 0.00, 0, NULL, 60, NULL, 'active', '12', '1.0', '2025-09-04 10:19:04', '2025-09-04 11:38:40'),
(10, '7', 'asd', 'asd', 1, NULL, 1.000, NULL, 0.00, 0.00, 0.00, 0.00, 0, NULL, 60, NULL, 'active', '12', '1.0', '2025-09-04 10:28:32', '2025-09-04 10:28:32'),
(11, '8', 'asd', 'asd', 1, NULL, 1.000, NULL, 0.00, 0.00, 0.00, 0.00, 0, NULL, 60, NULL, 'active', '12', '1.0', '2025-09-04 10:29:05', '2025-09-04 10:29:05'),
(12, '9', 'asd', '12', 1, NULL, 1.000, NULL, 100.00, 0.00, 0.00, 0.00, 0, '\"3\"', 60, NULL, 'active', 'adw', '1.0', '2025-09-04 10:32:02', '2025-09-04 10:32:02');

-- --------------------------------------------------------

--
-- Table structure for table `recipe_ingredients`
--

CREATE TABLE `recipe_ingredients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `recipe_id` bigint(20) UNSIGNED NOT NULL,
  `raw_material_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` decimal(10,3) NOT NULL,
  `unit` varchar(191) NOT NULL,
  `unit_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `recipe_ingredients`
--

INSERT INTO `recipe_ingredients` (`id`, `recipe_id`, `raw_material_id`, `quantity`, `unit`, `unit_cost`, `total_cost`, `notes`, `created_at`, `updated_at`) VALUES
(2, 2, 1, 1.000, 'gram', 0.00, 0.00, NULL, '2025-09-04 10:04:54', '2025-09-04 10:04:54'),
(3, 3, 1, 1.000, 'gram', 0.00, 0.00, NULL, '2025-09-04 10:06:14', '2025-09-04 10:06:14'),
(4, 4, 1, 1.000, 'gram', 0.00, 0.00, NULL, '2025-09-04 10:07:22', '2025-09-04 10:07:22'),
(5, 5, 1, 1.000, 'gram', 0.00, 0.00, NULL, '2025-09-04 10:10:25', '2025-09-04 10:10:25'),
(6, 6, 1, 1.000, 'gram', 0.00, 0.00, NULL, '2025-09-04 10:10:50', '2025-09-04 10:10:50'),
(7, 7, 1, 1.000, 'gram', 0.00, 0.00, NULL, '2025-09-04 10:12:44', '2025-09-04 10:12:44'),
(8, 8, 1, 1.000, 'gram', 0.00, 0.00, NULL, '2025-09-04 10:14:49', '2025-09-04 10:14:49'),
(9, 9, 1, 1.000, 'gram', 0.00, 0.00, NULL, '2025-09-04 10:19:04', '2025-09-04 10:19:04'),
(10, 10, 1, 1.000, 'gram', 0.00, 0.00, NULL, '2025-09-04 10:28:32', '2025-09-04 10:28:32'),
(11, 12, 1, 1.000, 'gram', 0.00, 0.00, NULL, '2025-09-04 10:32:02', '2025-09-04 10:32:02');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(191) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('Mov0tL6jG6kL1MFi5DvNbRPo2U5pJqnrKXogpQbo', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoib01rTWdXRGdGYjU0MVB1azlWVmdkQmJaV1d1MzhDeGs0Z3daRVk5bSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzU6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9vcmRlcnMvY3JlYXRlIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1757259471),
('PYddXHMybR1M6mWSr0qye051H8NMsOpglkgwrlHC', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiS0l0QW42NFZ3VDZySlExUkZOQjBiUXU3WTV5djJNOHVMN1MxU2pPYSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyNzoiaHR0cDovL2xvY2FsaG9zdDo4MDAwL3VzZXJzIjt9czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzU6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9vcmRlcnMvY3JlYXRlIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1757141270),
('YG3kss1osK0j1weka4qTFHac1dQSz4JzJxNPTrQT', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoid3I1YUtOcklkcnF6OGM0THRMdnpWZlM2N1p2TDZIbzNQZXZSaGowRiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozNToiaHR0cDovL2xvY2FsaG9zdDo4MDAwL29yZGVycy9jcmVhdGUiO31zOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czozNToiaHR0cDovL2xvY2FsaG9zdDo4MDAwL29yZGVycy9jcmVhdGUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1757178076);

-- --------------------------------------------------------

--
-- Table structure for table `stock_movements`
--

CREATE TABLE `stock_movements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `item_type` enum('product','raw_material') NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('in','out','adjustment','transfer','production','damage','return') NOT NULL,
  `quantity` decimal(10,3) NOT NULL,
  `unit_cost` decimal(15,2) DEFAULT NULL,
  `total_cost` decimal(15,2) DEFAULT NULL,
  `reference_type` varchar(191) DEFAULT NULL,
  `reference_id` bigint(20) UNSIGNED DEFAULT NULL,
  `from_location` varchar(191) DEFAULT NULL,
  `to_location` varchar(191) DEFAULT NULL,
  `before_stock` decimal(10,3) NOT NULL,
  `after_stock` decimal(10,3) NOT NULL,
  `reason` varchar(191) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ;

--
-- Dumping data for table `stock_movements`
--

INSERT INTO `stock_movements` (`id`, `item_type`, `item_id`, `type`, `quantity`, `unit_cost`, `total_cost`, `reference_type`, `reference_id`, `from_location`, `to_location`, `before_stock`, `after_stock`, `reason`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'raw_material', 1, 'in', 30.000, 0.00, 0.00, NULL, NULL, NULL, NULL, 0.000, 30.000, 'initial_stock', 'Initial stock entry', 1, '2025-08-30 09:07:32', '2025-08-30 09:07:32');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  `contact_person` varchar(191) DEFAULT NULL,
  `email` varchar(191) DEFAULT NULL,
  `phone` varchar(191) NOT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(191) DEFAULT NULL,
  `postal_code` varchar(191) DEFAULT NULL,
  `tax_id` varchar(191) DEFAULT NULL,
  `payment_terms` int(11) DEFAULT NULL,
  `lead_time_days` int(11) DEFAULT NULL,
  `minimum_order_value` decimal(15,2) DEFAULT NULL,
  `rating` decimal(2,1) NOT NULL DEFAULT 0.0,
  `notes` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `code`, `name`, `contact_person`, `email`, `phone`, `address`, `city`, `postal_code`, `tax_id`, `payment_terms`, `lead_time_days`, `minimum_order_value`, `rating`, `notes`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'SUP00001', 'CV. Gula Manis', 'Budi Santoso', 'sales@gulamanis.co.id', '021-7771234', 'Jl. Industri No. 45, Kawasan Industri Pulogadung, Jakarta Timur, DKI Jakarta', 'Jakarta Timur', '13260', '11.111.111.1-111.000', 30, 3, 1000000.00, 4.5, 'Pemasok utama untuk gula cair dan gula pasir. Kualitas terjamin.', 1, '2025-08-30 02:01:31', '2025-08-30 02:01:31'),
(2, 'SUP00002', 'PT. Herbal Nusantara', 'Siti Nurhaliza', 'info@herbalnusantara.com', '022-8882345', 'Jl. Raya Lembang No. 78, Bandung, Jawa Barat', 'Bandung', '40391', '22.222.222.2-222.000', 15, 2, 500000.00, 4.8, 'Pemasok temulawak, jahe, dan rempah-rempah herbal berkualitas.', 1, '2025-08-30 02:01:31', '2025-08-30 02:01:31'),
(3, 'SUP00003', 'CV. Kemasan Jaya', 'Ahmad Fauzi', 'kemasan.jaya@gmail.com', '031-5553456', 'Jl. Rungkut Industri No. 23, Surabaya, Jawa Timur', 'Surabaya', '60293', '33.333.333.3-333.000', 0, 1, 250000.00, 4.2, 'Pemasok botol, plastik kemasan, dan label produk.', 1, '2025-08-30 02:01:31', '2025-08-30 02:01:31'),
(4, 'SUP00004', 'UD. Tepung Sejahtera', 'Dewi Lestari', 'sales@tepungsejahtera.co.id', '021-8884567', 'Jl. Cikarang Barat No. 90, Bekasi, Jawa Barat', 'Bekasi', '17530', '44.444.444.4-444.000', 45, 5, 2000000.00, 4.6, 'Pemasok tepung tapioka dan tepung terigu berkualitas.', 1, '2025-08-30 02:01:31', '2025-08-30 02:01:31'),
(5, 'SUP00005', 'CV. Minyak Sejahtera', 'Joko Widodo', 'order@minyaksejahtera.com', '024-7775678', 'Jl. Industri Kecil No. 12, Semarang, Jawa Tengah', 'Semarang', '50198', '55.555.555.5-555.000', 21, 3, 1500000.00, 4.3, 'Pemasok minyak goreng untuk produksi krupuk.', 1, '2025-08-30 02:01:31', '2025-08-30 02:01:31'),
(6, 'SUP0721', 'Wali', 'asd', 'admin@gmail.com', '1212', 'asd', 'Jakarta', NULL, 'asd', 30, 7, 0.00, 3.0, NULL, 1, '2025-08-30 09:41:58', '2025-08-30 09:41:58'),
(7, 'SUP3469', 'qq', 'ww', 'ww@gmail.com', '08012', 'qwd', 'Jakarta', NULL, NULL, 30, 7, 0.00, 3.0, NULL, 0, '2025-09-04 09:45:14', '2025-09-04 09:45:14');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `transaction_number` varchar(191) NOT NULL,
  `transaction_date` date NOT NULL,
  `type` enum('income','expense','transfer') NOT NULL,
  `category` varchar(191) NOT NULL,
  `subcategory` varchar(191) DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `payment_method` varchar(191) DEFAULT NULL,
  `reference` varchar(191) DEFAULT NULL,
  `reference_type` varchar(191) DEFAULT NULL,
  `reference_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description` text NOT NULL,
  `status` enum('pending','approved','rejected','completed') NOT NULL DEFAULT 'pending',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `attachment` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `phone` varchar(191) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `avatar` varchar(191) DEFAULT NULL,
  `theme_preference` enum('light','dark','system') NOT NULL DEFAULT 'system',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_login_ip` varchar(45) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) NOT NULL,
  `role` enum('administrator','user') NOT NULL DEFAULT 'user',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `department`, `position`, `address`, `avatar`, `theme_preference`, `is_active`, `last_login_at`, `last_login_ip`, `email_verified_at`, `password`, `role`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', 'admin@mdagang.com', '081234567890', 'Management', 'System Administrator', NULL, NULL, 'system', 1, '2025-09-07 08:03:53', '127.0.0.1', '2025-08-30 02:01:30', '$2y$12$xYi.3wVzN2mFQeHvPM4hHuD038Pl2G3MH1JmuTKPq8PxWeGdPVZNG', 'administrator', NULL, '2025-08-30 02:01:30', '2025-09-07 08:03:53'),
(2, 'John Doe', 'john@mdagang.com', '081234567891', 'Production', 'Production Manager', NULL, NULL, 'system', 1, NULL, NULL, '2025-08-30 02:01:30', '$2y$12$Yy6F166KWsWW70Dh2/YzzeJDLuJLcIgCIbRFcZdrM6bsJmvKPeX9K', 'user', NULL, '2025-08-30 02:01:30', '2025-08-30 02:01:30'),
(3, 'Jane Smith', 'jane@mdagang.com', '081234567892', 'Sales', 'Sales Executive', NULL, NULL, 'system', 1, NULL, NULL, '2025-08-30 02:01:30', '$2y$12$nsHW8rozyypLe96DeJoA6ea5MVJtDu.YIpNoQ83IJH07Coo1Z/Ji2', 'user', NULL, '2025-08-30 02:01:31', '2025-08-30 02:01:31'),
(4, 'Bob Wilson', 'bob@mdagang.com', '081234567893', 'Finance', 'Finance Officer', NULL, NULL, 'system', 1, NULL, NULL, '2025-08-30 02:01:30', '$2y$12$OIdGbch6j7CRBBNgd8jCNeK2WZhr6sdTRZ2wrDOPqK.ZlayvEC6cO', 'user', NULL, '2025-08-30 02:01:31', '2025-08-30 02:01:31'),
(5, 'Alice Brown', 'alice@mdagang.com', '081234567894', 'Inventory', 'Inventory Manager', NULL, NULL, 'system', 1, NULL, NULL, '2025-08-30 02:01:30', '$2y$12$EYqRafUqQFhlTH8pU035vuKKH57uc28Z5NU8xjyPAXsA82SVLVyI2', 'user', NULL, '2025-08-30 02:01:31', '2025-08-30 02:01:31'),
(7, 'a1', 'a@gmail.com', '083165', NULL, NULL, NULL, NULL, 'system', 1, NULL, NULL, NULL, '$2y$12$3pLxWV.YpN3.Xl.LT/bwY.kyVmlzyplXBahAOCJCBGBKMDn7sMkRW', 'administrator', NULL, '2025-09-05 23:16:36', '2025-09-05 23:16:44');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_logs_user_id_action_type_index` (`user_id`,`action_type`),
  ADD KEY `activity_logs_module_created_at_index` (`module`,`created_at`),
  ADD KEY `activity_logs_model_type_model_id_index` (`model_type`,`model_id`),
  ADD KEY `activity_logs_risk_level_index` (`risk_level`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_slug_unique` (`slug`),
  ADD KEY `categories_parent_id_foreign` (`parent_id`),
  ADD KEY `categories_slug_is_active_index` (`slug`,`is_active`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customers_code_unique` (`code`),
  ADD KEY `customers_code_is_active_index` (`code`,`is_active`),
  ADD KEY `customers_type_index` (`type`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoices_invoice_number_unique` (`invoice_number`),
  ADD KEY `invoices_order_id_foreign` (`order_id`),
  ADD KEY `invoices_created_by_foreign` (`created_by`),
  ADD KEY `invoices_invoice_number_status_index` (`invoice_number`,`status`),
  ADD KEY `invoices_invoice_date_due_date_index` (`invoice_date`,`due_date`),
  ADD KEY `invoices_customer_id_status_index` (`customer_id`,`status`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `orders_order_number_unique` (`order_number`),
  ADD KEY `orders_user_id_foreign` (`user_id`),
  ADD KEY `orders_order_number_status_index` (`order_number`,`status`),
  ADD KEY `orders_order_date_delivery_date_index` (`order_date`,`delivery_date`),
  ADD KEY `orders_customer_id_payment_status_index` (`customer_id`,`payment_status`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_product_id_foreign` (`product_id`),
  ADD KEY `order_items_order_id_product_id_index` (`order_id`,`product_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payments_payment_number_unique` (`payment_number`),
  ADD KEY `payments_created_by_foreign` (`created_by`),
  ADD KEY `payments_payment_number_status_index` (`payment_number`,`status`),
  ADD KEY `payments_payment_date_type_index` (`payment_date`,`type`),
  ADD KEY `payments_reference_type_reference_id_index` (`reference_type`,`reference_id`),
  ADD KEY `payments_customer_id_index` (`customer_id`),
  ADD KEY `payments_supplier_id_index` (`supplier_id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indexes for table `production_orders`
--
ALTER TABLE `production_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `production_orders_order_number_unique` (`order_number`),
  ADD KEY `production_orders_recipe_id_foreign` (`recipe_id`),
  ADD KEY `production_orders_created_by_foreign` (`created_by`),
  ADD KEY `production_orders_order_number_status_index` (`order_number`,`status`),
  ADD KEY `production_orders_start_date_target_date_index` (`start_date`,`target_date`),
  ADD KEY `production_orders_product_id_index` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `products_sku_unique` (`sku`),
  ADD KEY `products_category_id_foreign` (`category_id`),
  ADD KEY `products_sku_is_active_index` (`sku`,`is_active`),
  ADD KEY `products_type_category_id_index` (`type`,`category_id`),
  ADD KEY `products_is_manufactured_index` (`is_manufactured`);

--
-- Indexes for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `purchase_orders_po_number_unique` (`po_number`),
  ADD KEY `purchase_orders_created_by_foreign` (`created_by`),
  ADD KEY `purchase_orders_po_number_status_index` (`po_number`,`status`),
  ADD KEY `purchase_orders_order_date_expected_date_index` (`order_date`,`expected_date`),
  ADD KEY `purchase_orders_supplier_id_index` (`supplier_id`);

--
-- Indexes for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_order_items_raw_material_id_foreign` (`raw_material_id`),
  ADD KEY `purchase_order_items_purchase_order_id_index` (`purchase_order_id`);

--
-- Indexes for table `quality_inspections`
--
ALTER TABLE `quality_inspections`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `quality_inspections_inspection_id_unique` (`inspection_id`),
  ADD KEY `quality_inspections_product_id_foreign` (`product_id`),
  ADD KEY `quality_inspections_inspection_id_status_index` (`inspection_id`,`status`),
  ADD KEY `quality_inspections_production_order_id_index` (`production_order_id`),
  ADD KEY `quality_inspections_inspection_date_index` (`inspection_date`);

--
-- Indexes for table `raw_materials`
--
ALTER TABLE `raw_materials`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `raw_materials_code_unique` (`code`),
  ADD KEY `raw_materials_category_id_foreign` (`category_id`),
  ADD KEY `raw_materials_code_status_index` (`code`,`status`),
  ADD KEY `raw_materials_category_is_active_index` (`is_active`),
  ADD KEY `raw_materials_status_index` (`status`),
  ADD KEY `raw_materials_supplier_id_index` (`supplier_id`),
  ADD KEY `raw_materials_is_active_index` (`is_active`);

--
-- Indexes for table `recipes`
--
ALTER TABLE `recipes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `recipes_code_unique` (`code`),
  ADD KEY `recipes_code_status_index` (`code`,`status`),
  ADD KEY `recipes_product_id_index` (`product_id`),
  ADD KEY `recipes_category_index` (`category`);

--
-- Indexes for table `recipe_ingredients`
--
ALTER TABLE `recipe_ingredients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recipe_ingredients_raw_material_id_foreign` (`raw_material_id`),
  ADD KEY `recipe_ingredients_recipe_id_raw_material_id_index` (`recipe_id`,`raw_material_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_movements_created_by_foreign` (`created_by`),
  ADD KEY `stock_movements_item_type_item_id_index` (`item_type`,`item_id`),
  ADD KEY `stock_movements_type_created_at_index` (`type`,`created_at`),
  ADD KEY `stock_movements_reference_type_reference_id_index` (`reference_type`,`reference_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `suppliers_code_unique` (`code`),
  ADD KEY `suppliers_code_is_active_index` (`code`,`is_active`),
  ADD KEY `suppliers_name_index` (`name`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transactions_transaction_number_unique` (`transaction_number`),
  ADD KEY `transactions_created_by_foreign` (`created_by`),
  ADD KEY `transactions_approved_by_foreign` (`approved_by`),
  ADD KEY `transactions_transaction_number_status_index` (`transaction_number`,`status`),
  ADD KEY `transactions_transaction_date_type_index` (`transaction_date`,`type`),
  ADD KEY `transactions_category_subcategory_index` (`category`,`subcategory`),
  ADD KEY `transactions_reference_type_reference_id_index` (`reference_type`,`reference_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_role_index` (`role`),
  ADD KEY `users_is_active_index` (`is_active`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `production_orders`
--
ALTER TABLE `production_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `quality_inspections`
--
ALTER TABLE `quality_inspections`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `raw_materials`
--
ALTER TABLE `raw_materials`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `recipes`
--
ALTER TABLE `recipes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `recipe_ingredients`
--
ALTER TABLE `recipe_ingredients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `stock_movements`
--
ALTER TABLE `stock_movements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `invoices_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `invoices_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payments_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payments_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `production_orders`
--
ALTER TABLE `production_orders`
  ADD CONSTRAINT `production_orders_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `production_orders_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `production_orders_recipe_id_foreign` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD CONSTRAINT `purchase_orders_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `purchase_orders_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD CONSTRAINT `purchase_order_items_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_order_items_raw_material_id_foreign` FOREIGN KEY (`raw_material_id`) REFERENCES `raw_materials` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `quality_inspections`
--
ALTER TABLE `quality_inspections`
  ADD CONSTRAINT `quality_inspections_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quality_inspections_production_order_id_foreign` FOREIGN KEY (`production_order_id`) REFERENCES `production_orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `raw_materials`
--
ALTER TABLE `raw_materials`
  ADD CONSTRAINT `raw_materials_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `raw_materials_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `recipes`
--
ALTER TABLE `recipes`
  ADD CONSTRAINT `recipes_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `recipe_ingredients`
--
ALTER TABLE `recipe_ingredients`
  ADD CONSTRAINT `recipe_ingredients_raw_material_id_foreign` FOREIGN KEY (`raw_material_id`) REFERENCES `raw_materials` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `recipe_ingredients_recipe_id_foreign` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD CONSTRAINT `stock_movements_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `transactions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
