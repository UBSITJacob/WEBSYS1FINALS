-- Database: evelio_ams_db
CREATE DATABASE IF NOT EXISTS `evelio_ams_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `evelio_ams_db`;

-- Accounts
CREATE TABLE IF NOT EXISTS `accounts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `username` VARCHAR(100) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` ENUM('admin','teacher','student') NOT NULL,
  `first_login_required` TINYINT(1) NOT NULL DEFAULT 1,
  `person_type` ENUM('admin','teacher','student') NOT NULL,
  `person_id` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_person` (`person_type`, `person_id`)
);

-- Admins
CREATE TABLE IF NOT EXISTS `admins` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `faculty_id` VARCHAR(50) NOT NULL UNIQUE,
  `full_name` VARCHAR(150) NOT NULL,
  `username` VARCHAR(100) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `sex` ENUM('Male','Female') NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Teachers
CREATE TABLE IF NOT EXISTS `teachers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `faculty_id` VARCHAR(50) NOT NULL UNIQUE,
  `full_name` VARCHAR(150) NOT NULL,
  `username` VARCHAR(100) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `sex` ENUM('Male','Female') NOT NULL,
  `active` TINYINT(1) NOT NULL DEFAULT 1,
  `advisory_section_id` INT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sections
CREATE TABLE IF NOT EXISTS `sections` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `department` ENUM('JHS','SHS') NOT NULL,
  `grade_level` ENUM('Grade 7','Grade 8','Grade 9','Grade 10','Grade 11','Grade 12') NOT NULL,
  `strand` ENUM('HUMSS','TVL') NULL,
  `capacity` INT NOT NULL DEFAULT 40,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Students
CREATE TABLE IF NOT EXISTS `students` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `lrn` VARCHAR(50) NOT NULL UNIQUE,
  `department` ENUM('JHS','SHS') NOT NULL,
  `grade_level` ENUM('Grade 7','Grade 8','Grade 9','Grade 10','Grade 11','Grade 12') NOT NULL,
  `strand` ENUM('HUMSS','TVL') NULL,
  `student_type` ENUM('Old Student','New Student','Transferee') NOT NULL,
  `advisory_section_id` INT NULL,
  `family_name` VARCHAR(100) NOT NULL,
  `first_name` VARCHAR(100) NOT NULL,
  `middle_name` VARCHAR(100) NOT NULL,
  `suffix` VARCHAR(20) NULL,
  `birthdate` DATE NOT NULL,
  `birthplace` VARCHAR(150) NOT NULL,
  `religion` VARCHAR(100) NOT NULL,
  `civil_status` VARCHAR(50) NOT NULL,
  `sex` ENUM('Male','Female') NOT NULL,
  `mobile` VARCHAR(30) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `curr_house_street` VARCHAR(150) NOT NULL,
  `curr_barangay` VARCHAR(100) NOT NULL,
  `curr_city` VARCHAR(100) NOT NULL,
  `curr_province` VARCHAR(100) NOT NULL,
  `curr_zip` VARCHAR(10) NOT NULL,
  `perm_house_street` VARCHAR(150) NOT NULL,
  `perm_barangay` VARCHAR(100) NOT NULL,
  `perm_city` VARCHAR(100) NOT NULL,
  `perm_province` VARCHAR(100) NOT NULL,
  `perm_zip` VARCHAR(10) NOT NULL,
  `elem_name` VARCHAR(150) NULL,
  `elem_address` VARCHAR(200) NULL,
  `elem_year_graduated` VARCHAR(10) NULL,
  `last_school_name` VARCHAR(150) NULL,
  `last_school_address` VARCHAR(200) NULL,
  `jhs_name` VARCHAR(150) NULL,
  `jhs_address` VARCHAR(200) NULL,
  `jhs_year_graduated` VARCHAR(10) NULL,
  `guardian_last_name` VARCHAR(100) NOT NULL,
  `guardian_first_name` VARCHAR(100) NOT NULL,
  `guardian_middle_name` VARCHAR(100) NOT NULL,
  `guardian_contact` VARCHAR(30) NOT NULL,
  `guardian_occupation` VARCHAR(100) NOT NULL,
  `guardian_address` VARCHAR(200) NOT NULL,
  `guardian_relationship` VARCHAR(100) NOT NULL,
  `mother_last_name` VARCHAR(100) NOT NULL,
  `mother_first_name` VARCHAR(100) NOT NULL,
  `mother_middle_name` VARCHAR(100) NOT NULL,
  `mother_contact` VARCHAR(30) NOT NULL,
  `mother_occupation` VARCHAR(100) NOT NULL,
  `mother_address` VARCHAR(200) NOT NULL,
  `father_last_name` VARCHAR(100) NOT NULL,
  `father_first_name` VARCHAR(100) NOT NULL,
  `father_middle_name` VARCHAR(100) NOT NULL,
  `father_contact` VARCHAR(30) NOT NULL,
  `father_occupation` VARCHAR(100) NOT NULL,
  `father_address` VARCHAR(200) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_students_section` (`advisory_section_id`)
);

