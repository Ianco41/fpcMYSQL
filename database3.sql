-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 13, 2025 at 03:11 PM
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
-- Database: `database3`
--

-- --------------------------------------------------------

--
-- Table structure for table `category_tbl`
--

CREATE TABLE `category_tbl` (
  `ID` int(255) NOT NULL,
  `cat_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category_tbl`
--

INSERT INTO `category_tbl` (`ID`, `cat_name`) VALUES
(1, 'sorting'),
(2, 'asdas');

-- --------------------------------------------------------

--
-- Table structure for table `fpc`
--

CREATE TABLE `fpc` (
  `ID` int(11) NOT NULL,
  `FY` varchar(255) NOT NULL,
  `MONTH` varchar(255) NOT NULL,
  `DATE` date NOT NULL,
  `CATEGORY_ID` varchar(255) NOT NULL,
  `TRIGGER_ID` varchar(255) NOT NULL,
  `NT_NF` varchar(255) NOT NULL,
  `ISSUE` varchar(255) NOT NULL,
  `PART_ID` varchar(255) NOT NULL,
  `PRODUCT` varchar(255) NOT NULL,
  `LOT_SUBLOT` varchar(255) NOT NULL,
  `IN_VALUE` int(11) NOT NULL,
  `OUT_VALUE` int(11) NOT NULL,
  `REJECT` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fpc`
--

INSERT INTO `fpc` (`ID`, `FY`, `MONTH`, `DATE`, `CATEGORY_ID`, `TRIGGER_ID`, `NT_NF`, `ISSUE`, `PART_ID`, `PRODUCT`, `LOT_SUBLOT`, `IN_VALUE`, `OUT_VALUE`, `REJECT`) VALUES
(1, 'FY25', 'March', '2025-03-13', '1', '1', 'NTPI', 'issues', '4', 'relunctant', 'asdasdasdasdasdas', 1, 2, 0),
(2, 'FY25', 'March', '2025-03-13', '1', '2', 'NTPI', 'rework', '2', 'qc_fvi', 'asdcdaawvfa', 3, 2, 1),
(3, 'FY25', 'February', '2025-03-12', '2', '3', 'NFLD', 'asdas', '5', 'asd', 'asdasdas', 32, 21, 11),
(4, 'FY25', 'March', '2025-03-14', '2', '4', 'NFLD', 'asdasdas', '6', 'dasdasdas', 'asdasdasd', 3, 2, 1),
(5, 'FY25', 'March', '2025-03-14', '1', '2', 'NTPI', 'rework', '1', 'qc_fvi', '2132', 21, 2, 19);

-- --------------------------------------------------------

--
-- Table structure for table `issue_tbl`
--

CREATE TABLE `issue_tbl` (
  `ID` int(255) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_list`
--

CREATE TABLE `product_list` (
  `ID` int(255) NOT NULL,
  `PARTNUMBER` varchar(255) NOT NULL,
  `PARTNAME` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_list`
--

INSERT INTO `product_list` (`ID`, `PARTNUMBER`, `PARTNAME`) VALUES
(1, '12345689-01', 'qc_fvi'),
(2, '12345689-02', 'qc_fvi'),
(3, '12345689-03', 're_entek'),
(4, '12345689-04', 'relunctant'),
(5, 'asdas', 'asd'),
(6, 'asdasdas', 'dasdasdas');

-- --------------------------------------------------------

--
-- Table structure for table `trigger_tbl`
--

CREATE TABLE `trigger_tbl` (
  `ID` int(255) NOT NULL,
  `trigger_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trigger_tbl`
--

INSERT INTO `trigger_tbl` (`ID`, `trigger_name`) VALUES
(1, 'trgigger'),
(2, 'NCPR'),
(3, 'asdas'),
(4, 'sadsadasd');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `category_tbl`
--
ALTER TABLE `category_tbl`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `fpc`
--
ALTER TABLE `fpc`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `product_list`
--
ALTER TABLE `product_list`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `trigger_tbl`
--
ALTER TABLE `trigger_tbl`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `category_tbl`
--
ALTER TABLE `category_tbl`
  MODIFY `ID` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `fpc`
--
ALTER TABLE `fpc`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `product_list`
--
ALTER TABLE `product_list`
  MODIFY `ID` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `trigger_tbl`
--
ALTER TABLE `trigger_tbl`
  MODIFY `ID` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
