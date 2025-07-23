-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 25, 2024 at 04:34 PM
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
-- Database: `ccs_elogsystem`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcement`
--

CREATE TABLE `announcement` (
  `id` int(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `time` time(6) NOT NULL,
  `description` varchar(1000) NOT NULL,
  `subjectId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcement`
--

INSERT INTO `announcement` (`id`, `title`, `date`, `time`, `description`, `subjectId`) VALUES
(1, 'CLAIMING OF ID LACE LANYARDS', '2024-05-15', '11:44:28.128000', 'Dangal Greetings, CCS!\r\n\r\nClaiming of CCS ID Lace from Batch 1-5 is now available from 3PM to 8PM at the PnC COMLAB 5.\r\nPlease look for Rob Fritz Abayari, our CCS-CSG 3rd year representative.\r\nDon\'t forget to bring your ID upon claiming.\r\n\r\nThank you! ❤', 0),
(2, 'TRIAL OF ANNOUNCEMENT', '2024-05-21', '23:16:04.881000', 'ANNOUNCEMENT CCS!', 0);

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `appointment_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `prof_id` int(11) NOT NULL,
  `type_of_concern` varchar(255) NOT NULL,
  `specific_concern` varchar(255) NOT NULL,
  `detailed_concern` varchar(255) NOT NULL,
  `appointment_status` varchar(50) NOT NULL,
  `remarks` varchar(255) NOT NULL,
  `time_start` time(6) DEFAULT NULL,
  `time_end` time(6) DEFAULT NULL,
  `day` varchar(50) NOT NULL,
  `evaluation_status` varchar(50) NOT NULL,
  `action_report` varchar(500) NOT NULL,
  `action_report_path` varchar(255) NOT NULL,
  `action_report_textbox` varchar(500) NOT NULL,
  `resched_reason` varchar(500) NOT NULL,
  `appoint_by` int(50) NOT NULL,
  `app_day` date DEFAULT NULL,
  `services_rendered` int(11) NOT NULL DEFAULT 1,
  `total_hours` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`appointment_id`, `student_id`, `prof_id`, `type_of_concern`, `specific_concern`, `detailed_concern`, `appointment_status`, `remarks`, `time_start`, `time_end`, `day`, `evaluation_status`, `action_report`, `action_report_path`, `action_report_textbox`, `resched_reason`, `appoint_by`, `app_day`, `services_rendered`, `total_hours`) VALUES
