-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 18, 2026 at 04:22 PM
-- Server version: 10.4.32-MariaDB
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
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `amount` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `cart` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `quantity`, `amount`, `created_at`, `cart`) VALUES
(115, 14, 25, 3, 2400, '2026-05-01 16:00:00', 0),
(116, 14, 23, 2, 2, '2026-05-01 16:00:00', 0),
(117, 14, 22, 1, 200, '2026-05-01 16:00:00', 0),
(118, 14, 19, 1, 200, '2026-05-01 16:00:00', 0),
(119, 14, 20, 1, 150, '2026-05-01 16:00:00', 0),
(120, 14, 21, 2, 500, '2026-05-01 16:00:00', 0),
(121, 14, 18, 1, 150, '2026-05-01 16:00:00', 0),
(122, 15, 27, 1, 160, '2026-05-01 16:00:00', 0),
(123, 15, 28, 1, 160, '2026-05-01 16:00:00', 0),
(124, 15, 25, 1, 800, '2026-05-01 16:00:00', 0),
(125, 14, 28, 2, 320, '2026-05-02 16:00:00', 0),
(126, 14, 27, 1, 160, '2026-05-02 16:00:00', 0),
(127, 14, 28, 1, 160, '2026-05-02 16:00:00', 0),
(128, 14, 27, 1, 160, '2026-05-02 16:00:00', 0),
(129, 14, 25, 1, 800, '2026-05-02 16:00:00', 0),
(131, 14, 27, 1, 160, '2026-05-02 16:00:00', 0),
(132, 14, 25, 1, 800, '2026-05-02 16:00:00', 0),
(136, 14, 27, 1, 160, '2026-05-02 16:00:00', 0),
(137, 14, 25, 1, 800, '2026-05-02 16:00:00', 0),
(138, 14, 28, 1, 250, '2026-05-02 16:00:00', 0),
(139, 14, 23, 1, 1, '2026-05-02 16:00:00', 0),
(140, 16, 23, 2, 2, '2026-05-02 16:00:00', 0),
(148, 15, 27, 3, 480, '2026-05-02 16:00:00', 0),
(149, 15, 28, 4, 1000, '2026-05-02 16:00:00', 0),
(150, 14, 21, 46, 11500, '2026-05-02 16:00:00', 1),
(151, 15, 27, 1, 160, '2026-05-10 16:00:00', 0);

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `category` varchar(255) NOT NULL,
  `date_created` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `delivery_details`
--

CREATE TABLE `delivery_details` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) NOT NULL,
  `vehicle_type` varchar(100) NOT NULL,
  `order_id` int(11) NOT NULL,
  `delivery_fee` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `delivery_details`
--

INSERT INTO `delivery_details` (`id`, `name`, `description`, `vehicle_type`, `order_id`, `delivery_fee`) VALUES
(2, 'adsa', 'asdasd', 'Motorcycle', 51, 120);

-- --------------------------------------------------------

--
-- Table structure for table `livestock`
--

