-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 16, 2025 at 04:44 PM
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
-- Database: `mcgcreamline`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` int(11) NOT NULL,
  `fullname` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `fullname`, `email`, `username`, `status`, `created_at`) VALUES
(1, 'Trexie  Caps', 'capenatrexiemhy@gmail.com', 'Trex', 'Active', '2025-04-28 21:28:18'),
(2, 'Kyhang Cabs', '2301110021@student.buksu.edu.ph', 'yang', 'Active', '2025-04-28 21:30:59'),
(3, 'trex lee', 'mhy@gmail.com', 'mhy', 'Active', '2025-04-29 22:07:38'),
(4, 'fe ewf', 'ace13@gmail.com', 'few', 'Active', '2025-05-06 18:22:56'),
(5, 'fer ewffr', 'acee13@gmail.com', 'fewfr', 'Active', '2025-05-06 18:23:29'),
(6, 'edw er', 'ae13@gmail.com', 'wer', 'Active', '2025-05-06 18:23:45'),
(7, 'wedr ere', 'aae13@gmail.com', 'wqe', 'Active', '2025-05-06 18:24:01'),
(8, 'wer g', 'acw@gmail.com', 'srf', 'Active', '2025-05-06 18:24:34'),
(9, 'ret ryty', 'a@gmail.com', 'ytuy', 'Active', '2025-05-06 18:25:30'),
(10, 'Ace Kyhang', 'acyhang13@gmail.com', 'Admin', 'Active', '2025-05-11 00:58:28'),
(11, 'Yang Yeng', '2301104718@student.buksu.edu.ph', 'User', 'Active', '2025-05-11 01:08:40');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `booked_date` date DEFAULT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `total_price` int(20) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `approved_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `name`, `email`, `address`, `booked_date`, `product_name`, `quantity`, `payment_method`, `status`, `total_price`, `created_at`, `approved_at`) VALUES
(1, 'Kyhang Cabs', '2301110021@student.buksu.edu.ph', 'Cabanglasan, Bukidnon', '2025-05-06', 'BarRR', 7, 'cod', 'Pending', 245, '2025-05-06 10:14:12', NULL),
(2, 'Yang Yeng', '2301104718@student.buksu.edu.ph', 'Cabanglasan, Bukidnon', '2025-05-06', 'BarRR', 1, 'cod', 'Approved', 35, '2025-05-06 10:14:12', '2025-05-13 18:06:11'),
(6, 'Yang Yeng', '2301104718@student.buksu.edu.ph', 'Cabanglasan, Bukidnon', '2025-05-06', 'BarBS', 2, 'cod', 'Pending', 70, '2025-05-06 10:14:12', NULL),
(7, 'Yang Yeng', '2301104718@student.buksu.edu.ph', 'Cabanglasan, Bukidnon', '2025-05-06', 'BarRR', 2, 'cod', 'Pending', 70, '2025-05-06 10:14:12', NULL),
(8, 'Yang Yeng', '2301104718@student.buksu.edu.ph', 'Cabanglasan, Bukidnon', '2025-05-06', 'BarRR', 3, 'cod', 'Pending', 105, '2025-05-06 10:14:12', NULL),
(9, 'Yang Yeng', '2301104718@student.buksu.edu.ph', 'Cabanglasan, Bukidnon', '2025-05-06', 'BarCC', 1, 'cod', 'Pending', 35, '2025-05-06 11:42:21', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_completed`
--

CREATE TABLE `order_completed` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` varchar(100) NOT NULL,
  `booked_date` int(50) NOT NULL,
  `product_name` varchar(100) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `total_price` int(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `delivery_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_completed`
--

INSERT INTO `order_completed` (`id`, `name`, `email`, `address`, `booked_date`, `product_name`, `quantity`, `payment_method`, `status`, `total_price`, `created_at`, `delivery_date`) VALUES
(1, 'Yang Yeng', '2301104718@student.buksu.edu.ph', 'Cabanglasan, Bukidnon', 2025, 'BarRR', 1, 'cod', 'Completed', 35, '2025-05-13 21:30:00', '2025-05-13'),
(2, 'Yang Yeng', '2301104718@student.buksu.edu.ph', 'Cabanglasan, Bukidnon', 2025, 'BarRR', 1, 'cod', 'Completed', 35, '2025-05-13 14:39:03', '2025-05-13'),
(3, 'Yang Yeng', '2301104718@student.buksu.edu.ph', 'Cabanglasan, Bukidnon', 2025, 'BarUbe', 1, 'cod', 'Completed', 35, '2025-05-13 14:39:03', '2025-05-13'),
(4, 'Yang Yeng', '2301104718@student.buksu.edu.ph', 'Cabanglasan, Bukidnon', 2025, 'BarUbe', 1, 'cod', 'Completed', 35, '2025-05-13 14:39:03', '2025-05-13');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `stock` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `product_name`, `category`, `price`, `stock`, `image`) VALUES
(1, 'Buko Salad', 'Cream Bar', 35.00, 47, '../uploads/BarBS.png'),
(2, 'Rocky Road', 'Cream Bar', 35.00, 64, '../uploads/BarRR.png'),
(3, 'Ube', 'Cream Bar', 35.00, 26, '../uploads/BarUbe.png'),
(4, 'Strawberry', 'Cream Cups', 18.00, 32, '../uploads/CCberry.png'),
(5, 'Caramel', 'Cream Cups', 18.00, 56, '../uploads/CCcaramel.png'),
(6, 'Choco', 'Cream Cups', 18.00, 72, '../uploads/CCchoco.png');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `firstname` varchar(255) DEFAULT NULL,
  `lastname` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `reset_code` int(50) DEFAULT NULL,
  `role_as` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `username`, `email`, `password`, `reset_code`, `role_as`) VALUES
