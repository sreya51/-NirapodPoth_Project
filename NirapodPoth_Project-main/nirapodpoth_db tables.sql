-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 12, 2025 at 06:00 PM
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
-- Database: `nirapodpoth_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `feedback_id` int(11) NOT NULL,
  `report_id` int(11) NOT NULL,
  `response` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`feedback_id`, `report_id`, `response`) VALUES
(63, 80, 'Yes, I\'m s'),
(64, 81, 'Prefer not'),
(65, 82, 'Yes, I\'m s'),
(66, 83, 'No, still '),
(67, 84, 'Yes, I\'m s'),
(68, 85, 'Prefer not'),
(69, 86, 'Yes, I\'m s'),
(70, 87, 'Yes, I\'m s');

-- --------------------------------------------------------

--
-- Table structure for table `flagged_reports`
--

CREATE TABLE `flagged_reports` (
  `flag_id` int(11) NOT NULL,
  `report_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `reason` text DEFAULT NULL,
  `flagged_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `flagged_reports`
--

INSERT INTO `flagged_reports` (`flag_id`, `report_id`, `admin_id`, `reason`, `flagged_at`) VALUES
(19, 85, 1, 'This report seems suspicious because it lacks specific details about the incident location and time. The description is vague, and there is no evidence provided to verify the claims. Further investigation is needed to confirm its authenticity.', '2025-08-10 13:42:44'),
(20, 83, 1, 'hooop', '2025-08-11 21:40:54'),
(21, 87, 1, 'Contains sensitive and potentially harmful content requiring further review', '2025-08-11 22:07:16');

-- --------------------------------------------------------

--
-- Table structure for table `incidents`
--

CREATE TABLE `incidents` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `incident_time` datetime DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `safety_status` varchar(50) NOT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Pending',
  `is_fake` tinyint(1) DEFAULT 0,
  `anonymous` tinyint(1) DEFAULT 0,
  `location_id` int(11) DEFAULT NULL,
  `type_id` int(11) DEFAULT NULL,
  `flagged` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `incidents`
--

INSERT INTO `incidents` (`id`, `user_id`, `type`, `location`, `incident_time`, `description`, `created_at`, `safety_status`, `contact_number`, `status`, `is_fake`, `anonymous`, `location_id`, `type_id`, `flagged`) VALUES
(80, 1, NULL, NULL, '2025-08-09 19:20:00', 'While waiting at the bus stop near Amborkhana a man started following me and making inappropriate comments. I felt very unsafe and uncomfortable. This happened around 7:30 PM in the evening.', '2025-08-10 13:29:54', 'Yes, I\'m safe now', NULL, 'Pending', 0, 0, 3, 2, 0),
(81, 1, 'Eve Teasing', NULL, '2025-08-08 03:30:00', 'While crossing the main intersection, a speeding motorcycle collided with a rickshaw. The driver of the motorcycle lost control and both vehicles fell. Several bystanders rushed to help, but the rider appeared to have suffered serious injuries.', '2025-08-10 13:30:26', 'Prefer not to say', NULL, 'Verified', 0, 0, 3, 4, 0),
(82, 3, NULL, NULL, '2025-08-09 17:30:00', 'I have noticed a person following me daily on my way to work. He maintains a close distance, often appearing when I stop or enter shops. It makes me feel very unsafe and anxious.', '2025-08-10 13:31:17', 'Yes, I\'m safe now', NULL, 'Verified', 0, 0, 1, 5, 0),
(83, NULL, NULL, NULL, '2025-08-07 14:28:00', 'Someone keeps calling me repeatedly and showing up near my home uninvited. Despite asking them to stop, the behavior has continued and escalated. I fear for my personal safety.', '2025-08-10 13:31:54', 'No, still in danger', '01764074936', 'Pending', 1, 0, 15, 5, 0),
(84, NULL, NULL, NULL, '2025-08-09 16:30:00', 'I noticed unusual behavior from an individual in my neighborhood who has been frequently loitering around without any clear purpose. This person often appears at different times of the day and night, watching nearby houses and people closely. Their movements seem deliberate and calculated, often avoiding eye contact and trying to stay unnoticed. Sometimes they are seen taking photos or videos of private properties, which makes me very uneasy. On several occasions, they have been seen talking on the phone in hushed tones, glancing around nervously. This pattern of behavior feels threatening and raises concerns about potential criminal intent, such as planning theft, stalking, or other harmful activities. I am reporting this suspicious activity to alert authorities and ensure community safety.', '2025-08-10 13:33:30', 'Yes, I\'m safe now', NULL, 'Verified', 0, 0, 10, 6, 0),
(85, NULL, NULL, NULL, '2025-08-07 16:30:00', 'On the evening of August 7th, while returning home from work, I noticed that the lock of my front door had been forcibly broken. Upon entering, I found that several valuable items including my laptop, wallet, and some jewelry were missing. The house appeared to be ransacked, with drawers and cabinets left open and scattered around. There were no signs of any struggle or injury, indicating that the thief entered when the house was empty. I immediately reported the incident to the local police and have secured the premises temporarily. This theft has caused me significant distress and loss. I hope the authorities will be able to catch the responsible person soon.', '2025-08-10 13:38:00', 'Prefer not to say', NULL, 'Unverified', 0, 0, 9, 7, 0),
(86, 2, NULL, NULL, '2025-08-10 17:32:00', 'Last night, my bicycle was stolen from outside my house. I had locked it securely, but the lock was broken and the bike was gone when I checked in the morning. There were no witnesses around, and no security cameras in the area. I reported the theft to the local authorities and hope to recover it soon. This incident has made me more cautious about my belongings.\r\n\r\n', '2025-08-10 13:39:33', 'Yes, I\'m safe now', NULL, 'Verified', 0, 0, 10, 8, 0),
(87, 7, NULL, NULL, '2025-08-12 15:35:00', 'On August 12th, an incident of harassment was reported in the Subidbazar area. The victim stated that they were approached and verbally harassed by an unknown individual while returning home. The situation caused distress and discomfort, prompting the victim to seek help from nearby people and later report the matter to authorities for further action.', '2025-08-11 21:35:58', 'Yes, I\'m safe now', NULL, 'Verified', 0, 0, 8, 2, 0);

-- --------------------------------------------------------

--
-- Table structure for table `incident_types`
--

CREATE TABLE `incident_types` (
  `type_id` int(11) NOT NULL,
  `type_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `incident_types`