(1, 2001003, 2145697, 'Consultation', 'Study Techniques', 'Can\'t comprehend', 'Standard', 'Done', '11:00:00.000000', '13:00:00.485000', 'Monday', 'Done', '../../assets/files/', '../../assets/files/Student Internship Evaluation - Toroy, Sheena Jane B. (1).docx', 'Look for other resources', 'das', 2145697, '2024-05-06', 1, 2),
(2, 2001003, 2423914, 'Advising', 'Option exploration and Goal setting', 'Difficult to do', 'Priority', 'Done', '13:30:00.000000', '01:00:00.000000', 'Monday', 'Done', '../../assets/files/', '../../assets/files/Student Internship Evaluation - Toroy, Sheena Jane B. (1).docx', 'Take time to do self-actualization', '', 2001003, '2024-05-13', 1, 1),
(3, 200237, 2423914, 'Consultation', 'Science Tutoring', 'Help with chemistry', 'Priority', 'Done', '14:00:00.000000', '15:00:00.000000', 'Monday', 'Done', '../../assets/files/', '../../assets/files/Student Internship Evaluation - Toroy, Sheena Jane B. (1).docx', 'Take time to do self-actualization', '', 200237, '2024-01-08', 1, 1),
(4, 200236, 2156798, 'Consultation', 'Job Interview Prep', 'Mock interview session', 'Standard', 'Done', '09:00:00.000000', '10:00:00.000000', 'Friday', 'Done', '../../assets/files/', '../../assets/files/Student Internship Evaluation - Toroy, Sheena Jane B. (1).docx', 'Take time to do self-actualization', '', 200236, '2024-01-12', 1, 1),
(5, 200109, 2143576, 'Consultation', 'English Tutoring', 'Help with essay writing', 'Priority', 'Done', '13:00:00.000000', '14:00:00.000000', 'Friday', 'Done', '../../assets/files/', '../../assets/files/Student Internship Evaluation - Toroy, Sheena Jane B. (1).docx', 'Look for other resources', '', 200109, '2024-01-19', 1, 1),
(6, 200224, 2145697, 'Consultation', 'Counseling', 'Dealing with anxiety', 'Standard', 'Done', '15:00:00.000000', '16:00:00.000000', 'Friday', 'Done', '../../assets/files/', '../../assets/files/Student Internship Evaluation - Toroy, Sheena Jane B. (1).docx', 'Check for ways to help add learnings', '', 200224, '2024-02-09', 1, 1),
(7, 200101, 2423914, 'Advising', 'Resume Building', 'Resume review session', 'Priority', 'Done', '12:00:00.000000', '13:00:00.000000', 'Tuesday', 'Done', '../../assets/files/', '../../assets/files/Student Internship Evaluation - Toroy, Sheena Jane B. (1).docx', 'Look for other resources', '', 2423914, '2024-02-16', 1, 1),
(8, 200108, 2145697, 'Advising', 'Physics Tutoring', 'Help with mechanics', 'Standard', 'Done', '08:00:00.000000', '09:00:00.000000', 'Wednesday', 'Done', '../../assets/files/', '../../assets/files/Student Internship Evaluation - Toroy, Sheena Jane B. (1).docx', 'Check for ways to help add learnings', '', 2145697, '2024-03-06', 1, 1),
(9, 200108, 2145697, 'Consultation', 'Counseling', 'Improving self-esteem', 'Priority', 'Done', '10:00:00.000000', '11:00:00.000000', 'Wednesday', 'Done', '../../assets/files/', '../../assets/files/Student Internship Evaluation - Toroy, Sheena Jane B. (1).docx', 'Take time to do self-actualization', '', 200108, '2024-03-13', 1, 1),
(10, 200105, 2156798, 'Advising', 'Networking Skills', 'Tips for networking', 'Standard', 'Done', '16:00:00.000000', '17:00:00.000000', 'Thursday', 'Done', '../../assets/files/', '../../assets/files/Student Internship Evaluation - Toroy, Sheena Jane B. (1).docx', 'Take time to know your purpose', '', 200105, '2024-04-18', 1, 1),
(13, 2001003, 2156798, 'Advising', 'Option exploration and Goal setting', 'Undecided what to do', 'Priority', 'Unresolved', '11:00:00.000000', '01:00:00.000000', 'Monday', 'Done', '../../assets/files/', '../../assets/files/Student Internship Evaluation - Toroy, Sheena Jane B. (1).docx', 'Take time to know your purpose', 'gfgdfgfd', 2001003, '2024-05-06', 1, 1),
(14, 2002613, 2145697, 'Advising', 'Option exploration and Goal setting', 'Undecided to chosen course', 'Priority', 'Done', '11:00:00.000000', '13:00:00.000000', 'Monday', 'Done', '../../assets/files/', '../../assets/files/Student Internship Evaluation - Toroy, Sheena Jane B. (1).docx', 'Take time to do self-actualization', '', 2002613, '2024-05-13', 1, 1),
(15, 2001003, 2423914, 'Consultation', 'Study Techniques', 'Slow learner', 'Standard', 'Pending', '13:30:00.000000', '01:00:00.000000', 'Monday', 'Not Done', '../../assets/files/', '../../assets/files/Student Internship Evaluation - Toroy, Sheena Jane B. (1).docx', 'Check for ways to help add learnings', '', 2001003, '2024-06-03', 1, 1),
(16, 2001003, 2143576, 'Advising', 'Option exploration and Goal setting', 'Difficulty in time and energy management', 'Priority', 'Pending', '09:00:00.000000', '01:00:00.000000', 'Friday', 'Not Done', '../../assets/files/', '../../assets/files/Student Internship Evaluation - Toroy, Sheena Jane B. (1).docx', 'Allow yourself to rest then grind again', '', 2001003, '2024-07-05', 1, 1),
(17, 2001003, 2143576, 'Consultation', 'Academic Planning', 'Time management probs', 'Standard', 'Unresolved', '10:00:00.000000', '01:00:00.000000', 'Friday', 'Done', '../../assets/files/', '../../assets/files/Student Internship Evaluation - Toroy, Sheena Jane B. (1).docx', 'Organize the tasks', '', 2001003, '2024-05-10', 1, 1),
(18, 2001003, 2156798, 'Advising', 'Option exploration and Goal setting', '', 'Priority', 'Done', '14:30:00.000000', '01:00:00.000000', 'Monday', 'Done', '../../assets/files/', '../../assets/files/7-removebg-preview.png', 'ashdjdhajkdhajdashdjashdjsadasjd', '', 2001003, '2024-05-27', 1, 1),
(19, 2002613, 2145697, 'Consultation', 'Academic Conferences', 'Low Grades in Prelim', 'Standard', 'Pending', '13:00:00.000000', '16:00:00.000000', 'Thursday', 'Not Done', '', '', '', '', 2145697, '2024-05-23', 1, 0),
(20, 2001003, 2156798, 'Consultation', 'Study Techniques', 'Asking for some lecture materials and key points for Midterm Exam.', 'Standard', 'Pending', '13:30:00.000000', '01:00:00.000000', 'Monday', 'Not Done', '', '', '', '', 2001003, '2024-05-27', 1, 0),
(21, 2001003, 2423914, 'Consultation', 'Study Techniques', 'Asking for lecture materials and pointers to review for Midterm Exam.', 'Standard', 'Pending', '13:30:00.000000', '01:00:00.000000', 'Monday', 'Not Done', '', '', '', '', 2001003, '2024-05-27', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `codes`
--

CREATE TABLE `codes` (
  `id` int(50) NOT NULL,
  `email` int(50) NOT NULL,
  `code` int(50) NOT NULL,
  `expire` int(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `codes`
--

INSERT INTO `codes` (`id`, `email`, `code`, `expire`) VALUES
(1, 0, 53606, 1715053531);

-- --------------------------------------------------------

--
-- Table structure for table `event_calendar`
--

CREATE TABLE `event_calendar` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `start_datetime` datetime NOT NULL,
  `end_datetime` datetime(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `prof`
--

CREATE TABLE `prof` (
  `username` int(11) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `middlename` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `contact_number` varchar(255) NOT NULL,
  `gender` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `account_type` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `itempic` varchar(255) NOT NULL,
  `itemlocation` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prof`
--

INSERT INTO `prof` (`username`, `firstname`, `lastname`, `middlename`, `email`, `contact_number`, `gender`, `address`, `account_type`, `password`, `itempic`, `itemlocation`) VALUES
(2143576, 'John Patrick ', 'Ogalesco', 'M', 'johnpatrickogalesco@gmail.com', '09090852555', 'male', 'Blk 14 Lot 14 New Santa Rosa Homes', 'faculty', '$2y$10$t.pqHVg30IYMAGx4YyKeceDnYMxYjRYFONK5AnxU1UHTWc/rA6Tl2', '../../assets/img/patrick.png', '../assets/img/patrick.png'),
(2145697, 'Gia Mae', 'Gaviola', 'Lariosa', 'giamaegaviola783@gmail.com', '09985678456', 'Female', 'St. Joseph 6, Brgy Marinig ', 'admin', '$2y$10$9vvcOg8Q7wkFMHrsrM02seR34gDMjRthjkGWwEILfOP1se2p8ir06', '../../assets/img/gia.png', 'assets/img/gia.php'),
(2156798, 'Janus Raymond', 'Tan', '', 'janusraymondtan@gmail.com', '09090852555', 'male', 'Blk 14 Lot 14 New Santa Rosa Homes', 'faculty', '$2y$10$kDKc54nfryPRfnfHDpcZ7ePWnERpYLiXfnFzJg3KU40QH67wVZEiG', '../../assets/img/janus.png', '../assets/img/janus.png'),
(2423914, 'Fe', 'Hablanida', 'L', 'fehablanida@gmail.com', '09090852555', 'female', 'Blk 14 Lot 14 New Santa Rosa Homes', 'faculty', '$2y$10$s2ouMXw4yIMrblbqUyx9ce3uhZpQgOH.8vPM5CacMIlCgogBSIVHi', '../../assets/img/fe.png', '../assets/img/fe.png');

-- --------------------------------------------------------

--
-- Table structure for table `prof_availability`
--

CREATE TABLE `prof_availability` (
  `id` int(10) NOT NULL,
  `prof_id` int(10) DEFAULT NULL,
  `day` varchar(50) DEFAULT NULL,
  `time_start` time NOT NULL,
  `time_end` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prof_availability`
--

INSERT INTO `prof_availability` (`id`, `prof_id`, `day`, `time_start`, `time_end`) VALUES
(1, 2145697, 'Monday', '07:00:00', '17:00:00'),
(2, 2156798, 'Monday', '13:30:00', '17:30:00'),
(3, 2143576, 'Friday', '08:00:00', '12:00:00'),
(4, 2423914, 'Monday', '13:30:00', '15:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `quasi_availability`
--

CREATE TABLE `quasi_availability` (
  `id` int(11) NOT NULL,
  `prof_id` int(50) NOT NULL,
  `day` varchar(50) NOT NULL,
  `time` time(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `rating_id` int(11) NOT NULL,
  `appointment_id` int(11) DEFAULT NULL,
  `question_1` int(11) DEFAULT NULL,
  `question_2` int(11) DEFAULT NULL,
  `question_3` int(11) DEFAULT NULL,
  `question_4` int(11) DEFAULT NULL,
  `question_5` int(11) DEFAULT NULL,
  `question_6` int(11) NOT NULL,
  `question_7` int(11) NOT NULL,
  `question_8` int(11) NOT NULL,
  `question_9` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ratings`
--

INSERT INTO `ratings` (`rating_id`, `appointment_id`, `question_1`, `question_2`, `question_3`, `question_4`, `question_5`, `question_6`, `question_7`, `question_8`, `question_9`) VALUES
(1, 8, 5, 5, 5, 5, 5, 5, 5, 5, ''),
(2, 1, 5, 5, 5, 5, 5, 5, 5, 5, ''),
(3, 2, 5, 5, 5, 5, 5, 5, 5, 5, ''),
(4, 4, 5, 5, 5, 5, 5, 5, 5, 5, ''),
(5, 8, 5, 5, 5, 5, 5, 5, 5, 5, ''),
(6, 6, 0, 5, 5, 5, 5, 5, 5, 5, ''),
(7, 7, 5, 5, 5, 5, 5, 5, 5, 5, ''),
(8, 1, 5, 5, 5, 5, 5, 5, 5, 5, ''),
(9, 1, 4, 4, 4, 4, 4, 4, 4, 4, 'Nothing'),
(10, 2, 0, 3, 3, 4, 1, 1, 3, 3, ''),
(11, 3, 4, 3, 3, 3, 2, 3, 4, 3, ''),
(12, 3, 3, 3, 3, 3, 2, 4, 4, 4, ''),
(13, 1, 5, 5, 5, 5, 5, 5, 5, 5, 'n/a'),
(14, 3, 5, 5, 5, 5, 5, 5, 5, 5, ''),
(15, 1, 0, 5, 5, 5, 5, 5, 5, 5, 'N/A'),
(16, 5, 0, 5, 5, 5, 5, 5, 5, 5, 'N/A'),
(17, 18, 5, 5, 4, 4, 4, 4, 4, 4, 'N/A');

-- --------------------------------------------------------

--
-- Table structure for table `semester`
--

CREATE TABLE `semester` (
  `id` int(11) NOT NULL,
  `semester` varchar(255) NOT NULL,
  `year_start` year(4) NOT NULL,
  `year_end` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `semester`
--

INSERT INTO `semester` (`id`, `semester`, `year_start`, `year_end`) VALUES
(1, '1st', '2023', '2024'),
(2, '2nd', '2024', '2023');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `username` int(11) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `middlename` varchar(255) NOT NULL,
  `year_section` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `contact_number` varchar(255) NOT NULL,
  `gender` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `account_type` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`username`, `firstname`, `lastname`, `middlename`, `year_section`, `email`, `contact_number`, `gender`, `address`, `account_type`, `password`) VALUES
(200100, 'Rob', 'Abayari', 'John', '1CSC', 'abayarirob123@gmail.com', '9918825100', 'Male', '123 Street, City', 'Student', '$2y$10$hashed_password_1'),
(200101, 'Jairus', 'Acu', 'Michael', '4ITA', 'acujairus123@gmail.com', '9918825101', 'Male', '456 Avenue, Town', 'Student', '$2y$10$hashed_password_2'),
(200102, 'Joshua', 'Adarne', 'David', '1CS', 'adarnejoshua123@gmail.com', '9918825102', 'Male', '789 Road, Village', 'Student', '$2y$10$hashed_password_3'),
(200103, 'John', 'Albay', 'Patrick', '4ITC', 'albayjohn123@gmail.com', '9918825103', 'Male', '101 Lane, County', 'Student', '$2y$10$hashed_password_4'),
(200104, 'Myca', 'Alimagno', 'Christopher', '2CSC', 'alimagnorio123@gmail.com', '9918825104', 'Female', '202 Boulevard, Country', 'Student', '$2y$10$hashed_password_5'),
(200105, 'Cecille', 'Alinsunurin', 'Elizabeth', '3CSA', 'alinsunurincecille123@gmail.com', '9918825105', 'Female', '303 Park, State', 'Student', '$2y$10$hashed_password_6'),
(200106, 'Jhon', 'Alinsunurin', 'Alexander', '3CSA', 'alinsunurinjohn123@gmail.com', '9918825106', 'Male', '404 Square, Province', 'Student', '$2y$10$hashed_password_7'),
(200107, 'Rinoa', 'Almodovar', 'Michael', '4ITC', 'almodovarrinoa123@gmail.com', '9918825107', 'Male', '505 Circle, Island', 'Student', '$2y$10$hashed_password_8'),
(200108, 'Ivan', 'Almonte', 'William', '4ITC', 'almonteivan123@gmail.com', '9918825108', 'Male', '606 Court, Peninsula', 'Student', '$2y$10$hashed_password_9'),
(200109, 'John', 'Amarante', 'Henry', '4CSA', 'amaranteromel123@gmail.com', '9918825109', 'Male', '707 Alley, Archipelago', 'Student', '$2y$10$hashed_password_10'),
(200110, 'John', 'Andaya', 'Robert', '4CSA', 'andayavincent123@gmail.com', '9918825110', 'Male', '808 Garden, Continent', 'Student', '$2y$10$hashed_password_11'),
(200111, 'Shine', 'Anol', 'William', '3CSA', 'anolsine123@gmail.com', '9918825111', 'Female', '909 Forest, Hemisphere', 'Student', '$2y$10$hashed_password_12'),
(200112, 'Cyrelle', 'Aragones', 'Michael', '2ITA', 'aragonescyrelle123@gmail.com', '9918825112', 'Female', '1010 Lake, Equator', 'Student', '$2y$10$hashed_password_13'),
(200113, 'Jairehn', 'Arambulo', 'Joseph', '4ITA', 'arambulojairehn123@gmail.com', '9918825113', 'Male', '1111 Mountain, Tropic', 'Student', '$2y$10$hashed_password_14'),
(200114, 'Christian', 'Arguelles', 'Richard', '3ITC', 'arguelleschristian123@gmail.com', '9918825114', 'Male', '1212 River, Polar', 'Student', '$2y$10$hashed_password_15'),
(200115, 'Larien', 'Artillero', 'Thomas', '4ITA', 'artillerolarien123@gmail.com', '9918825115', 'Male', '1313 Valley, Arctic', 'Student', '$2y$10$hashed_password_16'),
(200116, 'Joy', 'Balsomo', 'Edward', '4ITB', 'balsomojoy123@gmail.com', '9918825116', 'Female', '1414 Ocean, Antarctic', 'Student', '$2y$10$hashed_password_17'),
(200117, 'John', 'Banal', 'Rex', '4ITA', 'banalrex123@gmail.com', '9918825117', 'Male', 'Address', 'BSIT', '$2y$10$hashed_password_18'),
(200118, 'Brynch', 'Bano', '', '4ITA', 'banobrynch123@gmail.com', '9918825118', 'Male', 'Address', 'BSIT', '$2y$10$hashed_password_19'),
(200119, 'Paul', 'Bariring', '', '4ITC', 'baririningpaul123@gmail.com', '9918825119', 'Male', 'Address', 'BSIT', '$2y$10$hashed_password_20'),
(200220, 'Sharabelle', 'Bariring', '', '4ITA', 'bariringsharabelle123@gmail.com', '9918825120', 'Female', 'Address', 'BSIT', '$2y$10$hashed_password_21'),
(200221, 'Catherine', 'Batalon', '', '2CSB', 'batalloncath123@gmail.com', '9918825121', 'Female', 'Address', 'BSCS', '$2y$10$hashed_password_22'),
(200223, 'Elisha', 'Batayon', '', '4ITA', 'batayonelisha123@gmail.com', '9918825122', 'Male', 'Address', 'BSIT', '$2y$10$hashed_password_23'),
(200224, 'Jerald', 'Baustista', '', '2CSC', 'bautistajerald123@gmail.com', '9918825123', 'Male', 'Address', 'BSCS', '$2y$10$hashed_password_24'),
(200225, 'Arcelie', 'Baway', '', '3CSA', 'bawayarcelie123@gmail.com', '9918825124', 'Female', 'Address', 'BSCS', '$2y$10$hashed_password_25'),
(200226, 'Jade', 'Belen', '', '4ITB', 'belenjade123@gmail.com', '9918825125', 'Female', 'Address', 'BSIT', '$2y$10$hashed_password_26'),
(200227, 'Daniel Matthew', 'Benegas', '', '3CSA', 'benegasdaniel123@gmail.com', '9918825126', 'Male', 'Address', 'BSCS', '$2y$10$hashed_password_27'),
(200228, 'Ruther', 'Berino', '', '2ITB', 'berinoruther123@gmail.com', '9918825127', 'Male', 'Address', 'BSIT', '$2y$10$hashed_password_28'),
(200229, 'Pamela', 'Bernabe', '', '4CSA', 'bernabepamela123@gmail.com', '9918825128', 'Female', 'Address', 'BSCS', '$2y$10$hashed_password_29'),
(200230, 'Sam Reginald', 'Besa', '', '4ITA', 'besareginald123@gmail.com', '9918825129', 'Male', 'Address', 'BSIT', '$2y$10$hashed_password_30'),
(200231, 'April', 'Bicaldo', '', '2CSA', 'bicaldoapril123@gmail.com', '9918825130', 'Female', 'Address', 'BSCS', '$2y$10$hashed_password_31'),
(200232, 'Melvern', 'Bocayes', '', '4ITB', 'bocayesmelvern123@gmail.com', '9918825131', 'Male', 'Address', 'BSIT', '$2y$10$hashed_password_32'),
(200234, 'Kevin', 'Bonaos', '', '2ITB', 'bonaoskevin123@gmail.com', '9918825132', 'Male', 'Address', 'BSIT', '$2y$10$hashed_password_33'),
(200235, 'Francis', 'Cabusas', '', '4ITA', 'cabusasfrancis123@gmail.com', '9918825133', 'Male', 'Address', 'BSIT', '$2y$10$hashed_password_34'),
(200236, 'Angela', 'Calimag', '', '4ITB', 'calimagangela123@gmail.com', '9918825134', 'Female', 'Address', 'BSIT', '$2y$10$hashed_password_35'),
(200237, 'Abbygaile Blue', 'Calitis', '', '4ITC', 'calitisabby123@gmail.com', '9918825135', 'Female', 'Address', 'BSIT', '$2y$10$hashed_password_36'),
(2000317, 'Paul', 'Bariring', 'Lingat', '4ITB', 'bariringpaul17@gmail.com', '09852733984', 'Male', 'Mabuhay, Cabuyao, Laguna', 'student', '$2y$10$nZU0TwRgB.M6Nc.rDBhWR.t1Ovy1LVMkXVzKmfu3rQK.yEDY8JeTi'),
(2001003, 'Sheena Jane', 'Toroy', 'Bacar', '4ITC', 'toroysheena@gmail.com', '09090852555', 'Female', 'Blk 14 Lot 14 New Santa Rosa Homes', 'student', '$2y$10$FtaXI.EAUadNHpG2S3et8uTpXyxwTj8soUZzZRYo3p/RqHHi3No1i'),
(2002613, 'Jowee Kyla', 'Maramag', 'Cena', '4ITC', 'maramagjoweekyla13@gmail.com', '09917165518', 'Female', 'Olympia Homes Brgy Ibaba', 'student', '$2y$10$x2q6OpF6nHznotU8U1dCTORK8U3SEQRcjNxUFffLIVaguxZzp0R3q');

-- --------------------------------------------------------

--
-- Table structure for table `student_availability`
--

CREATE TABLE `student_availability` (
  `id` int(10) NOT NULL,
  `student_id` int(10) DEFAULT NULL,
  `day` varchar(50) DEFAULT NULL,
  `time_start` time NOT NULL,
  `time_end` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_availability`
--

INSERT INTO `student_availability` (`id`, `student_id`, `day`, `time_start`, `time_end`) VALUES
(1, 2001003, 'Monday', '11:00:00', '13:00:00'),
(2, 2002613, 'Thursday', '13:00:00', '16:00:00'),
(3, 2002613, 'Monday', '07:00:00', '09:00:00'),
(4, 2001003, 'Wednesday', '09:30:00', '10:30:00'),
(5, 2001003, 'Monday', '14:30:00', '15:30:00'),
(6, 2001003, 'Thursday', '10:00:00', '11:00:00'),
(7, 2001003, 'Friday', '14:00:00', '15:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `subject`
--

CREATE TABLE `subject` (
  `subject_id` int(11) NOT NULL,
  `subject_code` varchar(11) NOT NULL,
  `subject_name` varchar(255) DEFAULT NULL,
  `prof_id` int(11) DEFAULT NULL,
  `year_section` varchar(255) NOT NULL,
  `start_time` time(6) NOT NULL,
  `end_time` time(6) NOT NULL,
  `day` varchar(50) NOT NULL,
  `schedule_type` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subject`
--

INSERT INTO `subject` (`subject_id`, `subject_code`, `subject_name`, `prof_id`, `year_section`, `start_time`, `end_time`, `day`, `schedule_type`) VALUES
(1, 'CCS101', 'Introduction to Computing	', 2145697, '4ITC', '07:00:00.000000', '10:00:00.000000', 'Monday', 'Laboratory');

-- --------------------------------------------------------

--
-- Table structure for table `subj_management`
--

CREATE TABLE `subj_management` (
  `subject_id` int(20) NOT NULL,
  `subject_type` varchar(20) NOT NULL,
  `subject_code` varchar(20) NOT NULL,
  `subject_name` varchar(100) NOT NULL,
  `units` int(20) NOT NULL,
  `day` varchar(20) NOT NULL,
  `start_time` time(2) NOT NULL,
  `end_time` time(2) NOT NULL,
  `status` varchar(30) DEFAULT 'Not Assigned',
  `year_section` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subj_management`
--

INSERT INTO `subj_management` (`subject_id`, `subject_type`, `subject_code`, `subject_name`, `units`, `day`, `start_time`, `end_time`, `status`, `year_section`) VALUES
(1, 'Laboratory', 'CCS103', 'Computer Programming 3', 3, 'Friday', '15:30:00.00', '18:30:00.00', '2156798', '2ITA'),
(2, 'Lecture', 'ITEW8', 'Web Security and Optimization', 2, 'Wednesday', '13:00:00.00', '15:00:00.00', 'Not Assigned', 'Not Assigned'),
(3, 'Laboratory', 'CCS101', 'Computer Programming 1', 3, 'Wednesday', '17:30:00.00', '20:30:00.00', 'Not Assigned', 'Not Assigned'),
(4, 'Lecture', 'CCS105', 'Computer Programming 5', 3, 'Saturday', '14:00:00.00', '17:00:00.00', 'Not Assigned', 'Not Assigned'),
(5, 'Laboratory', 'ITP112', 'Capstone 2', 2, 'Friday', '20:30:00.00', '23:30:00.00', '2145697', '4ITC'),
(6, 'Lecture', 'HCI1', 'Human Computer Interaction 1', 2, 'Monday', '09:30:00.00', '11:30:00.00', 'Not Assigned', 'Not Assigned'),
(7, 'Laboratory', 'CCS104', 'Computer Programming 4', 3, 'Friday', '10:30:00.00', '13:30:00.00', 'Not Assigned', 'Not Assigned'),
(8, 'Laboratory', 'CCS102', 'Computer Programming 2', 3, 'Monday', '09:30:00.00', '12:30:00.00', 'Not Assigned', 'Not Assigned'),
(9, 'Laboratory', 'CCS103', 'Computer Programming 3', 3, 'Monday', '09:30:00.00', '12:30:00.00', 'Not Assigned', 'Not Assigned'),
(10, 'Lecture', 'HCI1', 'Human Computer Interaction 1', 2, 'Saturday', '12:30:00.00', '14:30:00.00', 'Not Assigned', 'Not Assigned'),
(11, 'Laboratory', 'ITEW1', 'Electronic Commerce', 3, 'Saturday', '07:00:00.00', '10:00:00.00', 'Not Assigned', 'Not Assigned'),
(12, 'Lecture', 'ITP101', 'Social and Professional Issue', 2, 'Friday', '13:00:00.00', '15:00:00.00', 'Not Assigned', 'Not Assigned'),
(13, 'Laboratory', 'CCS102', 'Computer Programming 2', 3, 'Thursday', '14:30:00.00', '17:30:00.00', 'Not Assigned', 'Not Assigned'),
(14, 'Lecture', 'ITEW9', 'Web Security and Optimization 9', 2, 'Tuesday', '13:45:00.00', '15:45:00.00', 'Not Assigned', 'Not Assigned'),
(15, 'Laboratory', 'CCS106', 'Computer Programming 6', 3, 'Saturday', '17:30:00.00', '20:30:00.00', 'Not Assigned', 'Not Assigned'),
(16, 'Lecture', 'ITEW8', 'Web Security and Optimization', 2, 'Friday', '14:30:00.00', '16:30:00.00', 'Not Assigned', 'Not Assigned'),
(17, 'Laboratory', 'CCS101', 'Computer Programming 1', 3, 'Saturday', '13:30:00.00', '16:30:00.00', 'Not Assigned', 'Not Assigned'),
(18, 'Lecture', 'ITP103', 'Information Management 1', 2, 'Friday', '08:30:00.00', '10:30:00.00', 'Not Assigned', 'Not Assigned');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcement`
--
ALTER TABLE `announcement`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`appointment_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `prof_id` (`prof_id`);

--
-- Indexes for table `codes`
--
ALTER TABLE `codes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `event_calendar`
--
ALTER TABLE `event_calendar`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `prof`
--
ALTER TABLE `prof`
  ADD PRIMARY KEY (`username`),
  ADD KEY `email` (`email`);

--
-- Indexes for table `prof_availability`
--
ALTER TABLE `prof_availability`
  ADD PRIMARY KEY (`id`),
  ADD KEY `prof_id_fk` (`prof_id`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`rating_id`),
  ADD KEY `appointment_id` (`appointment_id`);

--
-- Indexes for table `semester`
--
ALTER TABLE `semester`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `student_availability`
--
ALTER TABLE `student_availability`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id_fk` (`student_id`);

--
-- Indexes for table `subject`
--
ALTER TABLE `subject`
  ADD PRIMARY KEY (`subject_code`),
  ADD KEY `prof_id` (`prof_id`);

--
-- Indexes for table `subj_management`
--
ALTER TABLE `subj_management`
  ADD PRIMARY KEY (`subject_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcement`
--
ALTER TABLE `announcement`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `appointment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `codes`
--
ALTER TABLE `codes`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `event_calendar`
--
ALTER TABLE `event_calendar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `prof_availability`
--
ALTER TABLE `prof_availability`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `rating_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `student_availability`
--
ALTER TABLE `student_availability`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `subj_management`
--
ALTER TABLE `subj_management`
  MODIFY `subject_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`username`),
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`prof_id`) REFERENCES `prof` (`username`);

--
-- Constraints for table `prof_availability`
--
ALTER TABLE `prof_availability`
  ADD CONSTRAINT `prof_id_fk` FOREIGN KEY (`prof_id`) REFERENCES `prof` (`username`);

--
-- Constraints for table `student_availability`
--
ALTER TABLE `student_availability`
  ADD CONSTRAINT `student_id_fk` FOREIGN KEY (`student_id`) REFERENCES `student` (`username`);

--
-- Constraints for table `subject`
--
ALTER TABLE `subject`
  ADD CONSTRAINT `subject_ibfk_1` FOREIGN KEY (`prof_id`) REFERENCES `prof` (`username`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
