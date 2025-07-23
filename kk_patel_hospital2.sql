-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 06, 2025 at 09:55 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kk_patel_hospital2`
--

-- --------------------------------------------------------

--
-- Table structure for table `activation`
--

CREATE TABLE `activation` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `activation_code` varchar(64) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` timestamp NULL DEFAULT ((now() + interval 1 day))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_data`
--

CREATE TABLE `admin_data` (
  `admin_id` bigint UNSIGNED NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `postal_code` int NOT NULL,
  `phone` int NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_data`
--

INSERT INTO `admin_data` (`admin_id`, `username`, `email`, `logo`, `password`, `city`, `postal_code`, `phone`, `address`, `created_at`, `updated_at`) VALUES
(1, 'KK PATEL HOSPITAL', 'gorasiyabhoomin@gmail.com', 'logo_1743102942.png', '$2y$10$Pr9ix0FrmaHP50sWU76i4O5lAGFZ11ZFOa3ukaW4B9iJkfW.IuM42', 'Bhuj', 370001, 283223600, 'Near Kutch University, Mundra Road, Bhuj, Kachchh, Gujarat - 370001.', '2025-03-27 17:36:22', '2025-03-27 17:36:22');

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int NOT NULL,
  `appointmentname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `departmentname` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `consultancy_fees` decimal(10,2) NOT NULL,
  `appointment_date` date NOT NULL,
  `time_slot` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('pending','approved','cancelled') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `appointmentname`, `email`, `phone`, `gender`, `departmentname`, `username`, `consultancy_fees`, `appointment_date`, `time_slot`, `message`, `created_at`, `status`) VALUES
(1, 'fwq', 'gorasiyabhoomin@gmail.com', '5454545454', 'Male', 'oncology', '8', 3000.00, '2025-04-17', '10:12:00', 'bggygyg', '2025-04-04 20:09:33', 'pending'),
(4, 'nqegh', 'bgorasiya881@rku.ac.in', '5656565656', 'Male', 'Cardiology', '6', 2000.00, '2025-04-11', '10:09:00', 'ewg bhhegbqe', '2025-04-04 20:38:48', 'pending'),
(5, 'Anand', 'bgorasiya881@rku.ac.in', '2323232323', 'Male', 'Cardiology', '6', 2000.00, '2025-04-19', '13:01:00', 'wejbhbwehbvhbve', '2025-04-05 07:40:37', 'pending'),
(6, 'Bhoomin', 'bgorasiya881@rku.ac.in', '23232323232', 'Male', 'nephrolo', '9', 6000.00, '2025-04-12', '10:00:00', 'svwebvwebb', '2025-04-05 09:19:06', 'pending'),
(7, 'milan', 'gorasiyabhoomin@gmail.com', '2323232323', 'Male', 'Cardiology', '6', 2000.00, '2025-04-10', '13:01:00', 'gwenhgjwjehujhgwe', '2025-04-06 08:47:59', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` int NOT NULL,
  `contactname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `submitted_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `contactname`, `email`, `phone`, `subject`, `message`, `submitted_at`) VALUES
(1, 'egwngbj', 'bqw@gmail.com', '2323434356', 'a ghbewjbg', 'qwf bghbqhwbg', '2025-03-26 17:36:36'),
(6, 'geewg', 'gwg@gmail.cm', '2323232323', 'bvjbjw', 'wbhvjewbjbvew', '2025-03-26 17:43:31'),
(28, 'Bhomoin', 'sabgfb@gmail.com', '4545454545', 'as hhbasg', 'fBSHFBHSBHBGHBGHSAB', '2025-04-06 08:00:06'),
(30, 'dggd', 'gorasiyabhoomin@gmail.com', '6767676767', 'nbhabshgbgasd', 'snajfbbhabshggs', '2025-04-06 08:04:18');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int NOT NULL,
  `departmentimage` varchar(255) NOT NULL,
  `departmentname` varchar(100) NOT NULL,
  `departmenthead` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `departmentimage`, `departmentname`, `departmenthead`, `description`, `created_at`) VALUES
(4, 'uploads/Departments/1.png', 'Cardiology', 'Dr.fasbfh', 'Experience one of the best cardiology services in Bhuj, Kutch at K.K. Patel Super Speciality Hospital. Our department comprises an experienced and qualified team of Cardiologists and Critical Care Physicians. We provide round-the-clock support for Angiography and Primary Angioplasty (PAMI), ensuring immediate care for cardiac emergencies.', '2025-03-21 16:18:19'),
(5, 'uploads/Departments/5.png', 'oncology', 'Dr.bhvh', 'Experience comprehensive oncology services at K.K. Patel Super Speciality Hospital, one of the best cancer centers in the Bhuj, Kutch. Our dedicated Cancer Centre is equipped with state-of-the-art facilities and the latest radiation machine. Our highly experienced team, including radiation oncologist, surgical oncologist, and medical oncologist, ensures the best possible care for our patients.', '2025-03-21 17:23:57'),
(8, 'uploads/Departments/6.png', 'nephrolo', 'Dr.mejfbjqe', 'Management of acute renal failure, chronic renal failure, acute nephritis, nephrotic syndrome, reno-vascular hypertension, and collagen vascular disorders involving kidneys, etc.', '2025-03-30 19:15:16'),
(9, 'uploads/Departments/3.png', 'Physiotheraphist', 'Dr.Anand', 'qhvgvqgvgvq', '2025-04-06 09:10:20');

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `id` int NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `dob` date NOT NULL,
  `gender` enum('male','female') NOT NULL,
  `departmentname` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `city` varchar(255) NOT NULL,
  `postal_code` varchar(10) NOT NULL,
  `cv` varchar(255) NOT NULL,
  `avatar` varchar(255) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `password` varchar(255) NOT NULL,
  `consultancy_fees` decimal(10,2) NOT NULL,
  `bio` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`id`, `username`, `email`, `dob`, `gender`, `departmentname`, `address`, `city`, `postal_code`, `cv`, `avatar`, `phone`, `password`, `consultancy_fees`, `bio`, `created_at`) VALUES
