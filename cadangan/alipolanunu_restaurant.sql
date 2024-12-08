-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 02, 2024 at 10:37 AM
-- Server version: 5.7.44-cll-lve
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `alipolanunu_restaurant`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `item_id`, `quantity`) VALUES
(162, 112233, 35, 1);

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` int(11) NOT NULL,
  `restaurant_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,0) NOT NULL,
  `stock` int(11) DEFAULT '0',
  `is_available` tinyint(1) DEFAULT '1',
  `image_url` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `restaurant_id`, `name`, `price`, `stock`, `is_available`, `image_url`, `description`) VALUES
(2, 2, 'Bakso Urat', 12000, 9, 1, 'upload/671b836534b5b.jpg', ''),
(3, 3, 'Sate Kambing', 20000, 14, 1, '', ''),
(5, 1, 'Ayam Geprek', 15000, 15, 1, 'upload/67191cfcc1e78.jpeg', ''),
(6, 1, 'Ayam Gulai', 22000, 8, 1, 'upload/67191d333c4b2.jpeg', ''),
(7, 4, 'Brownies Kukus', 53000, 8, 1, '', ''),
(8, 4, 'Madona', 10000, 5, 0, '', ''),
(9, 1, 'Ayam Bawang', 22000, 51, 1, 'upload/67191d96d6df8.jpeg', ''),
(10, 1, 'Ayam Pop', 17000, 14, 1, 'upload/67191ddeecb7b.jpg', ''),
(11, 1, 'Ayam Semur', 21000, 17, 1, 'upload/67191ee8db427.png', ''),
(12, 1, 'Sup Ayam', 27000, 23, 1, 'upload/67191f1354fd3.jpg', ''),
(13, 4, 'Bolu', 5000, 41, 1, '', ''),
(14, 4, 'Tar Labu', 17000, 21, 1, '', ''),
(15, 4, 'Milo', 10000, 12, 1, '', ''),
(16, 4, 'Ice Tea', 5000, 22, 1, '', ''),
(17, 4, 'Hot Choco', 7000, 11, 1, '', ''),
(18, 4, 'Milk', 11000, 6, 1, '', ''),
(20, 1, 'Es Teh', 5000, 21, 1, 'upload/67191f4d41e30.jpg', ''),
(21, 1, 'Coklat Milo', 12000, 15, 1, 'upload/671920b640f38.jpg', ''),
(22, 1, 'Green Tea', 17000, 11, 1, 'upload/671920dfbcf21.jpg', ''),
(35, 1, 'Nutrisari', 6000, 21, 1, 'upload/6719213bcc540.jpg', ''),
(37, 1, 'Pop Ice', 5000, 13, 1, 'upload/671921eb6f01e.jpeg', ''),
(44, 2, 'Bakso Ikan', 17000, 21, 1, 'upload/671b8379d072d.jpeg', ''),
(45, 2, 'Bakso Telur', 22000, 16, 1, 'upload/671b838a5cfca.jpg', ''),
(46, 2, 'Bakso Sapi', 25000, 21, 1, 'upload/671b839f08046.jpeg', ''),
(47, 2, 'Bakso Mercon', 18000, 12, 1, 'upload/671b83b00534f.jpg', ''),
(48, 2, 'Bakso Iga', 19000, 17, 1, 'upload/671b83bdd2e6c.jpg', ''),
(49, 2, 'Es Teh', 5000, 22, 1, 'upload/671b83d601e82.jpg', ''),
(50, 2, 'Nutrisari', 5000, 21, 1, 'upload/671b83eb9a66a.jpg', ''),
(52, 3, 'Sate Ayam', 21000, 32, 1, '', ''),
(57, 1, 'Le Minerale', 5000, 22, 1, 'upload/le_minerale.jpeg', '');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `delivery_address` varchar(255) NOT NULL,
  `contact_information` varchar(255) NOT NULL,
  `payment_method` varchar(255) NOT NULL,
  `status` enum('Ditunda','Terkonfirmasi','Sedang Diantar','Sudah Diantar','Dibatalkan') DEFAULT 'Ditunda',
  `total_amount` decimal(10,2) DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `restaurant_id` int(11) NOT NULL,
  `estimated_delivery_time` varchar(255) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `delivery_address`, `contact_information`, `payment_method`, `status`, `total_amount`, `order_date`, `restaurant_id`, `estimated_delivery_time`, `name`) VALUES
