
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
  `class` varchar(10) NOT NULL,
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
  `title` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
  `status` varchar(10) NOT NULL DEFAULT 'INITIATED',
  `remarks` varchar(100) NOT NULL,
  `attachment_type` varchar(25) DEFAULT NULL,
  `attachment_path` varchar(40) DEFAULT NULL
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
  `number_of_classes` int(11) NOT NULL,
  `class` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `teacher_attendance`
--

CREATE TABLE `teacher_attendance` (
  `teacher_id` int(11) NOT NULL,
  `status` varchar(10) NOT NULL,
  `date` date NOT NULL,
  `period` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `email` varchar(30) NOT NULL,
  `password` varchar(15) NOT NULL,
  `role` varchar(10) NOT NULL,
  `user_id` int(11) NOT NULL,
  `first_name` varchar(20) NOT NULL,
  `profile_photo` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Triggers
--

DELIMITER $$

CREATE TRIGGER `checkDurationsIntersectBeforeInsertLeaveSystem` BEFORE INSERT ON `leave_system`
 FOR EACH ROW BEGIN
	DECLARE teacher_id INT;
    DECLARE cur CURSOR FOR  SELECT teacher_id FROM student WHERE student_id = NEW.student_id;
    
    SET @recCount = (SELECT COUNT(*) FROM leave_system WHERE student_id = NEW.student_id AND NOT ((from_date > NEW.from_date AND from_date > NEW.to_date) OR (to_date < NEW.from_date AND to_date < NEW.to_date)));
    IF NEW.from_date < CURRENT_DATE() OR NEW.from_date > NEW.to_date OR @recCount > 0 THEN
    	SIGNAL SQLSTATE '45000';
    END IF;
    
    OPEN cur;
        FETCH cur INTO teacher_id;
        IF teacher_id != NEW.teacher_id THEN
        	SIGNAL SQLSTATE '45000';
    	END IF;
    CLOSE cur;
END$$

CREATE TRIGGER `checkPeriodSizeAndDateBeforeInsertStudentAttendance` BEFORE INSERT ON `student_attendance`
 FOR EACH ROW BEGIN
    DECLARE periodSize INT;
    DECLARE cur CURSOR FOR  SELECT number_of_subjects FROM student WHERE student_id = NEW.student_id;
    
    OPEN cur;
        FETCH cur INTO periodSize;
        IF 0 > NEW.period OR periodSize < NEW.period OR NEW.date < CURRENT_DATE() THEN
            SIGNAL SQLSTATE '45000';
        END IF;
	CLOSE cur;
END$$

CREATE TRIGGER `checkPeriodSizeAndDateBeforeInsertTeacherAttendance` BEFORE INSERT ON `teacher_attendance`
 FOR EACH ROW BEGIN
    DECLARE periodSize INT;
    DECLARE cur CURSOR FOR  SELECT number_of_classes FROM teacher WHERE teacher_id = NEW.teacher_id;
    
    OPEN cur;
        FETCH cur INTO periodSize;
    CLOSE cur;
    
    IF 0 > NEW.period OR periodSize < NEW.period OR NEW.date < CURRENT_DATE() THEN
    	SIGNAL SQLSTATE '45000';
    END IF;
END$$

CREATE TRIGGER `checkPeriodSizeBeforeInsertClassroom` BEFORE INSERT ON `classroom`
 FOR EACH ROW BEGIN
    DECLARE periodSize INT;
    DECLARE cur0 CURSOR FOR  SELECT number_of_classes FROM teacher WHERE teacher_id = NEW.teacher_id;
    DECLARE cur1 CURSOR FOR SELECT MAX(number_of_subjects) FROM student WHERE class = NEW.class;
    
    OPEN cur0;
        FETCH cur0 INTO periodSize;
        IF 0 > NEW.period OR periodSize < NEW.period THEN
            SIGNAL SQLSTATE '45000';
        END IF;
	CLOSE cur0;
    
    OPEN cur1;
        FETCH cur1 INTO periodSize;
        IF 0 > NEW.period OR periodSize < NEW.period THEN
            SIGNAL SQLSTATE '45000';
        END IF;
	CLOSE cur1;
END$$

CREATE TRIGGER `checkPeriodSizeBeforeUpdateClassroom` BEFORE UPDATE ON `classroom`
 FOR EACH ROW BEGIN
    DECLARE periodSize INT;
    DECLARE cur0 CURSOR FOR  SELECT number_of_classes FROM teacher WHERE teacher_id = NEW.teacher_id;
    DECLARE cur1 CURSOR FOR  SELECT MAX(number_of_subjects) FROM student WHERE class = NEW.class;
    
    OPEN cur0;
        FETCH cur0 INTO periodSize;
        IF 0 > NEW.period OR periodSize < NEW.period THEN
            SIGNAL SQLSTATE '45000';
        END IF;
	CLOSE cur0;
    
    OPEN cur1;
        FETCH cur1 INTO periodSize;
        IF 0 > NEW.period OR periodSize < NEW.period THEN
            SIGNAL SQLSTATE '45000';
        END IF;
	CLOSE cur1;
END$$

CREATE TRIGGER `checkStatusBeforeUpdateStudentAttendance` BEFORE UPDATE ON `student_attendance`
 FOR EACH ROW BEGIN
    If OLD.status = "HOLIDAY" THEN
		SIGNAL SQLSTATE '45000';
    ELSEIF OLD.status = "LEAVE" AND NEW.status != "HOLIDAY" THEN
    	SIGNAL SQLSTATE '45000';
	END IF;
END$$

CREATE TRIGGER `checkStatusBeforeUpdateTeacherAttendance` BEFORE UPDATE ON `teacher_attendance`
 FOR EACH ROW BEGIN
    If OLD.status = "HOLIDAY" THEN
		SIGNAL SQLSTATE '45000';
    ELSEIF OLD.status = "LEAVE" AND NEW.status != "HOLIDAY" THEN
    	SIGNAL SQLSTATE '45000';
	END IF;
END$$

DELIMITER ;

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
        SET periodValue = 0;
        WHILE periodValue < periodSize DO
        	SET periodValue = periodValue + 1;
        	SET @recCount = (SELECT COUNT(*) FROM student_attendance WHERE student_id = studentId AND date = fromDate AND period = periodValue);
            
            IF @recCount > 0 THEN
            	UPDATE student_attendance SET STATUS = 'LEAVE' WHERE student_id = studentId AND date = fromDate AND period = periodValue;
            ELSE
               	INSERT INTO student_attendance VALUES (studentId, 'LEAVE', fromDate, periodValue);
            END IF;
        END WHILE;
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

CREATE DEFINER=`root`@`localhost` EVENT `performHolidayCheckStudent` ON SCHEDULE EVERY 1 DAY STARTS '2021-04-14 00:00:00' ON COMPLETION NOT PRESERVE ENABLE DO CALL isHolidayonStudentAttendance()$$

CREATE DEFINER=`root`@`localhost` EVENT `performHolidayCheckTeacher` ON SCHEDULE EVERY 1 DAY STARTS '2021-04-14 00:00:00' ON COMPLETION NOT PRESERVE ENABLE DO CALL isHolidayonTeacherAttendance()$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Indexes for table `classroom`
--

ALTER TABLE `classroom`
  ADD PRIMARY KEY (`teacher_id`,`class`,`day`),
  ADD UNIQUE KEY `UNIQUE_CLASSROOM_CLASSDAYPERIOD` (`class`,`day`,`period`),
  ADD KEY `IDX_CLASSROOM_SUBJECT` (`subject`),
  ADD KEY `IDX_CLASSROOM_CLASS` (`class`),
  ADD CONSTRAINT `CHECK_CLASSROOM_DAY` CHECK (`day` >= 0 AND `day` <= 4);

-- --------------------------------------------------------

--
-- Indexes and Constraints for table `holiday_calendar`
--

ALTER TABLE `holiday_calendar`
  ADD PRIMARY KEY (`date`);

-- --------------------------------------------------------

--
-- Indexes, Auto Increment and Constraints for table `leave_system`
--

ALTER TABLE `leave_system`
  ADD PRIMARY KEY (`leave_id`),
  MODIFY `leave_id` int(11) NOT NULL AUTO_INCREMENT,
  ADD UNIQUE KEY `UNIQUE_LEAVESYSTEM_STUDENTIDFROMDATE` (`student_id`,`from_date`),
  ADD UNIQUE KEY `UNIQUE_LEAVESYSTEM_STUDENTIDTODATE` (`student_id`,`to_date`),
  ADD CONSTRAINT `CHECK_LEAVESYSTEM_DURATION` CHECK (`from_date` <= `to_date`),
  ADD CONSTRAINT `CHECK_LEAVESYSTEM_STATUS` CHECK (STATUS IN ('INITIATED', 'READ', 'APPROVED', 'REJECTED'));

-- --------------------------------------------------------

--
-- Indexes and Constraints for table `student`
--

ALTER TABLE `student`
  ADD PRIMARY KEY (`student_id`);

-- --------------------------------------------------------

--
-- Indexes and Constraints for table `student_attendance`
--

ALTER TABLE `student_attendance`
  ADD PRIMARY KEY (`student_id`,`date`,`period`),
  ADD CONSTRAINT `CHECK_STUDENTATTENDANCE_STATUS` CHECK (`status` IN ('PRESENT', 'ABSENT', 'HOLIDAY', 'LEAVE'));

-- --------------------------------------------------------

--
-- Indexes and Constraints for table `teacher`
--

ALTER TABLE `teacher`
  ADD PRIMARY KEY (`teacher_id`),
  ADD UNIQUE KEY `UNIQUE_TEACHER_DEPARTMENTCLASS` (`department`,`class`);

-- --------------------------------------------------------

--
-- Indexes and Constraints for table `teacher_attendance`
--

ALTER TABLE `teacher_attendance`
  ADD PRIMARY KEY (`teacher_id`,`date`,`period`),
  ADD CONSTRAINT `CHECK_TEACHERATTENDANCE_STATUS` CHECK (`status` IN ('PRESENT', 'ABSENT', 'HOLIDAY', 'LEAVE'));

-- --------------------------------------------------------

--
-- Indexes and Constraints for table `user`
--

ALTER TABLE `user`
  ADD PRIMARY KEY (`email`),
  ADD UNIQUE KEY `UNIQUE_USER_ROLEUSERID` (`role`,`user_id`),
  ADD CONSTRAINT `CHECK_USER_ROLE` CHECK (`role` IN ('STUDENT', 'TEACHER'));

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
-- FOREIGN KEY for table `teacher`
--

ALTER TABLE `teacher`
  ADD CONSTRAINT `FK_TEACHER_CLASS` FOREIGN KEY (`class`) REFERENCES `classroom` (`class`);

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
