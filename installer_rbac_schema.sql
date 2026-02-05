-- =============================================
-- Web-Based Installer & RBAC Schema
-- Application: Master Project CI3
-- =============================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------
-- Table: mst_user (Updated for RBAC compatibility)
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `mst_user` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `nama` text NOT NULL,
  `nik` text NOT NULL,
  `email` text NOT NULL,
  `password` text NOT NULL,
  `level` varchar(50) NOT NULL COMMENT 'Kept for backward compatibility',
  `date_created` date NOT NULL,
  `image` text NOT NULL,
  `is_active` int(2) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: tbl_aplikasi (Application Settings)
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `tbl_aplikasi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_aplikasi` varchar(250) NOT NULL,
  `alamat` varchar(250) NOT NULL,
  `telp` varchar(250) NOT NULL,
  `nama_developer` varchar(250) NOT NULL,
  `logo` varchar(250) NOT NULL DEFAULT 'default-logo.png',
  `title_icon` varchar(250) NOT NULL DEFAULT 'default-icon.png',
  `uninstall_secret_key` varchar(255) DEFAULT NULL,
  `default_timezone` varchar(100) NOT NULL DEFAULT 'Asia/Jakarta',
  `maintenance_mode` tinyint(1) NOT NULL DEFAULT 0,
  `session_timeout` int(11) NOT NULL DEFAULT 300,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- RBAC Tables
-- --------------------------------------------------------

