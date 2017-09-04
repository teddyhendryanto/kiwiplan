-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 03, 2017 at 06:04 AM
-- Server version: 5.7.14
-- PHP Version: 7.0.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kiwiplan`
--

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(3, '2014_10_12_000000_create_users_table', 1),
(4, '2014_10_12_100000_create_password_resets_table', 1),
(8, '2017_08_29_051225_entrust_setup_tables', 2),
(10, '2017_08_29_085309_add_rstatus_to_users', 3),
(11, '2017_09_03_102425_add_department_to_users', 4);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rstatus` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `display_name`, `description`, `rstatus`, `created_by`, `updated_by`, `deleted_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'accounting-access', 'Accounting Menu', 'Access accounting menu', 'AM', 'mis01', 'mis01', NULL, '2017-08-29 00:41:32', '2017-08-29 02:23:29', NULL),
(2, 'accounting-create', 'Accounting Create Record', 'Accounting can create record', 'AM', 'mis01', 'mis01', '{null}', '2017-08-29 00:58:03', '2017-08-29 02:19:44', NULL),
(3, 'accounting-edit', 'Accounting Edit Record', 'Accounting can edit record', 'AM', 'mis01', 'mis01', NULL, '2017-08-29 02:16:12', '2017-08-29 02:19:55', NULL),
(4, 'accounting-destroy', 'Accounting Destroy Record', 'Accounting can destroy record', 'AM', 'mis01', 'mis01', NULL, '2017-08-29 02:16:45', '2017-08-29 02:20:05', NULL),
(5, 'rollstock-access', 'Rollstock Menu', 'Access rollstock menu', 'AM', 'mis01', 'mis01', NULL, '2017-08-29 02:17:16', '2017-09-02 01:15:41', NULL),
(6, 'rollstock-create', 'Rollstock Create Record', 'Rollstock can create record', 'NW', 'mis01', NULL, NULL, '2017-08-29 02:21:37', '2017-08-29 02:21:37', NULL),
(7, 'rollstock-edit', 'Rollstock Edit Record', 'Rollstock can edit record', 'NW', 'mis01', NULL, NULL, '2017-08-29 02:21:54', '2017-08-29 02:21:54', NULL),
(8, 'rollstock-destroy', 'Rollstock Destroy Record', 'Rollstock can destroy record', 'NW', 'mis01', NULL, NULL, '2017-08-29 02:22:20', '2017-08-29 02:22:20', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `permission_role`
--

CREATE TABLE `permission_role` (
  `permission_id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permission_role`
--

INSERT INTO `permission_role` (`permission_id`, `role_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(2, 1),
(2, 2),
(2, 3),
(3, 1),
(3, 2),
(3, 3),
(4, 1),
(4, 2),
(5, 1),
(5, 4),
(5, 5),
(6, 1),
(6, 4),
(6, 5),
(7, 1),
(7, 4),
(7, 5),
(8, 1),
(8, 4);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rstatus` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `display_name`, `description`, `rstatus`, `created_by`, `updated_by`, `deleted_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'superuser', 'Superuser', 'Superuser', 'AM', 'mis01', 'mis01', NULL, '2017-08-29 01:12:03', '2017-08-29 01:30:19', NULL),
(2, 'accounting-spv', 'Accounting  Supervisor', 'Accounting Supervisor', 'NW', 'mis01', NULL, NULL, '2017-08-29 02:25:05', '2017-08-29 02:25:05', NULL),
(3, 'accounting-adm', 'Accounting Admin', 'Accounting Admin', 'NW', 'mis01', NULL, NULL, '2017-08-29 02:25:34', '2017-08-29 02:25:34', NULL),
(4, 'rollstock-spv', 'Rollstock Supervisor', 'Rollstock Supervisor', 'NW', 'mis01', NULL, NULL, '2017-08-29 02:26:04', '2017-08-29 02:26:04', NULL),
(5, 'rollstock-adm', 'Rollstock Admin', 'Rollstock Admin', 'NW', 'mis01', NULL, NULL, '2017-08-29 02:26:52', '2017-08-29 02:26:52', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `role_user`
--

CREATE TABLE `role_user` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_user`
--

INSERT INTO `role_user` (`user_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 2);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `department` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rstatus` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `name`, `department`, `password`, `email`, `remember_token`, `rstatus`, `created_by`, `updated_by`, `deleted_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'mis01', 'Teddy Hendryanto', 'IT', '$2y$10$CCOqjT5bNJAKvWQwjE9GVuGGpwj7b50fit.vK0LQ6/eULq5HAziVO', 'thendryanto@multiindustry.com', 'uWLAE7kysehTWYzkjbjhIiYfyUKjo702DE2ntuiXynO4xaEXsPh9niRU1Mil', 'AM', 'mis01', 'mis01', NULL, '2017-08-28 21:25:13', '2017-08-29 01:56:58', NULL),
(2, 'mis02', 'Hendry Handaka', 'IT', '$2y$10$8RYv8bOPryRSe..soqUKwO.ZgO5/pYiIX1v5vE9guCW9d25JlNQGe', 'hhandaka@multiindustry.com', NULL, 'AM', 'mis01', 'mis01', '{null}', '2017-08-29 01:46:02', '2017-08-29 02:06:22', NULL),
(3, 'acc02', 'Ricky Agustian', 'Accounting', '$2y$10$akxFXXE5IwyLQWS.wWr0oeOKcApNttPWc.xAiUaDYOSxP0MamNwhm', 'ragustian@ptmbi.com', 'PATuH4Rs5hvJiSM40qJn2qDyVOjFrwon2oQMl4IrZstpswZMsUfN1lOcOQ94', 'NW', 'mis01', NULL, NULL, '2017-08-29 02:40:23', '2017-08-29 02:40:23', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_unique` (`name`);

--
-- Indexes for table `permission_role`
--
ALTER TABLE `permission_role`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `permission_role_role_id_foreign` (`role_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_unique` (`name`);

--
-- Indexes for table `role_user`
--
ALTER TABLE `role_user`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `role_user_role_id_foreign` (`role_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
