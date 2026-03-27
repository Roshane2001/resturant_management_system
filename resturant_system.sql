-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 26, 2026 at 06:07 AM
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
-- Database: `resturant_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `tblbranding`
--

CREATE TABLE `tblbranding` (
  `ID` int(11) NOT NULL DEFAULT 1,
  `company_name` varchar(255) DEFAULT NULL,
  `website_name` varchar(255) DEFAULT NULL,
  `phone_no` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `company_email` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `favicon` varchar(255) DEFAULT NULL,
  `service_charge` decimal(5,2) NOT NULL DEFAULT 0.00,
  `pax` int(11) DEFAULT 0,
  `notification_sound` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblbranding`
--

INSERT INTO `tblbranding` (`ID`, `company_name`, `website_name`, `phone_no`, `address`, `company_email`, `logo`, `favicon`, `service_charge`, `pax`, `notification_sound`) VALUES
(1, 'White Rose Breeze bay', '', '0770000000', 'Uswatakeiyawa', NULL, '1773399529_612794605_2325833554583809_7350564094185197008_n.jpg', '1773399529_612794605_2325833554583809_7350564094185197008_n.jpg', 10.00, 35, '1774501064_freesound_community-phone-ringing-48238 (1).mp3');

-- --------------------------------------------------------

--
-- Table structure for table `tblcategory`
--

