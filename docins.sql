-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 17, 2019 at 02:23 PM
-- Server version: 10.1.37-MariaDB
-- PHP Version: 7.3.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `final_form_system`
--
CREATE DATABASE IF NOT EXISTS `final_form_system` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `final_form_system`;

-- --------------------------------------------------------

--
-- Table structure for table `data`
--

CREATE TABLE IF NOT EXISTS `data` (
  `data_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `form_id` int(11) UNSIGNED NOT NULL,
  `data_group_id` int(11) NOT NULL,
  `label_key` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `data_value` varchar(512) COLLATE utf8_unicode_ci NOT NULL,
  `data_created_by` int(11) UNSIGNED DEFAULT NULL,
  `data_last_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`data_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `data_group`
--

CREATE TABLE IF NOT EXISTS `data_group` (
  `data_group_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `data_group_status` int(11) UNSIGNED NOT NULL,
  `data_group_created_by` int(11) UNSIGNED DEFAULT NULL,
  `data_group_last_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`data_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `form`
--

CREATE TABLE IF NOT EXISTS `form` (
  `form_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `form_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `form_status` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `form_created_by` int(11) UNSIGNED DEFAULT NULL,
  `form_created_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `form_modified_by` int(11) DEFAULT NULL,
  `form_last_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`form_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `label`
--

CREATE TABLE IF NOT EXISTS `label` (
  `label_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `form_id` int(11) UNSIGNED NOT NULL,
  `label_key` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `label_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `label_order` int(4) UNSIGNED NOT NULL,
  `label_created_by` int(11) UNSIGNED DEFAULT NULL,
  `label_last_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`label_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
