-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 23, 2023 at 10:02 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hms`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `emailid` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `emailid`, `password`) VALUES
(1, 'admin', 'test@test.in', '12345');

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `hosteller_id` int(11) DEFAULT NULL,
  `attendance_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `hosteller_id`, `attendance_date`) VALUES
(32, 1, '2023-06-04'),
(33, 1, '2023-07-14'),
(31, 1, '2023-07-15'),
(30, 1, '2023-07-16'),
(28, 1, '2023-07-17'),
(29, 3, '2023-07-17');

-- --------------------------------------------------------

--
-- Table structure for table `hostellers`
--

CREATE TABLE `hostellers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `address` text NOT NULL,
  `dob` date NOT NULL,
  `password` varchar(255) NOT NULL,
  `hosteller_type` varchar(20) NOT NULL,
  `food` varchar(50) DEFAULT NULL,
  `photo` varchar(255) NOT NULL,
  `hostellercode` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hostellers`
--

INSERT INTO `hostellers` (`id`, `name`, `email`, `phone`, `gender`, `address`, `dob`, `password`, `hosteller_type`, `food`, `photo`, `hostellercode`) VALUES
(1, 'Demo', 'test@test.in', '8660639268', 'Male', 'Shimoga', '1999-07-11', '12345', 'Student', 'Required', 'uploads/photo_64b57b268323f_1689615142.jpg', 'H000001'),
(3, 'Demo1', 'user2@user.com', '8660639268', 'Male', 'Shimoga', '2023-07-11', '[value-8]', 'Employee', 'Required', '[value-10]', 'H000002');

--
-- Triggers `hostellers`
--
DELIMITER $$
CREATE TRIGGER `before_insert_hostellers` BEFORE INSERT ON `hostellers` FOR EACH ROW BEGIN
    DECLARE new_code VARCHAR(50);
    SET new_code = CONCAT('H', LPAD((SELECT COALESCE(MAX(SUBSTRING(hostellercode, 2)), 0) + 1 FROM hostellers), 6, '0'));
    SET NEW.hostellercode = new_code;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `mess_food_menu`
--

CREATE TABLE `mess_food_menu` (
  `menu_date` date NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mess_food_menu`
--

INSERT INTO `mess_food_menu` (`menu_date`, `price`) VALUES
('2023-07-01', 100.00),
('2023-07-02', 100.00),
('2023-07-03', 100.00),
('2023-07-04', 100.00),
('2023-07-05', 100.00),
('2023-07-06', 100.00),
('2023-07-07', 100.00),
('2023-07-08', 100.00),
('2023-07-09', 100.00),
('2023-07-10', 100.00),
('2023-07-11', 100.00),
('2023-07-12', 100.00),
('2023-07-13', 100.00),
('2023-07-14', 100.00),
('2023-07-15', 100.00),
('2023-07-16', 100.00),
('2023-07-17', 100.00),
('2023-07-18', 100.00),
('2023-07-19', 100.00),
('2023-07-20', 100.00),
('2023-07-21', 100.00),
('2023-07-22', 100.00),
('2023-07-23', 100.00),
('2023-07-24', 100.00),
('2023-07-25', 100.00),
('2023-07-26', 100.00),
('2023-07-27', 100.00),
('2023-07-28', 100.00),
('2023-07-29', 100.00),
('2023-07-30', 100.00),
('2023-07-31', 100.00);

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `room_number` varchar(50) NOT NULL,
  `room_capacity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `room_number`, `room_capacity`) VALUES
(1, '100', 3),
(2, '102', 3),
(3, '101', 3);

-- --------------------------------------------------------

--
-- Table structure for table `room_allotment`
--

CREATE TABLE `room_allotment` (
  `id` int(11) NOT NULL,
  `hostellers_id` int(11) DEFAULT NULL,
  `room_id` int(11) DEFAULT NULL,
  `allocation_date` date DEFAULT NULL,
  `deallocation_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_allotment`
--

INSERT INTO `room_allotment` (`id`, `hostellers_id`, `room_id`, `allocation_date`, `deallocation_date`) VALUES
(26, 1, 1, '2023-07-16', '2023-07-16'),
(27, 3, 1, '2023-07-16', '2023-07-16'),
(28, 1, 1, '2023-07-16', '2023-07-17'),
(29, 3, 1, '2023-07-16', '2023-07-17'),
(30, 1, 1, '2023-07-17', '2023-07-17'),
(31, 3, 2, '2023-07-17', '2023-07-17'),
(32, 1, 1, '2023-07-17', '2023-07-17'),
(33, 3, 1, '2023-07-17', NULL),
(34, 1, 1, '2023-07-17', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uc_attendance` (`hosteller_id`,`attendance_date`);

--
-- Indexes for table `hostellers`
--
ALTER TABLE `hostellers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_hostellercode` (`hostellercode`);

--
-- Indexes for table `mess_food_menu`
--
ALTER TABLE `mess_food_menu`
  ADD PRIMARY KEY (`menu_date`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `room_allotment`
--
ALTER TABLE `room_allotment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hostellers_id` (`hostellers_id`),
  ADD KEY `room_id` (`room_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `hostellers`
--
ALTER TABLE `hostellers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `room_allotment`
--
ALTER TABLE `room_allotment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `fk_hosteller` FOREIGN KEY (`hosteller_id`) REFERENCES `hostellers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `room_allotment`
--
ALTER TABLE `room_allotment`
  ADD CONSTRAINT `room_allotment_ibfk_1` FOREIGN KEY (`hostellers_id`) REFERENCES `hostellers` (`id`),
  ADD CONSTRAINT `room_allotment_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
