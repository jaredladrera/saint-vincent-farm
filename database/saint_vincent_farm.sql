-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 02, 2026 at 05:38 PM
-- Server version: 8.0.34
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `saint_vincent_farm`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `amount` int NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cart` tinyint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `quantity`, `amount`, `created_at`, `cart`) VALUES
(11, 16, 17, 2, 46, '2026-04-29 16:00:00', 0),
(12, 16, 16, 1, 32, '2026-04-29 16:00:00', 0),
(13, 16, 5, 1, 11, '2026-04-29 16:00:00', 0),
(14, 16, 17, 2, 46, '2026-04-29 16:00:00', 0),
(15, 16, 16, 1, 32, '2026-04-29 16:00:00', 0),
(16, 16, 17, 3, 69, '2026-04-29 16:00:00', 0),
(17, 16, 16, 1, 32, '2026-04-29 16:00:00', 0),
(18, 16, 17, 2, 46, '2026-04-29 16:00:00', 0),
(19, 16, 16, 1, 32, '2026-04-29 16:00:00', 0),
(20, 16, 17, 2, 46, '2026-04-29 16:00:00', 0),
(21, 16, 16, 1, 32, '2026-04-29 16:00:00', 0),
(22, 16, 17, 1, 23, '2026-05-01 16:00:00', 0),
(23, 16, 16, 1, 32, '2026-05-01 16:00:00', 0);

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` int NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `date_created` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `livestock`
--

CREATE TABLE `livestock` (
  `id` int NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `quantity` int NOT NULL,
  `price` int NOT NULL,
  `is_vaccinated` tinyint(1) NOT NULL,
  `condition_notes` longtext COLLATE utf8mb4_general_ci NOT NULL,
  `health_score` int NOT NULL COMMENT 'e.g., 1–100 scale if 100 quantity',
  `date_created` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `livestock`
--

INSERT INTO `livestock` (`id`, `category`, `name`, `quantity`, `price`, `is_vaccinated`, `condition_notes`, `health_score`, `date_created`) VALUES
(3, 'Poultry', '45 Days Chicken', 50, 200, 0, 'Healthy Chicken', 50, '2026-04-22'),
(4, 'Cattle', '44', 44, 44, 0, '44', 445, '2026-04-22'),
(5, 'Cattle', '11', 1, 11, 0, '11', 11, '2026-04-22'),
(10, 'Cattle', '3', 31, 3, 0, '3', 3, '2026-04-22'),
(11, 'Cattle', '21', 1, 21, 0, '21', 21, '2026-04-22'),
(12, 'Cattle', '123', 12, 21, 1, '12', 12, '2026-04-22'),
(13, 'Cattle', '12', 32, 32, 1, '32', 32, '2026-04-22'),
(14, 'Cattle', '123123', 32, 12, 1, '32', 32, '2026-04-22'),
(15, 'Cattle', '32', 32, 32, 1, '32', 32, '2026-04-22'),
(16, 'Cattle', '32', 32, 32, 1, '32', 32, '2026-04-22'),
(17, 'Cattle', '23', 23, 23, 1, '23', 23, '2026-04-22');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `order_status` enum('pending','paid','shipped','delivered','cancelled') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `total_amount` decimal(10,2) NOT NULL,
  `mode_of_payment` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `notes` longtext COLLATE utf8mb4_general_ci NOT NULL,
  `proof_of_payment` longtext COLLATE utf8mb4_general_ci,
  `prefered_delivery_date` date NOT NULL,
  `order_ids` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `order_status`, `total_amount`, `mode_of_payment`, `created_at`, `updated_at`, `notes`, `proof_of_payment`, `prefered_delivery_date`, `order_ids`) VALUES
(46, 16, 'pending', 55.00, 'GCash', '2026-05-01 16:00:00', '2026-05-02 15:33:55', 'fdgfgdfg', 'assets/images/proof/proof_0_20260502_173355_c259ce.jpg', '2026-05-07', '16,17');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `product_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_name`, `quantity`, `price`) VALUES
(14, 45, '32', 1, 32.00),
(15, 45, '23', 3, 23.00),
(16, 46, '23', 1, 23.00),
(17, 46, '32', 1, 32.00);

-- --------------------------------------------------------

--
-- Table structure for table `user_profile`
--

CREATE TABLE `user_profile` (
  `id` int NOT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `middle_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `contact_number` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `email_address` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `address` longtext COLLATE utf8mb4_general_ci NOT NULL,
  `user_role` varchar(255) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'admin, staff, user',
  `date_created` date NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_profile`
--

INSERT INTO `user_profile` (`id`, `first_name`, `middle_name`, `last_name`, `contact_number`, `email_address`, `address`, `user_role`, `date_created`, `password`) VALUES
(14, 'naruto', 'namikaze', 'uzumaki', '09307980536', 'uzumaki@gmail.com', 'Malvar, Batangas, Philippines', 'user', '2026-04-24', '$2y$10$dD9vMLk86KOIwLUfOW2JU.mYtY7UUVyavBOmBID.vMR/SxDFn1Eca'),
(15, 'john', 'john', 'john', '09307980536', 'john@gmail.com', 'Malvar, Batangas, Philippines', 'user', '2026-04-26', '12345678'),
(16, 'jeje update', 'jeje', 'jeje', '09307980536', 'jeje@gmail.com', 'Malvar, Batangas, Philippines', 'user', '2026-04-26', 'lance123');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `livestock`
--
ALTER TABLE `livestock`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_profile`
--
ALTER TABLE `user_profile`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `livestock`
--
ALTER TABLE `livestock`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `user_profile`
--
ALTER TABLE `user_profile`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_profile` (`id`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `livestock` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_profile` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
