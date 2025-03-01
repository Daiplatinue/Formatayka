-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 01, 2025 at 03:51 AM
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
-- Database: `form_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `form_tb`
--

CREATE TABLE `form_tb` (
  `f_id` int(11) NOT NULL,
  `f_ln` varchar(50) NOT NULL,
  `f_fn` varchar(50) NOT NULL,
  `f_mi` varchar(50) NOT NULL,
  `f_dob` date NOT NULL,
  `f_sex` enum('Male','Female') NOT NULL,
  `f_civil` varchar(50) NOT NULL,
  `f_tin` int(50) NOT NULL,
  `f_nationality` varchar(50) NOT NULL,
  `f_religion` varchar(50) NOT NULL,
  `f_pob_bldg` varchar(50) NOT NULL,
  `f_pob_lot` varchar(50) NOT NULL,
  `f_pob_street` varchar(50) NOT NULL,
  `f_pob_subdivision` varchar(50) NOT NULL,
  `f_pob_barangay` varchar(50) NOT NULL,
  `f_pob_city` varchar(50) NOT NULL,
  `f_pob_province` varchar(50) NOT NULL,
  `f_pob_country` varchar(50) NOT NULL,
  `f_pob_zip` int(50) NOT NULL,
  `f_home_bldg` varchar(50) NOT NULL,
  `f_home_lot` varchar(50) NOT NULL,
  `f_home_street` varchar(50) NOT NULL,
  `f_home_subdivision` varchar(50) NOT NULL,
  `f_home_barangay` varchar(50) NOT NULL,
  `f_home_city` varchar(50) NOT NULL,
  `f_home_province` varchar(50) NOT NULL,
  `f_home_country` varchar(50) NOT NULL,
  `f_home_zip` int(50) NOT NULL,
  `f_home_mobile` varchar(50) NOT NULL,
  `f_home_email` varchar(50) NOT NULL,
  `f_home_telephone` varchar(50) NOT NULL,
  `f_father_ln` varchar(50) NOT NULL,
  `f_father_fn` varchar(50) NOT NULL,
  `f_father_mi` varchar(50) NOT NULL,
  `f_mother_ln` varchar(50) NOT NULL,
  `f_mother_fn` varchar(50) NOT NULL,
  `f_mother_mi` varchar(50) NOT NULL,
  `f_age` int(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `form_tb`
--

INSERT INTO `form_tb` (`f_id`, `f_ln`, `f_fn`, `f_mi`, `f_dob`, `f_sex`, `f_civil`, `f_tin`, `f_nationality`, `f_religion`, `f_pob_bldg`, `f_pob_lot`, `f_pob_street`, `f_pob_subdivision`, `f_pob_barangay`, `f_pob_city`, `f_pob_province`, `f_pob_country`, `f_pob_zip`, `f_home_bldg`, `f_home_lot`, `f_home_street`, `f_home_subdivision`, `f_home_barangay`, `f_home_city`, `f_home_province`, `f_home_country`, `f_home_zip`, `f_home_mobile`, `f_home_email`, `f_home_telephone`, `f_father_ln`, `f_father_fn`, `f_father_mi`, `f_mother_ln`, `f_mother_fn`, `f_mother_mi`, `f_age`) VALUES
(6, 'Vinsonasdasdpotangimo', 'Noah', 'Calvin Fulton', '1995-05-11', 'Female', 'separated', 382, 'Suscipit eius sint e', 'Obcaecati quo commod', 'Basia Kinney', 'Accusamus suscipit e', 'Kennedy Holmes', 'Autem quia eum beata', 'Et tempora illum at', 'Perferendis hic occa', 'Debitis veniam et s', 'Liechtenstein', 32660, 'Clark Harrell', 'Quis et irure odio a', 'Lucius Duran', 'Quo anim sed lorem r', 'Sed vel optio incid', 'Quis et velit ut dol', 'Est possimus illum', 'Greece', 21044, '12345678910', 'kawalysos@mailinator.com', '1', 'Dawson', 'Dane', 'Kevyn Hill', 'Nicholson', 'Eliana', 'Ira Campbell', 29);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `form_tb`
--
ALTER TABLE `form_tb`
  ADD PRIMARY KEY (`f_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `form_tb`
--
ALTER TABLE `form_tb`
  MODIFY `f_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
