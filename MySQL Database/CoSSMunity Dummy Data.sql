-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 27, 2016 at 03:13 PM
-- Server version: 5.5.49-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `CoSSMunity`
--

-- --------------------------------------------------------

--
-- Table structure for table `achievement`
--

CREATE TABLE IF NOT EXISTS `achievement` (
  `achievement_id` int(11) NOT NULL AUTO_INCREMENT,
  `achievement_name` varchar(63) NOT NULL,
  `description` varchar(255) NOT NULL,
  `achievement_image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`achievement_id`),
  UNIQUE KEY `AchivementID_UNIQUE` (`achievement_id`),
  UNIQUE KEY `name_UNIQUE` (`achievement_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `achievement`
--

INSERT INTO `achievement` (`achievement_id`, `achievement_name`, `description`, `achievement_image`) VALUES
(0, 'Getting Started', 'The user gets this achievement by finishing registration', NULL),
(1, 'Monthly Report', 'The user has been a part of the program for a month and is now properly represented on the monthly leaderboard.', NULL),
(2, 'Monthly Improver', 'The user has improved their monthly score', NULL),
(3, 'Quarterly Report', 'The user has been a part of the program for 3 months and is now properly represented on the quarterly leaderboard.', NULL),
(4, 'Quarterly Improver', 'The user has improved their quarterly score', NULL),
(5, 'Yearly Report', 'The user has been part of the program for a whole year and is now properly represented on the yearly leaderboard.', NULL),
(6, 'Yearly Improver', 'The user has improved their yearly score', NULL),
(7, 'Big numbers ', 'The user has accumulated 5,000 points total', NULL),
(8, 'Incredible Total', 'The user has accumulated 10,000 points total', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `household`
--

CREATE TABLE IF NOT EXISTS `household` (
  `household_id` int(11) NOT NULL,
  `neighbourhood` varchar(127) NOT NULL,
  `username` varchar(63) NOT NULL,
  `email_hash` varchar(127) NOT NULL,
  `joined` date NOT NULL,
  `residents` int(11) DEFAULT NULL,
  `house_type` varchar(31) DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `age` year(4) DEFAULT NULL,
  `electric_heating` tinyint(1) NOT NULL DEFAULT '0',
  `electric_car` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`household_id`),
  UNIQUE KEY `idUser_UNIQUE` (`household_id`),
  UNIQUE KEY `username_UNIQUE` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `household`
--

INSERT INTO `household` (`household_id`, `neighbourhood`, `username`, `email_hash`, `joined`, `residents`, `house_type`, `size`, `age`, `electric_heating`, `electric_car`) VALUES
(0, '0', 'Peter Miller', '6f64fce01ffa86130c3fae06b1fcadef', '2013-12-03', 2, 'House', 100, 1970, 1, 1),
(1, '0', 'Vegard', '17239e25b62e838eb4340418a8c0d4ae', '2016-03-10', 2, 'Apartment', 200, 1980, 0, 0),
(2, '1', 'Adam Smith', '61ba7c5f62eeace4d02d3f643f99a2d1', '2016-01-01', 4, 'House', 220, 1986, 0, 1),
(3, '0', 'Frank Mueller', 'abc7768058e25c1382df18414db72a10', '2015-12-01', 3, 'House', 160, 1971, 0, 1),
(4, '0', 'Carl Anderson', '6ac00e047724e6fbed304a083bc8234f', '2016-03-01', 6, 'Apartment', 80, 1996, 0, 0),
(5, '0', 'Thomas Doyle', '6e42063400331743a848740f8827c90e', '2016-02-08', 1, 'Apartment', 120, 2000, 1, 0),
(6, 'Trondheim', 'johnappleseed', '17239e25b62e838eb4340418a8c0d4ae', '2016-05-23', NULL, NULL, NULL, NULL, 0, 0),
(7, 'Trondheim', 'TonyStark', '17239e25b62e838eb4340418a8c0d4ae', '2016-05-23', NULL, NULL, NULL, NULL, 0, 0),
(8, 'Trondheim', 'FrodoBaggins', '17239e25b62e838eb4340418a8c0d4ae', '2016-05-23', NULL, NULL, NULL, NULL, 0, 0),
(9, '0', 'Michael Carson', '157c96de3d125abc1c363ff25c20dfaf', '2016-01-05', 4, 'Apartment', 170, 2002, 1, 0),
(10, '1', 'Andre Castello', '87c1c7daf06754ee8653cd84efda14ab', '2015-08-01', 6, 'House', 200, 1990, 0, 0),
(11, '0', 'Peter Hillside', 'ec9385dc533f1a6a93769077f852503e', '2015-12-01', 4, 'Apartment', 100, 2004, 1, 0),
(12, '0', 'Tom Armello', '7feab598abfc27783641fe2b796a0155', '2015-11-10', 8, 'House', 260, 2010, 1, 1),
(100, 'Trondheim', 'bent', 'cb9b7bd962c7088150086af61490350a', '2016-05-23', NULL, NULL, NULL, NULL, 0, 0),
(101, 'Trondheim', 'aaaa', '17239e25b62e838eb4340418a8c0d4ae', '2016-05-19', NULL, NULL, NULL, NULL, 0, 0),
(2013, 'San Fran  LA', 'elonMusk', '17239e25b62e838eb4340418a8c0d4ae', '2016-05-19', NULL, NULL, NULL, NULL, 0, 0),
(2014, 'Palo Alto', 'steveJobs', '17239e25b62e838eb4340418a8c0d4ae', '2016-05-19', NULL, NULL, NULL, NULL, 0, 0),
(2016, 'Scranton', 'dshrute', '17239e25b62e838eb4340418a8c0d4ae', '2016-05-19', NULL, NULL, NULL, NULL, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `household_achievements`
--

CREATE TABLE IF NOT EXISTS `household_achievements` (
  `household_household_id` int(11) NOT NULL,
  `achievement_achievement_id` int(11) NOT NULL,
  `achieved` tinyint(1) NOT NULL DEFAULT '0',
  `date_achieved` date DEFAULT NULL,
  PRIMARY KEY (`household_household_id`,`achievement_achievement_id`),
  KEY `fk_household_achievement_achievement1_idx` (`achievement_achievement_id`),
  KEY `household_household_id` (`household_household_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `household_achievements`
--

INSERT INTO `household_achievements` (`household_household_id`, `achievement_achievement_id`, `achieved`, `date_achieved`) VALUES
(0, 0, 1, '2016-04-10'),
(0, 1, 1, '2016-05-27'),
(0, 2, 0, NULL),
(0, 3, 0, NULL),
(0, 4, 0, NULL),
(0, 5, 0, NULL),
(0, 6, 0, NULL),
(0, 8, 0, NULL),
(1, 0, 1, '2016-04-11'),
(1, 1, 1, '2016-04-13'),
(1, 2, 0, NULL),
(1, 3, 0, NULL),
(1, 4, 1, '2016-04-13'),
(1, 5, 1, '2016-04-13'),
(1, 6, 0, NULL),
(1, 8, 0, NULL),
(2, 0, 1, '2016-04-13'),
(2, 1, 1, '2016-04-13'),
(2, 2, 0, NULL),
(2, 3, 0, NULL),
(2, 4, 1, '2016-04-13'),
(2, 5, 1, '2016-04-13'),
(2, 6, 0, NULL),
(2, 8, 0, NULL),
(3, 0, 1, '2016-04-13'),
(3, 1, 1, '2016-04-13'),
(3, 2, 0, NULL),
(3, 3, 0, NULL),
(3, 4, 1, '2016-04-13'),
(3, 5, 1, '2016-04-13'),
(3, 6, 0, NULL),
(3, 8, 0, NULL),
(4, 0, 1, '2016-04-13'),
(4, 1, 1, '2016-04-13'),
(4, 2, 0, NULL),
(4, 3, 0, NULL),
(4, 4, 1, '2016-04-13'),
(4, 5, 1, '2016-04-13'),
(4, 6, 0, NULL),
(4, 8, 0, NULL),
(5, 0, 1, '2016-04-13'),
(5, 1, 1, '2016-04-13'),
(5, 2, 0, NULL),
(5, 3, 0, NULL),
(5, 4, 1, '2016-04-13'),
(5, 5, 1, '2016-04-13'),
(5, 6, 0, NULL),
(5, 8, 0, NULL),
(6, 0, 1, '2016-05-23'),
(7, 0, 1, '2016-05-23'),
(8, 0, 1, '2016-05-23'),
(8, 1, 0, NULL),
(8, 2, 0, NULL),
(8, 3, 0, NULL),
(8, 4, 0, NULL),
(8, 5, 0, NULL),
(8, 6, 0, NULL),
(8, 7, 0, NULL),
(8, 8, 0, NULL),
(9, 0, 1, '2016-04-13'),
(9, 1, 1, '2016-04-13'),
(9, 2, 0, NULL),
(9, 3, 0, NULL),
(9, 4, 1, '2016-04-13'),
(9, 5, 1, '2016-04-13'),
(9, 6, 0, NULL),
(9, 8, 0, NULL),
(10, 0, 1, '2016-04-13'),
(10, 1, 1, '2016-04-13'),
(10, 2, 0, NULL),
(10, 3, 0, NULL),
(10, 4, 1, '2016-04-13'),
(10, 5, 1, '2016-04-13'),
(10, 6, 0, NULL),
(10, 8, 0, NULL),
(11, 0, 1, '2016-04-13'),
(11, 1, 1, '2016-04-13'),
(11, 2, 0, NULL),
(11, 3, 0, NULL),
(11, 4, 1, '2016-04-13'),
(11, 5, 1, '2016-04-13'),
(11, 6, 0, NULL),
(11, 8, 0, NULL),
(12, 0, 1, '2016-04-13'),
(12, 1, 1, '2016-04-13'),
(12, 2, 0, NULL),
(12, 3, 0, NULL),
(12, 4, 1, '2016-04-13'),
(12, 5, 1, '2016-04-13'),
(12, 6, 0, NULL),
(12, 8, 0, NULL),
(100, 0, 1, '2016-05-23'),
(100, 1, 0, NULL),
(100, 2, 0, NULL),
(100, 3, 0, NULL),
(100, 4, 0, NULL),
(100, 5, 0, NULL),
(100, 6, 0, NULL),
(100, 7, 0, NULL),
(100, 8, 0, NULL),
(101, 2, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `household_ranks`
--

CREATE TABLE IF NOT EXISTS `household_ranks` (
  `household_household_id` int(11) NOT NULL,
  `rank_rank_id` int(11) NOT NULL,
  `date_obtained` date DEFAULT NULL,
  PRIMARY KEY (`household_household_id`,`rank_rank_id`),
  KEY `fk_household_ranks_rank1_idx` (`rank_rank_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `household_ranks`
--

INSERT INTO `household_ranks` (`household_household_id`, `rank_rank_id`, `date_obtained`) VALUES
(0, 1, '2016-04-10'),
(0, 2, '2016-05-09'),
(0, 3, '2016-05-11'),
(0, 4, NULL),
(0, 5, NULL),
(0, 6, NULL),
(1, 1, '2016-04-10'),
(1, 2, '2016-05-16'),
(1, 3, NULL),
(1, 4, NULL),
(1, 5, NULL),
(1, 6, NULL),
(2, 1, '2016-04-10'),
(2, 2, '2016-04-11'),
(2, 3, '2016-04-13'),
(2, 4, '2016-05-26'),
(2, 5, '2016-05-27'),
(2, 6, '2016-05-28'),
(3, 1, '2016-04-10'),
(3, 2, '2016-05-26'),
(3, 3, NULL),
(3, 4, NULL),
(3, 5, NULL),
(3, 6, NULL),
(4, 1, '2016-04-10'),
(4, 2, '2016-04-11'),
(4, 3, '2016-04-13'),
(4, 4, '2016-05-12'),
(4, 5, NULL),
(4, 6, NULL),
(5, 1, '2016-04-10'),
(5, 2, NULL),
(5, 3, NULL),
(5, 4, NULL),
(5, 5, NULL),
(5, 6, NULL),
(8, 1, NULL),
(8, 2, NULL),
(8, 3, NULL),
(8, 4, NULL),
(8, 5, NULL),
(8, 6, NULL),
(9, 1, '2016-04-10'),
(9, 2, '2016-05-04'),
(9, 3, NULL),
(9, 4, NULL),
(9, 5, NULL),
(9, 6, NULL),
(10, 1, '2016-04-10'),
(10, 2, '2016-04-11'),
(10, 3, '2016-04-13'),
(10, 4, NULL),
(10, 5, NULL),
(10, 6, NULL),
(11, 1, '2016-04-10'),
(11, 2, NULL),
(11, 3, NULL),
(11, 4, NULL),
(11, 5, NULL),
(11, 6, NULL),
(12, 1, '2016-04-10'),
(12, 2, '2016-04-11'),
(12, 3, '2016-04-13'),
(12, 4, '2016-05-11'),
(12, 5, '2016-05-19'),
(12, 6, NULL),
(100, 1, NULL),
(100, 2, NULL),
(100, 3, NULL),
(100, 4, NULL),
(100, 5, NULL),
(100, 6, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `household_scores`
--

CREATE TABLE IF NOT EXISTS `household_scores` (
  `household_household_id` int(11) NOT NULL,
  `score_type_score_type_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `value` int(11) NOT NULL,
  PRIMARY KEY (`household_household_id`,`score_type_score_type_id`,`date`),
  KEY `fk_household_score_score_type1_idx` (`score_type_score_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `household_scores`
--

INSERT INTO `household_scores` (`household_household_id`, `score_type_score_type_id`, `date`, `value`) VALUES
(0, 0, '2016-05-09', 3750),
(0, 1, '2016-02-26', 110),
(0, 1, '2016-04-03', 105),
(0, 1, '2016-04-10', 81),
(0, 1, '2016-05-09', 55),
(0, 2, '2016-05-09', 124),
(0, 3, '2016-05-09', 103),
(0, 4, '2016-04-10', 89),
(0, 4, '2016-05-09', 55),
(1, 0, '2015-05-05', 16),
(1, 0, '2016-05-09', 95),
(1, 1, '2015-06-17', 85),
(1, 1, '2015-07-20', 20),
(1, 1, '2016-02-15', 100),
(1, 1, '2016-04-12', 10),
(1, 1, '2016-05-09', 80),
(1, 2, '2016-04-02', 10),
(1, 2, '2016-05-09', 75),
(1, 3, '2015-07-04', 81),
(1, 3, '2015-08-06', 91),
(1, 3, '2015-08-23', 56),
(1, 3, '2015-09-09', 90),
(1, 3, '2016-02-19', 99),
(1, 3, '2016-05-09', 35),
(1, 4, '2015-07-21', 88),
(1, 4, '2016-04-13', 10),
(1, 4, '2016-05-09', 101),
(2, 1, '2015-07-04', 117),
(2, 1, '2016-05-09', 43),
(2, 2, '2016-05-09', 104),
(2, 3, '2015-06-12', 79),
(2, 3, '2015-08-29', 32),
(2, 3, '2016-05-09', 156),
(2, 4, '2015-08-09', 53),
(2, 4, '2015-08-26', 44),
(2, 4, '2016-05-09', 125),
(3, 0, '2016-05-09', 72),
(3, 1, '2015-05-17', 127),
(3, 1, '2016-03-24', 163),
(3, 1, '2016-05-09', 176),
(3, 2, '2015-12-13', 60),
(3, 2, '2016-02-19', 183),
(3, 3, '2016-05-09', 85),
(3, 4, '2016-05-09', 176),
(4, 0, '2016-03-19', 28),
(4, 0, '2016-05-09', 114),
(4, 1, '2015-12-08', 143),
(4, 1, '2016-01-04', 109),
(4, 1, '2016-04-18', 38),
(4, 1, '2016-05-09', 33),
(4, 4, '2015-05-19', 17),
(4, 4, '2016-05-09', 152),
(5, 0, '2015-11-27', 49),
(5, 0, '2016-05-09', 77),
(5, 1, '2015-08-23', 18),
(5, 1, '2016-04-13', 110),
(5, 1, '2016-05-09', 61),
(5, 2, '2016-02-11', 151),
(5, 2, '2016-05-09', 120),
(5, 3, '2016-05-09', 35),
(5, 4, '2015-04-24', 21),
(5, 4, '2016-05-09', 68),
(6, 0, '2016-05-23', 0),
(6, 1, '2016-05-23', 0),
(6, 2, '2016-05-23', 0),
(6, 3, '2016-05-23', 0),
(6, 4, '2016-05-23', 0),
(7, 0, '2016-05-23', 0),
(7, 1, '2016-05-23', 0),
(7, 2, '2016-05-23', 0),
(7, 3, '2016-05-23', 0),
(7, 4, '2016-05-23', 0),
(8, 0, '2016-05-23', 0),
(8, 1, '2016-05-23', 0),
(8, 2, '2016-05-23', 0),
(8, 3, '2016-05-23', 0),
(8, 4, '2016-05-23', 0),
(9, 1, '2015-06-27', 157),
(9, 1, '2016-05-09', 55),
(9, 2, '2015-06-17', 39),
(9, 2, '2015-07-26', 178),
(9, 2, '2015-09-21', 181),
(9, 2, '2016-05-09', 173),
(9, 3, '2015-07-25', 192),
(9, 3, '2015-11-29', 47),
(9, 3, '2016-04-11', 97),
(9, 3, '2016-05-09', 56),
(9, 4, '2015-08-19', 119),
(9, 4, '2015-11-11', 99),
(9, 4, '2016-05-09', 62),
(10, 0, '2015-09-18', 25),
(10, 0, '2016-05-09', 167),
(10, 1, '2016-05-09', 117),
(10, 2, '2015-05-14', 160),
(10, 2, '2016-02-02', 177),
(10, 2, '2016-05-09', 75),
(10, 3, '2015-05-23', 67),
(10, 3, '2016-01-14', 13),
(10, 3, '2016-05-09', 115),
(10, 4, '2016-01-30', 114),
(10, 4, '2016-05-09', 122),
(11, 0, '2016-05-09', 104),
(11, 2, '2015-12-23', 112),
(11, 2, '2016-01-23', 16),
(11, 2, '2016-05-09', 93),
(11, 3, '2015-07-18', 198),
(11, 3, '2016-03-15', 128),
(11, 3, '2016-05-09', 63),
(11, 4, '2015-09-28', 84),
(11, 4, '2015-11-04', 25),
(11, 4, '2016-04-11', 17),
(11, 4, '2016-05-09', 117),
(12, 0, '2015-07-17', 79),
(12, 0, '2015-11-14', 12),
(12, 0, '2016-05-09', 146),
(12, 1, '2016-02-24', 196),
(12, 1, '2016-05-09', 162),
(12, 3, '2016-05-09', 132),
(12, 4, '2016-05-09', 41),
(100, 0, '2016-05-23', 0),
(100, 1, '2016-05-23', 0),
(100, 2, '2016-05-23', 0),
(100, 3, '2016-05-23', 0),
(100, 4, '2016-05-23', 0);

-- --------------------------------------------------------

--
-- Table structure for table `rank`
--

CREATE TABLE IF NOT EXISTS `rank` (
  `rank_id` int(11) NOT NULL AUTO_INCREMENT,
  `rank_name` varchar(63) NOT NULL,
  `requirement` int(11) NOT NULL,
  `rank_image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`rank_id`),
  UNIQUE KEY `rank_id_UNIQUE` (`rank_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `rank`
--

INSERT INTO `rank` (`rank_id`, `rank_name`, `requirement`, `rank_image`) VALUES
(1, 'Seed', 0, NULL),
(2, 'Seedling', 1600, NULL),
(3, 'Sapling', 3200, NULL),
(4, 'Pole', 6400, NULL),
(5, 'Tree', 9600, NULL),
(6, 'Mature Tree', 14400, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `score_type`
--

CREATE TABLE IF NOT EXISTS `score_type` (
  `score_type_id` int(11) NOT NULL,
  `score_type_name` varchar(63) NOT NULL,
  PRIMARY KEY (`score_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `score_type`
--

INSERT INTO `score_type` (`score_type_id`, `score_type_name`) VALUES
(0, 'Total Score'),
(1, 'PV Score'),
(2, 'Grid Score'),
(3, 'Scheduling Score'),
(4, 'Sharing Score');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `household_achievements`
--
ALTER TABLE `household_achievements`
  ADD CONSTRAINT `fk_household_achievement` FOREIGN KEY (`household_household_id`) REFERENCES `household` (`household_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_household_achievement_achievement1` FOREIGN KEY (`achievement_achievement_id`) REFERENCES `achievement` (`achievement_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `household_ranks`
--
ALTER TABLE `household_ranks`
  ADD CONSTRAINT `fk_household_ranks_household1` FOREIGN KEY (`household_household_id`) REFERENCES `household` (`household_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_household_ranks_rank1` FOREIGN KEY (`rank_rank_id`) REFERENCES `rank` (`rank_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `household_scores`
--
ALTER TABLE `household_scores`
  ADD CONSTRAINT `fk_household_score_household1` FOREIGN KEY (`household_household_id`) REFERENCES `household` (`household_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_household_score_score_type1` FOREIGN KEY (`score_type_score_type_id`) REFERENCES `score_type` (`score_type_id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
