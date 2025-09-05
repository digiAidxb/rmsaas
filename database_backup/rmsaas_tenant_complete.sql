-- MySQL dump 10.13  Distrib 8.0.43, for Win64 (x86_64)
--
-- Host: localhost    Database: rmsaas_gr1dub
-- ------------------------------------------------------
-- Server version	8.0.43

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `activity_logs`
--

DROP TABLE IF EXISTS `activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  `old_values` json DEFAULT NULL,
  `new_values` json DEFAULT NULL,
  `ip_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `activity_logs_user_id_created_at_index` (`user_id`,`created_at`),
  KEY `activity_logs_model_type_model_id_index` (`model_type`,`model_id`),
  KEY `activity_logs_action_created_at_index` (`action`,`created_at`),
  CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_logs`
--

LOCK TABLES `activity_logs` WRITE;
/*!40000 ALTER TABLE `activity_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_activity_logs`
--

DROP TABLE IF EXISTS `admin_activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admin_activity_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `admin_user_id` bigint unsigned DEFAULT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model_id` bigint unsigned DEFAULT NULL,
  `old_values` json DEFAULT NULL,
  `new_values` json DEFAULT NULL,
  `ip_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `admin_activity_logs_admin_user_id_created_at_index` (`admin_user_id`,`created_at`),
  KEY `admin_activity_logs_model_type_model_id_index` (`model_type`,`model_id`),
  CONSTRAINT `admin_activity_logs_admin_user_id_foreign` FOREIGN KEY (`admin_user_id`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_activity_logs`
--

LOCK TABLES `admin_activity_logs` WRITE;
/*!40000 ALTER TABLE `admin_activity_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_users`
--

DROP TABLE IF EXISTS `admin_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admin_users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'admin',
  `permissions` json DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_login_ip` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `two_factor_secret` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `two_factor_recovery_codes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admin_users_email_unique` (`email`),
  KEY `admin_users_is_active_role_index` (`is_active`,`role`),
  KEY `admin_users_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_users`
--

LOCK TABLES `admin_users` WRITE;
/*!40000 ALTER TABLE `admin_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `parent_id` bigint unsigned DEFAULT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `categories_parent_id_sort_order_index` (`parent_id`,`sort_order`),
  KEY `categories_is_active_sort_order_index` (`is_active`,`sort_order`),
  CONSTRAINT `categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `countries`
--

DROP TABLE IF EXISTS `countries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `countries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `currency_code` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `tax_rate` decimal(5,2) NOT NULL DEFAULT '0.00',
  `tax_settings` json DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `countries_code_unique` (`code`),
  KEY `countries_is_active_code_index` (`is_active`,`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `countries`
--

LOCK TABLES `countries` WRITE;
/*!40000 ALTER TABLE `countries` DISABLE KEYS */;
/*!40000 ALTER TABLE `countries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `daily_reconciliations`
--

DROP TABLE IF EXISTS `daily_reconciliations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `daily_reconciliations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reconciliation_date` date NOT NULL,
  `prepared_by` bigint unsigned NOT NULL,
  `approved_by` bigint unsigned DEFAULT NULL,
  `status` enum('draft','pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `total_sales` decimal(12,2) NOT NULL,
  `total_costs` decimal(12,2) NOT NULL,
  `total_waste` decimal(12,2) NOT NULL,
  `variance_amount` decimal(12,2) NOT NULL,
  `variance_percentage` decimal(5,2) NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `daily_reconciliations_reconciliation_date_unique` (`reconciliation_date`),
  KEY `daily_reconciliations_prepared_by_foreign` (`prepared_by`),
  KEY `daily_reconciliations_approved_by_foreign` (`approved_by`),
  KEY `daily_reconciliations_reconciliation_date_status_index` (`reconciliation_date`,`status`),
  CONSTRAINT `daily_reconciliations_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `daily_reconciliations_prepared_by_foreign` FOREIGN KEY (`prepared_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `daily_reconciliations`
--

LOCK TABLES `daily_reconciliations` WRITE;
/*!40000 ALTER TABLE `daily_reconciliations` DISABLE KEYS */;
/*!40000 ALTER TABLE `daily_reconciliations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `email_templates`
--

DROP TABLE IF EXISTS `email_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `email_templates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `html_content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `text_content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `variables` json DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_templates_name_unique` (`name`),
  KEY `email_templates_name_is_active_index` (`name`,`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `email_templates`
--

LOCK TABLES `email_templates` WRITE;
/*!40000 ALTER TABLE `email_templates` DISABLE KEYS */;
/*!40000 ALTER TABLE `email_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `file_uploads`
--

DROP TABLE IF EXISTS `file_uploads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `file_uploads` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `disk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 's3',
  `mime_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `size` bigint unsigned NOT NULL,
  `type` enum('import','image','document','export') COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('uploading','completed','failed','deleted') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `file_uploads_tenant_id_type_status_index` (`tenant_id`,`type`,`status`),
  KEY `file_uploads_created_at_index` (`created_at`),
  CONSTRAINT `file_uploads_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `file_uploads`
--

LOCK TABLES `file_uploads` WRITE;
/*!40000 ALTER TABLE `file_uploads` DISABLE KEYS */;
/*!40000 ALTER TABLE `file_uploads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `import_logs`
--

DROP TABLE IF EXISTS `import_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `import_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `import_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('processing','completed','failed','partial') COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_records` int NOT NULL,
  `processed_records` int NOT NULL,
  `failed_records` int NOT NULL,
  `error_details` json DEFAULT NULL,
  `summary` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `import_logs_user_id_import_type_status_index` (`user_id`,`import_type`,`status`),
  KEY `import_logs_status_created_at_index` (`status`,`created_at`),
  CONSTRAINT `import_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `import_logs`
--

LOCK TABLES `import_logs` WRITE;
/*!40000 ALTER TABLE `import_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `import_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_items`
--

DROP TABLE IF EXISTS `inventory_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint unsigned DEFAULT NULL,
  `unit_id` bigint unsigned NOT NULL,
  `supplier_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `barcode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_stock` decimal(12,3) NOT NULL DEFAULT '0.000',
  `minimum_stock` decimal(12,3) NOT NULL DEFAULT '0.000',
  `maximum_stock` decimal(12,3) DEFAULT NULL,
  `reorder_point` decimal(12,3) NOT NULL DEFAULT '0.000',
  `last_purchase_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `average_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `shelf_life_days` int DEFAULT NULL,
  `storage_type` enum('dry','refrigerated','frozen') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'dry',
  `is_perishable` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `inventory_items_sku_unique` (`sku`),
  KEY `inventory_items_unit_id_foreign` (`unit_id`),
  KEY `inventory_items_category_id_is_active_index` (`category_id`,`is_active`),
  KEY `inventory_items_supplier_id_is_active_index` (`supplier_id`,`is_active`),
  KEY `inventory_items_barcode_index` (`barcode`),
  KEY `inventory_items_current_stock_minimum_stock_index` (`current_stock`,`minimum_stock`),
  CONSTRAINT `inventory_items_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `inventory_items_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `inventory_items_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_items`
--

LOCK TABLES `inventory_items` WRITE;
/*!40000 ALTER TABLE `inventory_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventory_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `loss_analyses`
--

DROP TABLE IF EXISTS `loss_analyses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `loss_analyses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `inventory_item_id` bigint unsigned NOT NULL,
  `analysis_date` date NOT NULL,
  `theoretical_usage` decimal(12,3) NOT NULL,
  `actual_usage` decimal(12,3) NOT NULL,
  `variance` decimal(12,3) NOT NULL,
  `variance_percentage` decimal(5,2) NOT NULL,
  `cost_impact` decimal(10,2) NOT NULL,
  `variance_type` enum('normal','high','critical') COLLATE utf8mb4_unicode_ci NOT NULL,
  `analysis_notes` text COLLATE utf8mb4_unicode_ci,
  `recommendations` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `loss_analyses_inventory_item_id_analysis_date_unique` (`inventory_item_id`,`analysis_date`),
  KEY `loss_analyses_inventory_item_id_analysis_date_index` (`inventory_item_id`,`analysis_date`),
  KEY `loss_analyses_variance_type_analysis_date_index` (`variance_type`,`analysis_date`),
  CONSTRAINT `loss_analyses_inventory_item_id_foreign` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `loss_analyses`
--

LOCK TABLES `loss_analyses` WRITE;
/*!40000 ALTER TABLE `loss_analyses` DISABLE KEYS */;
/*!40000 ALTER TABLE `loss_analyses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu_item_variants`
--

DROP TABLE IF EXISTS `menu_item_variants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `menu_item_variants` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `menu_item_id` bigint unsigned NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price_modifier` decimal(8,2) NOT NULL DEFAULT '0.00',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `menu_item_variants_menu_item_id_type_index` (`menu_item_id`,`type`),
  CONSTRAINT `menu_item_variants_menu_item_id_foreign` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_item_variants`
--

LOCK TABLES `menu_item_variants` WRITE;
/*!40000 ALTER TABLE `menu_item_variants` DISABLE KEYS */;
/*!40000 ALTER TABLE `menu_item_variants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu_items`
--

DROP TABLE IF EXISTS `menu_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `menu_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `base_price` decimal(10,2) NOT NULL,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `images` json DEFAULT NULL,
  `nutritional_info` json DEFAULT NULL,
  `allergens` json DEFAULT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT '1',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `availability_schedule` json DEFAULT NULL,
  `preparation_time` int DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `menu_items_category_id_is_available_index` (`category_id`,`is_available`),
  KEY `menu_items_is_featured_is_available_index` (`is_featured`,`is_available`),
  CONSTRAINT `menu_items_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_items`
--

LOCK TABLES `menu_items` WRITE;
/*!40000 ALTER TABLE `menu_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `menu_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2025_08_27_225136_create_tenants_table',1),(5,'2025_08_27_225745_add_tenant_id_to_users_table',1),(6,'2025_08_28_051340_add_database_credentials_to_tenants_table',1),(7,'2025_08_28_065752_create_comprehensive_landlord_schema',1),(8,'2025_08_28_065845_create_comprehensive_tenant_schema',1),(9,'2025_08_28_074357_create_pulse_tables',1),(10,'2025_08_28_081346_add_preferred_language_to_users_table',1),(11,'2025_08_28_150049_add_extended_user_fields_to_users_table',1),(12,'2025_08_28_154656_create_permissions_table',1),(13,'2025_08_28_154742_create_roles_table',1),(14,'2025_08_28_154750_create_role_permissions_table',1),(15,'2025_08_28_154800_create_user_roles_table',1),(16,'2025_08_29_184344_add_onboarding_fields_to_tenants_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `tenant_subscription_id` bigint unsigned DEFAULT NULL,
  `stripe_payment_intent_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `tax_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(10,2) NOT NULL,
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','succeeded','failed','refunded','partially_refunded') COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('subscription','one_time','refund') COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `stripe_metadata` json DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `refunded_at` timestamp NULL DEFAULT NULL,
  `refunded_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payments_invoice_number_unique` (`invoice_number`),
  KEY `payments_tenant_subscription_id_foreign` (`tenant_subscription_id`),
  KEY `payments_tenant_id_status_index` (`tenant_id`,`status`),
  KEY `payments_invoice_number_index` (`invoice_number`),
  CONSTRAINT `payments_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_tenant_subscription_id_foreign` FOREIGN KEY (`tenant_subscription_id`) REFERENCES `tenant_subscriptions` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `module` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_unique` (`name`),
  UNIQUE KEY `permissions_slug_unique` (`slug`),
  KEY `permissions_module_category_index` (`module`,`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pulse_aggregates`
--

DROP TABLE IF EXISTS `pulse_aggregates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pulse_aggregates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `bucket` int unsigned NOT NULL,
  `period` mediumint unsigned NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `key` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `key_hash` binary(16) GENERATED ALWAYS AS (unhex(md5(`key`))) VIRTUAL,
  `aggregate` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` decimal(20,2) NOT NULL,
  `count` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pulse_aggregates_bucket_period_type_aggregate_key_hash_unique` (`bucket`,`period`,`type`,`aggregate`,`key_hash`),
  KEY `pulse_aggregates_period_bucket_index` (`period`,`bucket`),
  KEY `pulse_aggregates_type_index` (`type`),
  KEY `pulse_aggregates_period_type_aggregate_bucket_index` (`period`,`type`,`aggregate`,`bucket`)
) ENGINE=InnoDB AUTO_INCREMENT=2189 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pulse_aggregates`
--

LOCK TABLES `pulse_aggregates` WRITE;
/*!40000 ALTER TABLE `pulse_aggregates` DISABLE KEYS */;
INSERT INTO `pulse_aggregates` (`id`, `bucket`, `period`, `type`, `key`, `aggregate`, `value`, `count`) VALUES (1,1756638300,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',1.00,NULL),(2,1756638000,360,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',1.00,NULL),(3,1756637280,1440,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',2.00,NULL),(4,1756631520,10080,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',9.00,NULL),(5,1756638300,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',1.00,NULL),(6,1756638000,360,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',1.00,NULL),(7,1756637280,1440,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',2.00,NULL),(8,1756631520,10080,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',9.00,NULL),(9,1756638300,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',2392.00,NULL),(10,1756638000,360,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',2392.00,NULL),(11,1756637280,1440,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',2407.00,NULL),(12,1756631520,10080,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',2849.00,NULL),(13,1756638300,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2027.00,NULL),(14,1756638000,360,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2027.00,NULL),(15,1756637280,1440,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2027.00,NULL),(16,1756631520,10080,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2049.00,NULL),(17,1756638300,60,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','count',1.00,NULL),(18,1756638000,360,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','count',1.00,NULL),(19,1756637280,1440,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','count',1.00,NULL),(20,1756631520,10080,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','count',1.00,NULL),(21,1756638300,60,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','max',1020.00,NULL),(22,1756638000,360,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','max',1020.00,NULL),(23,1756637280,1440,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','max',1020.00,NULL),(24,1756631520,10080,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','max',1020.00,NULL),(25,1756638660,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',1.00,NULL),(26,1756638360,360,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',1.00,NULL),(27,1756638660,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',1.00,NULL),(28,1756638360,360,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',1.00,NULL),(33,1756638660,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',2407.00,NULL),(34,1756638360,360,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',2407.00,NULL),(35,1756638660,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2024.00,NULL),(36,1756638360,360,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2024.00,NULL),(41,1756638840,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',1.00,NULL),(42,1756638720,360,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',3.00,NULL),(43,1756638720,1440,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',7.00,NULL),(44,1756638840,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',1.00,NULL),(45,1756638720,360,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',3.00,NULL),(46,1756638720,1440,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',7.00,NULL),(49,1756638840,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',2419.00,NULL),(50,1756638720,360,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',2602.00,NULL),(51,1756638720,1440,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',2849.00,NULL),(52,1756638840,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2026.00,NULL),(53,1756638720,360,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2042.00,NULL),(54,1756638720,1440,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2049.00,NULL),(57,1756638900,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',2.00,NULL),(58,1756638900,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',2.00,NULL),(65,1756638900,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',2602.00,NULL),(66,1756638900,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2042.00,NULL),(89,1756639140,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',4.00,NULL),(90,1756639080,360,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',4.00,NULL),(91,1756639140,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',4.00,NULL),(92,1756639080,360,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',4.00,NULL),(97,1756639140,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',2849.00,NULL),(98,1756639080,360,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',2849.00,NULL),(99,1756639140,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2049.00,NULL),(100,1756639080,360,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2049.00,NULL),(153,1756639200,60,'user_request','336','count',2.00,NULL),(154,1756639080,360,'user_request','336','count',2.00,NULL),(155,1756638720,1440,'user_request','336','count',2.00,NULL),(156,1756631520,10080,'user_request','336','count',2.00,NULL),(161,1756665120,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',1.00,NULL),(162,1756665000,360,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',1.00,NULL),(163,1756664640,1440,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',1.00,NULL),(164,1756661760,10080,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',5.00,NULL),(165,1756665120,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',1.00,NULL),(166,1756665000,360,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',3.00,NULL),(167,1756664640,1440,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',3.00,NULL),(168,1756661760,10080,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',8.00,NULL),(169,1756665120,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',2607.00,NULL),(170,1756665000,360,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',2607.00,NULL),(171,1756664640,1440,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',2607.00,NULL),(172,1756661760,10080,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',2783.00,NULL),(173,1756665120,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2055.00,NULL),(174,1756665000,360,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2055.00,NULL),(175,1756664640,1440,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2055.00,NULL),(176,1756661760,10080,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2055.00,NULL),(177,1756665180,60,'user_request','336','count',11.00,NULL),(178,1756665000,360,'user_request','336','count',11.00,NULL),(179,1756664640,1440,'user_request','336','count',12.00,NULL),(180,1756661760,10080,'user_request','336','count',66.00,NULL),(185,1756665180,60,'slow_request','[\"GET\",\"\\/onboarding\\/step\\/{step}\",\"App\\\\Http\\\\Controllers\\\\OnboardingController@step\"]','count',2.00,NULL),(186,1756665000,360,'slow_request','[\"GET\",\"\\/onboarding\\/step\\/{step}\",\"App\\\\Http\\\\Controllers\\\\OnboardingController@step\"]','count',2.00,NULL),(187,1756664640,1440,'slow_request','[\"GET\",\"\\/onboarding\\/step\\/{step}\",\"App\\\\Http\\\\Controllers\\\\OnboardingController@step\"]','count',2.00,NULL),(188,1756661760,10080,'slow_request','[\"GET\",\"\\/onboarding\\/step\\/{step}\",\"App\\\\Http\\\\Controllers\\\\OnboardingController@step\"]','count',2.00,NULL),(189,1756665180,60,'slow_user_request','336','count',2.00,NULL),(190,1756665000,360,'slow_user_request','336','count',2.00,NULL),(191,1756664640,1440,'slow_user_request','336','count',3.00,NULL),(192,1756661760,10080,'slow_user_request','336','count',23.00,NULL),(193,1756665180,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',2.00,NULL),(201,1756665180,60,'slow_request','[\"GET\",\"\\/onboarding\\/step\\/{step}\",\"App\\\\Http\\\\Controllers\\\\OnboardingController@step\"]','max',2430.00,NULL),(202,1756665000,360,'slow_request','[\"GET\",\"\\/onboarding\\/step\\/{step}\",\"App\\\\Http\\\\Controllers\\\\OnboardingController@step\"]','max',2430.00,NULL),(203,1756664640,1440,'slow_request','[\"GET\",\"\\/onboarding\\/step\\/{step}\",\"App\\\\Http\\\\Controllers\\\\OnboardingController@step\"]','max',2430.00,NULL),(204,1756661760,10080,'slow_request','[\"GET\",\"\\/onboarding\\/step\\/{step}\",\"App\\\\Http\\\\Controllers\\\\OnboardingController@step\"]','max',2430.00,NULL),(205,1756665180,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2038.00,NULL),(261,1756665960,60,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','count',1.00,NULL),(262,1756665720,360,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','count',1.00,NULL),(263,1756664640,1440,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','count',1.00,NULL),(264,1756661760,10080,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','count',15.00,NULL),(265,1756665960,60,'slow_user_request','336','count',1.00,NULL),(266,1756665720,360,'slow_user_request','336','count',1.00,NULL),(267,1756665960,60,'user_request','336','count',1.00,NULL),(268,1756665720,360,'user_request','336','count',1.00,NULL),(269,1756665960,60,'exception','[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]','count',1.00,NULL),(270,1756665720,360,'exception','[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]','count',1.00,NULL),(271,1756664640,1440,'exception','[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]','count',1.00,NULL),(272,1756661760,10080,'exception','[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]','count',13.00,NULL),(277,1756665960,60,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','max',4171.00,NULL),(278,1756665720,360,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','max',4171.00,NULL),(279,1756664640,1440,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','max',4171.00,NULL),(280,1756661760,10080,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','max',8735.00,NULL),(281,1756665960,60,'exception','[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]','max',1756666011.00,NULL),(282,1756665720,360,'exception','[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]','max',1756666011.00,NULL),(283,1756664640,1440,'exception','[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]','max',1756666011.00,NULL),(284,1756661760,10080,'exception','[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]','max',1756667558.00,NULL),(285,1756666920,60,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','count',1.00,NULL),(286,1756666800,360,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','count',5.00,NULL),(287,1756666080,1440,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','count',10.00,NULL),(288,1756666920,60,'slow_user_request','336','count',1.00,NULL),(289,1756666800,360,'slow_user_request','336','count',5.00,NULL),(290,1756666080,1440,'slow_user_request','336','count',10.00,NULL),(291,1756666920,60,'user_request','336','count',1.00,NULL),(292,1756666800,360,'user_request','336','count',5.00,NULL),(293,1756666080,1440,'user_request','336','count',10.00,NULL),(294,1756666920,60,'exception','[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]','count',1.00,NULL),(295,1756666800,360,'exception','[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]','count',5.00,NULL),(296,1756666080,1440,'exception','[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]','count',10.00,NULL),(301,1756666920,60,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','max',2567.00,NULL),(302,1756666800,360,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','max',2567.00,NULL),(303,1756666080,1440,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','max',2987.00,NULL),(304,1756666920,60,'exception','[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]','max',1756666978.00,NULL),(305,1756666800,360,'exception','[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]','max',1756667111.00,NULL),(306,1756666080,1440,'exception','[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]','max',1756667361.00,NULL),(309,1756666980,60,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','count',2.00,NULL),(310,1756666980,60,'slow_user_request','336','count',2.00,NULL),(311,1756666980,60,'user_request','336','count',2.00,NULL),(312,1756666980,60,'exception','[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]','count',2.00,NULL),(325,1756666980,60,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','max',1301.00,NULL),(326,1756666980,60,'exception','[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]','max',1756667009.00,NULL),(333,1756666980,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',1.00,NULL),(334,1756666800,360,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',2.00,NULL),(335,1756666080,1440,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',4.00,NULL),(336,1756666980,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',1.00,NULL),(337,1756666800,360,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',2.00,NULL),(338,1756666080,1440,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',4.00,NULL),(341,1756666980,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',2466.00,NULL),(342,1756666800,360,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',2478.00,NULL),(343,1756666080,1440,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',2783.00,NULL),(344,1756666980,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2028.00,NULL),(345,1756666800,360,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2030.00,NULL),(346,1756666080,1440,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2054.00,NULL),(373,1756667040,60,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','count',1.00,NULL),(374,1756667040,60,'slow_user_request','336','count',1.00,NULL),(375,1756667040,60,'user_request','336','count',1.00,NULL),(376,1756667040,60,'exception','[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]','count',1.00,NULL),(389,1756667040,60,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','max',1245.00,NULL),(390,1756667040,60,'exception','[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]','max',1756667095.00,NULL),(397,1756667040,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',1.00,NULL),(398,1756667100,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',1.00,NULL),(405,1756667040,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',2478.00,NULL),(406,1756667100,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2030.00,NULL),(413,1756667100,60,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','count',1.00,NULL),(414,1756667100,60,'slow_user_request','336','count',1.00,NULL),(415,1756667100,60,'user_request','336','count',1.00,NULL),(416,1756667100,60,'exception','[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]','count',1.00,NULL),(429,1756667100,60,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','max',2306.00,NULL),(430,1756667100,60,'exception','[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]','max',1756667111.00,NULL),(437,1756667160,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',1.00,NULL),(438,1756667160,360,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',2.00,NULL),(439,1756667160,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',1.00,NULL),(440,1756667160,360,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',2.00,NULL),(445,1756667160,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',2487.00,NULL),(446,1756667160,360,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',2783.00,NULL),(447,1756667160,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2054.00,NULL),(448,1756667160,360,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2054.00,NULL),(453,1756667220,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',1.00,NULL),(454,1756667220,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',1.00,NULL),(461,1756667220,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',2783.00,NULL),(462,1756667220,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2047.00,NULL),(469,1756667280,60,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','count',2.00,NULL),(470,1756667160,360,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','count',5.00,NULL),(471,1756667280,60,'slow_user_request','336','count',2.00,NULL),(472,1756667160,360,'slow_user_request','336','count',5.00,NULL),(473,1756667280,60,'user_request','336','count',2.00,NULL),(474,1756667160,360,'user_request','336','count',5.00,NULL),(475,1756667280,60,'exception','[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]','count',2.00,NULL),(476,1756667160,360,'exception','[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]','count',5.00,NULL),(485,1756667280,60,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','max',2987.00,NULL),(486,1756667160,360,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','max',2987.00,NULL),(487,1756667280,60,'exception','[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]','max',1756667305.00,NULL),(488,1756667160,360,'exception','[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]','max',1756667361.00,NULL),(517,1756667340,60,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','count',3.00,NULL),(518,1756667340,60,'slow_user_request','336','count',3.00,NULL),(519,1756667340,60,'user_request','336','count',3.00,NULL),(520,1756667340,60,'exception','[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]','count',3.00,NULL),(533,1756667340,60,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','max',2191.00,NULL),(534,1756667340,60,'exception','[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]','max',1756667361.00,NULL),(589,1756667520,60,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','count',2.00,NULL),(590,1756667520,360,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','count',4.00,NULL),(591,1756667520,1440,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','count',4.00,NULL),(592,1756667520,60,'slow_user_request','336','count',2.00,NULL),(593,1756667520,360,'slow_user_request','336','count',5.00,NULL),(594,1756667520,1440,'slow_user_request','336','count',10.00,NULL),(595,1756667520,60,'user_request','336','count',2.00,NULL),(596,1756667520,360,'user_request','336','count',6.00,NULL),(597,1756667520,1440,'user_request','336','count',37.00,NULL),(598,1756667520,60,'exception','[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]','count',2.00,NULL),(599,1756667520,360,'exception','[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]','count',2.00,NULL),(600,1756667520,1440,'exception','[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]','count',2.00,NULL),(605,1756667520,60,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','max',8735.00,NULL),(606,1756667520,360,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','max',8735.00,NULL),(607,1756667520,1440,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','max',8735.00,NULL),(608,1756667520,60,'exception','[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]','max',1756667558.00,NULL),(609,1756667520,360,'exception','[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]','max',1756667558.00,NULL),(610,1756667520,1440,'exception','[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]','max',1756667558.00,NULL),(637,1756667580,60,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','count',1.00,NULL),(638,1756667580,60,'slow_user_request','336','count',1.00,NULL),(639,1756667580,60,'user_request','336','count',1.00,NULL),(640,1756667580,60,'exception','[\"InvalidArgumentException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]','count',1.00,NULL),(641,1756667520,360,'exception','[\"InvalidArgumentException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]','count',2.00,NULL),(642,1756667520,1440,'exception','[\"InvalidArgumentException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]','count',2.00,NULL),(643,1756661760,10080,'exception','[\"InvalidArgumentException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]','count',2.00,NULL),(653,1756667580,60,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','max',2220.00,NULL),(654,1756667580,60,'exception','[\"InvalidArgumentException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]','max',1756667608.00,NULL),(655,1756667520,360,'exception','[\"InvalidArgumentException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]','max',1756667660.00,NULL),(656,1756667520,1440,'exception','[\"InvalidArgumentException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]','max',1756667660.00,NULL),(657,1756661760,10080,'exception','[\"InvalidArgumentException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]','max',1756667660.00,NULL),(661,1756667640,60,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','count',1.00,NULL),(662,1756667640,60,'slow_user_request','336','count',1.00,NULL),(663,1756667640,60,'user_request','336','count',1.00,NULL),(664,1756667640,60,'exception','[\"InvalidArgumentException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]','count',1.00,NULL),(677,1756667640,60,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','max',6842.00,NULL),(678,1756667640,60,'exception','[\"InvalidArgumentException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]','max',1756667660.00,NULL),(685,1756667760,60,'user_request','336','count',1.00,NULL),(689,1756667820,60,'slow_request','[\"GET\",\"\\/imports\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@index\"]','count',1.00,NULL),(690,1756667520,360,'slow_request','[\"GET\",\"\\/imports\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@index\"]','count',1.00,NULL),(691,1756667520,1440,'slow_request','[\"GET\",\"\\/imports\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@index\"]','count',5.00,NULL),(692,1756661760,10080,'slow_request','[\"GET\",\"\\/imports\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@index\"]','count',5.00,NULL),(693,1756667820,60,'slow_user_request','336','count',1.00,NULL),(694,1756667820,60,'user_request','336','count',1.00,NULL),(695,1756667820,60,'exception','[\"Error\",\"resources\\\\views\\\\tenant\\\\imports\\\\index.blade.php\"]','count',1.00,NULL),(696,1756667520,360,'exception','[\"Error\",\"resources\\\\views\\\\tenant\\\\imports\\\\index.blade.php\"]','count',1.00,NULL),(697,1756667520,1440,'exception','[\"Error\",\"resources\\\\views\\\\tenant\\\\imports\\\\index.blade.php\"]','count',5.00,NULL),(698,1756661760,10080,'exception','[\"Error\",\"resources\\\\views\\\\tenant\\\\imports\\\\index.blade.php\"]','count',5.00,NULL),(705,1756667820,60,'slow_request','[\"GET\",\"\\/imports\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@index\"]','max',3984.00,NULL),(706,1756667520,360,'slow_request','[\"GET\",\"\\/imports\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@index\"]','max',3984.00,NULL),(707,1756667520,1440,'slow_request','[\"GET\",\"\\/imports\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@index\"]','max',3984.00,NULL),(708,1756661760,10080,'slow_request','[\"GET\",\"\\/imports\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@index\"]','max',3984.00,NULL),(709,1756667820,60,'exception','[\"Error\",\"resources\\\\views\\\\tenant\\\\imports\\\\index.blade.php\"]','max',1756667879.00,NULL),(710,1756667520,360,'exception','[\"Error\",\"resources\\\\views\\\\tenant\\\\imports\\\\index.blade.php\"]','max',1756667879.00,NULL),(711,1756667520,1440,'exception','[\"Error\",\"resources\\\\views\\\\tenant\\\\imports\\\\index.blade.php\"]','max',1756668780.00,NULL),(712,1756661760,10080,'exception','[\"Error\",\"resources\\\\views\\\\tenant\\\\imports\\\\index.blade.php\"]','max',1756668780.00,NULL),(713,1756667880,60,'user_request','336','count',3.00,NULL),(714,1756667880,360,'user_request','336','count',13.00,NULL),(725,1756667940,60,'user_request','336','count',1.00,NULL),(729,1756668000,60,'user_request','336','count',1.00,NULL),(733,1756668060,60,'user_request','336','count',2.00,NULL),(741,1756668120,60,'slow_request','[\"GET\",\"\\/imports\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@index\"]','count',1.00,NULL),(742,1756667880,360,'slow_request','[\"GET\",\"\\/imports\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@index\"]','count',1.00,NULL),(743,1756668120,60,'slow_user_request','336','count',1.00,NULL),(744,1756667880,360,'slow_user_request','336','count',1.00,NULL),(745,1756668120,60,'user_request','336','count',3.00,NULL),(746,1756668120,60,'exception','[\"Error\",\"resources\\\\views\\\\tenant\\\\imports\\\\index.blade.php\"]','count',1.00,NULL),(747,1756667880,360,'exception','[\"Error\",\"resources\\\\views\\\\tenant\\\\imports\\\\index.blade.php\"]','count',1.00,NULL),(757,1756668120,60,'slow_request','[\"GET\",\"\\/imports\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@index\"]','max',2398.00,NULL),(758,1756667880,360,'slow_request','[\"GET\",\"\\/imports\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@index\"]','max',2398.00,NULL),(759,1756668120,60,'exception','[\"Error\",\"resources\\\\views\\\\tenant\\\\imports\\\\index.blade.php\"]','max',1756668128.00,NULL),(760,1756667880,360,'exception','[\"Error\",\"resources\\\\views\\\\tenant\\\\imports\\\\index.blade.php\"]','max',1756668128.00,NULL),(773,1756668180,60,'user_request','336','count',3.00,NULL),(785,1756668300,60,'user_request','336','count',5.00,NULL),(786,1756668240,360,'user_request','336','count',14.00,NULL),(793,1756668300,60,'slow_request','[\"GET\",\"\\/imports\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@index\"]','count',1.00,NULL),(794,1756668240,360,'slow_request','[\"GET\",\"\\/imports\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@index\"]','count',1.00,NULL),(795,1756668300,60,'slow_user_request','336','count',1.00,NULL),(796,1756668240,360,'slow_user_request','336','count',2.00,NULL),(797,1756668300,60,'exception','[\"Error\",\"resources\\\\views\\\\tenant\\\\imports\\\\index.blade.php\"]','count',1.00,NULL),(798,1756668240,360,'exception','[\"Error\",\"resources\\\\views\\\\tenant\\\\imports\\\\index.blade.php\"]','count',1.00,NULL),(809,1756668300,60,'slow_request','[\"GET\",\"\\/imports\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@index\"]','max',2271.00,NULL),(810,1756668240,360,'slow_request','[\"GET\",\"\\/imports\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@index\"]','max',2271.00,NULL),(811,1756668300,60,'exception','[\"Error\",\"resources\\\\views\\\\tenant\\\\imports\\\\index.blade.php\"]','max',1756668337.00,NULL),(812,1756668240,360,'exception','[\"Error\",\"resources\\\\views\\\\tenant\\\\imports\\\\index.blade.php\"]','max',1756668337.00,NULL),(825,1756668360,60,'slow_request','[\"GET\",\"\\/profile\",\"App\\\\Http\\\\Controllers\\\\ProfileController@edit\"]','count',1.00,NULL),(826,1756668240,360,'slow_request','[\"GET\",\"\\/profile\",\"App\\\\Http\\\\Controllers\\\\ProfileController@edit\"]','count',1.00,NULL),(827,1756667520,1440,'slow_request','[\"GET\",\"\\/profile\",\"App\\\\Http\\\\Controllers\\\\ProfileController@edit\"]','count',1.00,NULL),(828,1756661760,10080,'slow_request','[\"GET\",\"\\/profile\",\"App\\\\Http\\\\Controllers\\\\ProfileController@edit\"]','count',1.00,NULL),(829,1756668360,60,'slow_user_request','336','count',1.00,NULL),(830,1756668360,60,'user_request','336','count',3.00,NULL),(831,1756668360,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',1.00,NULL),(832,1756668240,360,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',1.00,NULL),(833,1756667520,1440,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',1.00,NULL),(841,1756668360,60,'slow_request','[\"GET\",\"\\/profile\",\"App\\\\Http\\\\Controllers\\\\ProfileController@edit\"]','max',2489.00,NULL),(842,1756668240,360,'slow_request','[\"GET\",\"\\/profile\",\"App\\\\Http\\\\Controllers\\\\ProfileController@edit\"]','max',2489.00,NULL),(843,1756667520,1440,'slow_request','[\"GET\",\"\\/profile\",\"App\\\\Http\\\\Controllers\\\\ProfileController@edit\"]','max',2489.00,NULL),(844,1756661760,10080,'slow_request','[\"GET\",\"\\/profile\",\"App\\\\Http\\\\Controllers\\\\ProfileController@edit\"]','max',2489.00,NULL),(845,1756668360,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2039.00,NULL),(846,1756668240,360,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2039.00,NULL),(847,1756667520,1440,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2039.00,NULL),(857,1756668420,60,'user_request','336','count',4.00,NULL),(873,1756668480,60,'user_request','336','count',1.00,NULL),(877,1756668540,60,'user_request','336','count',1.00,NULL),(881,1756668600,60,'user_request','336','count',2.00,NULL),(882,1756668600,360,'user_request','336','count',4.00,NULL),(885,1756668600,60,'slow_request','[\"GET\",\"\\/imports\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@index\"]','count',1.00,NULL),(886,1756668600,360,'slow_request','[\"GET\",\"\\/imports\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@index\"]','count',2.00,NULL),(887,1756668600,60,'slow_user_request','336','count',1.00,NULL),(888,1756668600,360,'slow_user_request','336','count',2.00,NULL),(889,1756668600,60,'exception','[\"Error\",\"resources\\\\views\\\\tenant\\\\imports\\\\index.blade.php\"]','count',1.00,NULL),(890,1756668600,360,'exception','[\"Error\",\"resources\\\\views\\\\tenant\\\\imports\\\\index.blade.php\"]','count',2.00,NULL),(901,1756668600,60,'slow_request','[\"GET\",\"\\/imports\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@index\"]','max',2456.00,NULL),(902,1756668600,360,'slow_request','[\"GET\",\"\\/imports\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@index\"]','max',3668.00,NULL),(903,1756668600,60,'exception','[\"Error\",\"resources\\\\views\\\\tenant\\\\imports\\\\index.blade.php\"]','max',1756668645.00,NULL),(904,1756668600,360,'exception','[\"Error\",\"resources\\\\views\\\\tenant\\\\imports\\\\index.blade.php\"]','max',1756668780.00,NULL),(909,1756668660,60,'user_request','336','count',1.00,NULL),(913,1756668780,60,'slow_request','[\"GET\",\"\\/imports\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@index\"]','count',1.00,NULL),(914,1756668780,60,'slow_user_request','336','count',1.00,NULL),(915,1756668780,60,'user_request','336','count',1.00,NULL),(916,1756668780,60,'exception','[\"Error\",\"resources\\\\views\\\\tenant\\\\imports\\\\index.blade.php\"]','count',1.00,NULL),(929,1756668780,60,'slow_request','[\"GET\",\"\\/imports\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@index\"]','max',3668.00,NULL),(930,1756668780,60,'exception','[\"Error\",\"resources\\\\views\\\\tenant\\\\imports\\\\index.blade.php\"]','max',1756668780.00,NULL),(937,1756669200,60,'user_request','336','count',6.00,NULL),(938,1756668960,360,'user_request','336','count',7.00,NULL),(939,1756668960,1440,'user_request','336','count',7.00,NULL),(961,1756669260,60,'user_request','336','count',1.00,NULL),(965,1756693200,60,'slow_request','[\"GET\",\"\\/\",\"Closure\"]','count',1.00,NULL),(966,1756693080,360,'slow_request','[\"GET\",\"\\/\",\"Closure\"]','count',1.00,NULL),(967,1756692000,1440,'slow_request','[\"GET\",\"\\/\",\"Closure\"]','count',1.00,NULL),(968,1756692000,10080,'slow_request','[\"GET\",\"\\/\",\"Closure\"]','count',1.00,NULL),(969,1756693200,60,'slow_request','[\"GET\",\"\\/\",\"Closure\"]','max',4669.00,NULL),(970,1756693080,360,'slow_request','[\"GET\",\"\\/\",\"Closure\"]','max',4669.00,NULL),(971,1756692000,1440,'slow_request','[\"GET\",\"\\/\",\"Closure\"]','max',4669.00,NULL),(972,1756692000,10080,'slow_request','[\"GET\",\"\\/\",\"Closure\"]','max',4669.00,NULL),(973,1756693200,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',1.00,NULL),(974,1756693080,360,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',1.00,NULL),(975,1756692000,1440,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',1.00,NULL),(976,1756692000,10080,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',1.00,NULL),(977,1756693200,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',1.00,NULL),(978,1756693080,360,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',1.00,NULL),(979,1756692000,1440,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',1.00,NULL),(980,1756692000,10080,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',1.00,NULL),(981,1756693200,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',5661.00,NULL),(982,1756693080,360,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',5661.00,NULL),(983,1756692000,1440,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',5661.00,NULL),(984,1756692000,10080,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',5661.00,NULL),(985,1756693200,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2590.00,NULL),(986,1756693080,360,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2590.00,NULL),(987,1756692000,1440,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2590.00,NULL),(988,1756692000,10080,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2590.00,NULL),(989,1756693200,60,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','count',1.00,NULL),(990,1756693080,360,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','count',1.00,NULL),(991,1756692000,1440,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','count',1.00,NULL),(992,1756692000,10080,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','count',1.00,NULL),(993,1756693200,60,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','max',2497.00,NULL),(994,1756693080,360,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','max',2497.00,NULL),(995,1756692000,1440,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','max',2497.00,NULL),(996,1756692000,10080,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','max',2497.00,NULL),(997,1756693200,60,'user_request','336','count',1.00,NULL),(998,1756693080,360,'user_request','336','count',6.00,NULL),(999,1756692000,1440,'user_request','336','count',6.00,NULL),(1000,1756692000,10080,'user_request','336','count',6.00,NULL),(1001,1756693260,60,'slow_request','[\"GET\",\"\\/imports\\/create\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@create\"]','count',1.00,NULL),(1002,1756693080,360,'slow_request','[\"GET\",\"\\/imports\\/create\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@create\"]','count',1.00,NULL),(1003,1756692000,1440,'slow_request','[\"GET\",\"\\/imports\\/create\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@create\"]','count',1.00,NULL),(1004,1756692000,10080,'slow_request','[\"GET\",\"\\/imports\\/create\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@create\"]','count',1.00,NULL),(1005,1756693260,60,'slow_user_request','336','count',1.00,NULL),(1006,1756693080,360,'slow_user_request','336','count',1.00,NULL),(1007,1756692000,1440,'slow_user_request','336','count',1.00,NULL),(1008,1756692000,10080,'slow_user_request','336','count',1.00,NULL),(1009,1756693260,60,'user_request','336','count',4.00,NULL),(1013,1756693260,60,'slow_request','[\"GET\",\"\\/imports\\/create\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@create\"]','max',1416.00,NULL),(1014,1756693080,360,'slow_request','[\"GET\",\"\\/imports\\/create\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@create\"]','max',1416.00,NULL),(1015,1756692000,1440,'slow_request','[\"GET\",\"\\/imports\\/create\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@create\"]','max',1416.00,NULL),(1016,1756692000,10080,'slow_request','[\"GET\",\"\\/imports\\/create\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@create\"]','max',1416.00,NULL),(1029,1756693380,60,'user_request','336','count',1.00,NULL),(1033,1756703700,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',1.00,NULL),(1034,1756703520,360,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',1.00,NULL),(1035,1756703520,1440,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',11.00,NULL),(1036,1756702080,10080,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',15.00,NULL),(1037,1756703700,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',1.00,NULL),(1038,1756703520,360,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',1.00,NULL),(1039,1756703520,1440,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',11.00,NULL),(1040,1756702080,10080,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',15.00,NULL),(1041,1756703700,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',2574.00,NULL),(1042,1756703520,360,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',2574.00,NULL),(1043,1756703520,1440,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',3197.00,NULL),(1044,1756702080,10080,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',23673.00,NULL),(1045,1756703700,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2039.00,NULL),(1046,1756703520,360,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2039.00,NULL),(1047,1756703520,1440,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2057.00,NULL),(1048,1756702080,10080,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2702.00,NULL),(1049,1756703760,60,'user_request','336','count',2.00,NULL),(1050,1756703520,360,'user_request','336','count',3.00,NULL),(1051,1756703520,1440,'user_request','336','count',6.00,NULL),(1052,1756702080,10080,'user_request','336','count',45.00,NULL),(1053,1756703760,60,'slow_request','[\"POST\",\"\\/imports\\/clear-data\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@clearData\"]','count',1.00,NULL),(1054,1756703520,360,'slow_request','[\"POST\",\"\\/imports\\/clear-data\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@clearData\"]','count',1.00,NULL),(1055,1756703520,1440,'slow_request','[\"POST\",\"\\/imports\\/clear-data\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@clearData\"]','count',2.00,NULL),(1056,1756702080,10080,'slow_request','[\"POST\",\"\\/imports\\/clear-data\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@clearData\"]','count',2.00,NULL),(1057,1756703760,60,'slow_user_request','336','count',1.00,NULL),(1058,1756703520,360,'slow_user_request','336','count',1.00,NULL),(1059,1756703520,1440,'slow_user_request','336','count',3.00,NULL),(1060,1756702080,10080,'slow_user_request','336','count',4.00,NULL),(1061,1756703760,60,'exception','[\"Error\",\"app\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController.php:236\"]','count',1.00,NULL),(1062,1756703520,360,'exception','[\"Error\",\"app\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController.php:236\"]','count',2.00,NULL),(1063,1756703520,1440,'exception','[\"Error\",\"app\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController.php:236\"]','count',3.00,NULL),(1064,1756702080,10080,'exception','[\"Error\",\"app\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController.php:236\"]','count',3.00,NULL),(1069,1756703760,60,'slow_request','[\"POST\",\"\\/imports\\/clear-data\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@clearData\"]','max',1926.00,NULL),(1070,1756703520,360,'slow_request','[\"POST\",\"\\/imports\\/clear-data\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@clearData\"]','max',1926.00,NULL),(1071,1756703520,1440,'slow_request','[\"POST\",\"\\/imports\\/clear-data\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@clearData\"]','max',4422.00,NULL),(1072,1756702080,10080,'slow_request','[\"POST\",\"\\/imports\\/clear-data\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@clearData\"]','max',4422.00,NULL),(1073,1756703760,60,'exception','[\"Error\",\"app\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController.php:236\"]','max',1756703819.00,NULL),(1074,1756703520,360,'exception','[\"Error\",\"app\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController.php:236\"]','max',1756703834.00,NULL),(1075,1756703520,1440,'exception','[\"Error\",\"app\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController.php:236\"]','max',1756704128.00,NULL),(1076,1756702080,10080,'exception','[\"Error\",\"app\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController.php:236\"]','max',1756704128.00,NULL),(1077,1756703820,60,'user_request','336','count',1.00,NULL),(1078,1756703820,60,'exception','[\"Error\",\"app\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController.php:236\"]','count',1.00,NULL),(1085,1756703820,60,'exception','[\"Error\",\"app\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController.php:236\"]','max',1756703834.00,NULL),(1089,1756703940,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',2.00,NULL),(1090,1756703880,360,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',10.00,NULL),(1091,1756703940,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',2.00,NULL),(1092,1756703880,360,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',10.00,NULL),(1097,1756703940,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',2559.00,NULL),(1098,1756703880,360,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',3197.00,NULL),(1099,1756703940,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2035.00,NULL),(1100,1756703880,360,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2057.00,NULL),(1121,1756704000,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',4.00,NULL),(1122,1756704000,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',4.00,NULL),(1129,1756704000,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',2465.00,NULL),(1130,1756704000,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2057.00,NULL),(1185,1756704060,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',4.00,NULL),(1186,1756704060,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',4.00,NULL),(1193,1756704060,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',3197.00,NULL),(1194,1756704060,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2056.00,NULL),(1249,1756704060,60,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','count',1.00,NULL),(1250,1756703880,360,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','count',1.00,NULL),(1251,1756703520,1440,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','count',1.00,NULL),(1252,1756702080,10080,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','count',1.00,NULL),(1253,1756704060,60,'slow_user_request','336','count',1.00,NULL),(1254,1756703880,360,'slow_user_request','336','count',2.00,NULL),(1255,1756704060,60,'user_request','336','count',1.00,NULL),(1256,1756703880,360,'user_request','336','count',3.00,NULL),(1261,1756704060,60,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','max',1067.00,NULL),(1262,1756703880,360,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','max',1067.00,NULL),(1263,1756703520,1440,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','max',1067.00,NULL),(1264,1756702080,10080,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','max',1067.00,NULL),(1265,1756704120,60,'slow_request','[\"POST\",\"\\/imports\\/clear-data\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@clearData\"]','count',1.00,NULL),(1266,1756703880,360,'slow_request','[\"POST\",\"\\/imports\\/clear-data\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@clearData\"]','count',1.00,NULL),(1267,1756704120,60,'slow_user_request','336','count',1.00,NULL),(1268,1756704120,60,'user_request','336','count',2.00,NULL),(1269,1756704120,60,'exception','[\"Error\",\"app\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController.php:236\"]','count',1.00,NULL),(1270,1756703880,360,'exception','[\"Error\",\"app\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController.php:236\"]','count',1.00,NULL),(1281,1756704120,60,'slow_request','[\"POST\",\"\\/imports\\/clear-data\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@clearData\"]','max',4422.00,NULL),(1282,1756703880,360,'slow_request','[\"POST\",\"\\/imports\\/clear-data\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@clearData\"]','max',4422.00,NULL),(1283,1756704120,60,'exception','[\"Error\",\"app\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController.php:236\"]','max',1756704128.00,NULL),(1284,1756703880,360,'exception','[\"Error\",\"app\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController.php:236\"]','max',1756704128.00,NULL),(1293,1756705560,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',1.00,NULL),(1294,1756705320,360,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',1.00,NULL),(1295,1756704960,1440,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',3.00,NULL),(1296,1756705620,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',1.00,NULL),(1297,1756705320,360,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',1.00,NULL),(1298,1756704960,1440,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',3.00,NULL),(1301,1756705560,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',23673.00,NULL),(1302,1756705320,360,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',23673.00,NULL),(1303,1756704960,1440,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',23673.00,NULL),(1304,1756705620,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2702.00,NULL),(1305,1756705320,360,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2702.00,NULL),(1306,1756704960,1440,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2702.00,NULL),(1309,1756705620,60,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','count',1.00,NULL),(1310,1756705320,360,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','count',1.00,NULL),(1311,1756704960,1440,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','count',1.00,NULL),(1312,1756702080,10080,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','count',1.00,NULL),(1313,1756705620,60,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','max',2291.00,NULL),(1314,1756705320,360,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','max',2291.00,NULL),(1315,1756704960,1440,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','max',2291.00,NULL),(1316,1756702080,10080,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','max',2291.00,NULL),(1317,1756705620,60,'user_request','336','count',3.00,NULL),(1318,1756705320,360,'user_request','336','count',3.00,NULL),(1319,1756704960,1440,'user_request','336','count',7.00,NULL),(1329,1756705980,60,'user_request','336','count',2.00,NULL),(1330,1756705680,360,'user_request','336','count',2.00,NULL),(1337,1756706100,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',1.00,NULL),(1338,1756706040,360,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',2.00,NULL),(1339,1756706100,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',1.00,NULL),(1340,1756706040,360,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',2.00,NULL),(1345,1756706100,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',2611.00,NULL),(1346,1756706040,360,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',2611.00,NULL),(1347,1756706100,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2026.00,NULL),(1348,1756706040,360,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2045.00,NULL),(1353,1756706160,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',1.00,NULL),(1354,1756706160,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',1.00,NULL),(1361,1756706160,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',2469.00,NULL),(1362,1756706160,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2045.00,NULL),(1369,1756706160,60,'user_request','336','count',2.00,NULL),(1370,1756706040,360,'user_request','336','count',2.00,NULL),(1377,1756707300,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',1.00,NULL),(1378,1756707120,360,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',1.00,NULL),(1379,1756706400,1440,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',1.00,NULL),(1380,1756707300,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',1.00,NULL),(1381,1756707120,360,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',1.00,NULL),(1382,1756706400,1440,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',1.00,NULL),(1385,1756707300,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',3076.00,NULL),(1386,1756707120,360,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',3076.00,NULL),(1387,1756706400,1440,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',3076.00,NULL),(1388,1756707300,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2053.00,NULL),(1389,1756707120,360,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2053.00,NULL),(1390,1756706400,1440,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2053.00,NULL),(1393,1756707300,60,'user_request','336','count',5.00,NULL),(1394,1756707120,360,'user_request','336','count',11.00,NULL),(1395,1756706400,1440,'user_request','336','count',15.00,NULL),(1413,1756707360,60,'user_request','336','count',4.00,NULL),(1429,1756707420,60,'user_request','336','count',2.00,NULL),(1437,1756707480,60,'user_request','336','count',3.00,NULL),(1438,1756707480,360,'user_request','336','count',4.00,NULL),(1449,1756707780,60,'user_request','336','count',1.00,NULL),(1453,1756707840,60,'user_request','336','count',3.00,NULL),(1454,1756707840,360,'user_request','336','count',14.00,NULL),(1455,1756707840,1440,'user_request','336','count',17.00,NULL),(1465,1756707900,60,'user_request','336','count',2.00,NULL),(1473,1756707960,60,'user_request','336','count',4.00,NULL),(1489,1756708020,60,'user_request','336','count',1.00,NULL),(1493,1756708080,60,'user_request','336','count',4.00,NULL),(1501,1756708080,60,'slow_request','[\"GET\",\"\\/imports\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@index\"]','count',1.00,NULL),(1502,1756707840,360,'slow_request','[\"GET\",\"\\/imports\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@index\"]','count',1.00,NULL),(1503,1756707840,1440,'slow_request','[\"GET\",\"\\/imports\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@index\"]','count',1.00,NULL),(1504,1756702080,10080,'slow_request','[\"GET\",\"\\/imports\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@index\"]','count',1.00,NULL),(1505,1756708080,60,'slow_user_request','336','count',1.00,NULL),(1506,1756707840,360,'slow_user_request','336','count',1.00,NULL),(1507,1756707840,1440,'slow_user_request','336','count',1.00,NULL),(1508,1756708080,60,'exception','[\"Illuminate\\\\Database\\\\QueryException\",\"app\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController.php:29\"]','count',1.00,NULL),(1509,1756707840,360,'exception','[\"Illuminate\\\\Database\\\\QueryException\",\"app\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController.php:29\"]','count',1.00,NULL),(1510,1756707840,1440,'exception','[\"Illuminate\\\\Database\\\\QueryException\",\"app\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController.php:29\"]','count',1.00,NULL),(1511,1756702080,10080,'exception','[\"Illuminate\\\\Database\\\\QueryException\",\"app\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController.php:29\"]','count',1.00,NULL),(1517,1756708080,60,'slow_request','[\"GET\",\"\\/imports\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@index\"]','max',4952.00,NULL),(1518,1756707840,360,'slow_request','[\"GET\",\"\\/imports\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@index\"]','max',4952.00,NULL),(1519,1756707840,1440,'slow_request','[\"GET\",\"\\/imports\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@index\"]','max',4952.00,NULL),(1520,1756702080,10080,'slow_request','[\"GET\",\"\\/imports\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@index\"]','max',4952.00,NULL),(1521,1756708080,60,'exception','[\"Illuminate\\\\Database\\\\QueryException\",\"app\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController.php:29\"]','max',1756708103.00,NULL),(1522,1756707840,360,'exception','[\"Illuminate\\\\Database\\\\QueryException\",\"app\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController.php:29\"]','max',1756708103.00,NULL),(1523,1756707840,1440,'exception','[\"Illuminate\\\\Database\\\\QueryException\",\"app\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController.php:29\"]','max',1756708103.00,NULL),(1524,1756702080,10080,'exception','[\"Illuminate\\\\Database\\\\QueryException\",\"app\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController.php:29\"]','max',1756708103.00,NULL),(1529,1756708260,60,'user_request','336','count',3.00,NULL),(1530,1756708200,360,'user_request','336','count',3.00,NULL),(1541,1756735560,60,'slow_request','[\"GET\",\"\\/\",\"Closure\"]','count',1.00,NULL),(1542,1756735560,360,'slow_request','[\"GET\",\"\\/\",\"Closure\"]','count',1.00,NULL),(1543,1756735200,1440,'slow_request','[\"GET\",\"\\/\",\"Closure\"]','count',1.00,NULL),(1544,1756732320,10080,'slow_request','[\"GET\",\"\\/\",\"Closure\"]','count',1.00,NULL),(1545,1756735560,60,'slow_request','[\"GET\",\"\\/\",\"Closure\"]','max',14967.00,NULL),(1546,1756735560,360,'slow_request','[\"GET\",\"\\/\",\"Closure\"]','max',14967.00,NULL),(1547,1756735200,1440,'slow_request','[\"GET\",\"\\/\",\"Closure\"]','max',14967.00,NULL),(1548,1756732320,10080,'slow_request','[\"GET\",\"\\/\",\"Closure\"]','max',14967.00,NULL),(1549,1756735620,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',1.00,NULL),(1550,1756735560,360,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',1.00,NULL),(1551,1756735200,1440,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',1.00,NULL),(1552,1756732320,10080,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',1.00,NULL),(1553,1756735620,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',1.00,NULL),(1554,1756735560,360,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',1.00,NULL),(1555,1756735200,1440,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',1.00,NULL),(1556,1756732320,10080,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',1.00,NULL),(1557,1756735620,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',5654.00,NULL),(1558,1756735560,360,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',5654.00,NULL),(1559,1756735200,1440,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',5654.00,NULL),(1560,1756732320,10080,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',5654.00,NULL),(1561,1756735620,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2470.00,NULL),(1562,1756735560,360,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2470.00,NULL),(1563,1756735200,1440,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2470.00,NULL),(1564,1756732320,10080,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2470.00,NULL),(1565,1756735620,60,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','count',1.00,NULL),(1566,1756735560,360,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','count',1.00,NULL),(1567,1756735200,1440,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','count',1.00,NULL),(1568,1756732320,10080,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','count',1.00,NULL),(1569,1756735620,60,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','max',2213.00,NULL),(1570,1756735560,360,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','max',2213.00,NULL),(1571,1756735200,1440,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','max',2213.00,NULL),(1572,1756732320,10080,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','max',2213.00,NULL),(1573,1756735620,60,'user_request','336','count',4.00,NULL),(1574,1756735560,360,'user_request','336','count',4.00,NULL),(1575,1756735200,1440,'user_request','336','count',4.00,NULL),(1576,1756732320,10080,'user_request','336','count',5.00,NULL),(1581,1756735620,60,'slow_request','[\"POST\",\"\\/imports\\/preview\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@preview\"]','count',1.00,NULL),(1582,1756735560,360,'slow_request','[\"POST\",\"\\/imports\\/preview\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@preview\"]','count',1.00,NULL),(1583,1756735200,1440,'slow_request','[\"POST\",\"\\/imports\\/preview\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@preview\"]','count',1.00,NULL),(1584,1756732320,10080,'slow_request','[\"POST\",\"\\/imports\\/preview\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@preview\"]','count',1.00,NULL),(1585,1756735620,60,'slow_user_request','336','count',1.00,NULL),(1586,1756735560,360,'slow_user_request','336','count',1.00,NULL),(1587,1756735200,1440,'slow_user_request','336','count',1.00,NULL),(1588,1756732320,10080,'slow_user_request','336','count',1.00,NULL),(1593,1756735620,60,'slow_request','[\"POST\",\"\\/imports\\/preview\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@preview\"]','max',3205.00,NULL),(1594,1756735560,360,'slow_request','[\"POST\",\"\\/imports\\/preview\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@preview\"]','max',3205.00,NULL),(1595,1756735200,1440,'slow_request','[\"POST\",\"\\/imports\\/preview\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@preview\"]','max',3205.00,NULL),(1596,1756732320,10080,'slow_request','[\"POST\",\"\\/imports\\/preview\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@preview\"]','max',3205.00,NULL),(1601,1756738620,60,'user_request','336','count',1.00,NULL),(1602,1756738440,360,'user_request','336','count',1.00,NULL),(1603,1756738080,1440,'user_request','336','count',1.00,NULL),(1605,1756811100,60,'slow_request','[\"GET\",\"\\/\",\"Closure\"]','count',1.00,NULL),(1606,1756810800,360,'slow_request','[\"GET\",\"\\/\",\"Closure\"]','count',1.00,NULL),(1607,1756810080,1440,'slow_request','[\"GET\",\"\\/\",\"Closure\"]','count',1.00,NULL),(1608,1756802880,10080,'slow_request','[\"GET\",\"\\/\",\"Closure\"]','count',1.00,NULL),(1609,1756811100,60,'slow_request','[\"GET\",\"\\/\",\"Closure\"]','max',17708.00,NULL),(1610,1756810800,360,'slow_request','[\"GET\",\"\\/\",\"Closure\"]','max',17708.00,NULL),(1611,1756810080,1440,'slow_request','[\"GET\",\"\\/\",\"Closure\"]','max',17708.00,NULL),(1612,1756802880,10080,'slow_request','[\"GET\",\"\\/\",\"Closure\"]','max',17708.00,NULL),(1613,1756811160,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',1.00,NULL),(1614,1756811160,360,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',1.00,NULL),(1615,1756810080,1440,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',1.00,NULL),(1616,1756802880,10080,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',1.00,NULL),(1617,1756811160,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',1.00,NULL),(1618,1756811160,360,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',1.00,NULL),(1619,1756810080,1440,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',1.00,NULL),(1620,1756802880,10080,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',1.00,NULL),(1621,1756811160,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',5908.00,NULL),(1622,1756811160,360,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',5908.00,NULL),(1623,1756810080,1440,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',5908.00,NULL),(1624,1756802880,10080,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',5908.00,NULL),(1625,1756811160,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2472.00,NULL),(1626,1756811160,360,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2472.00,NULL),(1627,1756810080,1440,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2472.00,NULL),(1628,1756802880,10080,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2472.00,NULL),(1629,1756811460,60,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','count',1.00,NULL),(1630,1756811160,360,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','count',1.00,NULL),(1631,1756810080,1440,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','count',1.00,NULL),(1632,1756802880,10080,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','count',1.00,NULL),(1633,1756811460,60,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','max',10322.00,NULL),(1634,1756811160,360,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','max',10322.00,NULL),(1635,1756810080,1440,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','max',10322.00,NULL),(1636,1756802880,10080,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','max',10322.00,NULL),(1637,1756811460,60,'user_request','336','count',4.00,NULL),(1638,1756811160,360,'user_request','336','count',4.00,NULL),(1639,1756810080,1440,'user_request','336','count',4.00,NULL),(1640,1756802880,10080,'user_request','336','count',13.00,NULL),(1645,1756811460,60,'slow_request','[\"POST\",\"\\/imports\\/preview\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@preview\"]','count',1.00,NULL),(1646,1756811160,360,'slow_request','[\"POST\",\"\\/imports\\/preview\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@preview\"]','count',1.00,NULL),(1647,1756810080,1440,'slow_request','[\"POST\",\"\\/imports\\/preview\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@preview\"]','count',1.00,NULL),(1648,1756802880,10080,'slow_request','[\"POST\",\"\\/imports\\/preview\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@preview\"]','count',2.00,NULL),(1649,1756811460,60,'slow_user_request','336','count',1.00,NULL),(1650,1756811160,360,'slow_user_request','336','count',1.00,NULL),(1651,1756810080,1440,'slow_user_request','336','count',1.00,NULL),(1652,1756802880,10080,'slow_user_request','336','count',3.00,NULL),(1657,1756811460,60,'slow_request','[\"POST\",\"\\/imports\\/preview\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@preview\"]','max',3290.00,NULL),(1658,1756811160,360,'slow_request','[\"POST\",\"\\/imports\\/preview\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@preview\"]','max',3290.00,NULL),(1659,1756810080,1440,'slow_request','[\"POST\",\"\\/imports\\/preview\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@preview\"]','max',3290.00,NULL),(1660,1756802880,10080,'slow_request','[\"POST\",\"\\/imports\\/preview\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@preview\"]','max',3290.00,NULL),(1665,1756811520,60,'slow_request','[\"GET\",\"\\/imports\\/validation\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@validation\"]','count',1.00,NULL),(1666,1756811520,360,'slow_request','[\"GET\",\"\\/imports\\/validation\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@validation\"]','count',1.00,NULL),(1667,1756811520,1440,'slow_request','[\"GET\",\"\\/imports\\/validation\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@validation\"]','count',1.00,NULL),(1668,1756802880,10080,'slow_request','[\"GET\",\"\\/imports\\/validation\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@validation\"]','count',1.00,NULL),(1669,1756811520,60,'slow_user_request','336','count',1.00,NULL),(1670,1756811520,360,'slow_user_request','336','count',1.00,NULL),(1671,1756811520,1440,'slow_user_request','336','count',2.00,NULL),(1672,1756811520,60,'user_request','336','count',2.00,NULL),(1673,1756811520,360,'user_request','336','count',6.00,NULL),(1674,1756811520,1440,'user_request','336','count',9.00,NULL),(1677,1756811520,60,'slow_request','[\"GET\",\"\\/imports\\/validation\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@validation\"]','max',1048.00,NULL),(1678,1756811520,360,'slow_request','[\"GET\",\"\\/imports\\/validation\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@validation\"]','max',1048.00,NULL),(1679,1756811520,1440,'slow_request','[\"GET\",\"\\/imports\\/validation\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@validation\"]','max',1048.00,NULL),(1680,1756802880,10080,'slow_request','[\"GET\",\"\\/imports\\/validation\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@validation\"]','max',1048.00,NULL),(1685,1756811580,60,'user_request','336','count',2.00,NULL),(1693,1756811700,60,'user_request','336','count',2.00,NULL),(1701,1756812060,60,'user_request','336','count',3.00,NULL),(1702,1756811880,360,'user_request','336','count',3.00,NULL),(1705,1756812060,60,'slow_request','[\"POST\",\"\\/imports\\/preview\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@preview\"]','count',1.00,NULL),(1706,1756811880,360,'slow_request','[\"POST\",\"\\/imports\\/preview\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@preview\"]','count',1.00,NULL),(1707,1756811520,1440,'slow_request','[\"POST\",\"\\/imports\\/preview\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@preview\"]','count',1.00,NULL),(1708,1756812060,60,'slow_user_request','336','count',1.00,NULL),(1709,1756811880,360,'slow_user_request','336','count',1.00,NULL),(1717,1756812060,60,'slow_request','[\"POST\",\"\\/imports\\/preview\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@preview\"]','max',1248.00,NULL),(1718,1756811880,360,'slow_request','[\"POST\",\"\\/imports\\/preview\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@preview\"]','max',1248.00,NULL),(1719,1756811520,1440,'slow_request','[\"POST\",\"\\/imports\\/preview\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@preview\"]','max',1248.00,NULL),(1725,1756817100,60,'slow_request','[\"GET\",\"\\/\",\"Closure\"]','count',1.00,NULL),(1726,1756816920,360,'slow_request','[\"GET\",\"\\/\",\"Closure\"]','count',1.00,NULL),(1727,1756815840,1440,'slow_request','[\"GET\",\"\\/\",\"Closure\"]','count',1.00,NULL),(1728,1756812960,10080,'slow_request','[\"GET\",\"\\/\",\"Closure\"]','count',1.00,NULL),(1729,1756817100,60,'slow_request','[\"GET\",\"\\/\",\"Closure\"]','max',7268.00,NULL),(1730,1756816920,360,'slow_request','[\"GET\",\"\\/\",\"Closure\"]','max',7268.00,NULL),(1731,1756815840,1440,'slow_request','[\"GET\",\"\\/\",\"Closure\"]','max',7268.00,NULL),(1732,1756812960,10080,'slow_request','[\"GET\",\"\\/\",\"Closure\"]','max',7268.00,NULL),(1733,1756817100,60,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','count',1.00,NULL),(1734,1756816920,360,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','count',1.00,NULL),(1735,1756815840,1440,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','count',1.00,NULL),(1736,1756812960,10080,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','count',1.00,NULL),(1737,1756817100,60,'slow_user_request','336','count',1.00,NULL),(1738,1756816920,360,'slow_user_request','336','count',2.00,NULL),(1739,1756815840,1440,'slow_user_request','336','count',2.00,NULL),(1740,1756812960,10080,'slow_user_request','336','count',3.00,NULL),(1741,1756817100,60,'user_request','336','count',1.00,NULL),(1742,1756816920,360,'user_request','336','count',3.00,NULL),(1743,1756815840,1440,'user_request','336','count',3.00,NULL),(1744,1756812960,10080,'user_request','336','count',5.00,NULL),(1745,1756817100,60,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','max',1421.00,NULL),(1746,1756816920,360,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','max',1421.00,NULL),(1747,1756815840,1440,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','max',1421.00,NULL),(1748,1756812960,10080,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','max',1421.00,NULL),(1749,1756817160,60,'slow_request','[\"GET\",\"\\/imports\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@index\"]','count',1.00,NULL),(1750,1756816920,360,'slow_request','[\"GET\",\"\\/imports\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@index\"]','count',1.00,NULL),(1751,1756815840,1440,'slow_request','[\"GET\",\"\\/imports\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@index\"]','count',1.00,NULL),(1752,1756812960,10080,'slow_request','[\"GET\",\"\\/imports\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@index\"]','count',1.00,NULL),(1753,1756817160,60,'slow_user_request','336','count',1.00,NULL),(1754,1756817160,60,'user_request','336','count',1.00,NULL),(1755,1756817160,60,'exception','[\"Illuminate\\\\Database\\\\QueryException\",\"app\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController.php:29\"]','count',1.00,NULL),(1756,1756816920,360,'exception','[\"Illuminate\\\\Database\\\\QueryException\",\"app\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController.php:29\"]','count',1.00,NULL),(1757,1756815840,1440,'exception','[\"Illuminate\\\\Database\\\\QueryException\",\"app\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController.php:29\"]','count',1.00,NULL),(1758,1756812960,10080,'exception','[\"Illuminate\\\\Database\\\\QueryException\",\"app\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController.php:29\"]','count',1.00,NULL),(1765,1756817160,60,'slow_request','[\"GET\",\"\\/imports\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@index\"]','max',7487.00,NULL),(1766,1756816920,360,'slow_request','[\"GET\",\"\\/imports\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@index\"]','max',7487.00,NULL),(1767,1756815840,1440,'slow_request','[\"GET\",\"\\/imports\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@index\"]','max',7487.00,NULL),(1768,1756812960,10080,'slow_request','[\"GET\",\"\\/imports\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@index\"]','max',7487.00,NULL),(1769,1756817160,60,'exception','[\"Illuminate\\\\Database\\\\QueryException\",\"app\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController.php:29\"]','max',1756817168.00,NULL),(1770,1756816920,360,'exception','[\"Illuminate\\\\Database\\\\QueryException\",\"app\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController.php:29\"]','max',1756817168.00,NULL),(1771,1756815840,1440,'exception','[\"Illuminate\\\\Database\\\\QueryException\",\"app\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController.php:29\"]','max',1756817168.00,NULL),(1772,1756812960,10080,'exception','[\"Illuminate\\\\Database\\\\QueryException\",\"app\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController.php:29\"]','max',1756817168.00,NULL),(1773,1756817220,60,'user_request','336','count',1.00,NULL),(1777,1756817280,60,'slow_request','[\"POST\",\"\\/imports\\/preview\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@preview\"]','count',1.00,NULL),(1778,1756817280,360,'slow_request','[\"POST\",\"\\/imports\\/preview\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@preview\"]','count',1.00,NULL),(1779,1756817280,1440,'slow_request','[\"POST\",\"\\/imports\\/preview\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@preview\"]','count',1.00,NULL),(1780,1756812960,10080,'slow_request','[\"POST\",\"\\/imports\\/preview\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@preview\"]','count',1.00,NULL),(1781,1756817280,60,'slow_user_request','336','count',1.00,NULL),(1782,1756817280,360,'slow_user_request','336','count',1.00,NULL),(1783,1756817280,1440,'slow_user_request','336','count',1.00,NULL),(1784,1756817280,60,'user_request','336','count',2.00,NULL),(1785,1756817280,360,'user_request','336','count',2.00,NULL),(1786,1756817280,1440,'user_request','336','count',2.00,NULL),(1789,1756817280,60,'slow_request','[\"POST\",\"\\/imports\\/preview\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@preview\"]','max',1856.00,NULL),(1790,1756817280,360,'slow_request','[\"POST\",\"\\/imports\\/preview\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@preview\"]','max',1856.00,NULL),(1791,1756817280,1440,'slow_request','[\"POST\",\"\\/imports\\/preview\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@preview\"]','max',1856.00,NULL),(1792,1756812960,10080,'slow_request','[\"POST\",\"\\/imports\\/preview\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@preview\"]','max',1856.00,NULL),(1797,1756846440,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',1.00,NULL),(1798,1756846440,360,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',1.00,NULL),(1799,1756846080,1440,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',1.00,NULL),(1800,1756843200,10080,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',1.00,NULL),(1801,1756846440,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',1.00,NULL),(1802,1756846440,360,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',1.00,NULL),(1803,1756846080,1440,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',1.00,NULL),(1804,1756843200,10080,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',1.00,NULL),(1805,1756846440,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',5117.00,NULL),(1806,1756846440,360,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',5117.00,NULL),(1807,1756846080,1440,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',5117.00,NULL),(1808,1756843200,10080,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',5117.00,NULL),(1809,1756846440,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2608.00,NULL),(1810,1756846440,360,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2608.00,NULL),(1811,1756846080,1440,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2608.00,NULL),(1812,1756843200,10080,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2608.00,NULL),(1813,1756846440,60,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','count',1.00,NULL),(1814,1756846440,360,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','count',1.00,NULL),(1815,1756846080,1440,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','count',1.00,NULL),(1816,1756843200,10080,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','count',1.00,NULL),(1817,1756846440,60,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','max',1415.00,NULL),(1818,1756846440,360,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','max',1415.00,NULL),(1819,1756846080,1440,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','max',1415.00,NULL),(1820,1756843200,10080,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','max',1415.00,NULL),(1821,1756846440,60,'user_request','336','count',1.00,NULL),(1822,1756846440,360,'user_request','336','count',6.00,NULL),(1823,1756846080,1440,'user_request','336','count',6.00,NULL),(1824,1756843200,10080,'user_request','336','count',6.00,NULL),(1825,1756846500,60,'user_request','336','count',4.00,NULL),(1841,1756846680,60,'user_request','336','count',1.00,NULL),(1845,1756865940,60,'slow_request','[\"GET\",\"\\/\",\"Closure\"]','count',1.00,NULL),(1846,1756865880,360,'slow_request','[\"GET\",\"\\/\",\"Closure\"]','count',1.00,NULL),(1847,1756864800,1440,'slow_request','[\"GET\",\"\\/\",\"Closure\"]','count',1.00,NULL),(1848,1756863360,10080,'slow_request','[\"GET\",\"\\/\",\"Closure\"]','count',1.00,NULL),(1849,1756865940,60,'slow_request','[\"GET\",\"\\/\",\"Closure\"]','max',1752.00,NULL),(1850,1756865880,360,'slow_request','[\"GET\",\"\\/\",\"Closure\"]','max',1752.00,NULL),(1851,1756864800,1440,'slow_request','[\"GET\",\"\\/\",\"Closure\"]','max',1752.00,NULL),(1852,1756863360,10080,'slow_request','[\"GET\",\"\\/\",\"Closure\"]','max',1752.00,NULL),(1853,1756865940,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',1.00,NULL),(1854,1756865880,360,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',2.00,NULL),(1855,1756864800,1440,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',2.00,NULL),(1856,1756863360,10080,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',6.00,NULL),(1857,1756865940,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',1.00,NULL),(1858,1756865880,360,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',2.00,NULL),(1859,1756864800,1440,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',2.00,NULL),(1860,1756863360,10080,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',6.00,NULL),(1861,1756865940,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',5615.00,NULL),(1862,1756865880,360,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',5615.00,NULL),(1863,1756864800,1440,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',5615.00,NULL),(1864,1756863360,10080,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',5615.00,NULL),(1865,1756865940,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2627.00,NULL),(1866,1756865880,360,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2627.00,NULL),(1867,1756864800,1440,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2627.00,NULL),(1868,1756863360,10080,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2627.00,NULL),(1869,1756866180,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',1.00,NULL),(1870,1756866180,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',1.00,NULL),(1877,1756866180,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',2438.00,NULL),(1878,1756866180,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2028.00,NULL),(1885,1756867020,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',1.00,NULL),(1886,1756866960,360,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',1.00,NULL),(1887,1756866240,1440,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',1.00,NULL),(1888,1756867020,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',1.00,NULL),(1889,1756866960,360,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',1.00,NULL),(1890,1756866240,1440,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',1.00,NULL),(1893,1756867020,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',2714.00,NULL),(1894,1756866960,360,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',2714.00,NULL),(1895,1756866240,1440,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',2714.00,NULL),(1896,1756867020,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2044.00,NULL),(1897,1756866960,360,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2044.00,NULL),(1898,1756866240,1440,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2044.00,NULL),(1901,1756872660,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',1.00,NULL),(1902,1756872360,360,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',1.00,NULL),(1903,1756872000,1440,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',3.00,NULL),(1904,1756872660,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',1.00,NULL),(1905,1756872360,360,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',1.00,NULL),(1906,1756872000,1440,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',3.00,NULL),(1909,1756872660,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',2717.00,NULL),(1910,1756872360,360,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',2717.00,NULL),(1911,1756872000,1440,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',2717.00,NULL),(1912,1756872660,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2044.00,NULL),(1913,1756872360,360,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2044.00,NULL),(1914,1756872000,1440,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2044.00,NULL),(1917,1756872780,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',1.00,NULL),(1918,1756872720,360,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',1.00,NULL),(1919,1756872780,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',1.00,NULL),(1920,1756872720,360,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',1.00,NULL),(1925,1756872780,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',2417.00,NULL),(1926,1756872720,360,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',2417.00,NULL),(1927,1756872780,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2040.00,NULL),(1928,1756872720,360,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2040.00,NULL),(1933,1756872780,60,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','count',1.00,NULL),(1934,1756872720,360,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','count',1.00,NULL),(1935,1756872000,1440,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','count',1.00,NULL),(1936,1756863360,10080,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','count',1.00,NULL),(1937,1756872780,60,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','max',2171.00,NULL),(1938,1756872720,360,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','max',2171.00,NULL),(1939,1756872000,1440,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','max',2171.00,NULL),(1940,1756863360,10080,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','max',2171.00,NULL),(1941,1756872780,60,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','count',1.00,NULL),(1942,1756872720,360,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','count',1.00,NULL),(1943,1756872000,1440,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','count',1.00,NULL),(1944,1756863360,10080,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','count',1.00,NULL),(1945,1756872780,60,'slow_user_request','336','count',1.00,NULL),(1946,1756872720,360,'slow_user_request','336','count',1.00,NULL),(1947,1756872000,1440,'slow_user_request','336','count',1.00,NULL),(1948,1756863360,10080,'slow_user_request','336','count',1.00,NULL),(1949,1756872780,60,'user_request','336','count',1.00,NULL),(1950,1756872720,360,'user_request','336','count',1.00,NULL),(1951,1756872000,1440,'user_request','336','count',3.00,NULL),(1952,1756863360,10080,'user_request','336','count',3.00,NULL),(1953,1756872780,60,'exception','[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]','count',1.00,NULL),(1954,1756872720,360,'exception','[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]','count',1.00,NULL),(1955,1756872000,1440,'exception','[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]','count',1.00,NULL),(1956,1756863360,10080,'exception','[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]','count',1.00,NULL),(1957,1756872780,60,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','max',3011.00,NULL),(1958,1756872720,360,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','max',3011.00,NULL),(1959,1756872000,1440,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','max',3011.00,NULL),(1960,1756863360,10080,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','max',3011.00,NULL),(1961,1756872780,60,'exception','[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]','max',1756872829.00,NULL),(1962,1756872720,360,'exception','[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]','max',1756872829.00,NULL),(1963,1756872000,1440,'exception','[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]','max',1756872829.00,NULL),(1964,1756863360,10080,'exception','[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]','max',1756872829.00,NULL),(1965,1756873080,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',1.00,NULL),(1966,1756873080,360,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',1.00,NULL),(1967,1756873080,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',1.00,NULL),(1968,1756873080,360,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',1.00,NULL),(1973,1756873080,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',2451.00,NULL),(1974,1756873080,360,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',2451.00,NULL),(1975,1756873080,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2030.00,NULL),(1976,1756873080,360,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2030.00,NULL),(1981,1756873080,60,'user_request','336','count',1.00,NULL),(1982,1756873080,360,'user_request','336','count',2.00,NULL),(1985,1756873140,60,'user_request','336','count',1.00,NULL),(1989,1756873800,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',1.00,NULL),(1990,1756873800,360,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',3.00,NULL),(1991,1756873440,1440,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',3.00,NULL),(1992,1756873440,10080,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',3.00,NULL),(1993,1756873800,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',1.00,NULL),(1994,1756873800,360,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',3.00,NULL),(1995,1756873440,1440,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',3.00,NULL),(1996,1756873440,10080,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',3.00,NULL),(1997,1756873800,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',2454.00,NULL),(1998,1756873800,360,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',2498.00,NULL),(1999,1756873440,1440,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',2498.00,NULL),(2000,1756873440,10080,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',2498.00,NULL),(2001,1756873800,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2026.00,NULL),(2002,1756873800,360,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2027.00,NULL),(2003,1756873440,1440,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2027.00,NULL),(2004,1756873440,10080,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2027.00,NULL),(2005,1756873860,60,'user_request','336','count',1.00,NULL),(2006,1756873800,360,'user_request','336','count',6.00,NULL),(2007,1756873440,1440,'user_request','336','count',6.00,NULL),(2008,1756873440,10080,'user_request','336','count',11.00,NULL),(2009,1756873920,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',1.00,NULL),(2010,1756873920,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',1.00,NULL),(2017,1756873920,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',2383.00,NULL),(2018,1756873920,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2027.00,NULL),(2025,1756873920,60,'user_request','336','count',2.00,NULL),(2033,1756873980,60,'user_request','336','count',1.00,NULL),(2037,1756874040,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',1.00,NULL),(2038,1756874040,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',1.00,NULL),(2045,1756874040,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',2498.00,NULL),(2046,1756874040,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2015.00,NULL),(2053,1756874040,60,'user_request','336','count',2.00,NULL),(2061,1756879320,60,'slow_request','[\"GET\",\"\\/\",\"Closure\"]','count',1.00,NULL),(2062,1756879200,360,'slow_request','[\"GET\",\"\\/\",\"Closure\"]','count',1.00,NULL),(2063,1756879200,1440,'slow_request','[\"GET\",\"\\/\",\"Closure\"]','count',1.00,NULL),(2064,1756873440,10080,'slow_request','[\"GET\",\"\\/\",\"Closure\"]','count',1.00,NULL),(2065,1756879320,60,'slow_request','[\"GET\",\"\\/\",\"Closure\"]','max',6708.00,NULL),(2066,1756879200,360,'slow_request','[\"GET\",\"\\/\",\"Closure\"]','max',6708.00,NULL),(2067,1756879200,1440,'slow_request','[\"GET\",\"\\/\",\"Closure\"]','max',6708.00,NULL),(2068,1756873440,10080,'slow_request','[\"GET\",\"\\/\",\"Closure\"]','max',6708.00,NULL),(2069,1756879320,60,'user_request','336','count',3.00,NULL),(2070,1756879200,360,'user_request','336','count',5.00,NULL),(2071,1756879200,1440,'user_request','336','count',5.00,NULL),(2081,1756879440,60,'user_request','336','count',2.00,NULL),(2089,1756974660,60,'slow_request','[\"GET\",\"\\/\",\"Closure\"]','count',1.00,NULL),(2090,1756974600,360,'slow_request','[\"GET\",\"\\/\",\"Closure\"]','count',1.00,NULL),(2091,1756974240,1440,'slow_request','[\"GET\",\"\\/\",\"Closure\"]','count',1.00,NULL),(2092,1756974240,10080,'slow_request','[\"GET\",\"\\/\",\"Closure\"]','count',1.00,NULL),(2093,1756974660,60,'slow_request','[\"GET\",\"\\/\",\"Closure\"]','max',20934.00,NULL),(2094,1756974600,360,'slow_request','[\"GET\",\"\\/\",\"Closure\"]','max',20934.00,NULL),(2095,1756974240,1440,'slow_request','[\"GET\",\"\\/\",\"Closure\"]','max',20934.00,NULL),(2096,1756974240,10080,'slow_request','[\"GET\",\"\\/\",\"Closure\"]','max',20934.00,NULL),(2097,1756974660,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',1.00,NULL),(2098,1756974600,360,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',1.00,NULL),(2099,1756974240,1440,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',2.00,NULL),(2100,1756974240,10080,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',2.00,NULL),(2101,1756974660,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',1.00,NULL),(2102,1756974600,360,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',1.00,NULL),(2103,1756974240,1440,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',2.00,NULL),(2104,1756974240,10080,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',2.00,NULL),(2105,1756974660,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',5743.00,NULL),(2106,1756974600,360,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',5743.00,NULL),(2107,1756974240,1440,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',5743.00,NULL),(2108,1756974240,10080,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',5743.00,NULL),(2109,1756974660,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2512.00,NULL),(2110,1756974600,360,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2512.00,NULL),(2111,1756974240,1440,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2512.00,NULL),(2112,1756974240,10080,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2512.00,NULL),(2113,1756974660,60,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','count',1.00,NULL),(2114,1756974600,360,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','count',1.00,NULL),(2115,1756974240,1440,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','count',1.00,NULL),(2116,1756974240,10080,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','count',1.00,NULL),(2117,1756974660,60,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','max',2529.00,NULL),(2118,1756974600,360,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','max',2529.00,NULL),(2119,1756974240,1440,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','max',2529.00,NULL),(2120,1756974240,10080,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]','max',2529.00,NULL),(2121,1756974660,60,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','count',1.00,NULL),(2122,1756974600,360,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','count',1.00,NULL),(2123,1756974240,1440,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','count',1.00,NULL),(2124,1756974240,10080,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','count',1.00,NULL),(2125,1756974660,60,'slow_user_request','336','count',1.00,NULL),(2126,1756974600,360,'slow_user_request','336','count',1.00,NULL),(2127,1756974240,1440,'slow_user_request','336','count',2.00,NULL),(2128,1756974240,10080,'slow_user_request','336','count',2.00,NULL),(2129,1756974660,60,'user_request','336','count',1.00,NULL),(2130,1756974600,360,'user_request','336','count',1.00,NULL),(2131,1756974240,1440,'user_request','336','count',5.00,NULL),(2132,1756974240,10080,'user_request','336','count',5.00,NULL),(2133,1756974660,60,'exception','[\"Illuminate\\\\Database\\\\QueryException\",\"app\\\\Http\\\\Controllers\\\\DashboardController.php:188\"]','count',1.00,NULL),(2134,1756974600,360,'exception','[\"Illuminate\\\\Database\\\\QueryException\",\"app\\\\Http\\\\Controllers\\\\DashboardController.php:188\"]','count',1.00,NULL),(2135,1756974240,1440,'exception','[\"Illuminate\\\\Database\\\\QueryException\",\"app\\\\Http\\\\Controllers\\\\DashboardController.php:188\"]','count',1.00,NULL),(2136,1756974240,10080,'exception','[\"Illuminate\\\\Database\\\\QueryException\",\"app\\\\Http\\\\Controllers\\\\DashboardController.php:188\"]','count',1.00,NULL),(2137,1756974660,60,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','max',10381.00,NULL),(2138,1756974600,360,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','max',10381.00,NULL),(2139,1756974240,1440,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','max',10381.00,NULL),(2140,1756974240,10080,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]','max',10381.00,NULL),(2141,1756974660,60,'exception','[\"Illuminate\\\\Database\\\\QueryException\",\"app\\\\Http\\\\Controllers\\\\DashboardController.php:188\"]','max',1756974704.00,NULL),(2142,1756974600,360,'exception','[\"Illuminate\\\\Database\\\\QueryException\",\"app\\\\Http\\\\Controllers\\\\DashboardController.php:188\"]','max',1756974704.00,NULL),(2143,1756974240,1440,'exception','[\"Illuminate\\\\Database\\\\QueryException\",\"app\\\\Http\\\\Controllers\\\\DashboardController.php:188\"]','max',1756974704.00,NULL),(2144,1756974240,10080,'exception','[\"Illuminate\\\\Database\\\\QueryException\",\"app\\\\Http\\\\Controllers\\\\DashboardController.php:188\"]','max',1756974704.00,NULL),(2145,1756975080,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',1.00,NULL),(2146,1756974960,360,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','count',1.00,NULL),(2147,1756975080,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',1.00,NULL),(2148,1756974960,360,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','count',1.00,NULL),(2153,1756975080,60,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',2488.00,NULL),(2154,1756974960,360,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]','max',2488.00,NULL),(2155,1756975080,60,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2033.00,NULL),(2156,1756974960,360,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]','max',2033.00,NULL),(2161,1756975140,60,'user_request','336','count',4.00,NULL),(2162,1756974960,360,'user_request','336','count',4.00,NULL),(2169,1756975140,60,'slow_request','[\"POST\",\"\\/imports\\/preview\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@preview\"]','count',1.00,NULL),(2170,1756974960,360,'slow_request','[\"POST\",\"\\/imports\\/preview\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@preview\"]','count',1.00,NULL),(2171,1756974240,1440,'slow_request','[\"POST\",\"\\/imports\\/preview\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@preview\"]','count',1.00,NULL),(2172,1756974240,10080,'slow_request','[\"POST\",\"\\/imports\\/preview\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@preview\"]','count',1.00,NULL),(2173,1756975140,60,'slow_user_request','336','count',1.00,NULL),(2174,1756974960,360,'slow_user_request','336','count',1.00,NULL),(2181,1756975140,60,'slow_request','[\"POST\",\"\\/imports\\/preview\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@preview\"]','max',2951.00,NULL),(2182,1756974960,360,'slow_request','[\"POST\",\"\\/imports\\/preview\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@preview\"]','max',2951.00,NULL),(2183,1756974240,1440,'slow_request','[\"POST\",\"\\/imports\\/preview\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@preview\"]','max',2951.00,NULL),(2184,1756974240,10080,'slow_request','[\"POST\",\"\\/imports\\/preview\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@preview\"]','max',2951.00,NULL);
/*!40000 ALTER TABLE `pulse_aggregates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pulse_entries`
--

DROP TABLE IF EXISTS `pulse_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pulse_entries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` int unsigned NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `key` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `key_hash` binary(16) GENERATED ALWAYS AS (unhex(md5(`key`))) VIRTUAL,
  `value` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pulse_entries_timestamp_index` (`timestamp`),
  KEY `pulse_entries_type_index` (`type`),
  KEY `pulse_entries_key_hash_index` (`key_hash`),
  KEY `pulse_entries_timestamp_type_key_hash_value_index` (`timestamp`,`type`,`key_hash`,`value`)
) ENGINE=InnoDB AUTO_INCREMENT=377 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pulse_entries`
--

LOCK TABLES `pulse_entries` WRITE;
/*!40000 ALTER TABLE `pulse_entries` DISABLE KEYS */;
INSERT INTO `pulse_entries` (`id`, `timestamp`, `type`, `key`, `value`) VALUES (1,1756638314,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]',2392),(2,1756638317,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]',2027),(3,1756638336,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]',1020),(4,1756638698,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]',2407),(5,1756638700,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]',2024),(6,1756638879,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]',2419),(7,1756638882,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]',2026),(8,1756638911,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]',2602),(9,1756638914,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]',2042),(10,1756638936,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]',2571),(11,1756638938,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]',2025),(12,1756639148,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]',2743),(13,1756639151,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]',2049),(14,1756639161,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]',2570),(15,1756639164,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]',2040),(16,1756639176,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]',2775),(17,1756639179,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]',2034),(18,1756639189,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]',2849),(19,1756639192,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]',2040),(20,1756639208,'user_request','336',NULL),(21,1756639208,'user_request','336',NULL),(22,1756665170,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]',2607),(23,1756665172,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]',2055),(24,1756665186,'user_request','336',NULL),(25,1756665186,'user_request','336',NULL),(26,1756665194,'slow_request','[\"GET\",\"\\/onboarding\\/step\\/{step}\",\"App\\\\Http\\\\Controllers\\\\OnboardingController@step\"]',2430),(27,1756665194,'slow_user_request','336',NULL),(28,1756665194,'user_request','336',NULL),(29,1756665196,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]',2038),(30,1756665200,'user_request','336',NULL),(31,1756665201,'user_request','336',NULL),(32,1756665201,'user_request','336',NULL),(33,1756665207,'slow_request','[\"GET\",\"\\/onboarding\\/step\\/{step}\",\"App\\\\Http\\\\Controllers\\\\OnboardingController@step\"]',2417),(34,1756665207,'slow_user_request','336',NULL),(35,1756665207,'user_request','336',NULL),(36,1756665209,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]',2036),(37,1756665227,'user_request','336',NULL),(38,1756665228,'user_request','336',NULL),(39,1756665238,'user_request','336',NULL),(40,1756665239,'user_request','336',NULL),(41,1756666010,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]',4171),(42,1756666010,'slow_user_request','336',NULL),(43,1756666010,'user_request','336',NULL),(44,1756666011,'exception','[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]',1756666011),(45,1756666977,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]',2567),(46,1756666977,'slow_user_request','336',NULL),(47,1756666977,'user_request','336',NULL),(48,1756666978,'exception','[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]',1756666978),(49,1756666989,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]',1278),(50,1756666989,'slow_user_request','336',NULL),(51,1756666989,'user_request','336',NULL),(52,1756666990,'exception','[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]',1756666990),(53,1756666995,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]',2466),(54,1756666998,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]',2028),(55,1756667008,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]',1301),(56,1756667008,'slow_user_request','336',NULL),(57,1756667008,'user_request','336',NULL),(58,1756667009,'exception','[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]',1756667009),(59,1756667095,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]',1245),(60,1756667095,'slow_user_request','336',NULL),(61,1756667095,'user_request','336',NULL),(62,1756667095,'exception','[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]',1756667095),(63,1756667099,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]',2478),(64,1756667101,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]',2030),(65,1756667110,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]',2306),(66,1756667110,'slow_user_request','336',NULL),(67,1756667110,'user_request','336',NULL),(68,1756667111,'exception','[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]',1756667111),(69,1756667210,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]',2487),(70,1756667212,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]',2054),(71,1756667221,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]',2783),(72,1756667224,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]',2047),(73,1756667295,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]',2987),(74,1756667295,'slow_user_request','336',NULL),(75,1756667295,'user_request','336',NULL),(76,1756667295,'exception','[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]',1756667295),(77,1756667304,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]',1526),(78,1756667304,'slow_user_request','336',NULL),(79,1756667304,'user_request','336',NULL),(80,1756667305,'exception','[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]',1756667305),(81,1756667342,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]',1345),(82,1756667342,'slow_user_request','336',NULL),(83,1756667342,'user_request','336',NULL),(84,1756667342,'exception','[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]',1756667342),(85,1756667350,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]',1222),(86,1756667350,'slow_user_request','336',NULL),(87,1756667350,'user_request','336',NULL),(88,1756667350,'exception','[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]',1756667350),(89,1756667361,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]',2191),(90,1756667361,'slow_user_request','336',NULL),(91,1756667361,'user_request','336',NULL),(92,1756667361,'exception','[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]',1756667361),(93,1756667527,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]',8735),(94,1756667527,'slow_user_request','336',NULL),(95,1756667527,'user_request','336',NULL),(96,1756667528,'exception','[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]',1756667528),(97,1756667558,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]',1732),(98,1756667558,'slow_user_request','336',NULL),(99,1756667558,'user_request','336',NULL),(100,1756667558,'exception','[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]',1756667558),(101,1756667607,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]',2220),(102,1756667607,'slow_user_request','336',NULL),(103,1756667607,'user_request','336',NULL),(104,1756667608,'exception','[\"InvalidArgumentException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]',1756667608),(105,1756667660,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]',6842),(106,1756667660,'slow_user_request','336',NULL),(107,1756667660,'user_request','336',NULL),(108,1756667660,'exception','[\"InvalidArgumentException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]',1756667660),(109,1756667775,'user_request','336',NULL),(110,1756667878,'slow_request','[\"GET\",\"\\/imports\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@index\"]',3984),(111,1756667878,'slow_user_request','336',NULL),(112,1756667878,'user_request','336',NULL),(113,1756667879,'exception','[\"Error\",\"resources\\\\views\\\\tenant\\\\imports\\\\index.blade.php\"]',1756667879),(114,1756667886,'user_request','336',NULL),(115,1756667913,'user_request','336',NULL),(116,1756667917,'user_request','336',NULL),(117,1756667981,'user_request','336',NULL),(118,1756668008,'user_request','336',NULL),(119,1756668063,'user_request','336',NULL),(120,1756668117,'user_request','336',NULL),(121,1756668128,'slow_request','[\"GET\",\"\\/imports\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@index\"]',2398),(122,1756668128,'slow_user_request','336',NULL),(123,1756668128,'user_request','336',NULL),(124,1756668128,'exception','[\"Error\",\"resources\\\\views\\\\tenant\\\\imports\\\\index.blade.php\"]',1756668128),(125,1756668149,'user_request','336',NULL),(126,1756668179,'user_request','336',NULL),(127,1756668194,'user_request','336',NULL),(128,1756668217,'user_request','336',NULL),(129,1756668234,'user_request','336',NULL),(130,1756668323,'user_request','336',NULL),(131,1756668328,'user_request','336',NULL),(132,1756668337,'slow_request','[\"GET\",\"\\/imports\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@index\"]',2271),(133,1756668337,'slow_user_request','336',NULL),(134,1756668337,'user_request','336',NULL),(135,1756668337,'exception','[\"Error\",\"resources\\\\views\\\\tenant\\\\imports\\\\index.blade.php\"]',1756668337),(136,1756668350,'user_request','336',NULL),(137,1756668353,'user_request','336',NULL),(138,1756668362,'slow_request','[\"GET\",\"\\/profile\",\"App\\\\Http\\\\Controllers\\\\ProfileController@edit\"]',2489),(139,1756668362,'slow_user_request','336',NULL),(140,1756668362,'user_request','336',NULL),(141,1756668364,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]',2039),(142,1756668375,'user_request','336',NULL),(143,1756668383,'user_request','336',NULL),(144,1756668432,'user_request','336',NULL),(145,1756668441,'user_request','336',NULL),(146,1756668454,'user_request','336',NULL),(147,1756668474,'user_request','336',NULL),(148,1756668484,'user_request','336',NULL),(149,1756668543,'user_request','336',NULL),(150,1756668614,'user_request','336',NULL),(151,1756668644,'slow_request','[\"GET\",\"\\/imports\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@index\"]',2456),(152,1756668644,'slow_user_request','336',NULL),(153,1756668644,'user_request','336',NULL),(154,1756668645,'exception','[\"Error\",\"resources\\\\views\\\\tenant\\\\imports\\\\index.blade.php\"]',1756668645),(155,1756668702,'user_request','336',NULL),(156,1756668780,'slow_request','[\"GET\",\"\\/imports\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@index\"]',3668),(157,1756668780,'slow_user_request','336',NULL),(158,1756668780,'user_request','336',NULL),(159,1756668780,'exception','[\"Error\",\"resources\\\\views\\\\tenant\\\\imports\\\\index.blade.php\"]',1756668780),(160,1756669204,'user_request','336',NULL),(161,1756669206,'user_request','336',NULL),(162,1756669226,'user_request','336',NULL),(163,1756669229,'user_request','336',NULL),(164,1756669248,'user_request','336',NULL),(165,1756669249,'user_request','336',NULL),(166,1756669264,'user_request','336',NULL),(167,1756693213,'slow_request','[\"GET\",\"\\/\",\"Closure\"]',4669),(168,1756693218,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]',5661),(169,1756693223,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]',2590),(170,1756693245,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]',2497),(171,1756693248,'user_request','336',NULL),(172,1756693262,'slow_request','[\"GET\",\"\\/imports\\/create\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@create\"]',1416),(173,1756693262,'slow_user_request','336',NULL),(174,1756693262,'user_request','336',NULL),(175,1756693269,'user_request','336',NULL),(176,1756693274,'user_request','336',NULL),(177,1756693313,'user_request','336',NULL),(178,1756693433,'user_request','336',NULL),(179,1756703708,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]',2574),(180,1756703711,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]',2039),(181,1756703810,'user_request','336',NULL),(182,1756703819,'slow_request','[\"POST\",\"\\/imports\\/clear-data\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@clearData\"]',1926),(183,1756703819,'slow_user_request','336',NULL),(184,1756703819,'user_request','336',NULL),(185,1756703819,'exception','[\"Error\",\"app\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController.php:236\"]',1756703819),(186,1756703834,'user_request','336',NULL),(187,1756703834,'exception','[\"Error\",\"app\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController.php:236\"]',1756703834),(188,1756703968,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]',2559),(189,1756703970,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]',2035),(190,1756703995,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]',2460),(191,1756703998,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]',2020),(192,1756704002,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]',2462),(193,1756704005,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]',2057),(194,1756704042,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]',2414),(195,1756704044,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]',2034),(196,1756704048,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]',2465),(197,1756704051,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]',2033),(198,1756704056,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]',2457),(199,1756704058,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]',2027),(200,1756704063,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]',2463),(201,1756704065,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]',2035),(202,1756704069,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]',2465),(203,1756704072,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]',2056),(204,1756704076,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]',2434),(205,1756704078,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]',2028),(206,1756704105,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]',3197),(207,1756704108,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]',2049),(208,1756704117,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]',1067),(209,1756704117,'slow_user_request','336',NULL),(210,1756704117,'user_request','336',NULL),(211,1756704127,'slow_request','[\"POST\",\"\\/imports\\/clear-data\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@clearData\"]',4422),(212,1756704127,'slow_user_request','336',NULL),(213,1756704127,'user_request','336',NULL),(214,1756704128,'exception','[\"Error\",\"app\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController.php:236\"]',1756704128),(215,1756704176,'user_request','336',NULL),(216,1756705607,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]',23673),(217,1756705630,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]',2702),(218,1756705647,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]',2291),(219,1756705649,'user_request','336',NULL),(220,1756705656,'user_request','336',NULL),(221,1756705666,'user_request','336',NULL),(222,1756706018,'user_request','336',NULL),(223,1756706031,'user_request','336',NULL),(224,1756706137,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]',2611),(225,1756706140,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]',2026),(226,1756706181,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]',2469),(227,1756706183,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]',2045),(228,1756706195,'user_request','336',NULL),(229,1756706201,'user_request','336',NULL),(230,1756707301,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]',3076),(231,1756707304,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]',2053),(232,1756707317,'user_request','336',NULL),(233,1756707337,'user_request','336',NULL),(234,1756707342,'user_request','336',NULL),(235,1756707354,'user_request','336',NULL),(236,1756707359,'user_request','336',NULL),(237,1756707366,'user_request','336',NULL),(238,1756707368,'user_request','336',NULL),(239,1756707373,'user_request','336',NULL),(240,1756707394,'user_request','336',NULL),(241,1756707437,'user_request','336',NULL),(242,1756707464,'user_request','336',NULL),(243,1756707500,'user_request','336',NULL),(244,1756707502,'user_request','336',NULL),(245,1756707539,'user_request','336',NULL),(246,1756707835,'user_request','336',NULL),(247,1756707855,'user_request','336',NULL),(248,1756707859,'user_request','336',NULL),(249,1756707864,'user_request','336',NULL),(250,1756707937,'user_request','336',NULL),(251,1756707947,'user_request','336',NULL),(252,1756707972,'user_request','336',NULL),(253,1756707977,'user_request','336',NULL),(254,1756707981,'user_request','336',NULL),(255,1756708018,'user_request','336',NULL),(256,1756708031,'user_request','336',NULL),(257,1756708085,'user_request','336',NULL),(258,1756708089,'user_request','336',NULL),(259,1756708103,'slow_request','[\"GET\",\"\\/imports\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@index\"]',4952),(260,1756708103,'slow_user_request','336',NULL),(261,1756708103,'user_request','336',NULL),(262,1756708103,'exception','[\"Illuminate\\\\Database\\\\QueryException\",\"app\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController.php:29\"]',1756708103),(263,1756708113,'user_request','336',NULL),(264,1756708265,'user_request','336',NULL),(265,1756708269,'user_request','336',NULL),(266,1756708273,'user_request','336',NULL),(267,1756735607,'slow_request','[\"GET\",\"\\/\",\"Closure\"]',14967),(268,1756735622,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]',5654),(269,1756735628,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]',2470),(270,1756735640,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]',2213),(271,1756735642,'user_request','336',NULL),(272,1756735649,'user_request','336',NULL),(273,1756735653,'slow_request','[\"POST\",\"\\/imports\\/preview\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@preview\"]',3205),(274,1756735653,'slow_user_request','336',NULL),(275,1756735653,'user_request','336',NULL),(276,1756735660,'user_request','336',NULL),(277,1756738650,'user_request','336',NULL),(278,1756811146,'slow_request','[\"GET\",\"\\/\",\"Closure\"]',17708),(279,1756811165,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]',5908),(280,1756811171,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]',2472),(281,1756811465,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]',10322),(282,1756811475,'user_request','336',NULL),(283,1756811481,'user_request','336',NULL),(284,1756811496,'slow_request','[\"POST\",\"\\/imports\\/preview\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@preview\"]',3290),(285,1756811496,'slow_user_request','336',NULL),(286,1756811496,'user_request','336',NULL),(287,1756811502,'user_request','336',NULL),(288,1756811521,'slow_request','[\"GET\",\"\\/imports\\/validation\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@validation\"]',1048),(289,1756811521,'slow_user_request','336',NULL),(290,1756811521,'user_request','336',NULL),(291,1756811536,'user_request','336',NULL),(292,1756811602,'user_request','336',NULL),(293,1756811605,'user_request','336',NULL),(294,1756811703,'user_request','336',NULL),(295,1756811706,'user_request','336',NULL),(296,1756812069,'user_request','336',NULL),(297,1756812073,'slow_request','[\"POST\",\"\\/imports\\/preview\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@preview\"]',1248),(298,1756812073,'slow_user_request','336',NULL),(299,1756812073,'user_request','336',NULL),(300,1756812080,'user_request','336',NULL),(301,1756817129,'slow_request','[\"GET\",\"\\/\",\"Closure\"]',7268),(302,1756817136,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]',1421),(303,1756817136,'slow_user_request','336',NULL),(304,1756817136,'user_request','336',NULL),(305,1756817168,'slow_request','[\"GET\",\"\\/imports\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@index\"]',7487),(306,1756817168,'slow_user_request','336',NULL),(307,1756817168,'user_request','336',NULL),(308,1756817168,'exception','[\"Illuminate\\\\Database\\\\QueryException\",\"app\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController.php:29\"]',1756817168),(309,1756817278,'user_request','336',NULL),(310,1756817288,'slow_request','[\"POST\",\"\\/imports\\/preview\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@preview\"]',1856),(311,1756817288,'slow_user_request','336',NULL),(312,1756817288,'user_request','336',NULL),(313,1756817292,'user_request','336',NULL),(314,1756846475,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]',5117),(315,1756846480,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]',2608),(316,1756846491,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]',1415),(317,1756846493,'user_request','336',NULL),(318,1756846507,'user_request','336',NULL),(319,1756846513,'user_request','336',NULL),(320,1756846517,'user_request','336',NULL),(321,1756846554,'user_request','336',NULL),(322,1756846730,'user_request','336',NULL),(323,1756865990,'slow_request','[\"GET\",\"\\/\",\"Closure\"]',1752),(324,1756865993,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]',5615),(325,1756865998,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]',2627),(326,1756866218,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]',2438),(327,1756866221,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]',2028),(328,1756867054,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]',2714),(329,1756867057,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]',2044),(330,1756872708,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]',2717),(331,1756872711,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]',2044),(332,1756872813,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]',2417),(333,1756872815,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]',2040),(334,1756872826,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]',2171),(335,1756872828,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]',3011),(336,1756872828,'slow_user_request','336',NULL),(337,1756872828,'user_request','336',NULL),(338,1756872829,'exception','[\"Symfony\\\\Component\\\\Routing\\\\Exception\\\\RouteNotFoundException\",\"resources\\\\views\\\\tenant\\\\dashboard.blade.php\"]',1756872829),(339,1756873118,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]',2451),(340,1756873121,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]',2030),(341,1756873128,'user_request','336',NULL),(342,1756873155,'user_request','336',NULL),(343,1756873853,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]',2454),(344,1756873855,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]',2026),(345,1756873865,'user_request','336',NULL),(346,1756873936,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]',2383),(347,1756873938,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]',2027),(348,1756873949,'user_request','336',NULL),(349,1756873978,'user_request','336',NULL),(350,1756874017,'user_request','336',NULL),(351,1756874047,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]',2498),(352,1756874050,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]',2015),(353,1756874056,'user_request','336',NULL),(354,1756874068,'user_request','336',NULL),(355,1756879335,'slow_request','[\"GET\",\"\\/\",\"Closure\"]',6708),(356,1756879342,'user_request','336',NULL),(357,1756879373,'user_request','336',NULL),(358,1756879376,'user_request','336',NULL),(359,1756879462,'user_request','336',NULL),(360,1756879467,'user_request','336',NULL),(361,1756974660,'slow_request','[\"GET\",\"\\/\",\"Closure\"]',20934),(362,1756974682,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]',5743),(363,1756974687,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]',2512),(364,1756974701,'slow_request','[\"POST\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@store\"]',2529),(365,1756974704,'slow_request','[\"GET\",\"\\/dashboard\",\"App\\\\Http\\\\Controllers\\\\DashboardController@index\"]',10381),(366,1756974704,'slow_user_request','336',NULL),(367,1756974704,'user_request','336',NULL),(368,1756974704,'exception','[\"Illuminate\\\\Database\\\\QueryException\",\"app\\\\Http\\\\Controllers\\\\DashboardController.php:188\"]',1756974704),(369,1756975131,'slow_request','[\"GET\",\"\\/login\",\"App\\\\Http\\\\Controllers\\\\Auth\\\\AuthenticatedSessionController@create\"]',2488),(370,1756975134,'slow_outgoing_request','[\"POST\",\"http:\\/\\/127.0.0.1:13714\\/render\"]',2033),(371,1756975145,'user_request','336',NULL),(372,1756975156,'user_request','336',NULL),(373,1756975169,'slow_request','[\"POST\",\"\\/imports\\/preview\",\"App\\\\Http\\\\Controllers\\\\Tenant\\\\ImportController@preview\"]',2951),(374,1756975169,'slow_user_request','336',NULL),(375,1756975169,'user_request','336',NULL),(376,1756975174,'user_request','336',NULL);
/*!40000 ALTER TABLE `pulse_entries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pulse_values`
--

DROP TABLE IF EXISTS `pulse_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pulse_values` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` int unsigned NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `key` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `key_hash` binary(16) GENERATED ALWAYS AS (unhex(md5(`key`))) VIRTUAL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pulse_values_type_key_hash_unique` (`type`,`key_hash`),
  KEY `pulse_values_timestamp_index` (`timestamp`),
  KEY `pulse_values_type_index` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pulse_values`
--

LOCK TABLES `pulse_values` WRITE;
/*!40000 ALTER TABLE `pulse_values` DISABLE KEYS */;
/*!40000 ALTER TABLE `pulse_values` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_order_items`
--

DROP TABLE IF EXISTS `purchase_order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_order_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `purchase_order_id` bigint unsigned NOT NULL,
  `inventory_item_id` bigint unsigned NOT NULL,
  `quantity_ordered` decimal(12,3) NOT NULL,
  `quantity_received` decimal(12,3) NOT NULL DEFAULT '0.000',
  `unit_id` bigint unsigned NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(12,2) NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `batch_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_order_items_unit_id_foreign` (`unit_id`),
  KEY `purchase_order_items_purchase_order_id_index` (`purchase_order_id`),
  KEY `purchase_order_items_inventory_item_id_index` (`inventory_item_id`),
  CONSTRAINT `purchase_order_items_inventory_item_id_foreign` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_order_items_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_order_items_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_order_items`
--

LOCK TABLES `purchase_order_items` WRITE;
/*!40000 ALTER TABLE `purchase_order_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `purchase_order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_orders`
--

DROP TABLE IF EXISTS `purchase_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` bigint unsigned NOT NULL,
  `created_by` bigint unsigned NOT NULL,
  `po_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('draft','sent','received','partial','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `order_date` date NOT NULL,
  `expected_delivery_date` date DEFAULT NULL,
  `actual_delivery_date` date DEFAULT NULL,
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `approved_by` bigint unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `purchase_orders_po_number_unique` (`po_number`),
  KEY `purchase_orders_created_by_foreign` (`created_by`),
  KEY `purchase_orders_approved_by_foreign` (`approved_by`),
  KEY `purchase_orders_supplier_id_status_index` (`supplier_id`,`status`),
  KEY `purchase_orders_status_order_date_index` (`status`,`order_date`),
  CONSTRAINT `purchase_orders_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `purchase_orders_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `purchase_orders_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_orders`
--

LOCK TABLES `purchase_orders` WRITE;
/*!40000 ALTER TABLE `purchase_orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `purchase_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recipe_ingredients`
--

DROP TABLE IF EXISTS `recipe_ingredients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `recipe_ingredients` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `recipe_id` bigint unsigned NOT NULL,
  `inventory_item_id` bigint unsigned NOT NULL,
  `quantity` decimal(8,3) NOT NULL,
  `unit_id` bigint unsigned NOT NULL,
  `cost` decimal(8,2) NOT NULL DEFAULT '0.00',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `recipe_ingredients_unit_id_foreign` (`unit_id`),
  KEY `recipe_ingredients_recipe_id_index` (`recipe_id`),
  KEY `recipe_ingredients_inventory_item_id_index` (`inventory_item_id`),
  CONSTRAINT `recipe_ingredients_inventory_item_id_foreign` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `recipe_ingredients_recipe_id_foreign` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `recipe_ingredients_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recipe_ingredients`
--

LOCK TABLES `recipe_ingredients` WRITE;
/*!40000 ALTER TABLE `recipe_ingredients` DISABLE KEYS */;
/*!40000 ALTER TABLE `recipe_ingredients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recipes`
--

DROP TABLE IF EXISTS `recipes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `recipes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `menu_item_id` bigint unsigned NOT NULL,
  `version` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1.0',
  `instructions` text COLLATE utf8mb4_unicode_ci,
  `yield_quantity` decimal(8,2) NOT NULL,
  `preparation_time` int DEFAULT NULL,
  `cooking_time` int DEFAULT NULL,
  `cost_per_serving` decimal(8,2) NOT NULL DEFAULT '0.00',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `recipes_menu_item_id_version_unique` (`menu_item_id`,`version`),
  KEY `recipes_menu_item_id_is_active_index` (`menu_item_id`,`is_active`),
  CONSTRAINT `recipes_menu_item_id_foreign` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recipes`
--

LOCK TABLES `recipes` WRITE;
/*!40000 ALTER TABLE `recipes` DISABLE KEYS */;
/*!40000 ALTER TABLE `recipes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_permissions`
--

DROP TABLE IF EXISTS `role_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint unsigned NOT NULL,
  `permission_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_permissions_role_id_permission_id_unique` (`role_id`,`permission_id`),
  KEY `role_permissions_permission_id_foreign` (`permission_id`),
  CONSTRAINT `role_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_permissions`
--

LOCK TABLES `role_permissions` WRITE;
/*!40000 ALTER TABLE `role_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `role_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `level` int NOT NULL DEFAULT '1',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `meta_data` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_unique` (`name`),
  UNIQUE KEY `roles_slug_unique` (`slug`),
  KEY `roles_level_is_active_index` (`level`,`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales`
--

DROP TABLE IF EXISTS `sales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `transaction_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `menu_item_id` bigint unsigned DEFAULT NULL,
  `item_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` decimal(8,2) NOT NULL,
  `unit_price` decimal(8,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `discount_amount` decimal(8,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(8,2) NOT NULL DEFAULT '0.00',
  `status` enum('completed','cancelled','refunded') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'completed',
  `service_type` enum('dine_in','takeaway','drive_through','delivery') COLLATE utf8mb4_unicode_ci NOT NULL,
  `modifiers` json DEFAULT NULL,
  `sale_date` timestamp NOT NULL,
  `pos_system` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pos_metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sales_transaction_id_unique` (`transaction_id`),
  KEY `sales_menu_item_id_sale_date_index` (`menu_item_id`,`sale_date`),
  KEY `sales_status_sale_date_index` (`status`,`sale_date`),
  KEY `sales_service_type_sale_date_index` (`service_type`,`sale_date`),
  KEY `sales_transaction_id_index` (`transaction_id`),
  CONSTRAINT `sales_menu_item_id_foreign` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales`
--

LOCK TABLES `sales` WRITE;
/*!40000 ALTER TABLE `sales` DISABLE KEYS */;
/*!40000 ALTER TABLE `sales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_movements`
--

DROP TABLE IF EXISTS `stock_movements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_movements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `inventory_item_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `type` enum('in','out','adjustment','waste','transfer') COLLATE utf8mb4_unicode_ci NOT NULL,
  `reason` enum('purchase','sale','waste_spoilage','waste_theft','waste_preparation','adjustment','return') COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` decimal(12,3) NOT NULL,
  `unit_id` bigint unsigned NOT NULL,
  `unit_cost` decimal(10,2) DEFAULT NULL,
  `total_cost` decimal(12,2) DEFAULT NULL,
  `reference_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_id` bigint unsigned DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `expiry_date` date DEFAULT NULL,
  `batch_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_movements_user_id_foreign` (`user_id`),
  KEY `stock_movements_unit_id_foreign` (`unit_id`),
  KEY `stock_movements_inventory_item_id_type_created_at_index` (`inventory_item_id`,`type`,`created_at`),
  KEY `stock_movements_type_reason_created_at_index` (`type`,`reason`,`created_at`),
  KEY `stock_movements_reference_type_reference_id_index` (`reference_type`,`reference_id`),
  CONSTRAINT `stock_movements_inventory_item_id_foreign` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_movements_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `stock_movements_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_movements`
--

LOCK TABLES `stock_movements` WRITE;
/*!40000 ALTER TABLE `stock_movements` DISABLE KEYS */;
/*!40000 ALTER TABLE `stock_movements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subscription_plans`
--

DROP TABLE IF EXISTS `subscription_plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subscription_plans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `monthly_price` decimal(10,2) NOT NULL,
  `yearly_price` decimal(10,2) NOT NULL,
  `features` json NOT NULL,
  `limits` json NOT NULL,
  `is_popular` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subscription_plans_slug_unique` (`slug`),
  KEY `subscription_plans_is_active_sort_order_index` (`is_active`,`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subscription_plans`
--

LOCK TABLES `subscription_plans` WRITE;
/*!40000 ALTER TABLE `subscription_plans` DISABLE KEYS */;
/*!40000 ALTER TABLE `subscription_plans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `suppliers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_person` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `tax_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_terms` json DEFAULT NULL,
  `rating` decimal(3,2) NOT NULL DEFAULT '0.00',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `suppliers_is_active_rating_index` (`is_active`,`rating`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suppliers`
--

LOCK TABLES `suppliers` WRITE;
/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;
/*!40000 ALTER TABLE `suppliers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_settings`
--

DROP TABLE IF EXISTS `system_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `system_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'string',
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_public` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `system_settings_key_unique` (`key`),
  KEY `system_settings_key_index` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_settings`
--

LOCK TABLES `system_settings` WRITE;
/*!40000 ALTER TABLE `system_settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `system_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tenant_subscriptions`
--

DROP TABLE IF EXISTS `tenant_subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tenant_subscriptions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `subscription_plan_id` bigint unsigned NOT NULL,
  `stripe_subscription_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','cancelled','past_due','incomplete','incomplete_expired','trialing','unpaid') COLLATE utf8mb4_unicode_ci NOT NULL,
  `billing_cycle` enum('monthly','yearly') COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'AED',
  `current_period_start` date NOT NULL,
  `current_period_end` date NOT NULL,
  `trial_ends_at` date DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tenant_subscriptions_subscription_plan_id_foreign` (`subscription_plan_id`),
  KEY `tenant_subscriptions_tenant_id_status_index` (`tenant_id`,`status`),
  KEY `tenant_subscriptions_stripe_subscription_id_index` (`stripe_subscription_id`),
  CONSTRAINT `tenant_subscriptions_subscription_plan_id_foreign` FOREIGN KEY (`subscription_plan_id`) REFERENCES `subscription_plans` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tenant_subscriptions_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tenant_subscriptions`
--

LOCK TABLES `tenant_subscriptions` WRITE;
/*!40000 ALTER TABLE `tenant_subscriptions` DISABLE KEYS */;
/*!40000 ALTER TABLE `tenant_subscriptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tenants`
--

DROP TABLE IF EXISTS `tenants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tenants` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `domain` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `database` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `settings` json DEFAULT NULL,
  `onboarding_status` json DEFAULT NULL,
  `onboarding_completed_at` timestamp NULL DEFAULT NULL,
  `skip_onboarding` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `db_username` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `db_password` text COLLATE utf8mb4_unicode_ci,
  `db_host` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '127.0.0.1',
  `db_port` int NOT NULL DEFAULT '3306',
  `country_id` bigint unsigned DEFAULT NULL,
  `subscription_plan_id` bigint unsigned DEFAULT NULL,
  `status` enum('pending','approved','suspended','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `service_types` json DEFAULT NULL,
  `business_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_person` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `business_address` text COLLATE utf8mb4_unicode_ci,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trial_ends_at` date DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approved_by` bigint unsigned DEFAULT NULL,
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `usage_limits` json DEFAULT NULL,
  `usage_current` json DEFAULT NULL,
  `last_activity_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tenants_domain_unique` (`domain`),
  KEY `tenants_country_id_foreign` (`country_id`),
  KEY `tenants_subscription_plan_id_foreign` (`subscription_plan_id`),
  KEY `tenants_approved_by_foreign` (`approved_by`),
  CONSTRAINT `tenants_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `admin_users` (`id`),
  CONSTRAINT `tenants_country_id_foreign` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tenants_subscription_plan_id_foreign` FOREIGN KEY (`subscription_plan_id`) REFERENCES `subscription_plans` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tenants`
--

LOCK TABLES `tenants` WRITE;
/*!40000 ALTER TABLE `tenants` DISABLE KEYS */;
/*!40000 ALTER TABLE `tenants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `unit_types`
--

DROP TABLE IF EXISTS `unit_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `unit_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `base_unit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `unit_types`
--

LOCK TABLES `unit_types` WRITE;
/*!40000 ALTER TABLE `unit_types` DISABLE KEYS */;
/*!40000 ALTER TABLE `unit_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `units`
--

DROP TABLE IF EXISTS `units`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `units` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `unit_type_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `symbol` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `conversion_factor` decimal(10,4) NOT NULL,
  `is_base` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `units_unit_type_id_is_base_index` (`unit_type_id`,`is_base`),
  CONSTRAINT `units_unit_type_id_foreign` FOREIGN KEY (`unit_type_id`) REFERENCES `unit_types` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `units`
--

LOCK TABLES `units` WRITE;
/*!40000 ALTER TABLE `units` DISABLE KEYS */;
/*!40000 ALTER TABLE `units` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_roles`
--

DROP TABLE IF EXISTS `user_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` timestamp NULL DEFAULT NULL,
  `assigned_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_roles_user_id_role_id_unique` (`user_id`,`role_id`),
  KEY `user_roles_role_id_foreign` (`role_id`),
  KEY `user_roles_assigned_by_foreign` (`assigned_by`),
  KEY `user_roles_expires_at_index` (`expires_at`),
  CONSTRAINT `user_roles_assigned_by_foreign` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`),
  CONSTRAINT `user_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_roles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_roles`
--

LOCK TABLES `user_roles` WRITE;
/*!40000 ALTER TABLE `user_roles` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `preferred_language` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `timezone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'UTC',
  `language_preferences` json DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `tenant_id` bigint unsigned DEFAULT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'staff',
  `permissions` json DEFAULT NULL,
  `employee_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `hire_date` date DEFAULT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `department` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_contact` json DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `two_factor_secret` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `two_factor_recovery_codes` text COLLATE utf8mb4_unicode_ci,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_login_ip` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_tenant_id_foreign` (`tenant_id`),
  KEY `users_preferred_language_index` (`preferred_language`),
  KEY `users_role_is_active_index` (`role`,`is_active`),
  KEY `users_employee_id_index` (`employee_id`),
  KEY `users_last_login_at_index` (`last_login_at`),
  CONSTRAINT `users_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `waste_records`
--

DROP TABLE IF EXISTS `waste_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `waste_records` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `inventory_item_id` bigint unsigned NOT NULL,
  `recorded_by` bigint unsigned NOT NULL,
  `waste_type` enum('spoilage','theft','preparation','customer_return','damage') COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` decimal(12,3) NOT NULL,
  `unit_id` bigint unsigned NOT NULL,
  `cost_impact` decimal(10,2) NOT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci,
  `prevention_notes` text COLLATE utf8mb4_unicode_ci,
  `photos` json DEFAULT NULL,
  `discovery_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `waste_records_recorded_by_foreign` (`recorded_by`),
  KEY `waste_records_unit_id_foreign` (`unit_id`),
  KEY `waste_records_inventory_item_id_waste_type_discovery_date_index` (`inventory_item_id`,`waste_type`,`discovery_date`),
  KEY `waste_records_waste_type_discovery_date_index` (`waste_type`,`discovery_date`),
  CONSTRAINT `waste_records_inventory_item_id_foreign` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `waste_records_recorded_by_foreign` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `waste_records_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `waste_records`
--

LOCK TABLES `waste_records` WRITE;
/*!40000 ALTER TABLE `waste_records` DISABLE KEYS */;
/*!40000 ALTER TABLE `waste_records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'rmsaas_gr1dub'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-09-05 22:37:25
