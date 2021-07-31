-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.5.10-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             11.2.0.6213
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for karali_group_db
CREATE DATABASE IF NOT EXISTS `karali_group_db` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `karali_group_db`;

-- Dumping structure for table karali_group_db.employee
CREATE TABLE IF NOT EXISTS `employee` (
  `employee_no` int(12) NOT NULL AUTO_INCREMENT,
  `public_id` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(225) NOT NULL,
  `pay_rate` int(12) NOT NULL DEFAULT 0,
  `restaurant_postal` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`employee_no`),
  UNIQUE KEY `public_id` (`public_id`),
  KEY `FK_employee_restaurant` (`restaurant_postal`),
  CONSTRAINT `FK_employee_restaurant` FOREIGN KEY (`restaurant_postal`) REFERENCES `restaurant` (`postal_code`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.

-- Dumping structure for table karali_group_db.pay_change_decision
CREATE TABLE IF NOT EXISTS `pay_change_decision` (
  `decision_id` int(12) NOT NULL,
  `approval` tinyint(1) NOT NULL DEFAULT 0,
  `signed_by` varchar(225) DEFAULT NULL COMMENT 'Manager username, can be replaced with manager id in future versions',
  `approved_date` datetime NOT NULL DEFAULT current_timestamp(),
  `effective_date` datetime DEFAULT NULL,
  PRIMARY KEY (`decision_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.

-- Dumping structure for table karali_group_db.pay_change_request
CREATE TABLE IF NOT EXISTS `pay_change_request` (
  `request_id` int(12) NOT NULL AUTO_INCREMENT,
  `employee_no` int(12) NOT NULL,
  `decision_id` int(12) NOT NULL,
  `old_pay_rate` int(12) NOT NULL,
  `new_pay_rate` int(12) NOT NULL,
  `pay_change_reason` text NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`request_id`),
  KEY `FK_pay_change_request_employee` (`employee_no`),
  CONSTRAINT `FK_pay_change_request_employee` FOREIGN KEY (`employee_no`) REFERENCES `employee` (`employee_no`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.

-- Dumping structure for table karali_group_db.restaurant
CREATE TABLE IF NOT EXISTS `restaurant` (
  `restaurant_id` int(12) NOT NULL AUTO_INCREMENT,
  `postal_code` varchar(10) NOT NULL,
  `name` text NOT NULL,
  `area_name` text NOT NULL,
  PRIMARY KEY (`restaurant_id`),
  KEY `postal_code` (`postal_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
