-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 12, 2025 at 01:15 PM
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
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fpc`
--

CREATE TABLE `fpc` (
  `ID` int(11) NOT NULL,
  `FY` varchar(255) NOT NULL,
  `MONTH` varchar(255) NOT NULL,
  `DATE` date NOT NULL,
  `CATEGORY` varchar(255) NOT NULL,
  `TRIGGER` varchar(255) NOT NULL,
  `NT_NF` varchar(255) NOT NULL,
  `ISSUE` varchar(255) NOT NULL,
  `PART_NO` varchar(255) NOT NULL,
  `PRODUCT` varchar(255) NOT NULL,
  `LOT_SUBLOT` varchar(255) NOT NULL,
  `IN_VALUE` int(11) NOT NULL,
  `OUT_VALUE` int(11) NOT NULL,
  `REJECT` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fpc`
--

INSERT INTO `fpc` (`ID`, `FY`, `MONTH`, `DATE`, `CATEGORY`, `TRIGGER`, `NT_NF`, `ISSUE`, `PART_NO`, `PRODUCT`, `LOT_SUBLOT`, `IN_VALUE`, `OUT_VALUE`, `REJECT`) VALUES
(1, 'FY', 'March', '2025-03-07', 'sorting', 'sorting', 'NTPI', 'rework', '123', '321', '123', 123, 312, 23),
(2, 'FY', 'March', '2025-03-07', 'sorting', 'sorting', 'NTPI', 'rework', '123', '321', '123', 123, 312, 23),
(3, 'FY', 'March', '2025-03-07', 'sorting', 'sorting', 'NTPI', 'rework', '123', '321', '123', 123, 312, 23),
(4, 'FY', 'March', '2025-03-07', 'sorting', 'sorting', 'NTPI', 'rework', '123', '321', '123', 123, 312, 23),
(5, 'FY', 'March', '2025-03-07', 'sorting', 'sorting', 'NTPI', 'rework', '123', '321', '123', 123, 312, 23),
(6, 'FY', 'March', '2025-03-07', 'sorting', 'sorting', 'NTPI', 'rework', '123', '321', '123', 123, 312, 23),
(7, 'FY25', 'March', '2025-03-07', 'REWORK', 'NCPR', 'NFLD', 'REPAIR', '123', 'RE-ENTEK', '1234', 12, 10, 2),
(8, 'FY25', 'Array', '2025-03-07', 's', 's', 'NTPI', 's', '0', 'sss', 'sss', 12, 12, 2),
(9, 'FY25', 'Array', '2025-03-07', 's', 's', 'NTPI', 's', '0', 's', 's', 1, 1, 2),
(10, 'FY25', 'Array', '2025-03-07', 'a', 'a', 'NTPI', 'a', '0', 'a', 'a', 2, 2, 1),
(11, 'FY25', 'Array', '2025-03-08', 'a', 'a', 'NTPI', 'a', '0', 'a', 'a', 213, 123, 123),
(12, 'FY25', 'Array', '2025-03-08', 'sorting', 's', 'NTPI', 's', '0', 's', 's', 123, 213, 123),
(13, 'FY25', 'Array', '2025-03-08', 'b', 'b', 'NTPI', 'b', '0', 'b', 'b', 12, 21, 212),
(14, 'FY25', 'Array', '2025-03-08', 'c', 'c', 'NTPI', 'c', '0', 'c', 'c', 123, 32, 4),
(15, 'FY25', 'Array', '2025-03-08', 'd', 'd', 'NFLD', 'd', '0', 'd', 'asd', 123, 123, 23),
(16, 'FY25', 'Array', '2025-03-08', 'e', 'e', 'NTPI', 'e', '0', 'e', 'e', 123, 32, 32),
(17, 'FY25', 'January', '2025-03-08', 'A', 'A', 'NTPI', 'A', '0', 'A', 'A', 1, 2, 2),
(18, 'FY25', 'a', '2025-03-08', 'B', 'B', 'NFLD', 'B', '1234-2', 'B', 'B', 3, 2, 1),
(19, 'FY25', 'January', '2025-03-08', 'a', 'a', 'NTPI', 'a', '123-2', 'a', 'a', 12, 21, 212),
(20, 'FY25', 'a', '2025-03-08', 'b', 'b', 'NTPI', 'b', '432-213', 'b', '123', 32, 3, 29),
(21, 'FY25', 'March', '2025-03-08', 'a', 'a', 'NTPI', 'a', 'a', 'a', 'a', 12, 21, 212),
(22, 'FY25', 'a', '2025-03-08', '21', '21', 'NFLD', '21', '21', '12', '12', 32, 1, 2),
(23, 'FY25', 'January', '2025-03-08', 'a', 'a', 'NTPI', 'a', 'a', 'a', 'a', 3, 2, 1),
(24, 'FY25', 'February', '2025-03-08', 'b', 'b', 'NFLD', 'b', 'b', 'b', 'b', 2, 1, 1),
(25, 'FY25', 'January', '2025-03-08', 'c', 'c', 'NFLD', 'c', 'c', 'c', 'c', 3, 2, 1),
(26, 'FY25', 'March', '2025-03-08', 'd', 'd', 'NFLD', 'd', 'd', 'd', 'd', 4, 2, 2),
(27, 'FY25', 'March', '2025-03-08', '213', '123', 'NTPI', '213', '123', '123', '123', 3, 2, 1),
(28, 'FY25', 'March', '2025-03-08', 'qwe', 'qwe', 'NFLD', 'qwe', 'qwe', 'wqe', 'qwe', 3, 21, 1),
(29, 'FY25', 'March', '2025-03-08', 'a', 'sorting', 'NFLD', 'rework', '12345689-01', 'qc_fvi', '12356546', 12, 2, 10),
(30, 'FY25', 'January', '2025-03-08', 'REWORK', 'NCPR', 'NFLD', 's', '12345689-01', 'qc_fvi', '123-4', 23, 1, 22),
(31, 'FY25', 'February', '2025-03-08', 'sorting', 'NCPR', 'NTPI', '12', 'a', 'q', '123-5', 3, 2, 1),
(32, 'FY25', 'March', '2025-03-12', 'sorting', 'NCPR', '', 'REPAIR', '12345689-02', 're-entek', '123456789-0000', 12, 11, 1),
(33, 'FY25', 'March', '2025-03-12', 'sorting', 'NCPR', '', 'REPAIR', '12345689-01', 'qc_fvi', '1234-4322', 3, 2, 1),
(34, 'FY25', 'March', '2025-03-12', 'REWORK', 'sorting', '', 'rework', '12345689-02', 'qc_fvi', '4567', 33, 31, 2),
(35, 'FY25', 'March', '2025-03-12', 'sorting', 'NCPR', '', 'qwe', '12345689-03', 're_entek', '234432', 3, 2, 1),
(36, 'FY25', 'March', '2025-03-12', 'sorting', 'sorting', '', 'rework', '12345689-01', 'qc_fvi', '1', 1, 1, 1),
(37, 'FY25', 'March', '2025-03-12', 'REWORK', 'NCPR', '', 'REPAIR', '12345689-03', 're_entek', '2', 2, 2, 2),
(38, 'FY25', 'March', '2025-03-12', 'sorting', 'sorting', '', 's', '12345689-02', 'qc_fvi', '3', 3, 3, 3),
(39, 'FY25', 'January', '2025-03-12', 'REWORK', 'NCPR', '', 'rework', '12345689-01', 'qc_fvi', '2', 2, 2, 2),
(40, 'FY25', 'February', '2025-03-12', 'sorting', 'sorting', '', 'REPAIR', '12345689-04', 'relunctant', '3', 3, 3, 3),
(41, 'FY25', 'March', '2025-03-12', 'sorting', 'NCPR', '', 'REPAIR', '12345689-04', 'relunctant', '4631', 31, 21, 11);

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
(4, '12345689-04', 'relunctant');

-- --------------------------------------------------------

--
-- Table structure for table `trigger_tbl`
--

CREATE TABLE `trigger_tbl` (
  `ID` int(255) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  MODIFY `ID` int(255) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fpc`
--
ALTER TABLE `fpc`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `product_list`
--
ALTER TABLE `product_list`
  MODIFY `ID` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `trigger_tbl`
--
ALTER TABLE `trigger_tbl`
  MODIFY `ID` int(255) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
