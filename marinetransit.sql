-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 29, 2025 at 10:01 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `marinetransit`
--

-- --------------------------------------------------------

--
-- Table structure for table `accomodations`
--

CREATE TABLE `accomodations` (
  `accomodation_id` int(10) NOT NULL,
  `accomodation_name` varchar(100) NOT NULL,
  `accomodation_type` varchar(100) NOT NULL DEFAULT 'passenger',
  `accomodation_status` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accomodations`
--

INSERT INTO `accomodations` (`accomodation_id`, `accomodation_name`, `accomodation_type`, `accomodation_status`) VALUES
(1, 'adult', 'passenger', 1),
(2, 'Child', 'passenger', 1),
(3, 'Senior', 'passenger', 1),
(4, 'Student', 'passenger', 1),
(5, 'VAN', 'cargo', 1),
(6, 'M-CAB', 'cargo', 1),
(7, 'ERTIGA', 'cargo', 1),
(8, 'SEDAN', 'cargo', 0),
(9, 'AUTO', 'cargo', 1),
(10, 'MIRAGE', 'cargo', 1),
(11, 'MOTORCYCLE', 'cargo', 1);

-- --------------------------------------------------------

--
-- Table structure for table `cargos`
--

CREATE TABLE `cargos` (
  `cargo_id` int(10) NOT NULL,
  `cargo_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cargos`
--

INSERT INTO `cargos` (`cargo_id`, `cargo_name`) VALUES
(1, 'SUV'),
(2, 'PICK-UP'),
(3, 'VAN'),
(4, 'M-CAB'),
(5, 'ERTIGA'),
(6, 'SEDAN'),
(7, 'AUTO'),
(8, 'MIRAGE'),
(9, 'MOTORCYCLE');

-- --------------------------------------------------------

--
-- Table structure for table `feedbacks`
--

CREATE TABLE `feedbacks` (
  `feedback_id` int(10) NOT NULL,
  `feedback_to_from` varchar(100) NOT NULL,
  `from_user_id` int(10) NOT NULL,
  `to_user_id` int(10) NOT NULL,
  `feedback_content` text NOT NULL,
  `feedback_created_at` datetime DEFAULT current_timestamp(),
  `feedback_read` int(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedbacks`
--

INSERT INTO `feedbacks` (`feedback_id`, `feedback_to_from`, `from_user_id`, `to_user_id`, `feedback_content`, `feedback_created_at`, `feedback_read`) VALUES
(1, '2_1', 2, 1, 'test', '2024-11-25 13:31:23', 0),
(2, '2_1', 1, 2, 'eyyy', '2024-11-25 13:32:08', 0),
(3, '2_1', 1, 0, 'Olla hello', '2024-11-25 14:31:28', 0),
(4, '2_1', 2, 0, 'text send', '2024-11-26 13:29:04', 0),
(5, '2_1', 2, 0, 'adsf', '2024-11-26 13:29:10', 0),
(6, '2_1', 2, 0, 'adf', '2024-11-26 13:29:56', 0),
(7, '2_1', 2, 0, 'asdf', '2024-11-26 13:30:12', 0),
(8, '2_1', 1, 0, 'fadsf', '2024-11-26 16:11:11', 0);

-- --------------------------------------------------------

--
-- Table structure for table `passengers`
--

CREATE TABLE `passengers` (
  `passenger_id` int(10) NOT NULL,
  `ticket_id` int(10) NOT NULL,
  `passenger_fname` varchar(100) NOT NULL,
  `passenger_mname` varchar(100) NOT NULL,
  `passenger_lname` varchar(100) NOT NULL,
  `passenger_bdate` date NOT NULL,
  `passenger_contact` varchar(100) NOT NULL,
  `passenger_address` varchar(100) NOT NULL,
  `passenger_type` varchar(100) NOT NULL,
  `passenger_gender` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `passenger_cargos`
--

CREATE TABLE `passenger_cargos` (
  `passenger_cargo_id` int(10) NOT NULL,
  `ticket_id` int(10) NOT NULL,
  `accomodation_id` int(11) NOT NULL,
  `passenger_cargo_brand` varchar(100) DEFAULT NULL,
  `passenger_cargo_plate` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ports`
--

CREATE TABLE `ports` (
  `port_id` int(10) NOT NULL,
  `port_name` varchar(100) NOT NULL,
  `port_location` varchar(100) NOT NULL,
  `port_status` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ports`
--

INSERT INTO `ports` (`port_id`, `port_name`, `port_location`, `port_status`) VALUES
(1, 'Balingoan', 'Balingoan Misamis Oriental', 1),
(2, 'Camiguin', 'Camiguin Misamis Oriental', 1),
(3, 'Bohol', 'Bohol City', 1);

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `message` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `report_id` int(11) NOT NULL,
  `ticket_code` varchar(255) NOT NULL,
  `ticket_price` decimal(10,2) NOT NULL,
  `ticket_type` varchar(50) NOT NULL,
  `ticket_status` varchar(50) NOT NULL,
  `schedule_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ticket_vehicle` varchar(255) DEFAULT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `contact_address` text DEFAULT NULL,
  `report_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `routes`
--

CREATE TABLE `routes` (
  `route_id` int(10) NOT NULL,
  `route_from` varchar(100) NOT NULL,
  `route_to` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `routes`
--

INSERT INTO `routes` (`route_id`, `route_from`, `route_to`) VALUES
(4, '1', '3'),
(5, '1', '2'),
(6, '2', '1'),
(7, '2', '3'),
(8, '3', '1'),
(9, '3', '2');

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `schedule_id` int(10) NOT NULL,
  `ship_id` int(10) NOT NULL,
  `route_id` int(10) NOT NULL,
  `schedule_date` date DEFAULT NULL,
  `schedule_time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`schedule_id`, `ship_id`, `route_id`, `schedule_date`, `schedule_time`) VALUES
(14, 1, 4, '2025-01-29', '19:00:00'),
(15, 2, 4, '2025-01-30', '10:00:00'),
(16, 3, 5, '2025-01-27', '11:45:00');

-- --------------------------------------------------------

--
-- Table structure for table `schedule_accom`
--

CREATE TABLE `schedule_accom` (
  `schedule_accom_id` int(10) NOT NULL,
  `schedule_id` int(10) NOT NULL,
  `accomodation_id` int(10) NOT NULL,
  `net_fare` float(10,2) NOT NULL,
  `max_passenger` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedule_accom`
--

INSERT INTO `schedule_accom` (`schedule_accom_id`, `schedule_id`, `accomodation_id`, `net_fare`, `max_passenger`) VALUES
(52, 14, 1, 500.23, 0),
(53, 14, 2, 250.00, 0),
(54, 14, 3, 400.00, 0),
(55, 14, 4, 400.00, 0),
(56, 14, 5, 1000.00, 0),
(57, 14, 6, 1000.00, 0),
(58, 14, 7, 1000.00, 0),
(59, 14, 8, 1000.00, 0),
(60, 14, 9, 1000.00, 0),
(61, 14, 10, 1000.00, 0),
(62, 14, 11, 1000.00, 0),
(63, 15, 1, 300.00, 0),
(64, 15, 2, 250.00, 0),
(65, 15, 3, 350.00, 0),
(66, 15, 4, 100.00, 0),
(67, 15, 5, 500.00, 0),
(68, 15, 6, 500.00, 0),
(69, 15, 7, 500.00, 0),
(70, 15, 8, 500.00, 0),
(71, 15, 9, 500.00, 0),
(72, 15, 10, 500.00, 0),
(73, 15, 11, 500.00, 0),
(74, 16, 1, 350.00, 0),
(75, 16, 2, 676.00, 0),
(76, 16, 3, 300.00, 0),
(77, 16, 4, 500.00, 0),
(78, 16, 5, 100.00, 0),
(79, 16, 6, 300.00, 0),
(80, 16, 7, 500.00, 0),
(81, 16, 8, 100.00, 0),
(82, 16, 9, 650.00, 0),
(83, 16, 10, 100.00, 0),
(84, 16, 11, 600.00, 0),
(85, 17, 1, 359.00, 0),
(86, 17, 2, 179.00, 0),
(87, 17, 3, 256.00, 0),
(88, 17, 4, 287.00, 0),
(89, 17, 5, 1800.00, 0),
(90, 17, 6, 150.00, 0),
(91, 17, 7, 280.00, 0),
(92, 17, 8, 380.00, 0),
(93, 17, 9, 300.00, 0),
(94, 17, 10, 800.00, 0),
(95, 17, 11, 900.00, 0),
(96, 18, 1, 200.00, 0),
(97, 18, 2, 200.00, 0),
(98, 18, 3, 0.00, 0),
(99, 18, 4, 0.00, 0),
(100, 18, 5, 0.00, 0),
(101, 18, 6, 0.00, 0),
(102, 18, 7, 0.00, 0),
(103, 18, 8, 0.00, 0),
(104, 18, 9, 0.00, 0),
(105, 18, 10, 0.00, 0),
(106, 18, 11, 0.00, 0),
(107, 19, 1, 200.00, 0),
(108, 19, 2, 200.00, 0),
(109, 19, 3, 150.00, 0),
(110, 19, 4, 300.00, 0),
(111, 19, 5, 500.00, 0),
(112, 19, 6, 200.00, 0),
(113, 19, 7, 100.00, 0),
(114, 19, 8, 50.00, 0),
(115, 19, 9, 50.00, 0),
(116, 19, 10, 70.00, 0),
(117, 19, 11, 800.00, 0);

-- --------------------------------------------------------

--
-- Table structure for table `ships`
--

CREATE TABLE `ships` (
  `ship_id` int(10) NOT NULL,
  `ship_code` varchar(100) NOT NULL,
  `ship_name` varchar(100) NOT NULL,
  `ship_status` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ships`
--

INSERT INTO `ships` (`ship_id`, `ship_code`, `ship_name`, `ship_status`) VALUES
(1, 'SSF21', 'Shuttle Ship Ferry 21', 'Active'),
(2, 'SSF24', 'Shuttle Ship Ferry 24', 'Active'),
(3, 'SSF9', 'Shuttle Ship Ferry 9', 'Active'),
(4, 'SSF6', 'Shuttle Ship Ferry 6', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `systeminfo`
--

CREATE TABLE `systeminfo` (
  `systeminfo_id` int(10) NOT NULL,
  `systeminfo_name` varchar(100) NOT NULL,
  `systeminfo_shortname` varchar(100) NOT NULL,
  `systeminfo_icon` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `systeminfo`
--

INSERT INTO `systeminfo` (`systeminfo_id`, `systeminfo_name`, `systeminfo_shortname`, `systeminfo_icon`) VALUES
(1, 'Balingoan Port Ticketing ', 'BPT', 'icon.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `ticket_id` int(10) NOT NULL,
  `ticket_date` date NOT NULL,
  `ticket_code` varchar(100) NOT NULL,
  `ticket_price` float(10,2) NOT NULL,
  `ticket_type` varchar(100) NOT NULL,
  `ticket_status` varchar(100) NOT NULL,
  `schedule_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `ticket_vehicle` int(10) NOT NULL DEFAULT 0,
  `ticket_date_return` date DEFAULT NULL,
  `schedule_id_return` int(10) DEFAULT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `contact_number` varchar(100) DEFAULT NULL,
  `contact_email` varchar(100) DEFAULT NULL,
  `contact_address` varchar(100) DEFAULT NULL,
  `qr_code` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(10) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `user_password` varchar(100) NOT NULL,
  `user_type` varchar(100) NOT NULL,
  `user_fname` varchar(100) NOT NULL,
  `user_mname` varchar(100) NOT NULL,
  `user_lname` varchar(100) NOT NULL,
  `user_email` varchar(100) NOT NULL,
  `user_contact` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_name`, `user_password`, `user_type`, `user_fname`, `user_mname`, `user_lname`, `user_email`, `user_contact`) VALUES
(1, 'admin', 'admin', 'admin', '', '', '', '', ''),
(2, 'user', 'user', 'user', 'fname', '', 'lname', 'user@email.com', '090909090909'),
(3, 'user2', 'user2', 'user', 'user2', 'user2', 'user2', 'user2@email.com', '1234'),
(6, 'cashier', 'cashier', 'cashier', 'cashier', 'cashier', 'cashier', 'cashier@gmail.com', '09495748301');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accomodations`
--
ALTER TABLE `accomodations`
  ADD PRIMARY KEY (`accomodation_id`);

--
-- Indexes for table `cargos`
--
ALTER TABLE `cargos`
  ADD PRIMARY KEY (`cargo_id`);

--
-- Indexes for table `feedbacks`
--
ALTER TABLE `feedbacks`
  ADD PRIMARY KEY (`feedback_id`);

--
-- Indexes for table `passengers`
--
ALTER TABLE `passengers`
  ADD PRIMARY KEY (`passenger_id`);

--
-- Indexes for table `passenger_cargos`
--
ALTER TABLE `passenger_cargos`
  ADD PRIMARY KEY (`passenger_cargo_id`);

--
-- Indexes for table `ports`
--
ALTER TABLE `ports`
  ADD PRIMARY KEY (`port_id`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`report_id`);

--
-- Indexes for table `routes`
--
ALTER TABLE `routes`
  ADD PRIMARY KEY (`route_id`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`schedule_id`);

--
-- Indexes for table `schedule_accom`
--
ALTER TABLE `schedule_accom`
  ADD PRIMARY KEY (`schedule_accom_id`);

--
-- Indexes for table `ships`
--
ALTER TABLE `ships`
  ADD PRIMARY KEY (`ship_id`);

--
-- Indexes for table `systeminfo`
--
ALTER TABLE `systeminfo`
  ADD PRIMARY KEY (`systeminfo_id`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`ticket_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accomodations`
--
ALTER TABLE `accomodations`
  MODIFY `accomodation_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `cargos`
--
ALTER TABLE `cargos`
  MODIFY `cargo_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `feedbacks`
--
ALTER TABLE `feedbacks`
  MODIFY `feedback_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `passengers`
--
ALTER TABLE `passengers`
  MODIFY `passenger_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=247;

--
-- AUTO_INCREMENT for table `passenger_cargos`
--
ALTER TABLE `passenger_cargos`
  MODIFY `passenger_cargo_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `ports`
--
ALTER TABLE `ports`
  MODIFY `port_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `routes`
--
ALTER TABLE `routes`
  MODIFY `route_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `schedule_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `schedule_accom`
--
ALTER TABLE `schedule_accom`
  MODIFY `schedule_accom_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=118;

--
-- AUTO_INCREMENT for table `ships`
--
ALTER TABLE `ships`
  MODIFY `ship_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `systeminfo`
--
ALTER TABLE `systeminfo`
  MODIFY `systeminfo_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `ticket_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=225;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
