-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 29, 2025 at 04:25 PM
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
-- Database: `career_platform`
--

-- --------------------------------------------------------

--
-- Table structure for table `career_paths`
--

CREATE TABLE `career_paths` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `path_id` varchar(50) NOT NULL,
  `cards` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`cards`)),
  `connections` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`connections`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `career_paths`
--

INSERT INTO `career_paths` (`id`, `user_id`, `path_id`, `cards`, `connections`, `created_at`, `updated_at`) VALUES
(1, 1, '1', '[{\"title\":\"Computer Science Degree\",\"duration\":\"4 years\",\"cost\":400000,\"type\":\"course\",\"id\":\"course-1748469734512\",\"position\":{\"x\":0,\"y\":0}},{\"title\":\"JEE Mains\",\"duration\":\"1 day\",\"cost\":1000,\"type\":\"exam\",\"id\":\"exam-1748469738555\",\"position\":{\"x\":0,\"y\":60}},{\"title\":\"IIT Delhi\",\"duration\":\"4 years\",\"cost\":800000,\"type\":\"institution\",\"id\":\"institution-1748469748537\",\"position\":{\"x\":30,\"y\":330}},{\"title\":\"Google Summer Internship\",\"duration\":\"3 months\",\"cost\":0,\"type\":\"internship\",\"id\":\"internship-1748469752197\",\"position\":{\"x\":270,\"y\":210}},{\"title\":\"Python Programming\",\"duration\":\"2 months\",\"cost\":15000,\"type\":\"skill\",\"id\":\"skill-1748469758757\",\"position\":{\"x\":330,\"y\":90}}]', '[]', '2025-05-28 22:02:41', '2025-05-28 22:02:41');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `type` enum('mentor','deadline','event') NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `duration` varchar(50) DEFAULT NULL,
  `mentor` varchar(255) DEFAULT NULL,
  `attendees` int(11) DEFAULT NULL,
  `priority` enum('low','medium','high') DEFAULT NULL,
  `status` enum('confirmed','pending','registered','cancelled') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `flashcards`
--