CREATE TABLE `livestock` (
  `id` int(11) NOT NULL,
  `category` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `is_vaccinated` tinyint(1) NOT NULL,
  `condition_notes` longtext NOT NULL,
  `health_score` int(11) NOT NULL COMMENT 'e.g., 1–100 scale if 100 quantity',
  `date_created` date NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `product_type` varchar(255) NOT NULL COMMENT 'product, livestock',
  `sku` varchar(255) NOT NULL COMMENT 'PC, KG, BOX'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `livestock`
--

INSERT INTO `livestock` (`id`, `category`, `name`, `quantity`, `price`, `is_vaccinated`, `condition_notes`, `health_score`, `date_created`, `image`, `product_type`, `sku`) VALUES
(19, 'Cattle', 'Cow', 10, 280, 1, 'Healthy', 10, '2026-05-03', '1777741803_cow.png', 'Livestock', 'KG'),
(20, 'Swine', 'Tagalog na Baboy', 10, 150, 1, 'Healthy', 10, '2026-05-03', '1777741948_native pig.png', 'Livestock', 'KG'),
(21, 'Swine', 'Pig', 100, 250, 1, 'Healthy Pig', 100, '2026-05-03', '1777741991_pig.png', 'Livestock', 'null'),
(22, 'Goats', 'Goat Boe', 10, 200, 1, 'Healthy Goat', 10, '2026-05-03', '1777747237_goat.jpg', 'Livestock', 'KG'),
(23, 'Poultry', 'Talisayin', 3, 1000, 1, 'Healthy', 5, '2026-05-03', '1777748644_images (1).jpg', 'Livestock', 'KG'),
(25, 'Poultry', 'Tachaw', 200, 800, 1, 'Quality', 180, '2026-05-03', '1777751919_download.jpg', 'Livestock', 'KG'),
(27, 'Swine', 'Barako Pig', 9, 160, 1, '', 8, '2026-05-03', '1777755245_images (5).jpg', 'Livestock', 'KG'),
(28, 'Swine', 'Inahin Pig', 10, 250, 1, '', 9, '2026-05-03', '1777755325_images (3).jpg', 'Livestock', 'KG'),
(32, 'Swine', 'Pork Liempo', 100, 250, 1, 'Fresh Liempo', 100, '2026-05-13', '1778685541_liempo.jpg', 'Product', 'KG'),
(33, 'Poultry', 'Chicken Thigh', 100, 200, 1, 'Fresh', 100, '2026-05-13', '1778685839_download (1).jpg', 'Product', 'KG');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_status` enum('pending','processing','out_for_delivery','delivered','cancelled') DEFAULT 'pending',
  `total_amount` decimal(10,2) NOT NULL,
  `mode_of_payment` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `notes` longtext NOT NULL,
  `proof_of_payment` longtext DEFAULT NULL,
  `prefered_delivery_date` date NOT NULL,
  `order_ids` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `order_status`, `total_amount`, `mode_of_payment`, `created_at`, `updated_at`, `notes`, `proof_of_payment`, `prefered_delivery_date`, `order_ids`) VALUES
(80, 15, 'delivered', 1120.00, 'Cash on Delivery', '2026-05-01 16:00:00', '2026-05-03 02:19:43', 'no notes', NULL, '2026-05-30', '25,27,28'),
(81, 14, 'delivered', 6080.00, 'Cash on Delivery', '2026-05-02 16:00:00', '2026-05-03 02:21:39', 'no notes', NULL, '2026-05-28', '18,19,20,21,22,23,25,27,28'),
(82, 14, 'out_for_delivery', 1120.00, 'GCash', '2026-05-02 16:00:00', '2026-05-03 08:37:51', 'no notes', 'assets/images/proof/proof_0_20260503_041748_f5e35c.png', '2026-05-28', '25,27,28'),
(83, 14, 'delivered', 960.00, 'GCash', '2026-05-02 16:00:00', '2026-05-03 04:46:49', 'qwqweqweqweqweqwe', 'assets/images/proof/proof_0_20260503_064419_58c823.png', '2026-05-22', '25,27'),
(84, 14, 'delivered', 2210.00, 'GCash', '2026-05-02 16:00:00', '2026-05-03 08:05:20', 'no notes', 'assets/images/proof/proof_0_20260503_082701_dc5151.png', '2026-05-30', '23,25,27,28'),
(85, 16, 'pending', 2000.00, 'Cash on Delivery', '2026-05-02 16:00:00', '2026-05-03 07:53:46', 'qwe', NULL, '2026-05-30', '23'),
(86, 15, 'processing', 1480.00, 'GCash', '2026-05-02 16:00:00', '2026-05-03 08:03:45', 'anak ni poqweko', 'assets/images/proof/proof_0_20260503_100300_7e900c.jpg', '2026-05-22', '27,28'),
(87, 15, 'pending', 160.00, 'Cash on Delivery', '2026-05-10 16:00:00', '2026-05-11 12:59:31', 'no notes', NULL, '2026-05-12', '27');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_name`, `quantity`, `price`) VALUES
(104, 80, 'Barako Pig', 1, 160.00),
(105, 80, 'Inahin Pig', 1, 160.00),
(106, 80, 'Tachaw', 1, 800.00),
(107, 81, 'Tachaw', 3, 800.00),
(108, 81, 'Talisayin', 2, 1000.00),
(109, 81, 'Goat Boe', 1, 200.00),
(110, 81, 'Cow', 1, 200.00),
(111, 81, 'Tagalog na Baboy', 1, 150.00),
(112, 81, 'Pig', 2, 250.00),
(113, 81, '45 Days Chicken', 1, 150.00),
(114, 81, 'Inahin Pig', 2, 160.00),
(115, 81, 'Barako Pig', 1, 160.00),
(116, 82, 'Inahin Pig', 1, 160.00),
(117, 82, 'Barako Pig', 1, 160.00),
(118, 82, 'Tachaw', 1, 800.00),
(119, 83, 'Barako Pig', 1, 160.00),
(120, 83, 'Tachaw', 1, 800.00),
(121, 84, 'Barako Pig', 1, 160.00),
(122, 84, 'Tachaw', 1, 800.00),
(123, 84, 'Inahin Pig', 1, 250.00),
(124, 84, 'Talisayin', 1, 1000.00),
(125, 85, 'Talisayin', 2, 1000.00),
(126, 86, 'Barako Pig', 3, 160.00),
(127, 86, 'Inahin Pig', 4, 250.00),
(128, 87, 'Barako Pig', 1, 160.00);

