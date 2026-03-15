-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 15, 2026 at 06:46 PM
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
-- Database: `bsb_fin_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart_item_tbl`
--

CREATE TABLE `cart_item_tbl` (
  `cart_item_id` int(10) UNSIGNED NOT NULL,
  `cart_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `qty` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `unit_price` decimal(10,2) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart_item_tbl`
--

INSERT INTO `cart_item_tbl` (`cart_item_id`, `cart_id`, `product_id`, `qty`, `unit_price`, `created_at`, `updated_at`) VALUES
(5, 1, 1, 1, 149.00, '2026-03-15 17:37:47', '2026-03-15 17:37:47');

-- --------------------------------------------------------

--
-- Table structure for table `cart_tbl`
--

CREATE TABLE `cart_tbl` (
  `cart_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart_tbl`
--

INSERT INTO `cart_tbl` (`cart_id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 2, '2026-03-15 16:28:50', '2026-03-15 16:28:50');

-- --------------------------------------------------------

--
-- Table structure for table `order_item_tbl`
--

CREATE TABLE `order_item_tbl` (
  `order_item_id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `category_name` varchar(120) DEFAULT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `qty` int(10) UNSIGNED NOT NULL,
  `line_total` decimal(10,2) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_item_tbl`
--

INSERT INTO `order_item_tbl` (`order_item_id`, `order_id`, `product_id`, `product_name`, `category_name`, `unit_price`, `qty`, `line_total`, `created_at`) VALUES
(1, 1, 1, 'Chocolate Chip Cookie 3D Model', 'Cookie', 149.00, 4, 596.00, '2026-03-15 17:15:20'),
(2, 1, 3, 'Pain au Chocolat', 'Pastry', 99.00, 3, 297.00, '2026-03-15 17:15:20'),
(3, 2, 3, 'Pain au Chocolat', 'Pastry', 99.00, 2, 198.00, '2026-03-15 17:37:15');

-- --------------------------------------------------------

--
-- Table structure for table `order_tbl`
--

CREATE TABLE `order_tbl` (
  `order_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `order_number` varchar(40) NOT NULL,
  `payment_method` enum('cod','gcash') NOT NULL,
  `delivery_address` text NOT NULL,
  `address_lat` decimal(10,7) DEFAULT NULL,
  `address_lng` decimal(10,7) DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `order_status` varchar(30) NOT NULL DEFAULT 'pending',
  `eta_minutes` int(11) NOT NULL DEFAULT 0,
  `eta_half_at` datetime DEFAULT NULL,
  `eta_due_at` datetime DEFAULT NULL,
  `delivered_at` datetime DEFAULT NULL,
  `stock_applied` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_tbl`
--

INSERT INTO `order_tbl` (`order_id`, `user_id`, `order_number`, `payment_method`, `delivery_address`, `address_lat`, `address_lng`, `subtotal`, `total_amount`, `order_status`, `eta_minutes`, `eta_half_at`, `eta_due_at`, `delivered_at`, `stock_applied`, `created_at`, `updated_at`) VALUES
(1, 2, 'BSB-20260315171520-C30324', 'cod', 'Lat: 14.671000, Lng: 120.980000', 14.6710000, 120.9800000, 893.00, 893.00, 'pending', 0, NULL, NULL, NULL, 0, '2026-03-15 17:15:20', '2026-03-15 17:15:20'),
(2, 2, 'BSB-0002-260315-1737-S-9436', 'cod', 'Lat: 14.671000, Lng: 120.980000', 14.6710000, 120.9800000, 198.00, 198.00, 'pending', 5, '2026-03-15 17:39:45', '2026-03-15 17:42:15', NULL, 0, '2026-03-15 17:37:15', '2026-03-15 17:37:15');

-- --------------------------------------------------------

--
-- Table structure for table `product_category_tbl`
--

CREATE TABLE `product_category_tbl` (
  `category_id` int(10) UNSIGNED NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `category_slug` varchar(120) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_category_tbl`
--

INSERT INTO `product_category_tbl` (`category_id`, `category_name`, `category_slug`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Pastry', 'pastry', 1, '2026-03-15 14:08:19', '2026-03-15 14:08:19'),
(2, 'Cake', 'cake', 1, '2026-03-15 14:08:19', '2026-03-15 14:08:19'),
(3, 'Cookie', 'cookie', 1, '2026-03-15 14:08:19', '2026-03-15 14:08:19'),
(4, 'Bundle', 'bundle', 1, '2026-03-15 14:08:19', '2026-03-15 14:08:19');

-- --------------------------------------------------------

--
-- Table structure for table `product_image_tbl`
--

CREATE TABLE `product_image_tbl` (
  `product_image_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `alt_text` varchar(180) DEFAULT NULL,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_tbl`
--

CREATE TABLE `product_tbl` (
  `product_id` int(10) UNSIGNED NOT NULL,
  `product_name` varchar(150) NOT NULL,
  `product_slug` varchar(180) NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stock_qty` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `main_image` varchar(255) NOT NULL DEFAULT 'assets/placeholder/bsb_product_default.png',
  `short_description` text DEFAULT NULL,
  `detailed_description` mediumtext DEFAULT NULL,
  `avg_rating` decimal(3,2) NOT NULL DEFAULT 0.00,
  `rating_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `updated_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_tbl`
--

INSERT INTO `product_tbl` (`product_id`, `product_name`, `product_slug`, `category_id`, `price`, `stock_qty`, `main_image`, `short_description`, `detailed_description`, `avg_rating`, `rating_count`, `is_active`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'Chocolate Chip Cookie 3D Model', 'chocolate-chip-cookie-3d-model', 3, 149.00, 50, 'uploads/products/1773585341_e0a8b9e0aff8a1776e0f.jpg', 'Stylized 3D chocolate chip cookie asset.', 'Game-ready and render-ready 3D cookie model with clean topology and textures.', 0.00, 0, 1, 1, 1, '2026-03-15 14:08:19', '2026-03-15 14:35:41'),
(2, 'Pain au Chocolat', 'pain-au-chocolat', 1, 67.00, 50, 'assets/placeholder/bsb_product_default.png', 'Short', 'Long', 0.00, 0, 0, 1, 1, '2026-03-15 14:22:42', '2026-03-15 14:34:29'),
(3, 'Pain au Chocolat', 'pain-au-chocolat-2', 1, 99.00, 100, 'assets/placeholder/bsb_product_default.png', 'Short', 'Long', 0.00, 0, 1, 1, 1, '2026-03-15 14:34:57', '2026-03-15 14:34:57');

-- --------------------------------------------------------

--
-- Table structure for table `user_tbl`
--

CREATE TABLE `user_tbl` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `fname` varchar(50) NOT NULL,
  `lname` varchar(50) NOT NULL,
  `uname` varchar(50) NOT NULL,
  `pword` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_tbl`
--

INSERT INTO `user_tbl` (`user_id`, `role`, `fname`, `lname`, `uname`, `pword`, `email`, `created_at`) VALUES
(1, 'admin', 'BSB', 'Admin', 'bsbadmin', 'bsbadmin1', 'bsbadmin@bytesizedbakes.local', '2026-03-15 13:22:08'),
(2, 'user', 'PETERCEN', 'GIRON', 'ptsn1', '$2y$10$LQ1pKFw5YdRzERmL9zydZeqp5p.9d4s00mad/3MCKKzM1s8vPBesy', 'prgiron@fit.edu.ph', '2026-03-15 07:28:29'),
(3, 'user', '', '', 'bsbadminas', '$2y$10$7pFvN2t9hUIg8IcP3JbkR.ttj9EG2/SjYTKGCo6AtKVNmwOBHZpq2', '', '2026-03-15 09:39:50'),
(4, 'user', 'TESTER', 'GIRON', 'unique0012', '$2y$10$6X6l9TmUriLZ102RfhFDHeqWJuxPFmgT3la8XaxUYOpJxlhGL0pZ2', 'unique@gmail.com', '2026-03-15 09:41:49');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart_item_tbl`
--
ALTER TABLE `cart_item_tbl`
  ADD PRIMARY KEY (`cart_item_id`),
  ADD UNIQUE KEY `uq_cart_product` (`cart_id`,`product_id`),
  ADD KEY `fk_cart_item_product` (`product_id`);

--
-- Indexes for table `cart_tbl`
--
ALTER TABLE `cart_tbl`
  ADD PRIMARY KEY (`cart_id`),
  ADD UNIQUE KEY `uq_cart_user` (`user_id`);

--
-- Indexes for table `order_item_tbl`
--
ALTER TABLE `order_item_tbl`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `fk_order_item_order` (`order_id`),
  ADD KEY `fk_order_item_product` (`product_id`);

--
-- Indexes for table `order_tbl`
--
ALTER TABLE `order_tbl`
  ADD PRIMARY KEY (`order_id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `fk_order_user` (`user_id`);

--
-- Indexes for table `product_category_tbl`
--
ALTER TABLE `product_category_tbl`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `category_name` (`category_name`),
  ADD UNIQUE KEY `category_slug` (`category_slug`);

--
-- Indexes for table `product_image_tbl`
--
ALTER TABLE `product_image_tbl`
  ADD PRIMARY KEY (`product_image_id`),
  ADD KEY `idx_img_product` (`product_id`),
  ADD KEY `idx_img_primary` (`is_primary`);

--
-- Indexes for table `product_tbl`
--
ALTER TABLE `product_tbl`
  ADD PRIMARY KEY (`product_id`),
  ADD UNIQUE KEY `product_slug` (`product_slug`),
  ADD KEY `idx_product_category` (`category_id`),
  ADD KEY `idx_product_active` (`is_active`),
  ADD KEY `idx_product_name` (`product_name`),
  ADD KEY `fk_product_created_by` (`created_by`),
  ADD KEY `fk_product_updated_by` (`updated_by`);

--
-- Indexes for table `user_tbl`
--
ALTER TABLE `user_tbl`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart_item_tbl`
--
ALTER TABLE `cart_item_tbl`
  MODIFY `cart_item_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `cart_tbl`
--
ALTER TABLE `cart_tbl`
  MODIFY `cart_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `order_item_tbl`
--
ALTER TABLE `order_item_tbl`
  MODIFY `order_item_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `order_tbl`
--
ALTER TABLE `order_tbl`
  MODIFY `order_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `product_category_tbl`
--
ALTER TABLE `product_category_tbl`
  MODIFY `category_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `product_image_tbl`
--
ALTER TABLE `product_image_tbl`
  MODIFY `product_image_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_tbl`
--
ALTER TABLE `product_tbl`
  MODIFY `product_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_tbl`
--
ALTER TABLE `user_tbl`
  MODIFY `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart_item_tbl`
--
ALTER TABLE `cart_item_tbl`
  ADD CONSTRAINT `fk_cart_item_cart` FOREIGN KEY (`cart_id`) REFERENCES `cart_tbl` (`cart_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cart_item_product` FOREIGN KEY (`product_id`) REFERENCES `product_tbl` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cart_tbl`
--
ALTER TABLE `cart_tbl`
  ADD CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `user_tbl` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `order_item_tbl`
--
ALTER TABLE `order_item_tbl`
  ADD CONSTRAINT `fk_order_item_order` FOREIGN KEY (`order_id`) REFERENCES `order_tbl` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_order_item_product` FOREIGN KEY (`product_id`) REFERENCES `product_tbl` (`product_id`) ON UPDATE CASCADE;

--
-- Constraints for table `order_tbl`
--
ALTER TABLE `order_tbl`
  ADD CONSTRAINT `fk_order_user` FOREIGN KEY (`user_id`) REFERENCES `user_tbl` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `product_image_tbl`
--
ALTER TABLE `product_image_tbl`
  ADD CONSTRAINT `fk_product_image_product` FOREIGN KEY (`product_id`) REFERENCES `product_tbl` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `product_tbl`
--
ALTER TABLE `product_tbl`
  ADD CONSTRAINT `fk_product_category` FOREIGN KEY (`category_id`) REFERENCES `product_category_tbl` (`category_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_product_created_by` FOREIGN KEY (`created_by`) REFERENCES `user_tbl` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_product_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `user_tbl` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
