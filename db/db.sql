-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: db:3306
-- Generation Time: May 17, 2026 at 10:19 AM
-- Server version: 8.0.44
-- PHP Version: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `webproject`
--

-- --------------------------------------------------------

--
-- Table structure for table `Address`
--

CREATE TABLE `Address` (
  `ID` int NOT NULL,
  `address` varchar(20) NOT NULL,
  `user_id` varchar(10) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Address`
--

INSERT INTO `Address` (`ID`, `address`, `user_id`, `created_at`) VALUES
(1, 'via mima 12', '8', '2026-04-29 10:28:53'),
(2, 'via mima 12', '8', '2026-04-29 10:29:50'),
(3, 'via mima 12', '8', '2026-04-29 10:30:35'),
(4, 'via mima 12', '8', '2026-04-29 10:30:51'),
(5, 'via mima 12', '8', '2026-04-29 10:31:20'),
(6, 'via mima 12', '8', '2026-04-29 10:32:30'),
(7, 'via mima 12', '8', '2026-05-03 16:30:05'),
(8, 'via mima 12', '8', '2026-05-05 09:21:06'),
(9, 'via mima 12', '8', '2026-05-06 08:49:38'),
(10, 'via mima 12', '8', '2026-05-07 12:39:24'),
(11, 'via mima 12', '8', '2026-05-07 12:44:01'),
(12, 'via mima 12', '8', '2026-05-07 12:49:19'),
(13, 'via mima 12', '8', '2026-05-07 13:09:56'),
(14, 'via mima 12', '8', '2026-05-07 13:11:13'),
(15, 'via mima 12', '8', '2026-05-07 13:11:33'),
(16, 'via mima 12', '8', '2026-05-07 13:12:08'),
(17, 'via mima 12', '8', '2026-05-07 13:18:37'),
(18, 'via mima 12', '8', '2026-05-07 13:19:14'),
(19, 'via mima 12', '8', '2026-05-07 13:20:13'),
(20, 'via mima 12', '8', '2026-05-07 13:22:18'),
(21, 'via mima 12', '8', '2026-05-07 13:31:06'),
(22, 'via mima 12', '8', '2026-05-07 13:33:13'),
(23, 'via mima 12', '8', '2026-05-07 13:33:48'),
(24, 'via mima 12', '8', '2026-05-07 13:35:07'),
(25, 'via mima 12', '8', '2026-05-07 13:37:52'),
(26, 'via mima 12', '8', '2026-05-07 13:38:26'),
(27, 'via mima 12', '8', '2026-05-07 13:41:56'),
(28, 'via mima 12', '8', '2026-05-07 13:43:10'),
(29, 'via mima 12', '8', '2026-05-08 21:47:51'),
(30, 'via mima 12', '8', '2026-05-11 13:40:52'),
(31, 'via mima 12', '8', '2026-05-16 15:12:32');

-- --------------------------------------------------------

--
-- Table structure for table `Categories`
--

CREATE TABLE `Categories` (
  `ID` int NOT NULL,
  `CategoryName` varchar(30) NOT NULL,
  `Description` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Categories`
--

INSERT INTO `Categories` (`ID`, `CategoryName`, `Description`) VALUES
(1, 'BEVERAGES', 'DRINKS AND REFRESHMENT'),
(2, 'FOOD', 'ANTIPASTI,PRIMI,SECONDI'),
(3, 'DESERTS', 'REFRESHEMENTS,SWEETS'),
(4, 'SIDES', 'side dishes'),
(5, 'SUSHI', 'suhiiiiiiiiii'),
(6, 'BURGERS', 'burgers');

-- --------------------------------------------------------

--
-- Table structure for table `Customers`
--

CREATE TABLE `Customers` (
  `ID` int NOT NULL,
  `Fname` varchar(20) DEFAULT NULL,
  `Lname` varchar(20) DEFAULT NULL,
  `Adress` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Customers`
--

INSERT INTO `Customers` (`ID`, `Fname`, `Lname`, `Adress`) VALUES
(8, 'christian', 'kabarisa manzi', 'via mima 12'),
(12, 'mapenzi', 'digne', 'via mima 12');

-- --------------------------------------------------------

--
-- Table structure for table `Delivery`
--

CREATE TABLE `Delivery` (
  `ID` int NOT NULL,
  `Name` varchar(30) DEFAULT NULL,
  `Surname` varchar(30) DEFAULT NULL,
  `Address` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Delivery`
--

INSERT INTO `Delivery` (`ID`, `Name`, `Surname`, `Address`) VALUES
(2, 'manzi', 'christian', 'mubigori iyo'),
(3, 'digne', 'mapenzi', 'mu makara'),
(13, 'manzi', 'christian', 'via roma');

-- --------------------------------------------------------

--
-- Table structure for table `Delivery_users`
--

CREATE TABLE `Delivery_users` (
  `ID` int NOT NULL,
  `pass` varchar(150) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `Tel` varchar(13) DEFAULT NULL,
  `deliveryID` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Orderdetails`
--

CREATE TABLE `Orderdetails` (
  
  `ID` int NOT NULL,
  `orderID` int DEFAULT NULL,
  `productID` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `quantity` int DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Orderdetails`
--

INSERT INTO `Orderdetails` (`ID`, `orderID`, `productID`, `quantity`) VALUES
(76, NULL, 'p_1', 1),
(138, 98, 'p_10', 6),
(139, 98, 'p_11', 1),
(140, 98, 'p_12', 1),
(141, 99, 'p_10', 3),
(142, 100, 'p_12', 2),
(143, 101, 'p_12', 2),
(144, 102, 'p_11', 2),
(145, 103, 'p_11', 2),
(146, 104, 'p_10', 2),
(147, 105, 'p_10', 2),
(148, 106, 'p_12', 2),
(149, 107, 'p_11', 2),
(150, 108, 'p_10', 2),
(151, 109, 'p_10', 2),
(152, 110, 'p_11', 2),
(153, 111, 'p_20', 1),
(154, 112, 'p_22', 1),
(155, 111, 'p_10', 1),
(156, 113, 'p_69f8c6', 1),
(157, 114, 'p_21', 8),
(158, 115, 'p_69f8c6', 1),
(159, 114, 'p_22', 8),
(161, 115, 'p_69fe60', 2),
(165, 118, 'p_19', 1),
(166, 119, 'p_19', 1),
(167, 120, 'p_19', 1),
(169, 115, 'p_69f205', 1),
(174, 124, 'p_14', 1),
(175, 124, 'p_16', 1);

-- --------------------------------------------------------

--
-- Table structure for table `Orders`
--

CREATE TABLE `Orders` (
  `ID` int NOT NULL,
  `customerID` int DEFAULT NULL,
  `deliveryID` int DEFAULT NULL,
  `order_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `PartnersID` varchar(10) DEFAULT NULL,
  `stutus` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'PENDING',
  `paymentID` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Orders`
--

INSERT INTO `Orders` (`ID`, `customerID`, `deliveryID`, `order_date`, `PartnersID`, `stutus`, `paymentID`) VALUES
(98, 8, 13, '2026-04-28 10:01:00', 'p_69b', 'DELI', 5),
(99, 8, 13, '2026-04-29 08:53:27', 'p_69b', 'DONE', 3),
(100, 8, 13, '2026-04-29 08:56:02', 'p_69b', 'DONE', 3),
(101, 8, 13, '2026-04-29 09:03:33', 'p_69b', 'DONE', 3),
(102, 8, 13, '2026-04-29 09:04:46', 'p_69b', 'DONE', 3),
(103, 8, 13, '2026-04-29 09:09:56', 'p_69b', 'DONE', 5),
(104, 8, NULL, '2026-04-29 09:11:00', 'p_69b', 'DONE', 5),
(105, 8, NULL, '2026-04-29 09:14:30', 'p_69b', 'READY', 5),
(106, 8, 13, '2026-04-29 09:16:47', 'p_69b', 'DONE', 5),
(107, 8, 13, '2026-04-29 09:21:11', 'p_69b', 'DONE', 3),
(108, 8, 13, '2026-04-29 09:23:09', 'p_69b', 'DONE', 5),
(109, 8, 13, '2026-04-29 09:24:09', 'p_69b', 'DONE', 5),
(110, 8, 13, '2026-04-29 09:49:53', 'p_69b', 'DONE', 5),
(111, 8, NULL, '2026-05-02 11:20:06', 'p_69b', 'ACTIVE', 5),
(112, 8, NULL, '2026-05-03 16:18:44', 'p_69be48', 'ACTIVE', 5),
(113, 8, NULL, '2026-05-04 17:05:44', 'p_69f8b4d4', 'ACTIVE', 5),
(114, 8, NULL, '2026-05-06 11:54:48', 'p_69be48', 'ACTIVE', 5),
(115, 8, 13, '2026-05-06 11:55:52', 'p_69f8b4d4', 'DONE', 5),
(118, 8, NULL, '2026-05-10 11:57:54', NULL, 'PENDING', NULL),
(119, 8, NULL, '2026-05-10 12:02:33', NULL, 'PENDING', NULL),
(120, 8, NULL, '2026-05-10 12:03:22', NULL, 'PENDING', NULL),
(124, 8, NULL, '2026-05-16 15:12:15', 'p_69b', 'ACTIVE', 5);

-- --------------------------------------------------------

--
-- Table structure for table `Partners`
--

CREATE TABLE `Partners` (
  `ID` varchar(10) NOT NULL,
  `Name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `Bname` varchar(20) NOT NULL DEFAULT 'D.som',
  `Address` varchar(30) DEFAULT NULL,
  `Image` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Partners`
--

INSERT INTO `Partners` (`ID`, `Name`, `Bname`, `Address`, `Image`) VALUES
('p_69b', 'christian kabarisa manzi', 'manzi', 'via princip 29', 'p_69b.jpg'),
('p_69be48', 'thabo helza', 'Thabo', 'fal away', 'p_69be48.jpg'),
('p_69f8b4d4', 'mapenzi digne', 'Kwamapenzi', 'via principe umberto 29', 'p_69f8b4d4.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `Partners_users`
--

CREATE TABLE `Partners_users` (
  `ID` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `pass` varchar(150) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `Tel` varchar(13) DEFAULT NULL,
  `PartnersID` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Partners_users`
--

INSERT INTO `Partners_users` (`ID`, `pass`, `email`, `Tel`, `PartnersID`) VALUES
(NULL, '$2y$12$sa6iGlFRerPltGjm/R5bXO/IhQ0Fj6WX/6Yb089hCeeCprDqhnncG', 'manzikchris@gmail.com', '3280669167', 'p_69b'),
(NULL, '$2y$12$aGGeagLka/VehtN7RlPeR.wDgjIr6GA8E4jdVhkxNhBh74dlnD7sO', 'thabo@gmail.com', '7860182389', 'p_69be48'),
(NULL, '$2y$12$j8.8MQytTp5pM0BJV6Rxn.oBtPh8KMRAQr8Lv3DtJ.E4JfXGbmTJ2', 'mapenzidigne@gmail.com', '3280669167', 'p_69f8b4d4');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `ID` int NOT NULL,
  `orderID` int DEFAULT NULL,
  `c_number` varchar(20) NOT NULL,
  `cvs` varchar(3) DEFAULT NULL,
  `customerID` int DEFAULT NULL,
  `brand` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`ID`, `orderID`, `c_number`, `cvs`, `customerID`, `brand`) VALUES
(3, NULL, '1234-5678-9098-7654', '123', 8, ''),
(5, NULL, '4147-4319-9349-4394', '567', 8, 'visa');

-- --------------------------------------------------------

--
-- Table structure for table `Products`
--

CREATE TABLE `Products` (
  `ID` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `Name` varchar(20) DEFAULT NULL,
  `Quantity` varchar(10) DEFAULT 'ACTIVE',
  `Price` int DEFAULT NULL,
  `CategoryID` int DEFAULT NULL,
  `PartnersID` varchar(10) DEFAULT NULL,
  `image` varchar(20) NOT NULL DEFAULT 'mainp.jpg',
  `stutus` int NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Products`
--

INSERT INTO `Products` (`ID`, `Name`, `Quantity`, `Price`, `CategoryID`, `PartnersID`, `image`, `stutus`) VALUES
('p_1', 'Cappucino', NULL, 2, 1, 'p_69b', 'P1.jpg', 1),
('p_10', 'Water', '-30', 1, 1, 'p_69b', 'P10.jpg', 1),
('p_11', 'Beef Burger', '30', 5, 6, 'p_69b', 'P11.jpg', 1),
('p_12', 'Pizza', '30', 8, 2, 'p_69b', 'P12.jpg', 1),
('p_13', 'Fanta', '25', 2, 1, 'p_69b', 'P13.jpg', 1),
('p_14', 'french fries', '55', 5, 4, 'p_69b', 'P14.jpg', 1),
('p_15', 'vanilla icecreame', '40', 2, 3, 'p_69b', 'P15.jpg', 1),
('p_16', 'salad', '35', 5, 4, 'p_69b', 'P16.jpg', 1),
('p_17', 'choco smooth', '30', 4, 1, 'p_69b', 'P17.jpg', 1),
('p_18', 'Ice Cream Sundae', '60', 4, 3, 'p_69b', 'P18.jpg', 1),
('p_19', 'Tiramisu', '25', 6, 3, 'p_69b', 'P19.jpg', 1),
('p_2', 'Sparkling Water', '200', 1, 1, 'p_69b', 'P2.jpg', 1),
('p_20', 'Brownie', '44', 3, 3, 'p_69b', 'P20.jpg', 1),
('p_21', 'Classic Cola', NULL, 3, 1, 'p_69be48', 'p_21.jpg', 1),
('p_22', 'Sparkling Water', '195', 1, 1, 'p_69be48', 'p_22.jpg', 1),
('p_23', 'Fresh Orange Juice', '78', 3, 1, 'p_69be48', 'p_32.jpg', 1),
('p_24', 'Iced Coffee', '60', 4, 1, 'p_69be48', 'p_24.jpg', 1),
('p_25', 'Green Tea', '120', 2, 1, 'p_69be48', 'p_25.jpg', 1),
('p_26', 'Lemonade', '90', 3, 1, 'p_69be48', 'p_26.jpg', 1),
('p_27', 'Energy Drink', '100', 3, 1, 'p_69be48', 'p_27.jpg', 1),
('p_28', 'Chicken Sandwich', '50', 6, 2, 'p_69be48', 'p_28.jpg', 1),
('p_29', 'Caesar Salad', '40', 6, 2, 'p_69be48', 'p_29.jpg', 1),
('p_3', 'Fresh Orange Juice', '80', 3, 1, 'p_69b', 'P3.jpg', 1),
('p_30', 'Pizza', '29', 9, 2, 'p_69be48', 'p_30.jpg', 1),
('p_31', 'sushi sunset', '45', 7, 5, 'p_69be48', 'p_31.jpg', 1),
('p_32', 'Pasta Carbonara', '35', 8, 2, 'p_69be48', 'p_23.jpg', 1),
('p_33', 'Fish and Chips', '25', 8, 2, 'p_69be48', 'p_33.jpg', 1),
('p_34', 'Vegetable Wrap', '55', 5, 2, 'p_69be48', 'p_34.jpg', 1),
('p_35', 'sushi salmon', '40', 5, 3, 'p_69be48', 'p_35.jpg', 1),
('p_36', 'Cheesecake', '35', 5, 3, 'p_69be48', 'p_36.jpg', 1),
('p_37', 'sushi plus', '30', 4, 3, 'p_69be48', 'p_37.jpg', 1),
('p_38', 'Ice Cream Sundae', '60', 4, 3, 'p_69be48', 'p_38.jpg', 1),
('p_39', 'Tiramisu', '25', 6, 3, 'p_69be48', 'p_39.jpg', 1),
('p_4', 'Iced Coffee', '60', 4, 1, 'p_69b', 'P4.jpg', 1),
('p_40', 'Brownie', '45', 3, 3, 'p_69be48', 'p_40.jpg', 1),
('p_5', 'Green Tea', '120', 2, 1, 'p_69b', 'P5.jpg', 1),
('p_6', 'Lemonade', '90', 3, 1, 'p_69b', 'p6.jpg', 1),
('p_69f205', 'manzi', 'ACTIVE', 8, 1, 'p_69f8b4d4', 'p_69f205.jpg', 1),
('p_69f206', 'ibigori', 'ACTIVE', 5, 2, 'p_69b', 'p_69f206.jpg', 1),
('p_69f27d', 'ibifenes', 'ACTIVE', 3, 3, 'p_69b', 'p_69f27d.webp', 1),
('p_69f27e', 'inkoko', 'ACTIVE', 4, 2, 'p_69b', 'p_69f27e.jpg', 1),
('p_69f87a', 'N-burger', 'ACTIVE', 2, 6, 'p_69b', 'p_69f87a.jpg', 1),
('p_69f87b', 'pizza', 'ACTIVE', 3, 4, 'p_69b', 'p_69f87b.WEBP', 1),
('p_69f8c6', 'tiramisu', 'ACTIVE', 2, 3, 'p_69f8b4d4', 'p_69f8c6.jpg', 1),
('p_69fe60', 'oreo milkshake', 'ACTIVE', 2, 1, 'p_69f8b4d4', 'p_69fe60.jpg', 1),
('p_69fe629ca8', 'family special', 'ACTIVE', 3, 3, 'p_69f8b4d4', 'p_69fe629ca8.jpg', 1),
('p_69fe62c489', 'strawberry mlks', 'ACTIVE', 1, 1, 'p_69f8b4d4', 'p_69fe62c489.jpg', 1),
('p_69fe65e57e', 'special ick', 'ACTIVE', 1, 3, 'p_69f8b4d4', 'p_69fe65e57e.jpg', 1),
('p_69fe66130a', 'makaron', 'ACTIVE', 1, 3, 'p_69f8b4d4', 'p_69fe66130a.jpg', 1),
('p_69fe66246f', 'beagers', 'ACTIVE', 1, 3, 'p_69f8b4d4', 'p_69fe66246f.jpg', 1),
('p_69fee7f564', 'chocolate sunday', 'ACTIVE', 2, 1, 'p_69f8b4d4', 'p_69fee7f564.jpg', 1),
('p_7', 'isombe ugali', '99', 3, 2, 'p_69b', 'P7.jpg', 1),
('p_8', 'Chicken ', '50', 6, 2, 'p_69b', 'P8.jpg', 1),
('p_9', 'Caesar Salad', '40', 6, 2, 'p_69b', 'P9.jpg', 0);

-- --------------------------------------------------------

--
-- Table structure for table `Sides`
--

CREATE TABLE `Sides` (
  `ID` int NOT NULL,
  `Name` varchar(20) DEFAULT NULL,
  `Description` varchar(100) DEFAULT NULL,
  `productID` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Users`
--

CREATE TABLE `Users` (
  `ID` int NOT NULL,
  `user_id` varchar(12) NOT NULL,
  `Email` varchar(150) NOT NULL,
  `Tel` bigint DEFAULT NULL,
  `pass` varchar(180) DEFAULT NULL,
  `otp` int DEFAULT NULL,
  `ot_time` int DEFAULT NULL,
  `status` varchar(7) NOT NULL DEFAULT 'PENDING',
  `attribute` varchar(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Users`
--

INSERT INTO `Users` (`ID`, `user_id`, `Email`, `Tel`, `pass`, `otp`, `ot_time`, `status`, `attribute`) VALUES
(5, '8', 'manzikchris@gmail.com', 3280669167, '$2y$10$jcTM/v7RilT3321cf/yhP.1rwnPUQ.w4YYeFw2N6..eJWAwD.0QKS', 18474, 1778949287, 'PENDING', 'c'),
(9, '12', 'mapenzi@gmail.com', 3510123345, '$2y$12$TlxFSyxpeMeKJKMcjOe3EuCdEhcKfAclLzHboQ3FG6QvXV8Eoo2r2', NULL, NULL, 'PENDING', 'c'),
(15, '13', 'manzikchris@gmail.com', 3280669166, '$2y$10$rcoDq8Kda5yfLdi30zGB.uM4k6pMjg7fDkQBgCTX4O4iGpSZwDsky', 784545, 1779010968, 'PENDING', 'd');

-- --------------------------------------------------------

--
-- Table structure for table `User_Tokens`
--

CREATE TABLE `User_Tokens` (
  `token_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `User_id` varchar(20) NOT NULL,
  `time` int DEFAULT NULL,
  `session_id` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `User_Tokens`
--

INSERT INTO `User_Tokens` (`token_id`, `User_id`, `time`, `session_id`) VALUES
('12', 'p_69b', NULL, 'chris-store-12'),
('T_23u3n222n', '8', 8080808, 'chris-app-23'),
('T-69ef86', '12', 1777305149, 's-p69b'),
('T-69f8b4', 'p_69f8b4d4', 1777906900, 's-69f8b4'),
('T-6a0580', '13', 1778745545, 's-6a0580');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Address`
--
ALTER TABLE `Address`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `Categories`
--
ALTER TABLE `Categories`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `Customers`
--
ALTER TABLE `Customers`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `Delivery`
--
ALTER TABLE `Delivery`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `Delivery_users`
--
ALTER TABLE `Delivery_users`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `deliveryID` (`deliveryID`);

--
-- Indexes for table `Orderdetails`
--
ALTER TABLE `Orderdetails`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `orderID` (`orderID`),
  ADD KEY `productID` (`productID`);

--
-- Indexes for table `Orders`
--
ALTER TABLE `Orders`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `customerID` (`customerID`),
  ADD KEY `PartnersID` (`PartnersID`),
  ADD KEY `deliveryID` (`deliveryID`),
  ADD KEY `fk_orders_payment` (`paymentID`);

--
-- Indexes for table `Partners`
--
ALTER TABLE `Partners`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `Partners_users`
--
ALTER TABLE `Partners_users`
  ADD KEY `PartnersID` (`PartnersID`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `c_number` (`c_number`),
  ADD UNIQUE KEY `c_number_2` (`c_number`),
  ADD UNIQUE KEY `cvs` (`cvs`),
  ADD KEY `orderID` (`orderID`),
  ADD KEY `customerID` (`customerID`);

--
-- Indexes for table `Products`
--
ALTER TABLE `Products`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `CategoryID` (`CategoryID`),
  ADD KEY `PartnersID` (`PartnersID`);

--
-- Indexes for table `Sides`
--
ALTER TABLE `Sides`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `productID` (`productID`);

--
-- Indexes for table `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `Tel` (`Tel`),
  ADD UNIQUE KEY `otp` (`otp`);

--
-- Indexes for table `User_Tokens`
--
ALTER TABLE `User_Tokens`
  ADD PRIMARY KEY (`token_id`),
  ADD UNIQUE KEY `session_id` (`session_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Address`
--
ALTER TABLE `Address`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `Categories`
--
ALTER TABLE `Categories`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `Customers`
--
ALTER TABLE `Customers`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `Delivery`
--
ALTER TABLE `Delivery`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `Delivery_users`
--
ALTER TABLE `Delivery_users`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Orderdetails`
--
ALTER TABLE `Orderdetails`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=176;

--
-- AUTO_INCREMENT for table `Orders`
--
ALTER TABLE `Orders`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=125;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `Sides`
--
ALTER TABLE `Sides`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Users`
--
ALTER TABLE `Users`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Delivery_users`
--
ALTER TABLE `Delivery_users`
  ADD CONSTRAINT `Delivery_users_ibfk_1` FOREIGN KEY (`deliveryID`) REFERENCES `Delivery` (`ID`) ON DELETE CASCADE;

--
-- Constraints for table `Orderdetails`
--
ALTER TABLE `Orderdetails`
  ADD CONSTRAINT `Orderdetails_ibfk_1` FOREIGN KEY (`orderID`) REFERENCES `Orders` (`ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `Orderdetails_ibfk_2` FOREIGN KEY (`productID`) REFERENCES `Products` (`ID`);

--
-- Constraints for table `Orders`
--
ALTER TABLE `Orders`
  ADD CONSTRAINT `Orders_ibfk_1` FOREIGN KEY (`customerID`) REFERENCES `Customers` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Orders_ibfk_2` FOREIGN KEY (`PartnersID`) REFERENCES `Partners` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Orders_ibfk_3` FOREIGN KEY (`deliveryID`) REFERENCES `Delivery` (`ID`) ON UPDATE CASCADE;

--
-- Constraints for table `Partners_users`
--
ALTER TABLE `Partners_users`
  ADD CONSTRAINT `Partners_users_ibfk_1` FOREIGN KEY (`PartnersID`) REFERENCES `Partners` (`ID`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`orderID`) REFERENCES `Orders` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`customerID`) REFERENCES `Customers` (`ID`) ON DELETE SET NULL;

--
-- Constraints for table `Products`
--
ALTER TABLE `Products`
  ADD CONSTRAINT `Products_ibfk_1` FOREIGN KEY (`CategoryID`) REFERENCES `Categories` (`ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `Products_ibfk_2` FOREIGN KEY (`PartnersID`) REFERENCES `Partners` (`ID`) ON DELETE CASCADE;

--
-- Constraints for table `Sides`
--
ALTER TABLE `Sides`
  ADD CONSTRAINT `Sides_ibfk_1` FOREIGN KEY (`productID`) REFERENCES `Products` (`ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