-- --------------------------------------------------------

--
-- Table structure for table `payroll`
--

CREATE TABLE `payroll` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `period_start` date NOT NULL,
  `period_end` date NOT NULL,
  `daily_rate` float NOT NULL,
  `created_at` date NOT NULL,
  `ot_pay` float NOT NULL,
  `sss` float NOT NULL,
  `pagibig` float NOT NULL,
  `philhealth` float NOT NULL,
  `late_deduction` float NOT NULL,
  `other_deduction` float NOT NULL,
  `net_pay` float NOT NULL,
  `status` float NOT NULL,
  `total_deduction` float NOT NULL,
  `basic_pay` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payroll`
--

INSERT INTO `payroll` (`id`, `user_id`, `period_start`, `period_end`, `daily_rate`, `created_at`, `ot_pay`, `sss`, `pagibig`, `philhealth`, `late_deduction`, `other_deduction`, `net_pay`, `status`, `total_deduction`, `basic_pay`) VALUES
(1, 15, '2026-05-14', '2026-05-18', 100, '2026-05-14', 400, 50, 50, 50, 30, 0, 2320, 0, 180, 2100);

-- --------------------------------------------------------

--
-- Table structure for table `user_profile`
--

CREATE TABLE `user_profile` (
  `id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `contact_number` varchar(255) NOT NULL,
  `email_address` varchar(255) NOT NULL,
  `address` longtext NOT NULL,
  `user_role` varchar(255) NOT NULL COMMENT 'admin, staff, user',
  `date_created` date NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_profile`
--

INSERT INTO `user_profile` (`id`, `first_name`, `middle_name`, `last_name`, `contact_number`, `email_address`, `address`, `user_role`, `date_created`, `password`) VALUES
(14, 'Emilio', 'H', 'Aguinaldo', '09307980536', 'user@gmail.com', 'Malvar, Batangas, Philippines', 'User', '2026-04-24', '123456'),
(15, 'Saint Vincent', 'H', 'Farm', '09307980536', 'admin@gmail.com', 'Malvar, Batangas, Philippines', 'Administrator', '2026-04-26', '123456'),
(16, 'Saint Vincent', 'H', 'Staff', '09307980536', 'staff@gmail.com', 'Malvar, Batangas, Philippines', 'Staff', '2026-04-26', '123456'),
(17, 'rambo', 'tan', 'ar-ar', '095677521', 'ar-ar@gmail.com', 'Purok 5, Bilucao, Malvar, Batangas', 'user', '2026-05-03', '123456');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `delivery_details`
--
ALTER TABLE `delivery_details`
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
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payroll`
--
ALTER TABLE `payroll`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=152;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `delivery_details`
--
ALTER TABLE `delivery_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `livestock`
--
ALTER TABLE `livestock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=129;

--
-- AUTO_INCREMENT for table `payroll`
--
ALTER TABLE `payroll`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_profile`
--
ALTER TABLE `user_profile`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_profile` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_profile` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