-- Applicants
CREATE TABLE IF NOT EXISTS `applicants` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `department` ENUM('JHS','SHS') NOT NULL,
  `grade_level` ENUM('Grade 7','Grade 8','Grade 9','Grade 10','Grade 11','Grade 12') NOT NULL,
  `strand` ENUM('HUMSS','TVL') NULL,
  `student_type` ENUM('Old Student','New Student','Transferee') NOT NULL,
  `family_name` VARCHAR(100) NOT NULL,
  `first_name` VARCHAR(100) NOT NULL,
  `middle_name` VARCHAR(100) NOT NULL,
  `suffix` VARCHAR(20) NULL,
  `birthdate` DATE NOT NULL,
  `birthplace` VARCHAR(150) NOT NULL,
  `religion` VARCHAR(100) NOT NULL,
  `civil_status` VARCHAR(50) NOT NULL,
  `sex` ENUM('Male','Female') NOT NULL,
  `mobile` VARCHAR(30) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `curr_house_street` VARCHAR(150) NOT NULL,
  `curr_barangay` VARCHAR(100) NOT NULL,
  `curr_city` VARCHAR(100) NOT NULL,
  `curr_province` VARCHAR(100) NOT NULL,
  `curr_zip` VARCHAR(10) NOT NULL,
  `perm_house_street` VARCHAR(150) NOT NULL,
  `perm_barangay` VARCHAR(100) NOT NULL,
  `perm_city` VARCHAR(100) NOT NULL,
  `perm_province` VARCHAR(100) NOT NULL,
  `perm_zip` VARCHAR(10) NOT NULL,
  `elem_name` VARCHAR(150) NULL,
  `elem_address` VARCHAR(200) NULL,
  `elem_year_graduated` VARCHAR(10) NULL,
  `last_school_name` VARCHAR(150) NULL,
  `last_school_address` VARCHAR(200) NULL,
  `jhs_name` VARCHAR(150) NULL,
  `jhs_address` VARCHAR(200) NULL,
  `jhs_year_graduated` VARCHAR(10) NULL,
  `lrn` VARCHAR(50) NOT NULL,
  `guardian_last_name` VARCHAR(100) NOT NULL,
  `guardian_first_name` VARCHAR(100) NOT NULL,
  `guardian_middle_name` VARCHAR(100) NOT NULL,
  `guardian_contact` VARCHAR(30) NOT NULL,
  `guardian_occupation` VARCHAR(100) NOT NULL,
  `guardian_address` VARCHAR(200) NOT NULL,
  `guardian_relationship` VARCHAR(100) NOT NULL,
  `mother_last_name` VARCHAR(100) NOT NULL,
  `mother_first_name` VARCHAR(100) NOT NULL,
  `mother_middle_name` VARCHAR(100) NOT NULL,
  `mother_contact` VARCHAR(30) NOT NULL,
  `mother_occupation` VARCHAR(100) NOT NULL,
  `mother_address` VARCHAR(200) NOT NULL,
  `father_last_name` VARCHAR(100) NOT NULL,
  `father_first_name` VARCHAR(100) NOT NULL,
  `father_middle_name` VARCHAR(100) NOT NULL,
  `father_contact` VARCHAR(30) NOT NULL,
  `father_occupation` VARCHAR(100) NOT NULL,
  `father_address` VARCHAR(200) NOT NULL,
  `status` ENUM('pending','approved','declined') NOT NULL DEFAULT 'pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Subjects
CREATE TABLE IF NOT EXISTS `subjects` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(50) NOT NULL UNIQUE,
  `name` VARCHAR(150) NOT NULL,
  `department` ENUM('JHS','SHS') NOT NULL,
  `grade_level` ENUM('Grade 7','Grade 8','Grade 9','Grade 10','Grade 11','Grade 12') NOT NULL,
  `strand` ENUM('HUMSS','TVL') NULL,
  `semester` ENUM('First','Second') NULL
);