(76, 112233, 'stain', '08000800', 'Kartu Kredit', 'Sudah Diantar', 22000.00, '2024-07-16 04:12:57', 1, '1 jam', '0'),
(77, 112233, 'Poka', '0808080', 'Transfer Bank', 'Sudah Diantar', 44000.00, '2024-10-23 16:50:41', 1, '', '0'),
(80, 112233, 'gsgsh', 'hwhhe', 'Transfer Bank', 'Sudah Diantar', 32000.00, '2024-10-25 07:51:44', 1, '100', '0'),
(81, 112233, 'Poka', '0808080', 'Transfer Bank', 'Ditunda', 66000.00, '2024-10-25 11:43:42', 2, NULL, '0'),
(86, 112233, 'ooo', '999', 'Tunai/ Bayar di Tempat', 'Ditunda', 12000.00, '2024-10-26 14:50:36', 2, NULL, 'tes');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `item_id`, `quantity`) VALUES
(161, 76, 6, 1),
(162, 77, 6, 2),
(166, 80, 10, 1),
(167, 80, 5, 1),
(168, 81, 45, 3),
(173, 86, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `restaurants`
--

CREATE TABLE `restaurants` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `contact` varchar(255) DEFAULT NULL,
  `user_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `restaurants`
--

INSERT INTO `restaurants` (`id`, `name`, `address`, `contact`, `user_id`) VALUES
(1, 'Ayam Jago', 'stain', '082166728917', 2),
(2, 'Bakso Mas Rudi', 'Halong', '08978836776', 1),
(3, 'Sate Pak Mamat', 'Poka', '085266478254', 4),
(4, 'Dunia Kue', 'Passo', '082536725662', 3);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `level` text NOT NULL,
  `image_user` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `level`, `image_user`) VALUES
(1, 'bakso mas rudi', '$2y$10$VULWcqO3e3NZPPGrEyn.KeLzhM8Mz64NCXPUPrd8pOyqjo8/O1QYi', 'baksomasrudi@gmail.com', 'resto', 'upload/users/671b4aba5d916.jpg'),
(2, 'ayam jago', '$2y$10$wGektugFwtexHzd2BRXP0uTnYYpND0Uo6AdKhlRaMiy9.KxkPzM3y', 'ayamjago@gmail.com', 'resto', 'upload/users/671a0ea77b8ed.jpeg'),
(3, 'dunia kue', '$2y$10$wWvLw5HUe0QT1bEFdszDMOKnoKem07ZxXklN.xNJVJmq24TZNBSTS', 'duniakue@gmail.com', 'resto', 'upload/users/67255ba3d302d.png'),
(4, 'sate pak mamat', '$2y$10$6zIslC3v4K8YAFKE65/j1eSCdRMGPK7HwtCn7GYpbocqzdtUJOMc2', 'satepakmmat@gmail.com', 'resto', 'upload/users/67255b8e3bbbd.jpeg'),
(112233, 'ali polanunu', '$2y$10$CbZBhbusSAw7fEujcmfmK.wvxYn74fitcRaK6cIQC690t3xdeeMNS', 'boy932535@gmail.com', 'user', 'upload/users/671a098c95301.jpg'),
(112267, 'danny', '$2y$10$lWktFuQEFg7JeqjjXpk4Uu/csocuGofnDLNJTxezPXn40BktNEe..', 'danny@gmail.com', 'user', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `restaurant_id` (`restaurant_id`);

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `order_items_ibfk_2` (`item_id`);

--
-- Indexes for table `restaurants`
--
ALTER TABLE `restaurants`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=163;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=176;

--
-- AUTO_INCREMENT for table `restaurants`
--
ALTER TABLE `restaurants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=112282;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `menu_items` (`id`);

--
-- Constraints for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD CONSTRAINT `menu_items_ibfk_1` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