CREATE TABLE `tblcategory` (
  `ID` int(11) NOT NULL,
  `ParentCategoryID` int(11) DEFAULT NULL,
  `CategoryName` varchar(120) DEFAULT NULL,
  `Status` int(1) DEFAULT 1,
  `RegDate` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblcategory`
--

INSERT INTO `tblcategory` (`ID`, `ParentCategoryID`, `CategoryName`, `Status`, `RegDate`) VALUES
(1, 3, 'Drinks', 1, '2026-03-19 10:47:39'),
(2, 3, 'Juice', 1, '2026-03-19 10:48:28'),
(3, 1, 'Turkey', 1, '2026-03-19 10:49:22'),
(4, 1, 'Fish', 1, '2026-03-19 10:49:46'),
(5, 1, 'Prawns', 1, '2026-03-19 10:49:59'),
(6, 1, 'Cuttle Fish', 1, '2026-03-19 10:50:13'),
(7, 1, 'Matti', 1, '2026-03-19 10:50:31'),
(8, 1, 'Knkun & Garlic', 1, '2026-03-19 10:50:54'),
(9, 1, 'Salads', 1, '2026-03-19 10:51:07'),
(10, 1, 'Other Bites', 1, '2026-03-19 10:51:21'),
(11, 1, 'Basmathi fried rice', 1, '2026-03-19 10:52:28'),
(12, 1, 'Basmathi Chopsy rice', 1, '2026-03-19 10:52:50'),
(13, 1, 'Noodles', 1, '2026-03-19 10:53:01'),
(14, 1, 'Chopsuey Noodles', 1, '2026-03-19 10:53:18'),
(15, 1, 'Omelette', 1, '2026-03-19 10:53:32'),
(16, 1, 'Chiken', 1, '2026-03-19 10:53:44'),
(17, 1, 'Pork', 1, '2026-03-19 10:53:56'),
(18, 1, 'Beef', 1, '2026-03-19 10:54:07'),
(19, 1, 'Mutton', 1, '2026-03-19 10:54:25'),
(20, 1, 'Rooster', 1, '2026-03-19 10:54:39'),
(21, 2, 'Soup Corner', 1, '2026-03-19 10:55:12'),
(22, 2, 'Salad corner', 1, '2026-03-19 10:55:29'),
(23, 2, 'Pasta Corner', 1, '2026-03-19 10:55:43'),
(24, 2, 'Kottu Corner', 1, '2026-03-19 10:56:02'),
(25, 2, 'Breez Bay Special', 1, '2026-03-19 10:56:41'),
(26, 2, 'Rice corner', 1, '2026-03-19 10:56:55'),
(27, 2, 'Noodles Corner', 1, '2026-03-19 10:57:11'),
(28, 2, 'Juice Menu', 1, '2026-03-19 10:57:32'),
(29, 3, 'Sprite', 1, '2026-03-20 10:18:01'),
(30, 3, 'Coke', 1, '2026-03-20 10:18:22'),
(31, 3, 'EGB', 1, '2026-03-20 10:23:46'),
(32, 3, 'Other beverages', 1, '2026-03-20 10:24:51'),
(33, 3, 'Soda', 1, '2026-03-20 10:28:03'),
(34, 4, 'Gold Leaf', 1, '2026-03-20 10:32:11'),
(35, 4, 'Dunhil', 1, '2026-03-20 10:32:26'),
(36, 5, 'Desets', 1, '2026-03-20 10:40:28'),
(37, 3, 'Welcome drink', 1, '2026-03-20 10:44:02'),
(38, 6, 'Fried Meals', 1, '2026-03-23 03:53:14'),
(39, 6, 'Chop suey Melas', 1, '2026-03-23 03:55:34');

-- --------------------------------------------------------

--
-- Table structure for table `tblorder`
--

CREATE TABLE `tblorder` (
  `ID` int(11) NOT NULL,
  `TableID` int(11) DEFAULT 0,
  `OrderType` varchar(50) DEFAULT 'Dine In',
  `TotalAmount` decimal(10,2) DEFAULT 0.00,
  `ServiceCharge` decimal(10,2) DEFAULT 0.00,
  `Discount` decimal(10,2) DEFAULT 0.00,
  `Status` varchar(20) DEFAULT 'Pending',
  `OrderDate` timestamp NULL DEFAULT current_timestamp(),
  `Time` varchar(20) DEFAULT NULL,
  `PaymentMethod` varchar(50) DEFAULT '0' COMMENT '0 - cash, 1 - card, 2 - online transfer'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblorder`
--

INSERT INTO `tblorder` (`ID`, `TableID`, `OrderType`, `TotalAmount`, `ServiceCharge`, `Discount`, `Status`, `OrderDate`, `Time`, `PaymentMethod`) VALUES
(1, 1, 'Dine In', 1500.00, 0.00, 0.00, 'Paid', '2026-03-19 11:48:48', NULL, 'Cash'),
(5, 5, 'Dine In', 3850.00, 0.00, 0.00, 'Paid', '2026-03-20 13:00:55', NULL, 'Cash'),
(6, 4, 'Dine In', 25355.00, 0.00, 0.00, 'Paid', '2026-03-21 04:08:22', NULL, 'Cash'),
(7, 1, 'Dine In', 5610.00, 0.00, 0.00, 'Paid', '2026-03-21 04:25:06', NULL, 'Cash'),
(8, 1, 'Dine In', 20185.00, 0.00, 0.00, 'Paid', '2026-03-21 04:25:54', '01:01:11 PM', 'Cash'),
(9, 5, 'Dine In', 27600.00, 0.00, 0.00, 'Paid', '2026-03-21 04:33:26', '12:21:58 PM', '0'),
(10, 0, 'Dine In', 19985.00, 0.00, 0.00, 'Paid', '2026-03-21 06:03:40', NULL, 'Cash'),
(11, 0, 'Dine In', 187.00, 17.00, 0.00, 'Paid', '2026-03-21 07:39:20', '01:09:20 PM', 'Cash'),
(12, 0, 'Dine In', 1500.00, 0.00, 0.00, 'Paid', '2026-03-21 07:42:58', '01:12:58 PM', 'Cash'),
(13, 1, 'Dine In', 1635.00, 150.00, 15.00, 'Paid', '2026-03-21 07:43:45', '01:13:45 PM', 'Cash'),
(14, 1, 'Dine In', 24963.00, 2355.00, 942.00, 'Paid', '2026-03-23 05:10:02', '10:40:02 AM', 'Cash'),
(15, 1, 'Dine In', 3815.00, 350.00, 35.00, 'Paid', '2026-03-23 05:12:57', '10:42:57 AM', 'Cash'),
(16, 1, 'Dine In', 7889.55, 745.00, 305.45, 'Paid', '2026-03-23 05:14:15', '10:44:15 AM', 'Cash'),
(17, 1, 'Dine In', 3850.00, 350.00, 0.00, 'Paid', '2026-03-23 05:31:54', '11:01:54 AM', 'Card'),
(18, 1, 'Dine In', 1870.00, 170.00, 0.00, 'Paid', '2026-03-23 05:42:07', '11:12:07 AM', '1'),
(19, 1, 'Dine In', 38500.00, 3500.00, 0.00, 'Paid', '2026-03-23 05:45:00', '11:15:00 AM', '1'),
(20, 1, 'Dine In', 8195.00, 745.00, 0.00, 'Paid', '2026-03-23 05:47:54', '11:17:54 AM', '1'),
(21, 1, 'Dine In', 1650.00, 150.00, 0.00, 'Paid', '2026-03-23 05:50:14', '11:20:14 AM', '1'),
(22, 1, 'Dine In', 10230.00, 930.00, 0.00, 'Paid', '2026-03-23 05:54:08', '11:37:56 AM', '1'),
(23, 1, 'Dine In', 18375.00, 1225.00, 0.00, 'Paid', '2026-03-23 06:14:41', '12:35:03 PM', '0'),
(24, 1, 'Dine In', 32340.00, 2940.00, 0.00, 'Paid', '2026-03-23 07:18:04', '12:50:00 PM', '1'),
(25, 1, 'Dine In', 1870.00, 170.00, 0.00, 'Paid', '2026-03-23 07:26:24', '12:58:43 PM', '0'),
(26, 0, 'Dine In', 2400.00, 0.00, 0.00, 'Paid', '2026-03-23 07:46:00', '01:16:00 PM', '0'),
(27, 0, 'Dine In', 2450.00, 0.00, 0.00, 'Paid', '2026-03-23 07:58:27', '01:28:27 PM', '0'),
(28, 0, 'Dine In', 3500.00, 0.00, 0.00, 'Paid', '2026-03-23 08:26:38', '01:56:38 PM', '0'),
(29, 0, 'Take-away', 3500.00, 0.00, 0.00, 'Paid', '2026-03-23 08:34:08', '02:04:08 PM', '0'),
(30, 1, 'Dine-in', 5145.00, 245.00, 0.00, 'Paid', '2026-03-23 08:45:07', '02:15:26 PM', '0'),
(31, 0, 'Take-away', 1650.00, 0.00, 0.00, 'Paid', '2026-03-23 08:53:45', '02:23:45 PM', '0'),
(32, 1, 'Dine-in', 24255.00, 2205.00, 0.00, 'Paid', '2026-03-23 08:59:47', '03:35:06 PM', '0'),
(33, 4, 'Dine-in', 2695.00, 245.00, 0.00, 'Paid', '2026-03-23 10:05:56', '03:36:57 PM', '0'),
(34, 0, 'Take-away', 1500.00, 0.00, 0.00, 'Paid', '2026-03-23 10:16:55', '03:47:27 PM', '0'),
(35, 0, 'Take-away', 3500.00, 0.00, 0.00, 'Pending', '2026-03-23 10:17:40', '03:47:40 PM', '0'),
(36, 0, 'Take-away', 3500.00, 0.00, 0.00, 'Paid', '2026-03-23 10:19:52', '03:50:00 PM', '0'),
(37, 0, 'Take-away', 1650.00, 0.00, 0.00, 'Paid', '2026-03-23 10:25:40', '03:55:56 PM', '0'),
(38, 0, 'Take-away', 1500.00, 0.00, 0.00, 'Paid', '2026-03-23 10:26:40', '03:56:52 PM', '0'),
(39, 4, 'Dine-in', 2695.00, 245.00, 0.00, 'Paid', '2026-03-23 11:49:55', '05:20:26 PM', '0'),
(40, 1, 'Dine-in', 5286.50, 485.00, 48.50, 'Paid', '2026-03-23 12:22:55', '05:54:04 PM', '0'),
(41, 0, 'Take-away', 5150.00, 0.00, 0.00, 'Paid', '2026-03-23 12:25:01', '05:55:28 PM', '0'),
(42, 1, 'Dine-in', 29095.00, 2645.00, 0.00, 'Paid', '2026-03-23 14:15:47', '09:26:42 AM', '0'),
(43, 0, 'Take-away', 2450.00, 0.00, 0.00, 'Paid', '2026-03-23 14:22:33', '07:52:41 PM', '0'),
(44, 6, 'Dine-in', 47135.00, 4285.00, 0.00, 'Paid', '2026-03-24 04:33:04', '09:26:17 AM', '0'),
(45, 4, 'Dine-in', 24255.00, 2205.00, 0.00, 'Paid', '2026-03-24 09:01:00', '09:27:09 AM', '0'),
(46, 0, 'Take-away', 1500.00, 0.00, 0.00, 'Paid', '2026-03-24 10:24:38', '03:55:02 PM', '0'),
(47, 0, 'Dine-in', 3850.00, 350.00, 0.00, 'Pending', '2026-03-24 10:25:45', '03:55:45 PM', '0'),
(48, 0, 'Take-away', 1500.00, 0.00, 0.00, 'Pending', '2026-03-24 10:49:05', '04:19:05 PM', '0'),
(49, 0, 'Take-away', 1500.00, 0.00, 0.00, 'Pending', '2026-03-24 11:00:17', '04:30:17 PM', '0'),
(50, 0, 'Take-away', 1500.00, 0.00, 0.00, 'Pending', '2026-03-24 11:08:48', '04:38:48 PM', '0'),
(51, 0, 'Take-away', 180.00, 0.00, 0.00, 'Pending', '2026-03-24 11:21:16', '04:51:16 PM', '0'),
(52, 0, 'Take-away', 25500.00, 0.00, 0.00, 'Pending', '2026-03-25 04:07:56', '09:37:56 AM', '0'),
(53, 0, 'Dine-in', 187.00, 17.00, 0.00, 'Pending', '2026-03-25 04:12:58', '09:42:58 AM', '0'),
(54, 0, 'Take-away', 1500.00, 0.00, 0.00, 'Paid', '2026-03-25 04:13:29', '09:43:36 AM', '0'),
(55, 0, 'Take-away', 1500.00, 0.00, 0.00, 'Pending', '2026-03-25 04:20:41', '09:50:41 AM', '0'),
(56, 0, 'Dine-in', 187.00, 17.00, 0.00, 'Pending', '2026-03-25 04:20:56', '09:50:56 AM', '0'),
(57, 0, 'Take-away', 170.00, 0.00, 0.00, 'Pending', '2026-03-25 04:21:21', '09:51:21 AM', '0'),
(58, 0, 'Take-away', 180.00, 0.00, 0.00, 'Paid', '2026-03-25 04:24:41', '09:55:01 AM', '0'),
(59, 0, 'Take-away', 1500.00, 0.00, 0.00, 'Paid', '2026-03-25 04:45:55', '10:16:08 AM', '0'),
(60, 0, 'Take-away', 170.00, 0.00, 0.00, 'Paid', '2026-03-25 04:50:16', '10:20:26 AM', '0'),
(61, 0, 'Take-away', 3500.00, 0.00, 0.00, 'Paid', '2026-03-25 04:51:06', '10:21:10 AM', '0'),
(62, 1, 'Dine-in', 8965.00, 665.00, 0.00, 'Paid', '2026-03-25 05:05:07', '10:35:42 AM', '0'),
(63, 1, 'Dine-in', 7810.00, 560.00, 0.00, 'Paid', '2026-03-25 05:40:51', '11:13:45 AM', '0'),
(64, 1, 'Dine-in', 2695.00, 245.00, 0.00, 'Paid', '2026-03-25 06:32:08', '12:02:43 PM', '0'),
(65, 4, 'Dine-in', 2695.00, 245.00, 0.00, 'Paid', '2026-03-25 06:35:51', '12:06:52 PM', '0'),
(66, 6, 'Dine-in', 4195.00, 245.00, 0.00, 'Paid', '2026-03-25 06:41:46', '12:12:09 PM', '0'),
(67, 1, 'Dine-in', 2695.00, 245.00, 0.00, 'Paid', '2026-03-25 06:51:55', '12:22:21 PM', '0'),
(68, 4, 'Dine-in', 4345.00, 245.00, 0.00, 'Paid', '2026-03-25 06:56:04', '12:26:47 PM', '0'),
(69, 6, 'Dine-in', 1650.00, 150.00, 0.00, 'Paid', '2026-03-25 07:18:50', '12:49:54 PM', '0'),
(70, 4, 'Dine-in', 6930.00, 480.00, 0.00, 'Paid', '2026-03-25 07:20:09', '12:51:32 PM', '0'),
(71, 0, 'Take-away', 3200.00, 0.00, 0.00, 'Paid', '2026-03-25 07:22:24', '12:52:35 PM', '1'),
(72, 9, 'Dine-in', 5500.00, 500.00, 0.00, 'Paid', '2026-03-25 07:34:15', '01:05:59 PM', '0'),
(73, 6, 'Dine-in', 8085.00, 735.00, 0.00, 'Paid', '2026-03-25 08:35:20', '03:15:39 PM', '0'),
(74, 8, 'Dine-in', 3685.00, 335.00, 0.00, 'Paid', '2026-03-25 08:35:33', '02:07:27 PM', '0'),
(75, 9, 'Dine-in', 5390.00, 490.00, 0.00, 'Paid', '2026-03-25 08:37:42', '02:38:10 PM', '0'),
(76, 7, 'Dine-in', 7205.00, 655.00, 0.00, 'Paid', '2026-03-25 08:45:10', '02:55:49 PM', '0'),
(77, 8, 'Dine-in', 2695.00, 245.00, 0.00, 'Paid', '2026-03-25 09:07:39', '02:37:59 PM', '0'),
(78, 8, 'Dine-in', 5390.00, 490.00, 0.00, 'Paid', '2026-03-25 09:46:50', '03:17:28 PM', '0'),
(79, 7, 'Dine-in', 2695.00, 245.00, 0.00, 'Paid', '2026-03-25 09:49:41', '03:20:12 PM', '0'),
(80, 4, 'Dine-in', 4400.00, 400.00, 0.00, 'Paid', '2026-03-25 10:30:19', '04:01:57 PM', '0'),
(81, 8, 'Dine-in', 2695.00, 245.00, 0.00, 'Paid', '2026-03-25 10:30:47', '04:01:28 PM', '0');

-- --------------------------------------------------------

--
-- Table structure for table `tblorder_details`
--

CREATE TABLE `tblorder_details` (
  `ID` int(11) NOT NULL,
  `OrderID` int(11) DEFAULT NULL,
  `ProductID` int(11) DEFAULT NULL,
  `ProductName` varchar(255) DEFAULT NULL,
  `Price` decimal(10,2) DEFAULT 0.00,
  `Qty` int(11) DEFAULT 0,
  `KOT` varchar(50) DEFAULT NULL,
  `OrderDate` date DEFAULT NULL,
  `OrderTime` varchar(20) DEFAULT NULL,
  `order_status` tinyint(1) DEFAULT 0 COMMENT '0 - pending, 1 - process, 2 - completed',
  `staff_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblorder_details`
--

INSERT INTO `tblorder_details` (`ID`, `OrderID`, `ProductID`, `ProductName`, `Price`, `Qty`, `KOT`, `OrderDate`, `OrderTime`, `order_status`, `staff_id`) VALUES
(1, 1, 5, 'Coca Cola', 300.00, 2, NULL, NULL, NULL, NULL, 0),
(17, 10, 55, NULL, 350.00, 1, NULL, NULL, NULL, NULL, 0),
(20, 5, 14, NULL, 3500.00, 1, NULL, NULL, NULL, NULL, 0),
(71, 6, 33, NULL, 2450.00, 1, NULL, NULL, NULL, 2, 0),
(72, 6, 34, NULL, 2450.00, 1, NULL, NULL, NULL, 2, 0),
(73, 6, 33, NULL, 2450.00, 1, NULL, NULL, NULL, NULL, 0),
(74, 6, 34, NULL, 2450.00, 1, NULL, NULL, NULL, NULL, 0),
(75, 6, 34, NULL, 2450.00, 1, NULL, NULL, NULL, NULL, 0),
(76, 6, 33, NULL, 2450.00, 1, NULL, NULL, NULL, NULL, 0),
(77, 6, 34, NULL, 2450.00, 1, NULL, NULL, NULL, NULL, 0),
(78, 6, 33, NULL, 2450.00, 1, NULL, NULL, NULL, NULL, 0),
(79, 6, 25, NULL, 800.00, 1, NULL, NULL, NULL, NULL, 0),
(80, 6, 60, NULL, 200.00, 1, NULL, NULL, NULL, NULL, 0),
(81, 6, 34, NULL, 2450.00, 1, NULL, NULL, NULL, NULL, 0),
(97, 7, 33, NULL, 2450.00, 2, NULL, NULL, NULL, NULL, 0),
(98, 7, 60, NULL, 200.00, 1, NULL, NULL, NULL, NULL, 0),
(112, 8, 33, NULL, 2450.00, 1, NULL, NULL, NULL, NULL, 0),
(113, 8, 33, NULL, 2450.00, 1, NULL, NULL, NULL, NULL, 0),
(114, 8, 24, NULL, 1100.00, 1, NULL, NULL, NULL, NULL, 0),
(115, 8, 33, NULL, 2450.00, 1, NULL, NULL, NULL, NULL, 0),
(116, 8, 34, NULL, 2450.00, 1, NULL, NULL, NULL, NULL, 0),
(117, 8, 35, NULL, 2450.00, 1, NULL, NULL, NULL, NULL, 0),
(118, 8, 36, NULL, 2450.00, 1, NULL, NULL, NULL, NULL, 0),
(119, 8, 38, NULL, 2050.00, 1, NULL, NULL, NULL, NULL, 0),
(120, 8, 37, NULL, 500.00, 1, NULL, NULL, NULL, NULL, 0),
(121, 11, 53, NULL, 170.00, 1, NULL, NULL, NULL, NULL, 0),
(122, 12, 11, NULL, 1500.00, 1, NULL, NULL, NULL, NULL, 0),
(123, 13, 11, NULL, 1500.00, 1, NULL, NULL, NULL, NULL, 0),
(124, 10, 36, NULL, 2450.00, 3, NULL, NULL, NULL, NULL, 0),
(125, 10, 35, NULL, 2450.00, 1, NULL, NULL, NULL, NULL, 0),
(126, 10, 35, NULL, 2450.00, 1, NULL, NULL, NULL, NULL, 0),
(127, 10, 36, NULL, 2450.00, 1, NULL, NULL, NULL, NULL, 0),
(128, 14, 11, NULL, 1500.00, 1, NULL, NULL, NULL, NULL, 0),
(129, 14, 12, NULL, 1700.00, 1, NULL, NULL, NULL, NULL, 0),
(130, 14, 13, NULL, 1850.00, 11, NULL, NULL, NULL, NULL, 0),
(131, 15, 14, NULL, 3500.00, 1, NULL, NULL, NULL, NULL, 0),
(132, 16, 14, NULL, 3500.00, 1, NULL, NULL, NULL, 2, 0),
(133, 16, 15, NULL, 3950.00, 1, NULL, NULL, NULL, NULL, 0),
(134, 17, 14, NULL, 3500.00, 1, NULL, NULL, NULL, NULL, 0),
(135, 18, 12, NULL, 1700.00, 1, NULL, NULL, NULL, NULL, 0),
(136, 19, 14, NULL, 3500.00, 10, NULL, NULL, NULL, NULL, 0),
(137, 20, 14, NULL, 3500.00, 1, NULL, NULL, NULL, NULL, 0),
(138, 20, 15, NULL, 3950.00, 1, NULL, NULL, NULL, NULL, 0),
(139, 21, 11, NULL, 1500.00, 1, NULL, NULL, NULL, NULL, 0),
(145, 22, 7, NULL, 1700.00, 1, NULL, NULL, NULL, NULL, 0),
(146, 22, 8, NULL, 1750.00, 1, NULL, NULL, NULL, NULL, 0),
(147, 22, 7, NULL, 1700.00, 1, NULL, NULL, NULL, NULL, 0),
(148, 22, 33, NULL, 2450.00, 1, NULL, NULL, NULL, NULL, 0),
(149, 22, 7, NULL, 1700.00, 1, NULL, NULL, NULL, NULL, 0),
(150, 10, 33, NULL, 2450.00, 1, '4731', NULL, NULL, NULL, 0),
(151, 10, 34, NULL, 2450.00, 1, '4731', NULL, NULL, NULL, 0),
(163, 9, 33, NULL, 2450.00, 1, NULL, '2026-03-23', '12:21:58 PM', NULL, 0),
(164, 9, 34, NULL, 2450.00, 1, NULL, '2026-03-23', '12:21:58 PM', NULL, 0),
(165, 9, 1, NULL, 1650.00, 1, NULL, '2026-03-23', '12:21:58 PM', NULL, 0),
(166, 9, 31, NULL, 700.00, 1, NULL, '2026-03-23', '12:21:58 PM', NULL, 0),
(167, 9, 33, NULL, 2450.00, 1, NULL, '2026-03-23', '12:21:58 PM', NULL, 0),
(168, 9, 34, NULL, 2450.00, 1, NULL, '2026-03-23', '12:21:58 PM', NULL, 0),
(169, 9, 36, NULL, 2450.00, 1, NULL, '2026-03-23', '12:21:58 PM', NULL, 0),
(170, 9, 34, NULL, 2450.00, 1, NULL, '2026-03-23', '12:21:58 PM', NULL, 0),
(171, 9, 39, NULL, 750.00, 1, NULL, '2026-03-23', '12:21:58 PM', NULL, 0),
(172, 9, 35, NULL, 2450.00, 1, NULL, '2026-03-23', '12:21:58 PM', NULL, 0),
(173, 23, 35, NULL, 2450.00, 1, NULL, '2026-03-23', '12:35:03 PM', NULL, 0),
(174, 23, 35, NULL, 2450.00, 1, NULL, '2026-03-23', '12:35:03 PM', NULL, 0),
(175, 23, 35, NULL, 2450.00, 1, NULL, '2026-03-23', '12:35:03 PM', NULL, 0),
(176, 23, 35, NULL, 2450.00, 1, NULL, '2026-03-23', '12:35:03 PM', NULL, 0),
(177, 23, 35, NULL, 2450.00, 1, NULL, '2026-03-23', '12:35:03 PM', NULL, 0),
(178, 23, 36, NULL, 2450.00, 1, '1', '2026-03-23', '12:44:29 PM', NULL, 0),
(179, 23, 35, NULL, 2450.00, 1, '1', '2026-03-23', '12:44:29 PM', NULL, 0),
(180, 9, 35, NULL, 2450.00, 2, '2', '2026-03-23', '12:44:51 PM', NULL, 0),
(181, 9, 36, NULL, 2450.00, 1, '2', '2026-03-23', '12:44:51 PM', NULL, 0),
(185, 24, 34, NULL, 2450.00, 2, NULL, '2026-03-23', '12:50:00 PM', NULL, 0),
(186, 24, 35, NULL, 2450.00, 10, NULL, '2026-03-23', '12:50:00 PM', NULL, 0),
(188, 25, 12, NULL, 1700.00, 1, NULL, '2026-03-23', '12:58:43 PM', NULL, 0),
(189, 26, 23, NULL, 1200.00, 2, NULL, '2026-03-23', '01:16:00 PM', NULL, 0),
(190, 27, 33, NULL, 2450.00, 1, '3', '2026-03-23', '01:28:27 PM', NULL, 0),
(191, 28, 14, NULL, 3500.00, 1, '4', '2026-03-23', '01:56:38 PM', NULL, 0),
(192, 29, 14, NULL, 3500.00, 1, '5', '2026-03-23', '02:04:08 PM', NULL, 0),
(194, 30, 35, NULL, 2450.00, 1, '7', '2026-03-23', '02:15:26 PM', NULL, 0),
(195, 31, 17, NULL, 1650.00, 1, '8', '2026-03-23', '02:23:45 PM', NULL, 0),
(196, 30, 36, NULL, 2450.00, 1, '9', '2026-03-23', '02:28:21 PM', NULL, 0),
(208, 32, 35, NULL, 2450.00, 2, '18', '2026-03-23', '03:35:06 PM', NULL, 0),
(209, 32, 36, NULL, 2450.00, 7, '18', '2026-03-23', '03:35:06 PM', NULL, 0),
(211, 33, 35, NULL, 2450.00, 1, '20', '2026-03-23', '03:36:57 PM', NULL, 0),
(213, 34, 11, NULL, 1500.00, 1, '22', '2026-03-23', '03:47:27 PM', NULL, 0),
(214, 35, 14, NULL, 3500.00, 1, '23', '2026-03-23', '03:47:40 PM', NULL, 0),
(216, 36, 14, NULL, 3500.00, 1, '25', '2026-03-23', '03:50:00 PM', NULL, 0),
(218, 37, 17, NULL, 1650.00, 1, '27', '2026-03-23', '03:55:56 PM', NULL, 0),
(220, 38, 11, NULL, 1500.00, 1, '29', '2026-03-23', '03:56:52 PM', NULL, 0),
(222, 39, 34, NULL, 2450.00, 1, '31', '2026-03-23', '05:20:26 PM', NULL, 0),
(225, 40, 39, NULL, 750.00, 1, '33', '2026-03-23', '05:54:04 PM', NULL, 0),
(226, 40, 40, NULL, 600.00, 1, '33', '2026-03-23', '05:54:04 PM', NULL, 0),
(227, 40, 14, NULL, 3500.00, 1, '33', '2026-03-23', '05:54:04 PM', NULL, 0),
(230, 41, 7, NULL, 1700.00, 2, '35', '2026-03-23', '05:55:28 PM', NULL, 0),
(231, 41, 8, NULL, 1750.00, 1, '35', '2026-03-23', '05:55:28 PM', NULL, 0),
(236, 43, 33, NULL, 2450.00, 1, '39', '2026-03-23', '07:52:41 PM', NULL, 0),
(267, 46, 11, NULL, 1500.00, 1, '28', '2026-03-24', '03:55:02 PM', 2, 1),
(268, 47, 14, NULL, 3500.00, 1, '29', '2026-03-24', '03:55:45 PM', 2, 1),
(270, 48, 11, NULL, 1500.00, 1, '31', '2026-03-24', '04:19:05 PM', 2, 1),
(271, 49, 11, NULL, 1500.00, 1, '32', '2026-03-24', '04:30:17 PM', 2, 1),
(272, 50, 11, NULL, 1500.00, 1, '33', '2026-03-24', '04:38:48 PM', 2, 1),
(276, 51, 54, NULL, 180.00, 1, '37', '2026-03-24', '04:51:16 PM', 2, 1),
(284, 44, 2, NULL, 1500.00, 5, '1', '2026-03-25', '09:26:17 AM', 2, 1),
(285, 44, 6, NULL, 1600.00, 1, '1', '2026-03-25', '09:26:17 AM', 2, 1),
(286, 44, 9, NULL, 1800.00, 1, '1', '2026-03-25', '09:26:17 AM', 2, 1),
(287, 44, 33, NULL, 2450.00, 1, '1', '2026-03-25', '09:26:17 AM', 2, 1),
(288, 44, 34, NULL, 2450.00, 1, '1', '2026-03-25', '09:26:17 AM', 2, 1),
(289, 44, 35, NULL, 2450.00, 1, '1', '2026-03-25', '09:26:17 AM', 2, 1),
(290, 44, 36, NULL, 2450.00, 10, '1', '2026-03-25', '09:26:17 AM', 2, 1),
(291, 44, 50, NULL, 100.00, 1, '1', '2026-03-25', '09:26:17 AM', 2, 1),
(292, 42, 2, NULL, 1500.00, 3, '2', '2026-03-25', '09:26:42 AM', 2, 1),
(293, 42, 4, NULL, 1700.00, 1, '2', '2026-03-25', '09:26:42 AM', 2, 1),
(294, 42, 6, NULL, 1600.00, 3, '2', '2026-03-25', '09:26:42 AM', 2, 1),
(295, 42, 36, NULL, 2450.00, 6, '2', '2026-03-25', '09:26:42 AM', 2, 1),
(296, 42, 39, NULL, 750.00, 1, '2', '2026-03-25', '09:26:42 AM', 2, 1),
(297, 45, 36, NULL, 2450.00, 9, '3', '2026-03-25', '09:27:09 AM', 2, 1),
(298, 52, 11, NULL, 1500.00, 17, '4', '2026-03-25', '09:37:56 AM', 2, 1),
(299, 53, 53, NULL, 170.00, 1, '5', '2026-03-25', '09:42:58 AM', 2, 1),
(301, 54, 11, NULL, 1500.00, 1, '7', '2026-03-25', '09:43:36 AM', 2, 1),
(302, 55, 11, NULL, 1500.00, 1, '8', '2026-03-25', '09:50:41 AM', 2, 1),
(303, 56, 53, NULL, 170.00, 1, '9', '2026-03-25', '09:50:56 AM', 2, 1),
(304, 57, 53, NULL, 170.00, 1, '10', '2026-03-25', '09:51:21 AM', 2, 1),
(306, 58, 54, NULL, 180.00, 1, '12', '2026-03-25', '09:55:01 AM', 2, 1),
(308, 59, 11, NULL, 1500.00, 1, '14', '2026-03-25', '10:16:08 AM', 2, 1),
(310, 60, 53, NULL, 170.00, 1, '16', '2026-03-25', '10:20:26 AM', 2, 1),
(312, 61, 14, NULL, 3500.00, 1, '18', '2026-03-25', '10:21:10 AM', 2, 1),
(316, 62, 8, NULL, 1750.00, 1, '20', '2026-03-25', '10:35:42 AM', 2, 1),
(317, 62, 35, NULL, 2450.00, 1, '20', '2026-03-25', '10:35:42 AM', 2, 1),
(318, 62, 36, NULL, 2450.00, 1, '20', '2026-03-25', '10:35:42 AM', 2, 1),
(319, 62, 1, NULL, 1650.00, 1, '21', '2026-03-25', '10:38:55 AM', 2, 3),
(323, 63, 1, NULL, 1650.00, 1, '25', '2026-03-25', '11:13:45 AM', 2, 1),
(324, 63, 2, NULL, 1500.00, 1, '25', '2026-03-25', '11:13:45 AM', 2, 1),
(325, 63, 36, NULL, 2450.00, 1, '25', '2026-03-25', '11:13:45 AM', 2, 1),
(326, 63, 1, NULL, 1650.00, 1, '26', '2026-03-25', '12:01:31 PM', 2, 3),
(328, 64, 36, NULL, 2450.00, 1, '28', '2026-03-25', '12:02:43 PM', 2, 1),
(330, 65, 36, NULL, 2450.00, 1, '30', '2026-03-25', '12:06:52 PM', 2, 1),
(332, 66, 36, NULL, 2450.00, 1, '32', '2026-03-25', '12:12:09 PM', 2, 0),
(333, 66, 2, NULL, 1500.00, 1, '33', '2026-03-25', '12:18:43 PM', 2, 3),
(335, 67, 36, NULL, 2450.00, 1, '35', '2026-03-25', '12:22:21 PM', 2, 1),
(337, 68, 36, NULL, 2450.00, 1, '37', '2026-03-25', '12:26:47 PM', 2, 0),
(338, 68, 1, NULL, 1650.00, 1, '38', '2026-03-25', '12:47:22 PM', 2, 3),
(340, 69, 2, NULL, 1500.00, 1, '40', '2026-03-25', '12:49:54 PM', 2, 0),
(343, 70, 1, NULL, 1650.00, 2, '43', '2026-03-25', '12:51:32 PM', 2, 0),
(344, 70, 2, NULL, 1500.00, 1, '43', '2026-03-25', '12:51:32 PM', 2, 0),
(347, 71, 11, NULL, 1500.00, 1, '45', '2026-03-25', '12:52:35 PM', 2, 0),
(348, 71, 12, NULL, 1700.00, 1, '45', '2026-03-25', '12:52:35 PM', 2, 0),
(349, 70, 1, NULL, 1650.00, 1, '46', '2026-03-25', '12:53:54 PM', 2, 3),
(353, 72, 1, NULL, 1650.00, 1, '48', '2026-03-25', '01:05:59 PM', 2, 0),
(354, 72, 2, NULL, 1500.00, 1, '48', '2026-03-25', '01:05:59 PM', 2, 0),
(355, 72, 5, NULL, 1850.00, 1, '48', '2026-03-25', '01:05:59 PM', 2, 0),
(360, 74, 1, NULL, 1650.00, 1, '52', '2026-03-25', '02:07:27 PM', 2, 0),
(361, 74, 4, NULL, 1700.00, 1, '52', '2026-03-25', '02:07:27 PM', 2, 0),
(366, 77, 33, NULL, 2450.00, 1, '56', '2026-03-25', '02:37:59 PM', 2, 0),
(367, 75, 33, NULL, 2450.00, 1, '57', '2026-03-25', '02:38:10 PM', 2, 0),
(368, 75, 34, NULL, 2450.00, 1, '57', '2026-03-25', '02:38:10 PM', 2, 0),
(372, 76, 1, NULL, 1650.00, 1, '61', '2026-03-25', '02:55:49 PM', 2, 0),
(373, 76, 33, NULL, 2450.00, 2, '61', '2026-03-25', '02:55:49 PM', 2, 0),
(374, 73, 33, NULL, 2450.00, 1, '62', '2026-03-25', '03:15:39 PM', 2, 0),
(375, 73, 35, NULL, 2450.00, 1, '62', '2026-03-25', '03:15:39 PM', 2, 0),
(376, 73, 36, NULL, 2450.00, 1, '62', '2026-03-25', '03:15:39 PM', 2, 0),
(379, 78, 33, NULL, 2450.00, 1, '64', '2026-03-25', '03:17:28 PM', 2, 0),
(380, 78, 34, NULL, 2450.00, 1, '64', '2026-03-25', '03:17:28 PM', 2, 0),
(382, 79, 33, NULL, 2450.00, 1, '66', '2026-03-25', '03:20:12 PM', 2, 0),
(386, 81, 34, NULL, 2450.00, 1, '69', '2026-03-25', '04:01:28 PM', 2, 3),
(387, 80, 37, NULL, 500.00, 1, '70', '2026-03-25', '04:01:57 PM', 2, 3),
(388, 80, 14, NULL, 3500.00, 1, '70', '2026-03-25', '04:01:57 PM', 2, 3);

-- --------------------------------------------------------

--
-- Table structure for table `tblparentcategory`
--

CREATE TABLE `tblparentcategory` (
  `ID` int(11) NOT NULL,
  `ParentCategoryName` varchar(120) DEFAULT NULL,
  `RegDate` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblparentcategory`
--

INSERT INTO `tblparentcategory` (`ID`, `ParentCategoryName`, `RegDate`) VALUES
(1, 'Bites', '2026-03-19 10:45:50'),
(2, 'Breeze Bay Menu', '2026-03-19 10:46:39'),
(3, 'Beverages', '2026-03-19 10:47:08'),
(4, 'Cigarette', '2026-03-20 10:28:53'),
(5, 'Desets', '2026-03-20 10:40:18'),
(6, 'Add-Ons', '2026-03-23 03:52:59');

-- --------------------------------------------------------

--
-- Table structure for table `tblproducts`
--

CREATE TABLE `tblproducts` (
  `ID` int(11) NOT NULL,
  `ParentCategoryID` int(11) DEFAULT NULL,
  `SubCategoryID` int(11) DEFAULT NULL,
  `ProductName` varchar(120) DEFAULT NULL,
  `Price` decimal(10,2) DEFAULT NULL,
  `Quantity` int(11) DEFAULT 0,
  `Type` varchar(50) DEFAULT NULL,
  `Unit` varchar(50) DEFAULT NULL,
  `Status` int(1) DEFAULT 1,
  `RegDate` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblproducts`
--

INSERT INTO `tblproducts` (`ID`, `ParentCategoryID`, `SubCategoryID`, `ProductName`, `Price`, `Quantity`, `Type`, `Unit`, `Status`, `RegDate`) VALUES
(1, 2, 21, 'Cream of chicken soup', 1650.00, 0, 'Uncountable', NULL, 1, '2026-03-19 10:58:55'),
(2, 2, 21, 'Mushroom soup', 1500.00, 0, 'Uncountable', NULL, 1, '2026-03-19 10:59:16'),
(3, 2, 21, 'Vegetable soup', 1350.00, 0, 'Uncountable', NULL, 1, '2026-03-19 10:59:37'),
(4, 2, 22, 'Seafood salad', 1700.00, 0, 'Uncountable', NULL, 1, '2026-03-19 10:59:58'),
(5, 2, 22, 'Greek salad', 1850.00, 0, 'Uncountable', NULL, 1, '2026-03-19 11:00:13'),
(6, 2, 22, 'Green salad', 1600.00, 0, 'Uncountable', NULL, 1, '2026-03-19 11:00:32'),
(7, 2, 23, 'Carbonara (Pork/Chicken)', 1700.00, 0, 'Uncountable', NULL, 1, '2026-03-19 11:00:56'),
(8, 2, 23, 'Alfredo (Chicken)', 1750.00, 0, 'Uncountable', NULL, 1, '2026-03-19 11:01:13'),
(9, 2, 23, 'Bolognese (Beef)', 1800.00, 0, 'Uncountable', NULL, 1, '2026-03-19 11:01:31'),
(10, 2, 23, 'Breeze bay special creamy pasta (Seafood/Chicken)', 1800.00, 0, 'Uncountable', NULL, 1, '2026-03-19 11:02:18'),
(11, 2, 24, 'Chicken kottu', 1500.00, 0, 'Uncountable', NULL, 1, '2026-03-19 11:02:39'),
(12, 2, 24, 'Seafood kottu', 1700.00, 0, 'Uncountable', NULL, 1, '2026-03-19 11:03:06'),
(13, 2, 24, 'Mix kottu', 1850.00, 0, 'Uncountable', NULL, 1, '2026-03-19 11:03:25'),
(14, 2, 25, 'Special Seafood Sizzling', 3500.00, 0, 'Uncountable', NULL, 1, '2026-03-19 11:03:51'),
(15, 2, 25, 'Special Mix Sizzling', 3950.00, 0, 'Uncountable', NULL, 1, '2026-03-19 11:04:13'),
(16, 2, 25, 'Special Grill Fish', 2400.00, 0, 'Uncountable', NULL, 1, '2026-03-19 11:04:36'),
(17, 2, 26, 'Nasigorang', 1650.00, 0, 'Uncountable', NULL, 1, '2026-03-19 11:04:58'),
(18, 2, 26, 'Thai rice', 1600.00, 0, 'Uncountable', NULL, 1, '2026-03-19 11:05:20'),
(19, 2, 26, 'Mongolian rice', 1800.00, 0, 'Uncountable', NULL, 1, '2026-03-19 11:05:41'),
(20, 2, 27, 'Thai noodles', 1600.00, 0, 'Uncountable', NULL, 1, '2026-03-19 11:05:57'),
(21, 2, 27, 'Mongolian noodles', 1750.00, 0, 'Uncountable', NULL, 1, '2026-03-19 11:06:16'),
(22, 2, 27, 'Chicken singapore noodles', 1650.00, 0, 'Uncountable', NULL, 1, '2026-03-19 11:06:41'),
(23, 2, 28, 'Virgin mojito (Blue & Raspberry)', 1200.00, 0, 'Uncountable', NULL, 1, '2026-03-19 11:07:02'),
(24, 2, 28, 'Lemon mint juice', 1100.00, 0, 'Uncountable', NULL, 1, '2026-03-19 11:07:18'),
(25, 2, 28, 'Pine apple Juice', 800.00, 0, 'Uncountable', NULL, 1, '2026-03-19 11:07:38'),
(26, 2, 28, 'Orange juice', 800.00, 0, 'Uncountable', NULL, 1, '2026-03-19 11:07:54'),
(27, 2, 28, 'Watermelon juice', 750.00, 0, 'Uncountable', NULL, 1, '2026-03-19 11:08:19'),
(28, 2, 28, 'Papaya juice', 800.00, 0, 'Uncountable', NULL, 1, '2026-03-19 11:08:36'),
(29, 2, 28, 'Mango juice', 800.00, 0, 'Uncountable', NULL, 1, '2026-03-19 11:08:53'),
(30, 2, 28, 'Ice coffee', 700.00, 0, 'Uncountable', NULL, 1, '2026-03-19 11:09:36'),
(31, 2, 28, 'Chocolate milkshake', 700.00, 0, 'Uncountable', NULL, 1, '2026-03-19 11:09:54'),
(32, 2, 28, 'One liter Juice Jar', 1500.00, 0, 'Uncountable', NULL, 1, '2026-03-19 11:10:20'),
(33, 1, 3, 'TURKEY DEVELLED', 2450.00, 0, 'Uncountable', NULL, 1, '2026-03-19 11:15:54'),
(34, 1, 3, 'TURKEY BOILED', 2450.00, 0, 'Uncountable', NULL, 1, '2026-03-19 11:20:47'),
(35, 1, 3, 'TURKEY STEW', 2450.00, 0, 'Uncountable', NULL, 1, '2026-03-19 11:21:21'),
(36, 1, 3, 'TURKEY CURRY', 2450.00, 0, 'Uncountable', NULL, 1, '2026-03-19 11:21:58'),
(37, 1, 4, 'MULLET', 500.00, 10, 'Countable', 'Grams', 1, '2026-03-19 11:22:35'),
(38, 1, 4, 'FISH FRIED', 2050.00, 0, 'Uncountable', NULL, 1, '2026-03-19 11:23:19'),
(39, 3, 29, 'Sprite Mwga 1.5L ', 750.00, 10, 'Countable', 'Bottles', 1, '2026-03-20 10:18:55'),
(40, 3, 29, 'Sprite 1L', 600.00, 10, 'Countable', 'Bottles', 1, '2026-03-20 10:19:30'),
(41, 3, 29, 'Sprite 300ml', 250.02, 0, 'Uncountable', NULL, 1, '2026-03-20 10:20:00'),
(42, 3, 29, 'Sprite 175ml', 150.00, 0, 'Uncountable', NULL, 1, '2026-03-20 10:20:29'),
(43, 3, 30, 'Coke Mega 1.5L', 750.00, 10, 'Countable', 'Bottles', 1, '2026-03-20 10:21:10'),
(44, 3, 30, 'Coke 1L', 600.00, 1, 'Countable', 'Bottles', 1, '2026-03-20 10:21:38'),
(45, 3, 30, 'Coke 300ml', 250.00, 10, 'Countable', 'Bottles', 1, '2026-03-20 10:22:03'),
(46, 3, 30, 'Coke 175ml', 150.00, 0, 'Uncountable', NULL, 1, '2026-03-20 10:22:32'),
(47, 3, 31, 'EGB 500ml', 250.00, 10, 'Countable', 'Bottles', 1, '2026-03-20 10:24:12'),
(48, 3, 32, 'Spinner', 350.00, 0, 'Uncountable', NULL, 1, '2026-03-20 10:25:18'),
(49, 3, 32, 'Tonic', 300.00, 0, 'Uncountable', NULL, 1, '2026-03-20 10:25:38'),
(50, 3, 32, 'Water', 100.00, 10, 'Countable', 'Bottles', 1, '2026-03-20 10:27:13'),
(51, 3, 32, 'Ice cubes', 450.00, 10, 'Countable', 'Packets', 1, '2026-03-20 10:27:47'),
(52, 3, 33, 'Soda', 200.00, 0, 'Uncountable', NULL, 1, '2026-03-20 10:28:22'),
(53, 4, 35, 'Dunhil', 170.00, 10, 'Countable', 'Bottles', 1, '2026-03-20 10:32:53'),
(54, 4, 34, 'Gold Leaf', 180.00, 10, 'Countable', 'Bottles', 1, '2026-03-20 10:33:17'),
(55, 5, 36, 'Watalappan', 350.00, 0, 'Uncountable', NULL, 1, '2026-03-20 10:41:04'),
(56, 5, 36, 'Caramel Pudding', 350.00, 0, 'Uncountable', NULL, 1, '2026-03-20 10:41:33'),
(57, 5, 36, 'Ice Cream', 300.00, 0, 'Uncountable', NULL, 1, '2026-03-20 10:42:01'),
(58, 5, 36, 'Caremel JellyPudding', 300.00, 0, 'Uncountable', NULL, 1, '2026-03-20 10:42:41'),
(59, 3, 37, 'Mix Fruit', 200.00, 0, 'Uncountable', NULL, 1, '2026-03-20 10:44:35'),
(60, 3, 37, 'Orange Juice Welcome', 200.00, 0, 'Uncountable', NULL, 1, '2026-03-20 10:45:47'),
(61, 6, 38, 'Chicken', 100.00, 0, 'Uncountable', NULL, 1, '2026-03-23 03:53:35'),
(62, 6, 38, 'Beef', 100.00, 0, 'Uncountable', NULL, 1, '2026-03-23 03:53:52'),
(63, 6, 38, 'Pork', 100.00, 0, 'Uncountable', NULL, 1, '2026-03-23 03:54:07'),
(64, 6, 38, 'Fish', 100.00, 0, 'Uncountable', NULL, 1, '2026-03-23 03:54:21'),
(65, 6, 38, 'Prawns', 100.00, 0, 'Uncountable', NULL, 1, '2026-03-23 03:54:38'),
(66, 6, 38, 'Cuttlefish', 100.00, 0, 'Uncountable', NULL, 1, '2026-03-23 03:54:55'),
(67, 6, 38, 'Sausage', 100.00, 0, 'Uncountable', NULL, 1, '2026-03-23 03:55:12'),
(68, 6, 39, 'Chicken', 300.00, 0, 'Uncountable', NULL, 1, '2026-03-23 03:55:57');

-- --------------------------------------------------------

--
-- Table structure for table `tblreservation`
--

CREATE TABLE `tblreservation` (
  `ID` int(11) NOT NULL,
  `TableID` int(11) NOT NULL,
  `ReservationDate` date NOT NULL,
  `Pax` int(11) NOT NULL,
  `CustomerName` varchar(200) NOT NULL,
  `CustomerContact` varchar(20) NOT NULL,
  `RegDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `Status` varchar(20) NOT NULL DEFAULT '0' COMMENT '0 - pending, 1- confirm',
  `pay_amount` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblreservation`
--

INSERT INTO `tblreservation` (`ID`, `TableID`, `ReservationDate`, `Pax`, `CustomerName`, `CustomerContact`, `RegDate`, `Status`, `pay_amount`) VALUES
(1, 2, '2026-03-25', 4, 'Testing booking', '0998785673', '2026-03-20 07:13:01', 'Pending', 0.00),
(2, 2, '2026-03-20', 4, 'testing 2', '0994534234', '2026-03-20 07:14:58', 'Confirmed', 4000.00),
(3, 1, '2026-03-20', 8, 'testing book', '0332434231', '2026-03-20 08:58:09', 'Confirmed', 8000.00);

-- --------------------------------------------------------

--
-- Table structure for table `tblrole`
--

CREATE TABLE `tblrole` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblrole`
--

INSERT INTO `tblrole` (`role_id`, `role_name`) VALUES
(1, 'Admin'),
(2, 'Cashier'),
(3, 'Waitor');

-- --------------------------------------------------------

--
-- Table structure for table `tblstaff`
--

CREATE TABLE `tblstaff` (
  `ID` int(11) NOT NULL,
  `StaffName` varchar(255) NOT NULL,
  `StaffNIC` varchar(20) NOT NULL,
  `StaffTel` varchar(15) NOT NULL,
  `StaffRole` varchar(50) NOT NULL,
  `UserName` varchar(100) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `RegDate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblstaff`
--

INSERT INTO `tblstaff` (`ID`, `StaffName`, `StaffNIC`, `StaffTel`, `StaffRole`, `UserName`, `Password`, `RegDate`) VALUES
(1, 'Roshan Pere', '200104701031', '0778378673', 'Admin', 'Roshane2001', '$2y$10$qajwxXMkfpm/5AJFSvGmW.0imlaBBml.lkzzFvaiABvni.DjMQVZm', '2026-03-17 03:17:56'),
(3, 'waitor1', '200204703031', '02231748670', 'Waitor', 'waitor1', '$2y$10$aDA6Ah.RZyqvYnQ5lE.RAuB9c3XvRR6DdHkXLFY0Dboq43IzCTE4a', '2026-03-20 04:13:31'),
(4, 'cashier', '200204703031', '02231748673', 'Cashier', 'cashier', '$2y$10$PHwuKOt6d5gVBadenVga.OCnhXJqLQFuidIRMikapOy4AOxpc.Jj2', '2026-03-25 10:36:08');

-- --------------------------------------------------------

--
-- Table structure for table `tbltables`
--

CREATE TABLE `tbltables` (
  `ID` int(11) NOT NULL,
  `TableName` varchar(50) DEFAULT NULL,
  `ChairCount` int(11) DEFAULT 0,
  `Status` varchar(20) DEFAULT '0' COMMENT '0 - Availbale, 1 - Reserved, 2 - Seated  ',
  `RegDate` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbltables`
--

INSERT INTO `tbltables` (`ID`, `TableName`, `ChairCount`, `Status`, `RegDate`) VALUES
(1, 'Table 1', 8, '0', '2026-03-18 12:13:58'),
(2, 'Table 2', 4, '2', '2026-03-19 05:28:07'),
(3, 'Table 3', 6, '1', '2026-03-19 05:28:18'),
(4, 'Table 5', 6, '0', '2026-03-19 05:28:31'),
(5, 'Table 4', 10, '0', '2026-03-19 10:18:38'),
(6, 'Bean Bag Cabana 1', 2, '0', '2026-03-24 03:32:32'),
(7, 'Bean Bag Cabana 2', 2, '0', '2026-03-24 03:33:46'),
(8, 'Bean Bag Cabana 3', 2, '0', '2026-03-24 03:33:59'),
(9, 'Full Cabana ', 35, '0', '2026-03-24 03:38:10'),
(10, 'sub cabana', 20, '0', '2026-03-24 03:39:34');

-- --------------------------------------------------------

--
-- Table structure for table `tbltax`
--

CREATE TABLE `tbltax` (
  `ID` int(11) NOT NULL,
  `TaxName` varchar(200) DEFAULT NULL,
  `TaxPercentage` decimal(5,2) DEFAULT NULL,
  `CreationDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `Status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=Active, 0=Inactive'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbltax`
--

INSERT INTO `tbltax` (`ID`, `TaxName`, `TaxPercentage`, `CreationDate`, `Status`) VALUES
(1, 'VAT ', 10.00, '2026-03-16 11:23:07', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tblbranding`
--
ALTER TABLE `tblbranding`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblcategory`
--
ALTER TABLE `tblcategory`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblorder`
--
ALTER TABLE `tblorder`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblorder_details`
--
ALTER TABLE `tblorder_details`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblparentcategory`
--
ALTER TABLE `tblparentcategory`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblproducts`
--
ALTER TABLE `tblproducts`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblreservation`
--
ALTER TABLE `tblreservation`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblrole`
--
ALTER TABLE `tblrole`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `tblstaff`
--
ALTER TABLE `tblstaff`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `UserName` (`UserName`);

--
-- Indexes for table `tbltables`
--
ALTER TABLE `tbltables`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tbltax`
--
ALTER TABLE `tbltax`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tblcategory`
--
ALTER TABLE `tblcategory`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `tblorder`
--
ALTER TABLE `tblorder`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT for table `tblorder_details`
--
ALTER TABLE `tblorder_details`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=389;

--
-- AUTO_INCREMENT for table `tblparentcategory`
--
ALTER TABLE `tblparentcategory`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tblproducts`
--
ALTER TABLE `tblproducts`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `tblreservation`
--
ALTER TABLE `tblreservation`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tblrole`
--
ALTER TABLE `tblrole`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tblstaff`
--
ALTER TABLE `tblstaff`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbltables`
--
ALTER TABLE `tbltables`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tbltax`
--
ALTER TABLE `tbltax`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