-- Subject Loads
CREATE TABLE IF NOT EXISTS `subject_loads` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `teacher_id` INT NOT NULL,
  `subject_id` INT NOT NULL,
  `section_id` INT NOT NULL,
  `school_year` VARCHAR(20) NOT NULL,
  `semester` ENUM('First','Second') NULL,
  `active` TINYINT(1) NOT NULL DEFAULT 1,
  FOREIGN KEY (`teacher_id`) REFERENCES `teachers`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`subject_id`) REFERENCES `subjects`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`section_id`) REFERENCES `sections`(`id`) ON DELETE CASCADE
);

-- Enrollments
CREATE TABLE IF NOT EXISTS `enrollments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT NOT NULL,
  `subject_load_id` INT NOT NULL,
  `school_year` VARCHAR(20) NOT NULL,
  `semester` ENUM('First','Second') NULL,
  `status` ENUM('enrolled','dropped','completed') NOT NULL DEFAULT 'enrolled',
  FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`subject_load_id`) REFERENCES `subject_loads`(`id`) ON DELETE CASCADE
);

-- Grades
CREATE TABLE IF NOT EXISTS `grades` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `enrollment_id` INT NOT NULL,
  `grade` DECIMAL(5,2) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments`(`id`) ON DELETE CASCADE
);

-- Attendance
CREATE TABLE IF NOT EXISTS `attendance` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT NOT NULL,
  `subject_load_id` INT NOT NULL,
  `date` DATE NOT NULL,
  `status` ENUM('present','absent','tardy') NOT NULL,
  FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`subject_load_id`) REFERENCES `subject_loads`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `uniq_attendance` (`student_id`,`subject_load_id`,`date`)
);

-- Seed Data
INSERT INTO `admins` (`faculty_id`,`full_name`,`username`,`email`,`sex`) VALUES
('ADM-001','Juan Dela Cruz','adminjuan','admin@evelio.ams.edu','Male'),
('ADM-002','Maria Santos','adminmaria','admin2@evelio.ams.edu','Female');

INSERT INTO `teachers` (`faculty_id`,`full_name`,`username`,`email`,`sex`,`active`) VALUES
('FAC-101','Jose Rizal','trizal','teacher@evelio.ams.edu','Male',1),
('FAC-102','Andres Bonifacio','tbonifacio','teacher2@evelio.ams.edu','Male',1);

INSERT INTO `sections` (`name`,`department`,`grade_level`,`strand`,`capacity`) VALUES
('Grade 7 - A','JHS','Grade 7',NULL,40),
('Grade 8 - A','JHS','Grade 8',NULL,40),
('11 HUMSS-A','SHS','Grade 11','HUMSS',40);

INSERT INTO `subjects` (`code`,`name`,`department`,`grade_level`,`strand`,`semester`) VALUES
('ENG7','English 7','JHS','Grade 7',NULL,NULL),
('MATH7','Mathematics 7','JHS','Grade 7',NULL,NULL),
('SCI7','Science 7','JHS','Grade 7',NULL,NULL),
('HUMSS11-ENG','HUMSS English','SHS','Grade 11','HUMSS','First'),
('TVL11-TECH','TVL Tech','SHS','Grade 11','TVL','First');

