
--
-- Database Structure
--


SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `attendance_database`
--

DROP DATABASE IF EXISTS `attendance_database`;
CREATE DATABASE `attendance_database` CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `attendance_database`;

-- --------------------------------------------------------

--
-- Table structure for table `classroom`
--

CREATE TABLE `classroom` (
  `teacher_id` int(11) NOT NULL,
  `class` varchar(20) NOT NULL,
  `day` int(11) NOT NULL,
  `period` int(11) NOT NULL,
  `subject` varchar(30) NOT NULL,
  PRIMARY KEY (`teacher_id`,`class`,`day`),
  UNIQUE KEY `UNIQUE_CLASSROOM_CLASSDAYPERIOD` (`class`,`day`,`period`),
  KEY `IDX_CLASSROOM_CLASS` (`class`),
  CONSTRAINT `CHECK_CLASSROOM_DAY` CHECK (`day` >= 0 and `day` <= 4)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `holiday_calendar`
--

CREATE TABLE `holiday_calendar` (
  `date` date PRIMARY KEY,
  `title` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `leave_system`
--

CREATE TABLE `leave_system` (
  `leave_id` int(11) PRIMARY KEY AUTO_INCREMENT,
  `teacher_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `reason` varchar(150) NOT NULL,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'INITIATED',
  `status_change` int(11) NOT NULL DEFAULT 1,
  `remarks` varchar(100) NOT NULL,
  `attachment_path` varchar(40) DEFAULT NULL,
  UNIQUE KEY `UNIQUE_LEAVESYSTEM_STUDENTIDFROMDATE` (`student_id`,`from_date`),
  UNIQUE KEY `UNIQUE_LEAVESYSTEM_STUDENTIDTODATE` (`student_id`,`to_date`),
  CONSTRAINT `CHECK_LEAVESYSTEM_DURATION` CHECK (`from_date` <= `to_date`),
  CONSTRAINT `CHECK_LEAVESYSTEM_STATUSCHANGE` CHECK (`status_change` in (1, 0, -1)),
  CONSTRAINT `CHECK_LEAVESYSTEM_STATUS` CHECK (`status` in ('INITIATED','READ','APPROVED','REJECTED'))
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `student_id` int(11) PRIMARY KEY,
  `student_name` varchar(50) NOT NULL,
  `department` varchar(20) NOT NULL,
  `batch` int(11) NOT NULL,
  `semester` int(11) NOT NULL,
  `class` varchar(10) NOT NULL,
  `number_of_subjects` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `student_attendance`
--

CREATE TABLE `student_attendance` (
  `student_id` int(11) NOT NULL,
  `status` varchar(10) NOT NULL,
  `date` date NOT NULL,
  `period` int(11) NOT NULL,
  PRIMARY KEY (`student_id`,`date`,`period`),
  CONSTRAINT `CHECK_STUDENTATTENDANCE_STATUS` CHECK (`status` in ('PRESENT','ABSENT','HOLIDAY','LEAVE'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `teacher`
--

CREATE TABLE `teacher` (
  `teacher_id` int(11) PRIMARY KEY,
  `teacher_name` varchar(50) NOT NULL,
  `department` varchar(30) NOT NULL,
  `number_of_classes` int(11) NOT NULL,
  `class` varchar(10) DEFAULT NULL,
  UNIQUE KEY `UNIQUE_TEACHER_DEPARTMENTCLASS` (`department`,`class`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `teacher_attendance`
--

CREATE TABLE `teacher_attendance` (
  `teacher_id` int(11) NOT NULL,
  `status` varchar(10) NOT NULL,
  `date` date NOT NULL,
  `period` int(11) NOT NULL,
  PRIMARY KEY (`teacher_id`,`date`,`period`),
  CONSTRAINT `CHECK_TEACHERATTENDANCE_STATUS` CHECK (`status` in ('PRESENT','ABSENT','HOLIDAY','LEAVE'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `email` varchar(30) PRIMARY KEY,
  `password` varchar(15) NOT NULL,
  `role` varchar(10) NOT NULL,
  `user_id` int(11) NOT NULL,
  `first_name` varchar(20) NOT NULL,
  `profile_photo` varchar(40) DEFAULT NULL,
  UNIQUE KEY `UNIQUE_USER_ROLEUSERID` (`role`,`user_id`),
  CONSTRAINT `CHECK_USER_ROLE` CHECK (`role` in ('STUDENT','TEACHER', 'ADMIN'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Procedures
--

DELIMITER $$

CREATE DEFINER=`root`@`localhost` PROCEDURE `approveLeave`(IN `leaveId` INT)
BEGIN
  DECLARE studentId, periodSize, periodValue INT;
  DECLARE fromDate, toDate DATE;
  DECLARE cur0 CURSOR FOR  SELECT student_id, from_date, to_date FROM leave_system WHERE leave_id = leaveId;
  DECLARE cur1 CURSOR FOR  SELECT number_of_subjects FROM student WHERE student_id = studentId;
  
  SET @recCount = (SELECT COUNT(*) FROM leave_system WHERE leave_id = leaveId AND status IN ("INITIATED", "READ"));
  
  IF @recCount = 0 THEN
    SIGNAL SQLSTATE '45000';
  END IF;
  
  OPEN cur0;
    FETCH cur0 INTO studentId, fromDate, toDate;
  CLOSE cur0;
  
  OPEN cur1;
    FETCH cur1 INTO periodSize;
  CLOSE cur1;
  
  WHILE fromDate <= toDate DO
    IF 1 < DAYOFWEEK(fromDate) AND DAYOFWEEK(fromDate) < 7 THEN
      SET periodValue = 0;
      WHILE periodValue < periodSize DO
        SET periodValue = periodValue + 1;
        INSERT INTO student_attendance VALUES (studentId, 'LEAVE', fromDate, periodValue);
      END WHILE;
    END IF;
    SET fromDate = DATE_ADD(fromDate, INTERVAL 1 DAY);
	END WHILE;
  UPDATE leave_system SET status = "APPROVED" WHERE leave_id = leaveId;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `isHolidayonStudentAttendance`()
BEGIN
  DECLARE userId, periodSize, period, cursor_flag INT;
  DECLARE cur CURSOR FOR  SELECT student_id, number_of_subjects FROM student;
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET cursor_flag = 1;
  SET @recCount0 = (SELECT COUNT(*) FROM holiday_calendar WHERE date=CURRENT_DATE());
  IF @recCount0 > 0 THEN
    OPEN cur;
     	FETCH cur INTO userId, periodSize;
      REPEAT
        SET period = 0;
        WHILE period < periodSize DO
          SET period = period + 1;
          SET @recCount1 = (SELECT COUNT(*) FROM student_attendance WHERE student_id = userId AND `date` = CURRENT_DATE() AND `period` = period);
    			IF @recCount1 > 0 THEN
           	UPDATE student_attendance SET STATUS = 'HOLIDAY' WHERE student_id = userId AND `date` = CURRENT_DATE() AND `period` = period;
          ELSE
           	INSERT INTO student_attendance VALUES (userId, 'HOLIDAY', 'holiday', CURRENT_DATE(), period);
          END IF;
        END WHILE;
        FETCH cur INTO userId, periodSize;
      UNTIL cursor_flag = 1 END REPEAT;
    CLOSE cur;
  END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `isHolidayonTeacherAttendance`()
BEGIN
  DECLARE userId, periodSize, period, cursor_flag INT;
  DECLARE cur CURSOR FOR  SELECT teacher_id, number_of_classes FROM teacher;
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET cursor_flag = 1;
  SET @recCount = (SELECT COUNT(*) FROM holiday_calendar WHERE date=CURRENT_DATE());
  IF @recCount > 0 THEN
    OPEN cur;
     	FETCH cur INTO userId, periodSize;
      REPEAT
        SET period = 0;
        WHILE period < periodSize DO
         	SET period = period + 1;
      		SET @recCount1 = (SELECT COUNT(*) FROM teacher_attendance WHERE teacher_id = userId AND `date` = CURRENT_DATE() AND `period` = period);
    			IF @recCount1 > 0 THEN
           	UPDATE teacher_attendance SET STATUS = 'HOLIADY' WHERE teacher_id = userId AND `date` = CURRENT_DATE() AND `period` = period;
          ELSE
           	INSERT INTO teacher_attendance VALUES (userId, 'HOLIDAY', 'holiday', CURRENT_DATE(), period);
          END IF;
        END WHILE;
        FETCH cur INTO userId, periodSize;
      UNTIL cursor_flag = 1 END REPEAT;
    CLOSE cur;
  END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Events
--

DELIMITER $$

CREATE DEFINER=`root`@`localhost` EVENT `performHolidayCheckStudent` ON SCHEDULE EVERY 1 DAY STARTS '2021-04-14 01:00:00' ON COMPLETION NOT PRESERVE ENABLE DO CALL isHolidayonStudentAttendance()$$

CREATE DEFINER=`root`@`localhost` EVENT `performHolidayCheckTeacher` ON SCHEDULE EVERY 1 DAY STARTS '2021-04-14 01:00:00' ON COMPLETION NOT PRESERVE ENABLE DO CALL isHolidayonTeacherAttendance()$$

DELIMITER ;

-- --------------------------------------------------------

--
-- FOREIGN KEY for table `classroom`
--

ALTER TABLE `classroom`
  ADD CONSTRAINT `FK_CLASSROOM_TEACHERID` FOREIGN KEY (`teacher_id`) REFERENCES `teacher` (`teacher_id`);

-- --------------------------------------------------------

--
-- FOREIGN KEY for table `leave_system`
--

ALTER TABLE `leave_system`
  ADD CONSTRAINT `FK_LEAVESYSTEM_STUDENTID` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`),
  ADD CONSTRAINT `FK_LEAVESYSTEM_TEACHERID` FOREIGN KEY (`teacher_id`) REFERENCES `teacher` (`teacher_id`);

-- --------------------------------------------------------

--
-- FOREIGN KEY for table `student`
--

ALTER TABLE `student`
  ADD CONSTRAINT `FK_STUDENT_CLASS` FOREIGN KEY (`class`) REFERENCES `classroom` (`class`),
  ADD CONSTRAINT `FK_STUDENT_TEACHERID` FOREIGN KEY (`teacher_id`) REFERENCES `teacher` (`teacher_id`);

-- --------------------------------------------------------

--
-- FOREIGN KEY for table `student_attendance`
--

ALTER TABLE `student_attendance`
  ADD CONSTRAINT `FK_STUDENTATTENDANCE_STUDENTID` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`);

-- --------------------------------------------------------

--
-- FOREIGN KEY for table `teacher_attendance`
--

ALTER TABLE `teacher_attendance`
  ADD CONSTRAINT `FK_TEACHERATTENDANCE_TEACHERID` FOREIGN KEY (`teacher_id`) REFERENCES `teacher` (`teacher_id`);

-- --------------------------------------------------------

--
-- Set global variable
--

SET GLOBAL event_scheduler = TRUE;

-- --------------------------------------------------------

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
