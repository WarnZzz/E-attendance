-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 23, 2025 at 08:37 AM
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
-- Database: `attendancesystem`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbladmin`
--

CREATE TABLE `tbladmin` (
  `Id` int(10) NOT NULL,
  `firstName` varchar(50) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `emailAddress` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tbladmin`
--

INSERT INTO `tbladmin` (`Id`, `firstName`, `lastName`, `emailAddress`, `password`) VALUES
(1, 'Admin', 'Paudel', 'paudelranjan6@gmail.com', 'D00F5D5217896FB7FD601412CB890830');

-- --------------------------------------------------------

--
-- Table structure for table `tblassignments`
--

CREATE TABLE `tblassignments` (
  `Id` int(11) NOT NULL,
  `Title` varchar(255) NOT NULL,
  `Description` text DEFAULT NULL,
  `FilePath` varchar(255) DEFAULT NULL,
  `ClassArmId` int(11) NOT NULL,
  `Deadline` datetime NOT NULL,
  `UploadedBy` int(11) NOT NULL,
  `UploadDate` datetime NOT NULL,
  `isDeleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblassignments`
--

INSERT INTO `tblassignments` (`Id`, `Title`, `Description`, `FilePath`, `ClassArmId`, `Deadline`, `UploadedBy`, `UploadDate`, `isDeleted`) VALUES
(3, 'Assignment I', 'assignment', 'assignment_6849324fe80683.28961846.pdf', 11, '2025-06-11 14:22:00', 16, '2025-06-11 09:37:51', 0);

-- --------------------------------------------------------

--
-- Table structure for table `tblattendance`
--

CREATE TABLE `tblattendance` (
  `Id` int(10) NOT NULL,
  `SymbolNo` int(10) NOT NULL,
  `CourseId` int(11) NOT NULL,
  `status` varchar(10) NOT NULL,
  `dateTimeTaken` varchar(20) NOT NULL,
  `joinTime` datetime DEFAULT NULL,
  `leaveTime` datetime DEFAULT NULL,
  `durationInMinutes` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblattendance`
--

INSERT INTO `tblattendance` (`Id`, `SymbolNo`, `CourseId`, `status`, `dateTimeTaken`, `joinTime`, `leaveTime`, `durationInMinutes`) VALUES
(218, 21070565, 11, '1', '2025-06-13', NULL, NULL, NULL),
(219, 21070565, 11, '1', '2025-06-21', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tblattendance_sessions`
--

CREATE TABLE `tblattendance_sessions` (
  `Id` int(11) NOT NULL,
  `CourseId` int(11) NOT NULL,
  `UniqueCode` varchar(20) NOT NULL,
  `TeacherIP` varchar(45) NOT NULL,
  `CreatedAt` datetime DEFAULT current_timestamp(),
  `ExpiresAt` datetime NOT NULL,
  `Status` enum('active','closed') DEFAULT 'active',
  `Cancelled` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblattendance_sessions`
--

INSERT INTO `tblattendance_sessions` (`Id`, `CourseId`, `UniqueCode`, `TeacherIP`, `CreatedAt`, `ExpiresAt`, `Status`, `Cancelled`) VALUES
(1, 11, 'C1HZA6L4', '', '2025-06-11 21:58:06', '2025-06-11 18:18:06', 'closed', 0),
(2, 11, 'Y2IVPTOH', '', '2025-06-11 22:55:00', '2025-06-11 19:15:00', 'closed', 0),
(3, 11, 'YABSQHXM', '', '2025-06-11 23:01:04', '2025-06-11 19:21:04', 'closed', 0),
(4, 11, 'R49B3EKL', '', '2025-06-12 13:19:42', '2025-06-12 09:39:42', 'closed', 0),
(5, 11, 'HYLX2V8I', '', '2025-06-12 13:28:45', '2025-06-12 09:48:45', 'closed', 0),
(6, 11, 'IDLRYVE9', '', '2025-06-12 13:28:49', '2025-06-12 09:48:49', 'closed', 0),
(7, 11, 'B6U1YM3V', '', '2025-06-12 13:38:35', '2025-06-12 09:58:35', 'closed', 0),
(8, 11, 'DNYSCP16', '', '2025-06-12 13:59:40', '2025-06-12 10:19:40', 'closed', 0),
(9, 11, '0OX18W9C', '', '2025-06-12 14:09:13', '2025-06-12 10:29:13', 'closed', 0),
(10, 11, 'YJKH9S20', '', '2025-06-12 14:26:10', '2025-06-12 10:46:10', 'closed', 0),
(11, 11, '0SQ69MG1', '', '2025-06-12 15:54:01', '2025-06-12 12:14:01', 'closed', 0),
(12, 11, 'ZEAT2CQX', '', '2025-06-12 15:55:14', '2025-06-12 12:15:14', 'closed', 0),
(13, 11, 'NBKS8F0X', '', '2025-06-12 15:55:49', '2025-06-12 12:15:49', 'closed', 0),
(14, 11, '90SH831M', '', '2025-06-12 15:55:58', '2025-06-12 12:15:58', 'closed', 0),
(15, 11, 'YE318KLX', '', '2025-06-12 16:05:47', '2025-06-12 12:25:47', 'closed', 0),
(16, 11, 'XMYOEGSU', '', '2025-06-12 16:20:46', '2025-06-12 12:40:46', 'closed', 0),
(17, 11, 'HRFBU7L4', '', '2025-06-12 16:27:04', '2025-06-12 12:47:04', 'closed', 0),
(18, 11, 'B3K58XNZ', '', '2025-06-12 16:46:48', '2025-06-12 13:06:48', 'closed', 0),
(19, 11, '9ZK2SQY6', '', '2025-06-12 16:52:52', '2025-06-12 13:12:52', 'closed', 0),
(20, 11, '8T2G0197', '', '2025-06-12 16:52:57', '2025-06-12 13:12:57', 'closed', 0),
(21, 11, 'K9XRSZ5E', '', '2025-06-12 16:53:24', '2025-06-12 13:13:24', 'closed', 0),
(22, 11, 'ZHSJ03UI', '', '2025-06-12 16:53:37', '2025-06-12 13:13:37', 'closed', 0),
(23, 11, 'WP7NOGYX', '', '2025-06-12 16:53:45', '2025-06-12 13:13:45', 'closed', 0),
(24, 11, 'CKML0ZRB', '', '2025-06-12 16:54:18', '2025-06-12 13:14:18', 'closed', 0),
(25, 11, '4ZQM7109', '', '2025-06-12 16:57:14', '2025-06-12 13:17:14', 'closed', 0),
(26, 11, '84EOXBN2', '', '2025-06-12 16:59:17', '2025-06-12 13:19:17', 'closed', 0),
(30, 11, 'DIQ1PX8M', '', '2025-06-12 17:21:50', '2025-06-12 13:41:50', 'closed', 0),
(31, 11, 'ONW8YUB1', '', '2025-06-12 17:22:35', '2025-06-12 13:42:35', 'closed', 0),
(32, 11, 'V69BQFZ1', '', '2025-06-12 17:24:05', '2025-06-12 13:44:05', 'closed', 0),
(33, 11, 'JOPLHGM0', '', '2025-06-12 17:24:10', '2025-06-12 13:44:10', 'closed', 0),
(36, 11, 'PAY5H72O', '', '2025-06-12 17:42:09', '2025-06-12 14:02:09', 'closed', 0),
(38, 11, 'AM9BLXC4', '', '2025-06-12 17:44:21', '2025-06-12 14:04:21', 'closed', 0),
(39, 11, '6TDOFC2W', '', '2025-06-12 17:44:26', '2025-06-12 14:04:26', 'closed', 0),
(40, 11, '47FQGYSZ', '', '2025-06-12 17:52:52', '2025-06-12 14:12:52', 'closed', 0),
(41, 11, 'M60R8JDG', '', '2025-06-12 17:54:08', '2025-06-12 14:14:08', 'closed', 0),
(42, 11, '5R6Y2JCX', '', '2025-06-12 17:57:43', '2025-06-12 14:17:43', 'closed', 0),
(43, 11, 'I8EDY9OP', '', '2025-06-12 17:57:52', '2025-06-12 14:17:52', 'closed', 0),
(44, 11, 'FNHX5KJS', '', '2025-06-12 18:13:15', '2025-06-12 14:33:15', 'closed', 0),
(48, 11, 'B07LIFPQ', '', '2025-06-12 21:43:17', '2025-06-12 18:03:17', 'closed', 0),
(50, 11, '7TQDI0L9', '', '2025-06-12 22:55:25', '2025-06-12 19:15:25', 'closed', 0),
(51, 11, 'EB03X1JZ', '', '2025-06-12 23:05:26', '2025-06-12 19:25:26', 'closed', 0),
(52, 11, 'AZOB8NGS', '', '2025-06-13 02:16:10', '2025-06-13 02:21:10', 'closed', 0),
(53, 11, '54UASVFO', '', '2025-06-13 06:06:28', '2025-06-13 06:11:28', 'closed', 0),
(54, 11, 'Y47M0ONV', '', '2025-06-13 06:14:30', '2025-06-13 06:19:30', 'closed', 0),
(55, 11, 'NAFLRZ8T', '', '2025-06-13 06:26:49', '2025-06-13 06:31:49', 'closed', 0),
(56, 11, '9NGW6LCV', '', '2025-06-13 06:32:10', '2025-06-13 06:37:10', 'closed', 0),
(57, 11, 'Z6KFNEO3', '::1', '2025-06-13 06:53:32', '2025-06-13 06:58:32', 'closed', 0),
(58, 11, 'V81J4MKE', '::1', '2025-06-13 06:53:45', '2025-06-13 06:58:45', 'closed', 0),
(59, 11, 'PLZU6IMJ', '::1', '2025-06-13 08:56:04', '2025-06-13 09:01:04', 'closed', 0),
(60, 11, '7TFDXLZS', '::1', '2025-06-13 10:14:03', '2025-06-13 10:19:03', 'closed', 0),
(61, 11, '8O0P4R9B', '::1', '2025-06-13 10:15:25', '2025-06-13 10:20:25', 'closed', 0),
(62, 11, 'KPQN4MTO', '::1', '2025-06-13 10:15:56', '2025-06-13 10:20:56', 'closed', 0),
(63, 11, '7WT4S1L6', '::1', '2025-06-21 14:49:43', '2025-06-21 14:54:43', 'closed', 0);

-- --------------------------------------------------------

--
-- Table structure for table `tblattendance_temp`
--

CREATE TABLE `tblattendance_temp` (
  `Id` int(11) NOT NULL,
  `SessionId` int(11) NOT NULL,
  `SymbolNo` int(11) NOT NULL,
  `Timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblclass`
--

CREATE TABLE `tblclass` (
  `Id` int(10) NOT NULL,
  `Program` varchar(255) NOT NULL,
  `Year(Batch)` year(4) NOT NULL,
  `section` varchar(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblclass`
--

INSERT INTO `tblclass` (`Id`, `Program`, `Year(Batch)`, `section`) VALUES
(1, 'computer', '2020', '1'),
(2, 'civil', '2020', '1'),
(10, 'civil', '2020', '2');

-- --------------------------------------------------------

--
-- Table structure for table `tblclassarms`
--

CREATE TABLE `tblclassarms` (
  `Id` int(11) NOT NULL,
  `CourseCode` varchar(10) NOT NULL,
  `ClassId` int(10) NOT NULL,
  `CourseName` varchar(255) NOT NULL,
  `AssignedTo` varchar(50) NOT NULL,
  `isAssigned` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblclassarms`
--

INSERT INTO `tblclassarms` (`Id`, `CourseCode`, `ClassId`, `CourseName`, `AssignedTo`, `isAssigned`) VALUES
(10, 'CMP220', 1, 'Math I', '17', '1'),
(11, 'CMP100', 1, 'Math II', '16', '1');

-- --------------------------------------------------------

--
-- Table structure for table `tblclassteacher`
--

CREATE TABLE `tblclassteacher` (
  `Id` int(10) NOT NULL,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `emailAddress` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phoneNo` varchar(50) NOT NULL,
  `webauthn_setup_token` varchar(255) DEFAULT NULL,
  `webauthn_setup_token_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblclassteacher`
--

INSERT INTO `tblclassteacher` (`Id`, `firstName`, `lastName`, `emailAddress`, `password`, `phoneNo`, `webauthn_setup_token`, `webauthn_setup_token_expiry`) VALUES
(16, 'Ranjan', 'Paudel', 'mvp1lazer@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055', '9826645769', NULL, NULL),
(17, 'Shiva', 'Kumar', 'paudelranjan14@gmail.com', 'dd65ffb4d0ceec848e87d802b527f265', '9826645769', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tblnotes`
--

CREATE TABLE `tblnotes` (
  `id` int(11) NOT NULL,
  `courseId` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `filePath` varchar(255) NOT NULL,
  `uploadDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `uploadedBy` int(11) NOT NULL,
  `isDeleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblnotes`
--

INSERT INTO `tblnotes` (`id`, `courseId`, `title`, `filePath`, `uploadDate`, `uploadedBy`, `isDeleted`) VALUES
(5, 11, 'Math IV', 'Math IV.pdf', '2025-06-11 07:15:07', 16, 0),
(6, 11, 'subject', '8th-semester-syllabus.pdf', '2025-06-13 04:33:40', 16, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tblstudents`
--

CREATE TABLE `tblstudents` (
  `SymbolNo` int(10) NOT NULL,
  `ClassId` int(11) NOT NULL,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `RegistrationNo` varchar(255) NOT NULL,
  `Program` varchar(20) NOT NULL,
  `Year(Batch)` year(4) NOT NULL,
  `emailAddress` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `webauthn_setup_token` varchar(255) DEFAULT NULL,
  `webauthn_setup_token_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblstudents`
--

INSERT INTO `tblstudents` (`SymbolNo`, `ClassId`, `firstName`, `lastName`, `RegistrationNo`, `Program`, `Year(Batch)`, `emailAddress`, `password`, `webauthn_setup_token`, `webauthn_setup_token_expiry`) VALUES
(21070562, 1, 'Nikit', 'Pandey', '4-5-6', 'computer', '2020', 'nikitpandey18@gmail.com', 'cd41287b93a9317b6b2d1da8bec1def1', 'b3cf640ed27cb36e8b7bb585cc70628aabb0a4bd37792945d27698f2ebb2785e', '2025-06-14 06:21:15'),
(21070565, 1, 'Ranjan', 'Paudel', '1-2-3', 'computer', '2020', 'mvp1lazer@gmail.com', 'd8578edf8458ce06fbc5bb76a58c5ca4', NULL, NULL),
(21070575, 1, 'Shiva', 'Bhandari', '4-5-6', 'computer', '2020', 'shivabhandari292@gmail.com', 'd8578edf8458ce06fbc5bb76a58c5ca4', NULL, NULL),
(21070579, 1, 'Suman', 'Sharma', '4-5-6', 'computer', '2020', 'jrsuman2001@gmail.com', 'd8578edf8458ce06fbc5bb76a58c5ca4', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tblsubmissions`
--

CREATE TABLE `tblsubmissions` (
  `Id` int(11) NOT NULL,
  `AssignmentId` int(11) NOT NULL,
  `StudentId` int(11) NOT NULL,
  `SubmittedFile` varchar(255) NOT NULL,
  `SubmissionDate` datetime NOT NULL,
  `Grade` varchar(10) DEFAULT NULL,
  `Feedback` text DEFAULT NULL,
  `IsLate` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblsubmissions`
--

INSERT INTO `tblsubmissions` (`Id`, `AssignmentId`, `StudentId`, `SubmittedFile`, `SubmissionDate`, `Grade`, `Feedback`, `IsLate`) VALUES
(0, 3, 21070565, 'submission_684940e9458458.42493575.pdf', '2025-06-11 10:40:09', NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tblvirtualclass`
--

CREATE TABLE `tblvirtualclass` (
  `Id` int(11) NOT NULL,
  `courseId` int(11) NOT NULL,
  `teacherId` int(10) NOT NULL,
  `jitsiLink` varchar(255) NOT NULL,
  `classDate` datetime NOT NULL,
  `isActive` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblvirtualclass`
--

INSERT INTO `tblvirtualclass` (`Id`, `courseId`, `teacherId`, `jitsiLink`, `classDate`, `isActive`) VALUES
(8, 11, 16, 'https://meet.jit.si/ClassPlus-684932f6e533d', '2025-06-11 14:25:00', 0),
(9, 11, 16, 'ClassPlus-684953d6a9f9d', '2025-06-11 15:47:00', 0),
(10, 11, 16, 'ClassPlus-684ac40b8d65e', '2025-06-12 18:00:00', 0),
(11, 11, 16, 'ClassPlus-684baa7de897f', '2025-06-13 10:20:00', 0),
(12, 11, 16, 'ClassPlus-685676e62b6e3', '2025-06-21 15:00:00', 0);

-- --------------------------------------------------------

--
-- Table structure for table `tblvirtualsessions`
--

CREATE TABLE `tblvirtualsessions` (
  `id` int(11) NOT NULL,
  `virtualclassId` int(11) NOT NULL,
  `studentId` int(10) NOT NULL,
  `joinTime` datetime NOT NULL,
  `leaveTime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblvirtualsessions`
--

INSERT INTO `tblvirtualsessions` (`id`, `virtualclassId`, `studentId`, `joinTime`, `leaveTime`) VALUES
(1, 9, 21070565, '2025-06-11 15:47:15', NULL),
(2, 9, 21070565, '2025-06-11 15:49:20', NULL),
(3, 9, 21070565, '2025-06-11 15:49:43', '2025-06-11 15:50:18'),
(4, 12, 21070565, '2025-06-21 15:00:32', NULL),
(5, 12, 21070565, '2025-06-21 15:04:15', NULL),
(6, 12, 21070565, '2025-06-21 15:05:23', NULL),
(7, 12, 21070565, '2025-06-21 15:08:17', NULL),
(8, 12, 21070565, '2025-06-21 15:11:36', NULL),
(9, 12, 21070565, '2025-06-21 15:19:05', '2025-06-21 15:19:49'),
(10, 12, 21070565, '2025-06-21 15:20:09', '2025-06-21 15:25:15');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbladmin`
--
ALTER TABLE `tbladmin`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `tblassignments`
--
ALTER TABLE `tblassignments`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `tblassignments_ibfk_1` (`ClassArmId`),
  ADD KEY `tblassignments_ibfk_2` (`UploadedBy`);

--
-- Indexes for table `tblattendance`
--
ALTER TABLE `tblattendance`
  ADD PRIMARY KEY (`Id`),
  ADD UNIQUE KEY `unique_attendance` (`SymbolNo`,`CourseId`,`dateTimeTaken`),
  ADD KEY `tblattendance_ibfk_2` (`CourseId`);

--
-- Indexes for table `tblattendance_sessions`
--
ALTER TABLE `tblattendance_sessions`
  ADD PRIMARY KEY (`Id`),
  ADD UNIQUE KEY `UniqueCode` (`UniqueCode`),
  ADD KEY `CourseId` (`CourseId`);

--
-- Indexes for table `tblattendance_temp`
--
ALTER TABLE `tblattendance_temp`
  ADD PRIMARY KEY (`Id`),
  ADD UNIQUE KEY `SessionId` (`SessionId`,`SymbolNo`),
  ADD KEY `SymbolNo` (`SymbolNo`);

--
-- Indexes for table `tblclass`
--
ALTER TABLE `tblclass`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `tblclassarms`
--
ALTER TABLE `tblclassarms`
  ADD PRIMARY KEY (`Id`),
  ADD UNIQUE KEY `CourseCode` (`CourseCode`),
  ADD KEY `tblclassarms_ibfk_1` (`ClassId`);

--
-- Indexes for table `tblclassteacher`
--
ALTER TABLE `tblclassteacher`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `tblnotes`
--
ALTER TABLE `tblnotes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `courseId` (`courseId`),
  ADD KEY `uploadedBy` (`uploadedBy`);

--
-- Indexes for table `tblstudents`
--
ALTER TABLE `tblstudents`
  ADD PRIMARY KEY (`SymbolNo`),
  ADD KEY `tblstudents_ibfk_1` (`ClassId`);

--
-- Indexes for table `tblsubmissions`
--
ALTER TABLE `tblsubmissions`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `tblvirtualclass`
--
ALTER TABLE `tblvirtualclass`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `tblvirtualclass_ibfk_1` (`courseId`),
  ADD KEY `tblvirtualclass_ibfk_2` (`teacherId`);

--
-- Indexes for table `tblvirtualsessions`
--
ALTER TABLE `tblvirtualsessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `virtualclassId` (`virtualclassId`),
  ADD KEY `studentId` (`studentId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbladmin`
--
ALTER TABLE `tbladmin`
  MODIFY `Id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tblassignments`
--
ALTER TABLE `tblassignments`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tblattendance`
--
ALTER TABLE `tblattendance`
  MODIFY `Id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=220;

--
-- AUTO_INCREMENT for table `tblattendance_sessions`
--
ALTER TABLE `tblattendance_sessions`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `tblattendance_temp`
--
ALTER TABLE `tblattendance_temp`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblclass`
--
ALTER TABLE `tblclass`
  MODIFY `Id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tblclassarms`
--
ALTER TABLE `tblclassarms`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tblclassteacher`
--
ALTER TABLE `tblclassteacher`
  MODIFY `Id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `tblnotes`
--
ALTER TABLE `tblnotes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tblstudents`
--
ALTER TABLE `tblstudents`
  MODIFY `SymbolNo` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21070580;

--
-- AUTO_INCREMENT for table `tblvirtualclass`
--
ALTER TABLE `tblvirtualclass`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tblvirtualsessions`
--
ALTER TABLE `tblvirtualsessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tblassignments`
--
ALTER TABLE `tblassignments`
  ADD CONSTRAINT `tblassignments_ibfk_1` FOREIGN KEY (`ClassArmId`) REFERENCES `tblclassarms` (`Id`),
  ADD CONSTRAINT `tblassignments_ibfk_2` FOREIGN KEY (`UploadedBy`) REFERENCES `tblclassteacher` (`Id`);

--
-- Constraints for table `tblattendance`
--
ALTER TABLE `tblattendance`
  ADD CONSTRAINT `tblattendance_ibfk_1` FOREIGN KEY (`SymbolNo`) REFERENCES `tblstudents` (`SymbolNo`) ON DELETE CASCADE,
  ADD CONSTRAINT `tblattendance_ibfk_2` FOREIGN KEY (`CourseId`) REFERENCES `tblclassarms` (`Id`) ON DELETE CASCADE;

--
-- Constraints for table `tblattendance_sessions`
--
ALTER TABLE `tblattendance_sessions`
  ADD CONSTRAINT `tblattendance_sessions_ibfk_1` FOREIGN KEY (`CourseId`) REFERENCES `tblclassarms` (`Id`) ON DELETE CASCADE;

--
-- Constraints for table `tblattendance_temp`
--
ALTER TABLE `tblattendance_temp`
  ADD CONSTRAINT `tblattendance_temp_ibfk_1` FOREIGN KEY (`SessionId`) REFERENCES `tblattendance_sessions` (`Id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tblattendance_temp_ibfk_2` FOREIGN KEY (`SymbolNo`) REFERENCES `tblstudents` (`SymbolNo`) ON DELETE CASCADE;

--
-- Constraints for table `tblclassarms`
--
ALTER TABLE `tblclassarms`
  ADD CONSTRAINT `tblclassarms_ibfk_1` FOREIGN KEY (`ClassId`) REFERENCES `tblclass` (`Id`) ON DELETE CASCADE;

--
-- Constraints for table `tblnotes`
--
ALTER TABLE `tblnotes`
  ADD CONSTRAINT `tblnotes_ibfk_1` FOREIGN KEY (`courseId`) REFERENCES `tblclassarms` (`Id`),
  ADD CONSTRAINT `tblnotes_ibfk_2` FOREIGN KEY (`uploadedBy`) REFERENCES `tblclassteacher` (`Id`);

--
-- Constraints for table `tblstudents`
--
ALTER TABLE `tblstudents`
  ADD CONSTRAINT `tblstudents_ibfk_1` FOREIGN KEY (`ClassId`) REFERENCES `tblclass` (`Id`) ON DELETE CASCADE;

--
-- Constraints for table `tblvirtualclass`
--
ALTER TABLE `tblvirtualclass`
  ADD CONSTRAINT `tblvirtualclass_ibfk_1` FOREIGN KEY (`courseId`) REFERENCES `tblclassarms` (`Id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tblvirtualclass_ibfk_2` FOREIGN KEY (`teacherId`) REFERENCES `tblclassteacher` (`Id`) ON DELETE CASCADE;

--
-- Constraints for table `tblvirtualsessions`
--
ALTER TABLE `tblvirtualsessions`
  ADD CONSTRAINT `tblvirtualsessions_ibfk_1` FOREIGN KEY (`virtualclassId`) REFERENCES `tblvirtualclass` (`Id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tblvirtualsessions_ibfk_2` FOREIGN KEY (`studentId`) REFERENCES `tblstudents` (`SymbolNo`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