-- Create sample students
INSERT INTO `students` (
  `lrn`,`department`,`grade_level`,`strand`,`student_type`,`advisory_section_id`,
  `family_name`,`first_name`,`middle_name`,`suffix`,`birthdate`,`birthplace`,`religion`,`civil_status`,`sex`,
  `mobile`,`email`,
  `curr_house_street`,`curr_barangay`,`curr_city`,`curr_province`,`curr_zip`,
  `perm_house_street`,`perm_barangay`,`perm_city`,`perm_province`,`perm_zip`,
  `elem_name`,`elem_address`,`elem_year_graduated`,
  `last_school_name`,`last_school_address`,
  `jhs_name`,`jhs_address`,`jhs_year_graduated`,
  `guardian_last_name`,`guardian_first_name`,`guardian_middle_name`,`guardian_contact`,`guardian_occupation`,`guardian_address`,`guardian_relationship`,
  `mother_last_name`,`mother_first_name`,`mother_middle_name`,`mother_contact`,`mother_occupation`,`mother_address`,
  `father_last_name`,`father_first_name`,`father_middle_name`,`father_contact`,`father_occupation`,`father_address`
) VALUES
('LRN0000001','JHS','Grade 7',NULL,'New Student',1,'Garcia','Pedro','.','', '2012-05-01','Antique','Catholic','Single','Male',
 '09170000001','S-LRN0000001@evelio.ams.edu',
 '123 Street','Barangay 1','San Jose','Antique','5700',
 '123 Street','Barangay 1','San Jose','Antique','5700',
 'Elementary School 1','Address 1','2023',
 'Last School 1','Address LS1',
 '','','',
 'Garcia','Juan','.','09171234567','Farmer','San Jose','Father',
 'Garcia','Maria','.','09179876543','Vendor','San Jose',
 'Garcia','Pedro Sr.','.','09179999999','Laborer','San Jose'
),
('LRN0000002','JHS','Grade 7',NULL,'Old Student',1,'Santos','Ana','.','', '2012-08-10','Antique','Catholic','Single','Female',
 '09170000002','S-LRN0000002@evelio.ams.edu',
 '456 Street','Barangay 2','San Jose','Antique','5700',
 '456 Street','Barangay 2','San Jose','Antique','5700',
 'Elementary School 2','Address 2','2023',
 'Last School 2','Address LS2',
 '','','',
 'Santos','Jose','.','09171234568','Driver','San Jose','Father',
 'Santos','Maria','.','09179876544','Teacher','San Jose',
 'Santos','Jose Sr.','.','09179999998','Farmer','San Jose'
),
('LRN0000003','JHS','Grade 8',NULL,'Transferee',2,'Lopez','Juan','.','', '2011-03-12','Antique','Catholic','Single','Male',
 '09170000003','S-LRN0000003@evelio.ams.edu',
 '789 Street','Barangay 3','San Jose','Antique','5700',
 '789 Street','Barangay 3','San Jose','Antique','5700',
 'Elementary School 3','Address 3','2022',
 'Last School 3','Address LS3',
 '','','',
 'Lopez','Carlos','.','09171234569','Carpenter','San Jose','Uncle',
 'Lopez','Maria','.','09179876545','Nurse','San Jose',
 'Lopez','Carlos Sr.','.','09179999997','Farmer','San Jose'
),
('LRN0000004','SHS','Grade 11','HUMSS','New Student',3,'Cruz','Lara','.','', '2008-11-23','Antique','Catholic','Single','Female',
 '09170000004','S-LRN0000004@evelio.ams.edu',
 '101 Street','Barangay 4','San Jose','Antique','5700',
 '101 Street','Barangay 4','San Jose','Antique','5700',
 'Elementary School 4','Address 4','2020',
 'Last School 4','Address LS4',
 'JHS School','Address JHS','2024',
 'Cruz','Manuel','.','09171234570','Clerk','San Jose','Father',
 'Cruz','Elena','.','09179876546','Officer','San Jose',
 'Cruz','Manuel Sr.','.','09179999996','Farmer','San Jose'
),
('LRN0000005','SHS','Grade 11','TVL','Transferee',3,'Reyes','Marco','.','', '2008-04-30','Antique','Catholic','Single','Male',
 '09170000005','S-LRN0000005@evelio.ams.edu',
 '202 Street','Barangay 5','San Jose','Antique','5700',
 '202 Street','Barangay 5','San Jose','Antique','5700',
 'Elementary School 5','Address 5','2020',
 'Last School 5','Address LS5',
 'JHS School','Address JHS','2024',
 'Reyes','Mario','.','09171234571','Cook','San Jose','Father',
 'Reyes','Marta','.','09179876547','Vendor','San Jose',
 'Reyes','Mario Sr.','.','09179999995','Driver','San Jose'
);

-- Sample accounts
INSERT INTO `accounts` (`email`,`username`,`password_hash`,`role`,`first_login_required`,`person_type`,`person_id`) VALUES
('admin@evelio.ams.edu','admin','$2y$10$A9GE5Vqog1XjzvRv1K.nX.AHAQf1PoXWm7WOHS4UxcHZl2T9uwiiK','admin',0,'admin',1),
('teacher@evelio.ams.edu','teacher','$2y$10$Jn9XwGtzSzocOJlebWHk2.7b9wwOQY1DKQUVLy7Yg1uYQhgcLzLaa','teacher',1,'teacher',1),
('S-LRN0000001@evelio.ams.edu','student1','$2y$10$8KDS3Jfu0IzBRDqGUFq5mOb8ynplSQAyUKao/oEnZpyCliTL.VbAa','student',1,'student',1);