-- Table: mst_roles (Role Definitions)
CREATE TABLE IF NOT EXISTS `mst_roles` (
  `id_role` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(100) NOT NULL,
  `role_description` text,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_role`),
  UNIQUE KEY `unique_role_name` (`role_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: mst_parent_menus (Parent Menu for Module Grouping)
CREATE TABLE IF NOT EXISTS `mst_parent_menus` (
  `id_parent_menu` int(11) NOT NULL AUTO_INCREMENT,
  `menu_name` varchar(100) NOT NULL,
  `icon` varchar(50) DEFAULT 'fa fa-folder',
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_parent_menu`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: mst_modules (Application Modules)
CREATE TABLE IF NOT EXISTS `mst_modules` (
  `id_module` int(11) NOT NULL AUTO_INCREMENT,
  `module_name` varchar(100) NOT NULL,
  `module_description` text,
  `controller_name` varchar(100) NOT NULL,
  `icon` varchar(50) DEFAULT 'fa fa-cube',
  `parent_id` int(11) DEFAULT NULL,
  `parent_menu_id` int(11) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_module`),
  KEY `idx_parent` (`parent_id`),
  KEY `idx_parent_menu` (`parent_menu_id`),
  FOREIGN KEY (`parent_id`) REFERENCES `mst_modules`(`id_module`) ON DELETE CASCADE,
  FOREIGN KEY (`parent_menu_id`) REFERENCES `mst_parent_menus`(`id_parent_menu`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: mst_permissions (Permission Definitions)
CREATE TABLE IF NOT EXISTS `mst_permissions` (
  `id_permission` int(11) NOT NULL AUTO_INCREMENT,
  `permission_name` varchar(100) NOT NULL,
  `permission_key` varchar(100) NOT NULL COMMENT 'Unique key for checking (e.g., user.create)',
  `permission_description` text,
  `module_id` int(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_permission`),
  UNIQUE KEY `unique_permission_key` (`permission_key`),
  KEY `idx_module` (`module_id`),
  FOREIGN KEY (`module_id`) REFERENCES `mst_modules`(`id_module`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: tbl_role_permissions (Role-Permission Relationship)
CREATE TABLE IF NOT EXISTS `tbl_role_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_role_permission` (`role_id`, `permission_id`),
  KEY `idx_role` (`role_id`),
  KEY `idx_permission` (`permission_id`),
  FOREIGN KEY (`role_id`) REFERENCES `mst_roles`(`id_role`) ON DELETE CASCADE,
  FOREIGN KEY (`permission_id`) REFERENCES `mst_permissions`(`id_permission`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: tbl_user_roles (User-Role Relationship)
CREATE TABLE IF NOT EXISTS `tbl_user_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_role` (`user_id`, `role_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_role` (`role_id`),
  FOREIGN KEY (`user_id`) REFERENCES `mst_user`(`id_user`) ON DELETE CASCADE,
  FOREIGN KEY (`role_id`) REFERENCES `mst_roles`(`id_role`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: tbl_rbac_audit_log (Audit Log for RBAC Changes)
CREATE TABLE IF NOT EXISTS `tbl_rbac_audit_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL COMMENT 'create_role, assign_permission, etc.',
  `target_type` varchar(50) NOT NULL COMMENT 'role, permission, user_role, etc.',
  `target_id` int(11) NOT NULL,
  `description` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_action` (`user_id`, `action`),
  KEY `idx_target` (`target_type`, `target_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: tbl_crud_history (CRUD Generator History)
CREATE TABLE IF NOT EXISTS `tbl_crud_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `table_name` varchar(100) NOT NULL,
  `controller_name` varchar(100) NOT NULL,
  `model_name` varchar(100) NOT NULL,
  `view_directory` varchar(255) NOT NULL,
  `generated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `generated_by` int(11) NOT NULL,
  `notification_type` varchar(50) NOT NULL,
  `field_configs` text DEFAULT NULL COMMENT 'JSON configuration for fields',
  PRIMARY KEY (`id`),
  KEY `idx_table_name` (`table_name`),
  KEY `idx_generated_by` (`generated_by`),
  KEY `idx_generated_at` (`generated_at`),
  FOREIGN KEY (`generated_by`) REFERENCES `mst_user`(`id_user`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Default Data Insertion
-- --------------------------------------------------------

-- --------------------------------------------------------
-- Table: tbl_chart_gen (Chart Configurations)
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `tbl_chart_gen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `chart_title` varchar(255) NOT NULL,
  `chart_type` varchar(50) NOT NULL,
  `placement_identifier` varchar(100) DEFAULT 'dashboard',
  `allowed_roles` text COMMENT 'JSON array of role IDs',
  `data_config` text COMMENT 'JSON array of series configuration',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: tbl_summary_widgets (Summary Widget Configurations)
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `tbl_summary_widgets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `table_name` varchar(100) NOT NULL,
  `column_name` varchar(100) NOT NULL,
  `aggregate_func` varchar(20) NOT NULL COMMENT 'COUNT, SUM, AVG, MIN, MAX',
  `bg_color_class` varchar(50) NOT NULL DEFAULT 'navy-bg',
  `allowed_roles` text COMMENT 'JSON array of role IDs',
  `placement` varchar(50) NOT NULL DEFAULT 'dashboard' COMMENT 'dashboard, report_page, etc.',
  `formatting` varchar(20) DEFAULT NULL COMMENT 'rupiah, number, etc.',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_table_name` (`table_name`),
  KEY `idx_placement` (`placement`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



COMMIT;

-- --------------------------------------------------------
-- Default Data Insertion
-- --------------------------------------------------------

-- Insert Default Role: Super Admin Only
INSERT IGNORE INTO `mst_roles` (`role_name`, `role_description`, `is_active`) VALUES
('Super Admin', 'Full system access with all permissions - can manage everything including RBAC', 1);

-- Insert Default Parent Menus
-- INSERT IGNORE INTO `mst_parent_menus` (`menu_name`, `icon`, `sort_order`, `is_active`) VALUES
-- ('System Tools', 'fa fa-cogs', 1, 1);

-- Insert Default Modules (parent_menu_id = NULL means standalone menu item)
INSERT IGNORE INTO `mst_modules` (`module_name`, `module_description`, `controller_name`, `icon`, `parent_id`, `parent_menu_id`, `sort_order`, `is_active`) VALUES
('Dashboard', 'Dashboard utama aplikasi', 'dashboard', 'fa fa-dashboard', NULL, NULL, 1, 1),
('RBAC Management', 'Manajemen Role & Permission', 'rbac', ' fa fa-shield', NULL, NULL, 2, 1),
('Table Generator', 'Database table generator', 'databasemanager', 'fa fa-database', NULL, NULL, 3, 1),
('Chart Generator', 'Dynamic Chart Generator Studio', 'chart_generator', 'fa fa-bar-chart', NULL, NULL, 4, 1),
('Summary Widget', 'Summary Widget Management', 'summary_widget', 'fa fa-th-large', NULL, NULL, 5, 1);

-- Insert Default Permissions
INSERT IGNORE INTO `mst_permissions` (`permission_name`, `permission_key`, `permission_description`, `module_id`, `is_active`) VALUES
-- Dashboard Permissions
('View Dashboard', 'dashboard.view', 'Akses ke halaman dashboard', 1, 1),

-- RBAC Management Permissions
('View Roles', 'rbac.roles.view', 'Melihat daftar role', 2, 1),
('Manage Roles', 'rbac.roles.manage', 'Mengelola role (create, edit, delete)', 2, 1),
('View Modules', 'rbac.modules.view', 'Melihat daftar modules', 2, 1),
('Manage Modules', 'rbac.modules.manage', 'Mengelola modules (create, edit, delete)', 2, 1),
('View Permissions', 'rbac.permissions.view', 'Melihat daftar permission', 2, 1),
('Manage Permissions', 'rbac.permissions.manage', 'Mengelola permission', 2, 1),
('Assign Permissions to Role', 'rbac.assign.permissions', 'Mengassign permission ke role', 2, 1),
('Assign Roles to User', 'rbac.assign.roles', 'Mengassign role ke user', 2, 1),

-- Table Generator Permissions
('Access Table Generator', 'table.generator', 'Akses ke table generator', 3, 1),

-- Chart Generator Permissions
('View Charts', 'chart.view', 'Melihat chart generator', 4, 1),
('Manage Charts', 'chart.manage', 'Membuat dan mengelola chart', 4, 1),

-- Summary Widget Permissions
('View Widgets', 'widget.view', 'Melihat daftar summary widget', 5, 1),
('Manage Widgets', 'widget.manage', 'Membuat dan mengelola summary widget', 5, 1);

-- Assign All Permissions to Super Admin Role (role_id = 1)
INSERT IGNORE INTO `tbl_role_permissions` (`role_id`, `permission_id`)
SELECT 1, id_permission FROM mst_permissions WHERE is_active = 1;



COMMIT;