--

INSERT INTO `incident_types` (`type_id`, `type_name`) VALUES
(1, 'Eve Teasing'),
(2, 'Harassment'),
(3, 'Kidnapping\"'),
(4, 'Road Accident'),
(5, 'Stalking'),
(6, 'Suspicious Activity'),
(7, 'Theft\"'),
(8, 'Other');

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `location_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `zone_type` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`location_id`, `name`, `zone_type`) VALUES
(1, 'Bondor Bazar', 'Urban'),
(2, 'Sylhet Sadar', 'District'),
(3, 'Ambarkhana', 'Urban'),
(4, 'Mirabazar', 'Commercial'),
(5, 'Zindabazar', 'Commercial '),
(6, 'Chowhatta', 'Commercial '),
(7, 'Nayasarak', 'Residential '),
(8, 'Subidbazar', 'Commercial '),
(9, 'Kodomtoli', 'Residential / Mixed-use'),
(10, 'Uposhohor', 'Residential'),
(11, 'Pathantula', 'Commercial '),
(12, 'Majortila', 'Residential '),
(13, 'Shibganj', 'Residential '),
(14, 'Kumarpara', 'Residential'),
(15, 'Modina Market	', 'Commercial ');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `Id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `NID` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_admin` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`Id`, `fullname`, `Email`, `Password`, `NID`, `created_at`, `is_admin`) VALUES
(1, 'Rijoana Salam', 'rijoana123@gmail.com', '$2y$10$h1/BvLQwxR3gFQEuVGe1XOrFVqrlhMMHkJArSYx/q42EjgN.L/bBm', '9876543210', '2025-08-03 13:48:16', 1),
(2, 'Sumaiya Rahman', 'sumaiya.r99@example.com', '$2y$10$SydX2DsVYnOiiV4vkLY1reKab4wJJvU00IBZibLit47JeoHyEtqtC', '1234567891', '2025-08-03 14:20:43', 0),
(3, 'Sreya Ghosh', 'sreya101@gmail.com', '$2y$10$qnBRjWWUsYltbl6d6jzLFunp1t23AikGUrrzIN193HagPnGeGcG7W', '1254774506', '2025-08-10 11:25:38', 0),
(4, 'Saira Ghosh', 'saira101@gmail.com', '$2y$10$IG3XEte9PIBQ5emMupic4usXtQzD6dzcsOiKr0Kls3B/m/XEWjHbS', '1254774507', '2025-08-10 11:34:35', 0),
(5, 'Sairas Ghosh', 'sairas101@gmail.com', '$2y$10$QHWZNouNOj8oHcmTlPsZh.LubmViWQ3tS0OeU/A99LSW9hF3pdZTm', '1254774508', '2025-08-10 11:35:01', 0),
(6, 'riya salam', 'riya@gmail.com', '$2y$10$5pPqqoKuqvd4ebyVgAmEZ.qf.kZObgIm2Po4hoePjw.g7ROFiThXS', '7788990022', '2025-08-11 21:19:33', 0),
(7, 'ria salam', 'ria@gmail.com', '$2y$10$8hfR6l0buPmot4Ph4Y.BtunUwN8wvxKf5kGMPqFt0R5YGS8ZdLfKu', '0099778865', '2025-08-11 21:32:28', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `report_id` (`report_id`);

--
-- Indexes for table `flagged_reports`
--
ALTER TABLE `flagged_reports`
  ADD PRIMARY KEY (`flag_id`),
  ADD KEY `report_id` (`report_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `incidents`
--
ALTER TABLE `incidents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `fk_location` (`location_id`),
  ADD KEY `fk_type_id` (`type_id`);

--
-- Indexes for table `incident_types`
--
ALTER TABLE `incident_types`
  ADD PRIMARY KEY (`type_id`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`location_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`Id`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD UNIQUE KEY `NID` (`NID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `flagged_reports`
--
ALTER TABLE `flagged_reports`
  MODIFY `flag_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `incidents`
--
ALTER TABLE `incidents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT for table `incident_types`
--
ALTER TABLE `incident_types`
  MODIFY `type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `location_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`report_id`) REFERENCES `incidents` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `flagged_reports`
--
ALTER TABLE `flagged_reports`
  ADD CONSTRAINT `flagged_reports_ibfk_1` FOREIGN KEY (`report_id`) REFERENCES `incidents` (`id`),
  ADD CONSTRAINT `flagged_reports_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `users` (`Id`);

--
-- Constraints for table `incidents`
--
ALTER TABLE `incidents`
  ADD CONSTRAINT `fk_location` FOREIGN KEY (`location_id`) REFERENCES `locations` (`location_id`),
  ADD CONSTRAINT `fk_type_id` FOREIGN KEY (`type_id`) REFERENCES `incident_types` (`type_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `incidents_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`Id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