CREATE TABLE `flashcards` (
  `id` int(11) NOT NULL,
  `category` varchar(50) NOT NULL,
  `title` varchar(100) NOT NULL,
  `duration` varchar(50) NOT NULL,
  `cost` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `education_level` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `flashcards`
--

INSERT INTO `flashcards` (`id`, `category`, `title`, `duration`, `cost`, `type`, `education_level`) VALUES
(1, 'courses', 'Computer Science Degree', '4 years', 400000, 'course', '12th grade'),
(2, 'courses', 'Data Science Bootcamp', '6 months', 150000, 'course', '12th grade'),
(3, 'courses', 'Web Development Course', '3 months', 50000, 'course', '12th grade'),
(4, 'courses', 'Machine Learning Specialization', '8 months', 80000, 'course', NULL),
(5, 'courses', 'AI & Deep Learning', '1 year', 120000, 'course', NULL),
(6, 'courses', 'Cybersecurity Certification', '4 months', 75000, 'course', NULL),
(7, 'exams', 'JEE Mains', '1 day', 1000, 'exam', '12th grade'),
(8, 'exams', 'GATE', '1 day', 1500, 'exam', '12th grade'),
(9, 'exams', 'CAT', '1 day', 2000, 'exam', NULL),
(10, 'exams', 'NEET', '1 day', 1600, 'exam', NULL),
(11, 'exams', 'GRE', '1 day', 15000, 'exam', NULL),
(12, 'exams', 'IELTS', '1 day', 12000, 'exam', NULL),
(13, 'skills', 'Python Programming', '2 months', 15000, 'skill', '12th grade'),
(14, 'skills', 'Digital Marketing', '1 month', 12000, 'skill', NULL),
(15, 'skills', 'Graphic Design', '3 months', 25000, 'skill', NULL),
(16, 'skills', 'Project Management', '2 months', 18000, 'skill', NULL),
(17, 'skills', 'Cloud Computing (AWS)', '4 months', 35000, 'skill', NULL),
(18, 'skills', 'Data Analysis', '3 months', 22000, 'skill', '12th grade'),
(19, 'institutions', 'IIT Delhi', '4 years', 800000, 'institution', '12th grade'),
(20, 'institutions', 'IIM Bangalore', '2 years', 2000000, 'institution', '12th grade'),
(21, 'institutions', 'BITS Pilani', '4 years', 1600000, 'institution', '12th grade'),
(22, 'institutions', 'ISB Hyderabad', '1 year', 3500000, 'institution', '12th grade'),
(23, 'institutions', 'Stanford University', '4 years', 5000000, 'institution', NULL),
(24, 'institutions', 'MIT', '4 years', 4800000, 'institution', '12th grade'),
(25, 'internships', 'Google Summer Internship', '3 months', 0, 'internship', '12th grade'),
(26, 'internships', 'Microsoft Research Intern', '6 months', 0, 'internship', '12th grade'),
(27, 'internships', 'Startup Product Intern', '4 months', 0, 'internship', '12th grade'),
(28, 'internships', 'Goldman Sachs Analyst', '12 months', 0, 'internship', NULL),
(29, 'internships', 'Meta Software Engineer', '6 months', 0, 'internship', NULL),
(30, 'internships', 'Tesla Engineering Intern', '4 months', 0, 'internship', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `form_fields`
--

CREATE TABLE `form_fields` (
  `id` int(11) NOT NULL,
  `field_name` varchar(50) NOT NULL,
  `field_label` varchar(100) NOT NULL,
  `field_type` enum('select','multi-select') NOT NULL,
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`options`)),
  `icon` varchar(50) DEFAULT NULL,
  `is_required` tinyint(1) DEFAULT 0,
  `display_order` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `form_fields`
--

INSERT INTO `form_fields` (`id`, `field_name`, `field_label`, `field_type`, `options`, `icon`, `is_required`, `display_order`, `created_at`) VALUES
(1, 'educationLevel', 'Education Level', 'select', '[\"10th Grade\", \"12th Grade\", \"Graduation\", \"Post Graduation\"]', 'GraduationCap', 1, 1, '2025-05-28 19:57:01'),
(2, 'scoreRange', 'Score Range', 'select', '[\"40-60%\", \"60-80%\", \"80-90%\", \"90%+\"]', NULL, 1, 2, '2025-05-28 19:57:01'),
(3, 'interests', 'Interests', 'multi-select', '[\"Technology\", \"Healthcare\", \"Finance\", \"Arts\", \"Engineering\", \"Business\", \"Science\", \"Education\", \"Marketing\", \"Design\"]', NULL, 0, 3, '2025-05-28 19:57:01'),
(4, 'budget', 'Budget', 'select', '[{\"label\": \"Low (₹0-2L)\", \"value\": \"Low\"}, {\"label\": \"Medium (₹2-5L)\", \"value\": \"Medium\"}, {\"label\": \"High (₹5L+)\", \"value\": \"High\"}]', 'DollarSign', 1, 4, '2025-05-28 19:57:01'),
(5, 'timeline', 'Timeline', 'select', '[\"Fast Track (6 months - 1 year)\", \"Standard (1-3 years)\", \"Long-Term (3+ years)\"]', 'Clock', 1, 5, '2025-05-28 19:57:01');

-- --------------------------------------------------------

--
-- Table structure for table `grade_stream_options`
--

CREATE TABLE `grade_stream_options` (
  `id` int(11) NOT NULL,
  `type` enum('grade','stream') NOT NULL,
  `value` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grade_stream_options`
--

INSERT INTO `grade_stream_options` (`id`, `type`, `value`) VALUES
(1, 'grade', '10th Grade'),
(2, 'grade', '11th Grade'),
(3, 'grade', '12th Grade'),
(4, 'grade', 'Graduate'),
(8, 'stream', 'Arts'),
(7, 'stream', 'Commerce'),
(6, 'stream', 'Science (PCB)'),
(5, 'stream', 'Science (PCM)');

-- --------------------------------------------------------

--
-- Table structure for table `mentors`
--

CREATE TABLE `mentors` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `role` varchar(100) NOT NULL,
  `experience` varchar(50) NOT NULL,
  `rating` decimal(3,1) NOT NULL,
  `sessions` int(11) NOT NULL,
  `languages` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`languages`)),
  `specialization` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`specialization`)),
  `avatar` varchar(255) NOT NULL,
  `price` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `education_level` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mentors`
--

INSERT INTO `mentors` (`id`, `name`, `role`, `experience`, `rating`, `sessions`, `languages`, `specialization`, `avatar`, `price`, `created_at`, `education_level`) VALUES
(1, 'Dr. Priya Sharma', 'Data Scientist', '8 years', 4.9, 150, '[\"English\", \"Hindi\"]', '[\"AI/ML\", \"Career Transition\"]', 'https://images.unsplash.com/photo-1494790108755-2616b332163d?w=100&h=100&fit=crop&crop=face', '₹1500/hour', '2025-05-28 22:35:37', '12th grade'),
(2, 'Rohit Kumar', 'Software Engineer', '5 years', 4.7, 89, '[\"English\", \"Hindi\", \"Tamil\"]', '[\"Full Stack\", \"System Design\"]', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&h=100&fit=crop&crop=face', '₹1200/hour', '2025-05-28 22:35:37', '[]'),
(3, 'Anita Desai', 'Product Manager', '6 years', 4.8, 120, '[\"English\", \"Marathi\"]', '[\"Product Strategy\", \"Agile\"]', 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&h=100&fit=crop&crop=face', '₹1400/hour', '2025-05-28 22:35:37', '[]');

-- --------------------------------------------------------

--
-- Table structure for table `session_bookings`
--

CREATE TABLE `session_bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `mentor_id` int(11) NOT NULL,
  `session_time` datetime NOT NULL,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `session_bookings`
--

INSERT INTO `session_bookings` (`id`, `user_id`, `mentor_id`, `session_time`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2025-05-30 05:06:00', 'pending', '2025-05-29 05:06:05', '2025-05-29 05:06:05');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('student','mentor','institute') NOT NULL,
  `token` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `phone_number` varchar(15) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password`, `role`, `token`, `created_at`, `phone_number`, `address`) VALUES
(1, 'Vrushali', 'Nanavati', 'vrushali@gmail.com', '$2y$10$fkqG3j0tYxM7NQpkrx9H9e2BCND6XfyvXMuRA97YzTzjPbuEVtotK', 'student', '/RIG1ZQZGB1wTYDdOBYmiPGK17YrOwC3DU8SgRZHHTQ=', '2025-05-28 17:01:34', '9769418685', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_preferences`
--

CREATE TABLE `user_preferences` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(15) DEFAULT NULL,
  `grade` varchar(50) DEFAULT NULL,
  `stream` varchar(50) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `address` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_preferences`
--

INSERT INTO `user_preferences` (`id`, `email`, `phone_number`, `grade`, `stream`, `updated_at`, `address`) VALUES
(1, 'vrushali@gmail.com', '9769418685', '12th Grade', 'Science (PCB)', '2025-05-29 11:23:21', '23,Himmatnagar building ,S.V.P road next to M.C.F club borivali west');

-- --------------------------------------------------------

--
-- Table structure for table `user_profiles`
--

CREATE TABLE `user_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `education_level` varchar(50) NOT NULL,
  `score_range` varchar(50) NOT NULL,
  `interests` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`interests`)),
  `budget` varchar(50) NOT NULL,
  `timeline` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_profiles`
--

INSERT INTO `user_profiles` (`id`, `user_id`, `education_level`, `score_range`, `interests`, `budget`, `timeline`, `created_at`, `updated_at`) VALUES
(1, 1, '12th Grade', '40-60%', '[\"Engineering\",\"Technology\",\"Business\"]', 'Medium', 'Standard (1-3 years)', '2025-05-28 20:22:15', '2025-05-29 07:24:52');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `career_paths`
--
ALTER TABLE `career_paths`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`path_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `flashcards`
--
ALTER TABLE `flashcards`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `form_fields`
--
ALTER TABLE `form_fields`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `grade_stream_options`
--
ALTER TABLE `grade_stream_options`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `type` (`type`,`value`);

--
-- Indexes for table `mentors`
--
ALTER TABLE `mentors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `session_bookings`
--
ALTER TABLE `session_bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `mentor_id` (`mentor_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_preferences`
--
ALTER TABLE `user_preferences`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `career_paths`
--
ALTER TABLE `career_paths`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `flashcards`
--
ALTER TABLE `flashcards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `form_fields`
--
ALTER TABLE `form_fields`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `grade_stream_options`
--
ALTER TABLE `grade_stream_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `mentors`
--
ALTER TABLE `mentors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `session_bookings`
--
ALTER TABLE `session_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_preferences`
--
ALTER TABLE `user_preferences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_profiles`
--
ALTER TABLE `user_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `career_paths`
--
ALTER TABLE `career_paths`
  ADD CONSTRAINT `career_paths_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `session_bookings`
--
ALTER TABLE `session_bookings`
  ADD CONSTRAINT `session_bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `session_bookings_ibfk_2` FOREIGN KEY (`mentor_id`) REFERENCES `mentors` (`id`);

--
-- Constraints for table `user_preferences`
--
ALTER TABLE `user_preferences`
  ADD CONSTRAINT `user_preferences_ibfk_1` FOREIGN KEY (`email`) REFERENCES `users` (`email`) ON DELETE CASCADE;

--
-- Constraints for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD CONSTRAINT `user_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
