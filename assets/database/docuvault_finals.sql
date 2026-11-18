-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 21, 2025 at 03:23 PM
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
-- Database: `docuvault_finals`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `Admin_ID` int(11) NOT NULL COMMENT 'Primary Key',
  `Admin_Email` varchar(120) NOT NULL,
  `Admin_Pass` varchar(120) NOT NULL,
  `Admin_Name` varchar(120) NOT NULL,
  `Role` varchar(50) DEFAULT 'admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`Admin_ID`, `Admin_Email`, `Admin_Pass`, `Admin_Name`, `Role`) VALUES
(12, 'superadmin@example.com', '0192023a7bbd73250516f069df18b500', 'Super Admin', 'superadmin'),
(19, 'admin@example.com', '0192023a7bbd73250516f069df18b500', 'Admin User', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE `files` (
  `File_ID` int(11) NOT NULL COMMENT 'Primary Key',
  `File` varchar(255) NOT NULL,
  `File_Type` varchar(50) NOT NULL,
  `Archived` tinyint(1) DEFAULT 0,
  `Status` enum('pending','approved','rejected') DEFAULT 'pending',
  `Uploaded_By` varchar(255) NOT NULL DEFAULT 'admin',
  `Student_ID` varchar(50) NOT NULL COMMENT 'Foreign Key From student',
  `Size` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `Student_ID` varchar(50) NOT NULL COMMENT 'Primary Key',
  `First_Name` varchar(50) NOT NULL,
  `Middle_Name` varchar(50) DEFAULT NULL,
  `Last_Name` varchar(50) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Year_Section` varchar(50) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Reset_Token_Hash` varchar(64) DEFAULT NULL,
  `Reset_Token_Expires_At` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`Student_ID`, `First_Name`, `Middle_Name`, `Last_Name`, `Email`, `Year_Section`, `Password`, `Reset_Token_Hash`, `Reset_Token_Expires_At`) VALUES
('', '', '', '', '', '', '$2y$10$LftwEthq4/IJJi8YdJRZA.30/MBtipx13n.HZVZMJ35TZzyjv9dEK', NULL, NULL),
('S2025001', 'John', 'A.', 'Doe', 'student1@example.com', '1-A', 'ad6a280417a0f533d8b670c61667e1a0', NULL, NULL),
('S2025002', 'Jane', 'B.', 'Smith', 'student2@example.com', '2-B', 'ad6a280417a0f533d8b670c61667e1a0', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`Admin_ID`);

--
-- Indexes for table `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`File_ID`),
  ADD KEY `Student_ID` (`Student_ID`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`Student_ID`),
  ADD UNIQUE KEY `email` (`Email`),
  ADD UNIQUE KEY `reset_token_hash` (`Reset_Token_Hash`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `Admin_ID` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key', AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `File_ID` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key', AUTO_INCREMENT=127;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `files`
--
ALTER TABLE `files`
  ADD CONSTRAINT `files_ibfk_1` FOREIGN KEY (`Student_ID`) REFERENCES `student` (`Student_ID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