(6, 'Meet Thacker', 'meet@gmail.com', '1111-01-02', 'male', 'Cardiology', 'qewfwfqf', 'ew vn e', '239930', 'uploads/Doctors/1.png', 'uploads/1.png', '2323232323', '12341234', 2000.00, 'beahgbhqbehgb', '2025-03-30 18:39:13'),
(8, 'Rugved Thakkar', 'mojj@gmail.com', '2001-01-03', 'male', 'oncology', 'nebhbwe', 'Bhuj', '370030', 'uploads/3.png', 'uploads/3.png', '2323232323', '12341234', 3000.00, 'mewbhgbhwebhbg', '2025-04-04 19:24:30'),
(9, 'Eva Katba', 'evakatba23@gmail.com', '2002-01-17', 'female', 'nephrolo', 'Bhuj,near pantheon', 'Bhuj', '370030', 'uploads/cv/download.jpeg', 'avatar_1743932396.jpg', '23232323232', '12341234', 6000.00, 'rnenjbhbb3bhbh3', '2025-04-05 08:15:54');

-- --------------------------------------------------------

--
-- Table structure for table `doctor_time_slots`
--

CREATE TABLE `doctor_time_slots` (
  `id` int NOT NULL,
  `doctor_id` int DEFAULT NULL,
  `time_slot` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `doctor_time_slots`
--

INSERT INTO `doctor_time_slots` (`id`, `doctor_id`, `time_slot`) VALUES
(37, 8, '10:12:00'),
(38, 8, '13:02:00'),
(49, 9, '10:00:00'),
(50, 9, '10:20:00'),
(51, 9, '10:30:00'),
(52, 9, '10:40:00'),
(56, 6, '13:01:00'),
(57, 6, '10:09:00'),
(58, 6, '13:03:00');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `name`, `email`, `message`, `created_at`) VALUES
(2, 'Bhoomin', 'enwbhg@gmail.com', 'wnqhvhwq', '2025-03-26 16:42:20'),
(8, 'Anand', 'anand97@gmail.com', 'thank for giving good services', '2025-03-30 18:33:57'),
(10, 'Bhoomin', 'gorasiyabhoomin@gmail.com', 'an fhgbqwhbghqebe', '2025-04-06 08:35:29');

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` int NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `gender` enum('male','female') NOT NULL,
  `age` int NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`id`, `username`, `email`, `gender`, `age`, `address`, `phone`, `message`, `created_at`) VALUES
(5, 'dsdbw', 'shwh@gmail.com', 'male', 32, 'qejbjbqjwbf', '3434343423', 'eejbfhjqbwhfbhbhfq', '2025-04-05 16:39:23');

-- --------------------------------------------------------

--
-- Table structure for table `press_desk`
--

CREATE TABLE `press_desk` (
  `id` int NOT NULL,
  `pressimage` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `press_desk`
--

INSERT INTO `press_desk` (`id`, `pressimage`, `description`, `created_at`) VALUES
(1, 'uploads/Pressdesk/blog4.jpg', 'અંગ દાન એક જીવન દાન', '2025-03-21 16:27:13');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `dob` date NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `address` text NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('inactive','active') DEFAULT 'inactive',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `mobile`, `dob`, `gender`, `address`, `password`, `status`, `created_at`) VALUES
(3, 'Anand', 'gorasiyabhoomin@gmail.com', '0123456789', '2001-02-02', 'Male', 'sdn vheqb', '$2y$10$kUTz1XWtxMoHM3XIMFlH0epnkPEFjS9O3rPjMX0Q6ZdUJSOgMwoWu', 'active', '2025-04-03 14:28:29');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activation`
--
ALTER TABLE `activation`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `activation_code` (`activation_code`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `doctor_time_slots`
--
ALTER TABLE `doctor_time_slots`
  ADD PRIMARY KEY (`id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `press_desk`
--
ALTER TABLE `press_desk`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activation`
--
ALTER TABLE `activation`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `doctor_time_slots`
--
ALTER TABLE `doctor_time_slots`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `press_desk`
--
ALTER TABLE `press_desk`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activation`
--
ALTER TABLE `activation`
  ADD CONSTRAINT `activation_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `doctor_time_slots`
--
ALTER TABLE `doctor_time_slots`
  ADD CONSTRAINT `doctor_time_slots_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