(1, 'Trexie ', 'Caps', 'Trex', 'capenatrexiemhy@gmail.com', '$2y$10$6P/SOjHt3Qs.qyhn8oImP.HhlwQvKDJOihI9/U5GsJkx9Af9EIxlq', NULL, 0),
(2, 'Kyhang', 'Cabs', 'yang', '2301110021@student.buksu.edu.ph', '$2y$10$sUpgg4jgRjVC2h4VRRnOZuF6EN7A3nD/.Djm1CbKwf8DSO1104R4C', NULL, 0),
(3, 'trex', 'lee', 'mhy', 'mhy@gmail.com', '$2y$10$cgtZ/rBzFP9UJeZ4WQ53H.cPViMHSWvoxaTLB9ellcg6taFAu5C26', NULL, 0),
(4, 'fe', 'ewf', 'few', 'ace13@gmail.com', '$2y$10$3sMnjFtzv2BXJVF/CkVgs.NoLXk.9o0NcLO9KrVrKpxO7ai7ltcu6', NULL, 0),
(5, 'fer', 'ewffr', 'fewfr', 'acee13@gmail.com', '$2y$10$jtC5foptRYZKvO455EKMsObJhRwqtYTFRf95x.W7r2jL2JopYSUeW', NULL, 0),
(6, 'edw', 'er', 'wer', 'ae13@gmail.com', '$2y$10$6DVMVymRZCmDCE.MN6vd/OQEiETfwh4x499b0DhdxdDPVXE.dK0rq', NULL, 0),
(7, 'wedr', 'ere', 'wqe', 'aae13@gmail.com', '$2y$10$TBf4IDDomLkHydWI9YvfNeMikD5U.rHVyZvITVfC4GV9eUjfm817e', NULL, 0),
(8, 'wer', 'g', 'srf', 'acw@gmail.com', '$2y$10$C8L0heFtbiXFrY0nhNWtzu55Wd5vzutH0iv4PFr62ZwtPxH4CqjpO', 759638, 0),
(9, 'ret', 'ryty', 'ytuy', 'a@gmail.com', '$2y$10$Xe9onR/UYDqIhkTISYAPauZVgOHFHCc0oL18Tti1WUlPGOjdw.G/2', NULL, 0),
(10, 'Ace', 'Kyhang', 'Admin', 'acyhang13@gmail.com', '$2y$10$a3avhIvAtdgwgV7sy1P9O.Ukkhljw80fRsG2/O0doMjsUlfQ2YvrK', NULL, 1),
(11, 'Yang', 'Yeng', 'User', '2301104718@student.buksu.edu.ph', '$2y$10$vp7dsBQyqgCCT1RFUoTDYOta8zLHoyqI6x0xHS8duJwnjvA6nCi6S', NULL, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_completed`
--
ALTER TABLE `order_completed`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `order_completed`
--
ALTER TABLE `order_completed`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
