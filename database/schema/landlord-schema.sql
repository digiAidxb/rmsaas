/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
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
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
DROP TABLE IF EXISTS `tenants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tenants` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `domain` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `database` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `settings` json DEFAULT NULL,
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
  `address` text COLLATE utf8mb4_unicode_ci,
  `hire_date` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_login_ip` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_tenant_id_foreign` (`tenant_id`),
  KEY `users_preferred_language_index` (`preferred_language`),
  CONSTRAINT `users_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'0001_01_01_000000_create_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'0001_01_01_000001_create_cache_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2025_08_27_225136_create_tenants_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2025_08_27_225745_add_tenant_id_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2025_08_28_051340_add_database_credentials_to_tenants_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2025_08_28_065752_create_comprehensive_landlord_schema',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2025_08_28_065845_create_comprehensive_tenant_schema',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2025_08_28_074357_create_pulse_tables',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10,'2025_08_28_081346_add_preferred_language_to_users_table',5);
