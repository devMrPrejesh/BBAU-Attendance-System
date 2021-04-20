-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 20, 2021 at 02:34 PM
-- Server version: 10.4.18-MariaDB
-- PHP Version: 8.0.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bbau_attendance_system`
--
DROP DATABASE IF EXISTS `bbau_attendance_system`;
CREATE DATABASE `bbau_attendance_system`;
USE `bbau_attendance_system`;

--
-- Setting value for global variable
--
SET GLOBAL max_allowed_packet=20971520;
SET GLOBAL event_scheduler = TRUE;


DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `isHolidayonStudentAttendance` ()  BEGIN
    DECLARE userId, periodSize, period, cursor_flag INT;
    DECLARE cur1 CURSOR FOR  SELECT student_id, number_of_subjects FROM student;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET cursor_flag = 1;
    SET @recCount = (SELECT COUNT(*) FROM holiday_calendar WHERE date=CURRENT_DATE());
    If @recCount > 0 THEN
    	OPEN cur1;
        	FETCH cur1 INTO userId, periodSize;
            REPEAT
                SET period = 0;
                WHILE period < periodSize DO
                	SET period = period + 1;
        			INSERT INTO student_attendance VALUES (userId, 'HOLIDAY', 'holiday', CURRENT_DATE(), period);
                END WHILE;
                FETCH cur1 INTO userId, periodSize;
        	UNTIL cursor_flag = 1 END REPEAT;
        CLOSE cur1;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `isHolidayonTeacherAttendance` ()  BEGIN
    DECLARE userId, periodSize, period, cursor_flag INT;
    DECLARE cur1 CURSOR FOR  SELECT teacher_id, number_of_classes FROM teacher;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET cursor_flag = 1;
    SET @recCount = (SELECT COUNT(*) FROM holiday_calendar WHERE date=CURRENT_DATE());
    If @recCount > 0 THEN
    	OPEN cur1;
        	FETCH cur1 INTO userId, periodSize;
            REPEAT
                SET period = 0;
                WHILE period < periodSize DO
                	SET period = period + 1;
        			INSERT INTO teacher_attendance VALUES (userId, 'HOLIDAY', 'holiday', CURRENT_DATE(), period);
                END WHILE;
                FETCH cur1 INTO userId, periodSize;
        	UNTIL cursor_flag = 1 END REPEAT;
        CLOSE cur1;
    END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `admin_name` varchar(30) NOT NULL,
  `superior` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `classroom`
--

CREATE TABLE `classroom` (
  `teacher_id` int(11) NOT NULL,
  `class` varchar(20) NOT NULL,
  `day` int(11) NOT NULL,
  `period` int(11) NOT NULL,
  `subject` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `holiday_calendar`
--

CREATE TABLE `holiday_calendar` (
  `date` date NOT NULL,
  `title` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `leave_system`
--

CREATE TABLE `leave_system` (
  `leave_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `reason` varchar(150) NOT NULL,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  `status` varchar(10) NOT NULL,
  `remarks` varchar(100) NOT NULL,
  `attachment_type` varchar(25) NOT NULL,
  `attachment_data` mediumblob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `student_id` int(11) NOT NULL,
  `student_name` varchar(50) NOT NULL,
  `department` varchar(20) NOT NULL,
  `batch` int(11) NOT NULL,
  `semester` int(11) NOT NULL,
  `class` varchar(20) NOT NULL,
  `number_of_subjects` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `student_attendance`
--

CREATE TABLE `student_attendance` (
  `student_id` int(11) NOT NULL,
  `subject` varchar(30) NOT NULL,
  `status` varchar(10) NOT NULL,
  `date` date NOT NULL,
  `period` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `teacher`
--

CREATE TABLE `teacher` (
  `teacher_id` int(11) NOT NULL,
  `teacher_name` varchar(50) NOT NULL,
  `department` varchar(30) NOT NULL,
  `number_of_classes` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `teacher_attendance`
--

CREATE TABLE `teacher_attendance` (
  `teacher_id` int(11) NOT NULL,
  `subject` varchar(30) NOT NULL,
  `status` varchar(10) NOT NULL,
  `date` date NOT NULL,
  `period` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `email_id` varchar(30) NOT NULL,
  `password` varchar(15) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `classroom`
--
ALTER TABLE `classroom`
  ADD PRIMARY KEY (`teacher_id`,`class`,`day`);

--
-- Indexes for table `holiday_calendar`
--
ALTER TABLE `holiday_calendar`
  ADD PRIMARY KEY (`date`);

--
-- Indexes for table `leave_system`
--
ALTER TABLE `leave_system`
  ADD PRIMARY KEY (`leave_id`),
  ADD KEY `fk_leave_system_teacher` (`teacher_id`),
  ADD KEY `fk_leave_system_student` (`student_id`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`student_id`) USING BTREE,
  ADD KEY `fk_proctor` (`teacher_id`);

--
-- Indexes for table `student_attendance`
--
ALTER TABLE `student_attendance`
  ADD PRIMARY KEY (`student_id`,`date`,`period`);

--
-- Indexes for table `teacher`
--
ALTER TABLE `teacher`
  ADD PRIMARY KEY (`teacher_id`);

--
-- Indexes for table `teacher_attendance`
--
ALTER TABLE `teacher_attendance`
  ADD PRIMARY KEY (`teacher_id`,`date`,`period`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`email_id`) USING BTREE,
  ADD UNIQUE KEY `unique_users` (`user_id`,`role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `leave_system`
--
ALTER TABLE `leave_system`
  MODIFY `leave_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `classroom`
--
ALTER TABLE `classroom`
  ADD CONSTRAINT `classroom_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teacher` (`teacher_id`),
  ADD CONSTRAINT `fk_class_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `teacher` (`teacher_id`),
  ADD CONSTRAINT `fk_teacher_teacher_id` FOREIGN KEY (`teacher_id`) REFERENCES `teacher` (`teacher_id`);

--
-- Constraints for table `leave_system`
--
ALTER TABLE `leave_system`
  ADD CONSTRAINT `fk_leave_system_student` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`),
  ADD CONSTRAINT `fk_leave_system_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `teacher` (`teacher_id`);

--
-- Constraints for table `student`
--
ALTER TABLE `student`
  ADD CONSTRAINT `fk_proctor` FOREIGN KEY (`teacher_id`) REFERENCES `teacher` (`teacher_id`);

--
-- Constraints for table `student_attendance`
--
ALTER TABLE `student_attendance`
  ADD CONSTRAINT `fk_student` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`);

--
-- Constraints for table `teacher_attendance`
--
ALTER TABLE `teacher_attendance`
  ADD CONSTRAINT `fk_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `teacher` (`teacher_id`);

DELIMITER $$
--
-- Events
--
CREATE DEFINER=`root`@`localhost` EVENT `performHolidayCheckStudent` ON SCHEDULE EVERY 1 DAY STARTS '2021-04-14 00:00:00' ON COMPLETION NOT PRESERVE ENABLE DO CALL isHolidayonStudentAttendance()$$

CREATE DEFINER=`root`@`localhost` EVENT `performHolidayCheckTeacher` ON SCHEDULE EVERY 1 DAY STARTS '2021-04-14 00:00:00' ON COMPLETION NOT PRESERVE ENABLE DO CALL isHolidayonTeacherAttendance()$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
