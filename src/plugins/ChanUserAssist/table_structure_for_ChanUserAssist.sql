-- phpMyAdmin SQL Dump
-- version 4.0.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 31, 2013 at 06:31 AM
-- Server version: 5.5.31-0+wheezy1
-- PHP Version: 5.4.4-14+deb7u5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `osblast_phpbot`
--

-- --------------------------------------------------------

--
-- Table structure for table `osblastcua`
--

CREATE TABLE IF NOT EXISTS `osblastcua` (
  `code` varchar(50) NOT NULL,
  `response` text NOT NULL,
  `add_by` text NOT NULL,
  `modify_by` text NOT NULL,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `osblastcua`
--

INSERT INTO `osblastcua` (`code`, `response`, `add_by`, `modify_by`) VALUES
('google', 'Try searching on Google: www.google.com.au', 'anthonym', ''),
('php', 'PHP: Hypertext Preprocessor, www.php.net', 'anthonym', 'anthonym'),
('tias', 'Try It And See. If you want to know if or how something works, try it first. "Testing Is Absolutely Simple"', 'anthonym', '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
