-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Sep 28, 2018 at 12:45 PM
-- Server version: 5.6.21
-- PHP Version: 5.5.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `mockexam_db`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `Getexamrank`(IN `schid` INT)
    NO SQL
BEGIN
SELECT @a:=@a+1 serial_number,sr_stud_id,sr_total_score,sr_total_time, s.stud_name as studName,sr_attempt_que, sr_stud_roll_no FROM vid_student_final_result as sfs inner join vid_student as s on sfs.sr_stud_id = s.stud_id , (SELECT @a:= 0) AS a 

where sr_schedule_id = schid 

order by sr_total_score desc , sr_total_time asc;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetLoggedUser`(IN `unm` VARCHAR(50), IN `pwd` VARCHAR(50))
    NO SQL
BEGIN
	SELECT m.master_id as userid,m.master_id as instid,'master' as name,m.email,1 as type,u.usersessionid as authcode,1 as verify_flag,0 as register_type,0 as contact,1 as packageid
    FROM vid_master m left join vid_usersession u on u.userid=m.master_id and u.usertype = 1
    where m.active = '1' and m.email = unm and m.password = pwd;
 END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `Getstudentrank`(IN `schid` INT, IN `studid` INT)
    NO SQL
BEGIN
select temp.*, s.stud_name 
from (SELECT @a:=@a+1 serial_number,
      sr_stud_id,
      sr_total_score,
      sr_attempt_que,
      sr_stud_roll_no,
      sr_total_time FROM vid_student_final_result as sfs,
      
      (SELECT @a:= 0) AS a 
		where sr_schedule_id = schid 
		order by sr_total_score desc , sr_total_time asc) as temp
		inner join vid_student as s on temp.sr_stud_id = s.stud_id 
 where temp.sr_stud_id = studid;
 END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insertInstStudent`(IN `instid` INT, IN `name` VARCHAR(100), IN `email` VARCHAR(50), IN `contact` VARCHAR(11), IN `passwrd` VARCHAR(50), IN `createddate` DATETIME, IN `username` VARCHAR(50), OUT `id` INT)
    NO SQL
BEGIN

INSERT INTO vid_student
(branch_id, stud_name, stud_email, stud_contact,username, stud_password, submitdate, register_type)
    VALUES  (instid, name, email, contact,username, passwrd, createddate, 3);
    
    SET id = LAST_INSERT_ID();
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insertOption`(IN `quesid` INT, IN `optiontext` TEXT CHARSET utf8, OUT `id` INT)
    NO SQL
BEGIN

INSERT INTO vid_question_options
(ques_id, option_text)
    VALUES  (quesid, optiontext);
    
    SET id = LAST_INSERT_ID();
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insertStudentCouse`(IN `studid` INT, IN `courseid` INT, IN `branchid` INT, IN `createdate` DATETIME, IN `instid` INT, IN `startdate` DATE, IN `enddate` DATE, IN `duration` INT)
    NO SQL
BEGIN

INSERT INTO vid_student_course
(stud_id, course_id, branch_id, inst_id, is_payment, start_date, end_date, duration, submitdate)
    VALUES  (studid, courseid, branchid, instid, '1', startdate, enddate, duration, createdate);
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE IF NOT EXISTS `category` (
`id` int(11) NOT NULL,
  `catname` char(50) NOT NULL,
  `catimage` tinytext NOT NULL,
  `submitdate` date NOT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1' COMMENT '0:deactive,1:active'
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `catname`, `catimage`, `submitdate`, `active`) VALUES
(1, 'category1', 'images/Categoryimage/_5ada001888399.jpg', '2018-04-19', '1'),
(2, 'category2', 'images/Categoryimage/_5ad8b239c9441.jpg', '2018-04-19', '1'),
(5, '123', 'images/Categoryimage/_5ad9d1fba6f00.jpg', '2018-04-20', '1'),
(6, '123', 'images/Categoryimage/_5ad9d58fd7604.jpg', '2018-04-20', '0'),
(7, '123', 'images/Categoryimage/_5ad9d643cf642.jpg', '2018-04-20', '1'),
(8, '123', 'images/Categoryimage/_5ad9df6825ec3.jpg', '2018-04-20', '0');

-- --------------------------------------------------------

--
-- Table structure for table `plandetails`
--

CREATE TABLE IF NOT EXISTS `plandetails` (
  `id` int(11) NOT NULL,
  `planname` tinytext NOT NULL,
  `details` varchar(500) NOT NULL,
  `submitdate` date NOT NULL,
  `duration` smallint(6) NOT NULL COMMENT 'in month',
  `price` decimal(10,0) NOT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE IF NOT EXISTS `product` (
`pid` int(11) NOT NULL,
  `productno` int(11) NOT NULL,
  `productname` tinytext NOT NULL,
  `modelno` int(11) NOT NULL,
  `riskclass` tinytext NOT NULL,
  `productdiscri` varchar(700) DEFAULT NULL,
  `technicalfact` varchar(700) NOT NULL,
  `indectionanduse` varchar(700) NOT NULL,
  `warnings` varchar(500) NOT NULL,
  `precautions` varchar(700) NOT NULL,
  `certificates` varchar(500) NOT NULL,
  `prodimage` tinytext,
  `categoryid` smallint(6) NOT NULL,
  `fmeaid` smallint(6) NOT NULL,
  `submitdate` date NOT NULL,
  `modifydate` date NOT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1' COMMENT '0:deactive,1:active'
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`pid`, `productno`, `productname`, `modelno`, `riskclass`, `productdiscri`, `technicalfact`, `indectionanduse`, `warnings`, `precautions`, `certificates`, `prodimage`, `categoryid`, `fmeaid`, `submitdate`, `modifydate`, `active`) VALUES
(1, 12343, 'Product', 54543, 'test class', ' adipiscing elit,', 'Sed ut perspiciatis unde omnis iste ', 'I must explain to', 'warning', 'Lorem ipsum ', 'Sed ut perspiciatis inventore veritatis', '', 1, 1, '2018-04-20', '0000-00-00', '1'),
(2, 3455, 'Product1', 54543, 'product2', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.', 'I must explain to you how all this mistaken idea of denouncing pleasure and praising pain was born and I will give you a complete account of the system,', '"Lorem ipsum dolor sit amet, consectetur adipiscing elit,', 'risk', '"Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.', '', 1, 2, '2018-04-20', '0000-00-00', '1'),
(3, 123434, 'dfgdg', 23432, 'test', NULL, '', '', '', '', '', '', 1, 0, '0000-00-00', '0000-00-00', '1'),
(4, 123434, 'dfgdg', 23432, 'test', NULL, '', '', '', '', '', '', 1, 0, '2018-04-24', '0000-00-00', '1'),
(5, 123434, 'product', 23432, 'test', NULL, 'ssfsdfsdffff', 'ssfsdfsdffff', 'ncbvjhgzfbd', '', '', 'images/ProductImages/_5aded783dbbbd.jpg', 1, 0, '2018-04-24', '2018-04-24', '1');

-- --------------------------------------------------------

--
-- Table structure for table `productfmea`
--

CREATE TABLE IF NOT EXISTS `productfmea` (
`id` int(11) NOT NULL,
  `productid` smallint(6) NOT NULL,
  `functionorcomp` tinytext NOT NULL,
  `failuremode` tinytext NOT NULL,
  `effectonsystem` tinytext NOT NULL,
  `possiblehazrd` tinytext NOT NULL,
  `riskindex` tinyint(4) NOT NULL,
  `userdetectmeans` tinytext NOT NULL,
  `applicablecntrl` tinytext NOT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1' COMMENT '0:deactive1:active'
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `productfmea`
--

INSERT INTO `productfmea` (`id`, `productid`, `functionorcomp`, `failuremode`, `effectonsystem`, `possiblehazrd`, `riskindex`, `userdetectmeans`, `applicablecntrl`, `active`) VALUES
(1, 2, 'Lorem Ipsum is simply', 'It is a long', 'Where does it', 'to generate Lorem ', 45, 'The standard', '"Lorem ipsum dolor', '1'),
(2, 2, 'simply', 'It is a long ', 'does it come from?', 'to generate Lorem', 56, 'The standard', 'adipiscing elit,', '1');

-- --------------------------------------------------------

--
-- Table structure for table `riskclass`
--

CREATE TABLE IF NOT EXISTS `riskclass` (
`id` int(11) NOT NULL,
  `riskclass` tinytext NOT NULL,
  `submitdate` date NOT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1' COMMENT '0:deactive,1:active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
`id` int(11) NOT NULL,
  `fullname` tinytext NOT NULL,
  `emailaddress` tinytext NOT NULL,
  `contactno` varchar(15) NOT NULL,
  `password` tinytext NOT NULL,
  `issuperadmin` enum('0','1') NOT NULL DEFAULT '0',
  `pimage` tinytext,
  `usertype` tinytext,
  `submitdate` date NOT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1' COMMENT '1:active,0:deactive'
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `fullname`, `emailaddress`, `contactno`, `password`, `issuperadmin`, `pimage`, `usertype`, `submitdate`, `active`) VALUES
(1, 'abc', 'abc@gmail.com', '9989098745', '34567867', '1', '', '', '2017-04-18', '1'),
(2, 'abc1', 'abc@gmail.com', '9989098745', '852117177b81ae5e5b550dbaa18a0fb2', '0', '', '', '2017-04-18', '1'),
(3, 'abc1', 'abc1@gmail.com', '9989098745', '852117177b81ae5e5b550dbaa18a0fb2', '0', '', '', '2017-04-18', '1'),
(4, 'abc1', 'testpr@gmail.com', '9989098745', '852117177b81ae5e5b550dbaa18a0fb2', '0', NULL, '', '2018-04-17', '1'),
(10, 'test', 'test@gmail.com', '9978987878', '827ccb0eea8a706c4c34a16891f84e7b', '0', 'NULL', '', '2018-04-18', '1'),
(11, 'pramod', 'testpr1@gmail.com', '9978987878', '827ccb0eea8a706c4c34a16891f84e7b', '0', 'images/userimages/_5ad9909aae94c.jpg', '', '2018-04-18', '1'),
(12, '                         ', 'xyzw@gmail.com', '9989876756', '827ccb0eea8a706c4c34a16891f84e7b', '0', NULL, '', '2018-04-18', '1'),
(13, 'pramod deore12', 'pr12345@gmail.com', '9989898767', '81dc9bdb52d04dc20036dbd8313ed055', '0', NULL, NULL, '2018-04-19', '1');

-- --------------------------------------------------------

--
-- Table structure for table `userplans`
--

CREATE TABLE IF NOT EXISTS `userplans` (
`id` int(11) NOT NULL,
  `userid` smallint(6) NOT NULL,
  `plantype` tinytext NOT NULL,
  `submitdate` date NOT NULL,
  `enddate` date NOT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1' COMMENT '0:deactive,1:active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `usersession`
--

CREATE TABLE IF NOT EXISTS `usersession` (
`id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `sessionid` tinytext NOT NULL,
  `logindate` date DEFAULT NULL,
  `logoutdate` date DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `usersession`
--

INSERT INTO `usersession` (`id`, `userid`, `sessionid`, `logindate`, `logoutdate`) VALUES
(1, 3, '602bb4912514385c8f658a5f6c295cbd', '2018-04-18', '2018-04-18'),
(2, 3, '808afb36374be38113cf4aff1c0ce705', '2018-04-18', '2018-04-20'),
(3, 4, '06d32d207477d293269691722c9ccbc3', '2018-04-18', NULL),
(4, 10, '457f63c7d7327a7a7e431f9194b154f9', '2018-04-18', '2018-04-18'),
(5, 11, '07f642ab60a4856a1a8df2252832c11d', '2018-04-18', NULL),
(6, 3, '88f78d76b8abc6a4324b175f4468fdd4', '2018-04-20', '2018-04-20'),
(7, 3, 'f7417fc535b4868caeacbf7f2dd0ff9d', '2018-04-20', '2018-04-20'),
(8, 3, 'eabd98f5621c30f6d57434d6f80a6dba', '2018-04-20', NULL),
(9, 13, '168004ca0bfc343003cbe721c167d13b', '2018-04-24', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vid_chapter`
--

CREATE TABLE IF NOT EXISTS `vid_chapter` (
`chapter_id` int(11) NOT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `author_id` int(11) NOT NULL,
  `chapter_name` varchar(50) NOT NULL,
  `chapter_description` longtext,
  `is_topic` enum('0','1') NOT NULL DEFAULT '0' COMMENT '0-nottopic,1-topic',
  `weightage` float DEFAULT NULL,
  `submitdate` datetime NOT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1' COMMENT '0-inactive,1-active'
) ENGINE=InnoDB AUTO_INCREMENT=333 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `vid_chapter`
--

INSERT INTO `vid_chapter` (`chapter_id`, `subject_id`, `author_id`, `chapter_name`, `chapter_description`, `is_topic`, `weightage`, `submitdate`, `active`) VALUES
(285, 1, 123456789, 'Circular Motion 3', 'Circular Motion description3', '0', NULL, '2017-12-02 13:36:18', '1'),
(286, 1, 123456789, 'Period & Time', 'gfh', '0', NULL, '2017-12-02 13:36:34', '1'),
(287, 2, 123456789, 'Basic Concetpt Of Chemistry', 'sdf', '0', NULL, '2017-12-02 14:03:44', '1'),
(288, 2, 123456789, 'Organic Chemistry', 'dsf', '0', NULL, '2017-12-02 14:03:57', '1'),
(289, 2, 123456789, 'Structure Of Atoms', 'sdf', '0', NULL, '2017-12-02 14:04:13', '1'),
(290, 3, 123456789, 'Set', 'dfg', '0', NULL, '2017-12-02 14:04:24', '1'),
(291, 3, 123456789, 'Derivative', 'gfh', '0', NULL, '2017-12-02 14:04:40', '1'),
(292, 3, 123456789, 'Relations and Functions.', 'dsf', '0', NULL, '2017-12-02 14:05:20', '1'),
(293, 3, 123456789, 'Inverse Trigonometric Functions.', 'jhk', '0', NULL, '2017-12-02 14:05:40', '1'),
(294, 3, 123456789, 'Matrices', 'as', '0', NULL, '2017-12-02 14:05:54', '1'),
(295, 3, 123456789, 'Determinants', 'fd', '0', NULL, '2017-12-02 14:06:05', '1'),
(296, 2, 123456789, 'The Solid States', '', '0', NULL, '2017-12-08 14:57:11', '1'),
(297, 2, 123456789, 'solutions', NULL, '0', NULL, '2017-12-08 14:57:19', '1'),
(298, 2, 123456789, 'Electrochemistry', '', '0', NULL, '2017-12-08 14:57:28', '1'),
(299, 2, 123456789, 'Chemical kinetics', NULL, '0', NULL, '2017-12-08 14:57:40', '1'),
(300, 2, 123456789, 'Surface Chemistry', '', '0', NULL, '2017-12-08 14:57:47', '1'),
(301, 2, 123456789, 'Isolation of element', NULL, '0', NULL, '2017-12-08 14:57:58', '1'),
(302, 2, 123456789, 'The P-block element', '', '0', NULL, '2017-12-08 14:58:10', '1'),
(303, 4, 123456789, 'Reproduction in Organisms', '', '0', NULL, '2017-12-08 15:24:51', '1'),
(304, 4, 123456789, 'Sexual Reproduction in Flowering Plants', NULL, '0', NULL, '2017-12-08 15:25:00', '1'),
(305, 4, 123456789, 'Human Reproduction', '', '0', NULL, '2017-12-08 15:25:06', '1'),
(306, 4, 123456789, 'Reproductive Health', NULL, '0', NULL, '2017-12-08 15:25:11', '1'),
(307, 4, 123456789, 'Principle of Inheritance and Variation', '', '0', NULL, '2017-12-08 15:25:18', '1'),
(308, 4, 123456789, 'Molecular Basis of Inheritance', NULL, '0', NULL, '2017-12-08 15:25:27', '1'),
(309, 4, 123456789, 'Evolution', '', '0', NULL, '2017-12-08 15:25:34', '1'),
(310, 4, 123456789, 'Human Health and Diseases', NULL, '0', NULL, '2017-12-08 15:25:41', '1'),
(311, 4, 123456789, 'Strategies for Enhancement in Food Production', '', '0', NULL, '2017-12-08 15:25:45', '1'),
(312, 4, 123456789, 'Microbes in Human Welfare', NULL, '0', NULL, '2017-12-08 15:25:50', '1'),
(313, 4, 123456789, 'Biotechnology: Principles and Processes', '', '0', NULL, '2017-12-08 15:25:56', '1'),
(314, 4, 123456789, 'Biotechnology and its Applications', NULL, '0', NULL, '2017-12-08 15:26:03', '1'),
(315, 4, 123456789, 'Organisms and Populations', '', '0', NULL, '2017-12-08 15:26:09', '1'),
(316, 5, 123456789, 'chp1', 'ds', '0', NULL, '2017-12-11 18:26:05', '1'),
(317, 5, 123456789, 'chap2', '', '0', NULL, '2017-12-11 18:26:10', '1'),
(318, 6, 123456789, 'ch1', 'sds', '0', NULL, '2017-12-11 18:26:29', '1'),
(319, 6, 123456789, 'ch2', 'sad', '0', NULL, '2017-12-11 18:26:34', '1'),
(320, 7, 123456789, 'fsdf', 'sdfsdf', '0', NULL, '2017-12-14 12:36:05', '1'),
(321, 3, 123456789, 'demo set', 'asdf', '0', NULL, '2017-12-19 11:09:46', '1'),
(322, 3, 123456789, 'asdf', '', '0', NULL, '2017-12-19 15:23:19', '1'),
(323, 8, 123456789, 'chapter 1', '', '0', NULL, '2017-12-25 17:30:41', '1'),
(324, 11, 2, 'chaptrer', 'dasd', '0', NULL, '2018-03-08 17:00:28', '1'),
(325, 11, 2, 'asdf', 'asdf', '0', NULL, '2018-03-08 17:00:32', '1'),
(326, 11, 2, 'sdfg', 'sdfg', '0', NULL, '2018-03-08 17:00:36', '1'),
(327, 11, 2, 'dfgh', 'dfgh', '0', NULL, '2018-03-08 17:00:39', '1'),
(328, 9, 2, 'new chatprer by pratik', 'asdf', '0', NULL, '2018-03-09 11:57:18', '1'),
(329, 12, 2, 'chapter 1', 'c1', '0', NULL, '2018-03-09 16:55:58', '1'),
(330, 12, 2, 'chapter 2', 'c2', '0', NULL, '2018-03-09 16:56:06', '1'),
(331, 15, 123456789, 'chptert', 'dfsdf', '0', NULL, '2018-03-12 16:26:20', '1'),
(332, 16, 2, 'dfdf', 'dfdf', '0', NULL, '2018-03-31 15:20:03', '1');

-- --------------------------------------------------------

--
-- Table structure for table `vid_config_value`
--

CREATE TABLE IF NOT EXISTS `vid_config_value` (
  `id` int(11) NOT NULL,
  `text` varchar(200) NOT NULL,
  `value` int(11) NOT NULL,
  `type` varchar(10) NOT NULL,
  `submitdate` datetime NOT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1' COMMENT '0-inactive,1-active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `vid_config_value`
--

INSERT INTO `vid_config_value` (`id`, `text`, `value`, `type`, `submitdate`, `active`) VALUES
(1, 'Demo Test Counter', 10, 'input', '2018-03-15 00:00:00', '1');

-- --------------------------------------------------------

--
-- Table structure for table `vid_contact_us`
--

CREATE TABLE IF NOT EXISTS `vid_contact_us` (
`contact_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `contact` varchar(15) NOT NULL,
  `message` text NOT NULL,
  `city` varchar(100) DEFAULT NULL,
  `contact_date` date NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `vid_contact_us`
--

INSERT INTO `vid_contact_us` (`contact_id`, `name`, `email`, `contact`, `message`, `city`, `contact_date`) VALUES
(26, 'Amit', 'amitmanekar19@gmail.com', '9970286207', 'hello World', 'Pune', '2018-02-26'),
(27, 'pratik', 'pratk3892@gmail.com', '9637960396', 'this is testing msg plz remove', 'Nasik', '2018-02-27'),
(28, 'pratik', 'pratijk@gfma.csdf', '9879879878', 'asdfasdf', 'asdf', '2018-02-27'),
(29, 'pratik', 'paratik@gmail.cm', '9879879878', 'asdf', 'asdf', '2018-02-27'),
(30, 'asdf', 'pratii@gasd.sdfsd', '9876546545', 'asdfasdf', 'asdf', '2018-02-27');

-- --------------------------------------------------------

--
-- Table structure for table `vid_course`
--

CREATE TABLE IF NOT EXISTS `vid_course` (
`course_id` int(11) NOT NULL,
  `author_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `author_type` tinyint(4) NOT NULL,
  `course_name` varchar(50) NOT NULL,
  `course_level` int(3) NOT NULL,
  `description` text,
  `submitdate` datetime NOT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1' COMMENT '0-inactive,1-active'
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `vid_course`
--

INSERT INTO `vid_course` (`course_id`, `author_id`, `category_id`, `author_type`, `course_name`, `course_level`, `description`, `submitdate`, `active`) VALUES
(11, 123456789, 2, 1, 'MHT-CET', 2, 'a sadf asdf', '2017-11-30 15:44:24', '1'),
(12, 123456789, 2, 1, 'JEE MAIN', 2, 'ssdf', '2017-11-30 15:44:35', '1'),
(13, 123456789, 6, 1, 'CAT', 2, 'g', '2017-12-08 17:47:17', '1'),
(14, 123456789, 2, 1, 'AIEEE', 2, 'd', '2018-03-08 16:59:02', '1'),
(15, 123456789, 6, 1, 'MBA', 2, 'MBA CET Exams', '2018-03-12 16:23:08', '1'),
(16, 2, 6, 1, 'BITSAT', 2, 'aaacvxcvsdf dfsdf', '2018-03-12 16:23:47', '1'),
(17, 123456789, 7, 1, 'CDER', 2, 'DEREDSDSDDSD', '2018-03-12 16:24:24', '1'),
(18, 123456789, 2, 1, 'IPU CET', 2, 'f', '2018-03-31 15:19:45', '1'),
(19, 2, 6, 1, 'NDA', 2, 'aaacvxcvsdf dfsdf', '2018-03-31 16:53:58', '1'),
(20, 2, 6, 1, 'NEET', 2, 'aaacvxcv', '2018-03-31 17:04:29', '1'),
(21, 2, 6, 1, 'JEE', 2, 'aaa', '2018-03-31 17:21:42', '1'),
(22, 123456789, 6, 1, 'dgfd', 2, 'gfdgfd', '2018-03-31 17:23:37', '1'),
(23, 123456789, 6, 1, 'asdf', 2, 'asdf', '2018-03-31 17:23:49', '1'),
(24, 123456789, 7, 1, 'asdf', 2, 'asdfasd', '2018-03-31 17:25:25', '1'),
(25, 123456789, 2, 1, 'patric', 2, 'pratk s', '2018-03-31 19:00:28', '1'),
(26, 123456789, 2, 1, 'asdf', 2, 'asdf', '2018-03-31 19:04:34', '1'),
(27, 123456789, 2, 1, 'this last', 2, 'lsdtr', '2018-03-31 19:06:19', '1');

-- --------------------------------------------------------

--
-- Table structure for table `vid_course_category`
--

CREATE TABLE IF NOT EXISTS `vid_course_category` (
`category_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `author_type` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `submitdate` datetime NOT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1' COMMENT '0-inactive,1-active'
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `vid_course_category`
--

INSERT INTO `vid_course_category` (`category_id`, `author_id`, `author_type`, `category_name`, `submitdate`, `active`) VALUES
(1, 123456789, 1, 'Medical', '2017-11-30 15:43:31', '0'),
(2, 123456789, 1, 'Engineering', '2017-11-30 15:43:39', '1'),
(3, 123456789, 1, 'MPSC', '2017-11-30 15:43:56', '0'),
(4, 2, 1, 'testing category', '2018-03-08 16:54:56', '0'),
(5, 2, 1, 'asdfas', '2018-03-09 16:53:45', '0'),
(6, 123456789, 1, 'Management', '2018-03-12 16:19:30', '1'),
(7, 123456789, 1, 'CARED', '2018-03-12 16:24:09', '1');

-- --------------------------------------------------------

--
-- Table structure for table `vid_demo_test_student`
--

CREATE TABLE IF NOT EXISTS `vid_demo_test_student` (
`id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `contact` varchar(15) NOT NULL,
  `submitdate` date NOT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `vid_demo_test_student`
--

INSERT INTO `vid_demo_test_student` (`id`, `name`, `email`, `contact`, `submitdate`, `active`) VALUES
(9, 'Pratik', 'pratik@gmail.com', '9637960396', '2018-03-07', '1'),
(10, 'sdfg', 'ertdf@asdf.sdf', '8765486545', '2018-03-08', '1'),
(11, 'asdf', 'asdf@sdf.dfg', '3453534535', '2018-03-08', '1'),
(12, 'asdf', 'asdf@sfd.fgh', '2341234123', '2018-03-08', '1'),
(13, 'Mohnish', 'mohnish@gmail.com', '8087738274', '2018-03-08', '1'),
(14, 'asdfas', 'vidyarthimitra@gmail.com', '8055135896', '2018-03-08', '1'),
(15, 'xxxxx', 'xxxxx@gmail.com', '9028852043', '2018-03-12', '1'),
(16, 'asdf', 'asdf@zsdfg.fgh', '2345234523', '2018-03-14', '1'),
(17, 'asd', 'asdf@sdf.fgh', '3453453453', '2018-03-15', '1'),
(18, 'asdf', 'wersdr@sdfsd.dfgdf', '2222222222', '2018-03-21', '1'),
(19, 'asdf', 'asdf@sdf.sddf', '2342342342', '2018-03-27', '1'),
(20, 'asdf', 'wresdf@sdf.sdfgt', '3423423423', '2018-03-29', '1'),
(21, 'ghfgh', 'fghgf@gfg.hhg', '9028552043', '2018-03-30', '1'),
(22, 'sdfsdf', 'sdfsdf@gmail.com', '8055535049', '2018-03-31', '1'),
(23, 'asdfs', 'sdf@sdf.sdf', '9637960395', '2018-04-03', '1'),
(24, 'asdf', 'asdfwetr@dsfgs.fgh', '9876541233', '2018-04-03', '1'),
(25, 'asdf', 'asdfasdf@sdf.dgfdf', '6544646565', '2018-04-03', '1'),
(26, 'asdf', 'asdw@sdf.sdf', '6543216543', '2018-04-03', '1'),
(27, 'wer', 'sdfwt@sdfsf.fgh', '9541849654', '2018-04-03', '1'),
(28, 'sdfg', 'ddgd3dgd@dfgd.fgh', '1578965412', '2018-04-03', '1'),
(29, 'asdf', 'wersdf@sdf.fgh', '9999999999', '2018-04-03', '1'),
(30, 'wersd', 'sdfwe@jdfghfg.dfg', '1233213212', '2018-04-03', '1'),
(31, 'asdf', 'wsdfs@sdgfsgf.fghgf', '1233213131', '2018-04-03', '1'),
(32, 'sdfs', 'hsdfgd@kghjg.ghjg', '9637978798', '2018-04-03', '1'),
(33, 'pratik pawar', 'saerhin@gmek.dfe', '2222222223', '2018-04-09', '1');

-- --------------------------------------------------------

--
-- Table structure for table `vid_district`
--

CREATE TABLE IF NOT EXISTS `vid_district` (
`district_id` int(11) NOT NULL,
  `district_name` varchar(100) NOT NULL,
  `state_id` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=687 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `vid_district`
--

INSERT INTO `vid_district` (`district_id`, `district_name`, `state_id`) VALUES
(4, 'Adilabad', 3894),
(5, 'Anantapur', 1303),
(6, 'Chittoor', 1303),
(7, 'East Godavari', 1303),
(8, 'Guntur', 1303),
(9, 'Hyderabad', 3894),
(10, 'Kadapa', 1303),
(11, 'Karimnagar', 3894),
(12, 'Khammam', 3894),
(13, 'Krishna', 1303),
(14, 'Kurnool', 1303),
(15, 'Mahbubnagar', 3894),
(16, 'Medak', 3894),
(17, 'Nalgonda', 3894),
(18, 'Nellore', 1303),
(19, 'Nizamabad', 3894),
(20, 'Prakasam', 1303),
(21, 'Rangareddi', 1303),
(22, 'Srikakulam', 1303),
(23, 'Vishakhapatnam', 1303),
(24, 'Vizianagaram', 1303),
(25, 'Warangal', 3894),
(26, 'West Godavari', 1303),
(37, 'Barpeta', 1304),
(38, 'Bongaigaon', 1304),
(39, 'Cachar', 1304),
(40, 'Darrang', 1304),
(41, 'Dhemaji', 1304),
(42, 'Dhubri', 1304),
(43, 'Dibrugarh', 1304),
(44, 'Goalpara', 1304),
(45, 'Golaghat', 1304),
(46, 'Hailakandi', 1304),
(47, 'Jorhat', 1304),
(48, 'Karbi Anglong', 1304),
(49, 'Karimganj', 1304),
(50, 'Kokrajhar', 1304),
(51, 'Lakhimpur', 1304),
(52, 'Marigaon', 1304),
(53, 'Nagaon', 1304),
(54, 'Nalbari', 1304),
(55, 'North Cachar Hills', 1304),
(56, 'Sibsagar', 1304),
(57, 'Sonitpur', 1304),
(58, 'Tinsukia', 1304),
(59, 'Araria', 1305),
(60, 'Aurangabad', 1305),
(61, 'Banka', 1305),
(62, 'Begusarai', 1305),
(63, 'Bhagalpur', 1305),
(64, 'Bhojpur', 1305),
(65, 'Buxar', 1305),
(66, 'Darbhanga', 1305),
(67, 'Purba Champaran', 1305),
(68, 'Gaya', 1305),
(69, 'Gopalganj', 1305),
(70, 'Jamui', 1305),
(71, 'Jehanabad', 1305),
(72, 'Khagaria', 1305),
(73, 'Kishanganj', 1305),
(74, 'Kaimur', 1305),
(75, 'Katihar', 1305),
(76, 'Lakhisarai', 1305),
(77, 'Madhubani', 1305),
(78, 'Munger', 1305),
(79, 'Madhepura', 1305),
(80, 'Muzaffarpur', 1305),
(81, 'Nalanda', 1305),
(82, 'Nawada', 1305),
(83, 'Patna', 1305),
(84, 'Purnia', 1305),
(85, 'Rohtas', 1305),
(86, 'Saharsa', 1305),
(87, 'Samastipur', 1305),
(88, 'Sheohar', 1305),
(89, 'Sheikhpura', 1305),
(90, 'Saran', 1305),
(91, 'Sitamarhi', 1305),
(92, 'Supaul', 1305),
(93, 'Siwan', 1305),
(94, 'Vaishali', 1305),
(95, 'Pashchim Champaran', 1305),
(96, 'Bastar', 1337),
(97, 'Bilaspur', 1337),
(98, 'Dantewada', 1337),
(99, 'Dhamtari', 1337),
(100, 'Durg', 1337),
(101, 'Jashpur', 1337),
(102, 'Janjgir-Champa', 1337),
(103, 'Korba', 1337),
(104, 'Koriya', 1337),
(105, 'Kanker', 1337),
(106, 'Kawardha', 1337),
(107, 'Mahasamund', 1337),
(108, 'Raigarh', 1337),
(109, 'Rajnandgaon', 1337),
(110, 'Raipur', 1337),
(111, 'Surguja', 1337),
(112, 'Diu', 1332),
(113, 'Daman', 1332),
(123, 'North Goa', 1333),
(124, 'South Goa', 1333),
(125, 'Ahmedabad', 1309),
(126, 'Amreli District', 1309),
(127, 'Anand', 1309),
(128, 'Banaskantha', 1309),
(129, 'Bharuch', 1309),
(130, 'Bhavnagar', 1309),
(131, 'Dahod', 1309),
(132, 'The Dangs', 1309),
(133, 'Gandhinagar', 1309),
(134, 'Jamnagar', 1309),
(135, 'Junagadh', 1309),
(136, 'Kutch', 1309),
(137, 'Kheda', 1309),
(138, 'Mehsana', 1309),
(139, 'Narmada', 1309),
(140, 'Navsari', 1309),
(141, 'Patan', 1309),
(142, 'Panchmahal', 1309),
(143, 'Porbandar', 1309),
(144, 'Rajkot', 1309),
(145, 'Sabarkantha', 1309),
(146, 'Surendranagar', 1309),
(147, 'Surat', 1309),
(148, 'Vadodara', 1309),
(149, 'Valsad', 1309),
(150, 'Ambala', 1310),
(151, 'Bhiwani', 1310),
(152, 'Faridabad', 1310),
(153, 'Fatehabad', 1310),
(154, 'Gurgaon', 1310),
(155, 'Hissar', 1310),
(156, 'Jhajjar', 1310),
(157, 'Jind', 1310),
(158, 'Karnal', 1310),
(159, 'Kaithal', 1310),
(160, 'Kurukshetra', 1310),
(161, 'Mahendragarh', 1310),
(162, 'Mewat', 1310),
(163, 'Panchkula', 1310),
(164, 'Panipat', 1310),
(165, 'Rewari', 1310),
(166, 'Rohtak', 1310),
(167, 'Sirsa', 1310),
(168, 'Sonipat', 1310),
(169, 'Yamuna Nagar', 1310),
(170, 'Palwal', 1310),
(171, 'Bilaspur', 1311),
(172, 'Chamba', 1311),
(173, 'Hamirpur', 1311),
(174, 'Kangra', 1311),
(175, 'Kinnaur', 1311),
(176, 'Kulu', 1311),
(177, 'Lahaul and Spiti', 1311),
(178, 'Mandi', 1311),
(179, 'Shimla', 1311),
(180, 'Sirmaur', 1311),
(181, 'Solan', 1311),
(182, 'Una', 1311),
(199, 'Bokaro', 1338),
(200, 'Chatra', 1338),
(201, 'Deoghar', 1338),
(202, 'Dhanbad', 1338),
(203, 'Dumka', 1338),
(204, 'Purba Singhbhum', 1338),
(205, 'Garhwa', 1338),
(206, 'Giridih', 1338),
(207, 'Godda', 1338),
(208, 'Gumla', 1338),
(209, 'Hazaribagh', 1338),
(210, 'Koderma', 1338),
(211, 'Lohardaga', 1338),
(212, 'Pakur', 1338),
(213, 'Palamu', 1338),
(214, 'Ranchi', 1338),
(215, 'Sahibganj', 1338),
(216, 'Seraikela and Kharsawan', 1338),
(217, 'Pashchim Singhbhum', 1338),
(218, 'Ramgarh', 1338),
(219, 'Bidar', 1319),
(220, 'Belgaum', 1319),
(221, 'Bijapur', 1319),
(222, 'Bagalkot', 1319),
(223, 'Bellary', 1319),
(225, 'Bengaluru', 1319),
(226, 'Chamarajnagar', 1319),
(227, 'Chikmagalur', 1319),
(228, 'Chitradurga', 1319),
(229, 'Davanagere', 1319),
(230, 'Dharwad', 1319),
(231, 'Dakshina Kannada', 1319),
(232, 'Gadag', 1319),
(233, 'Gulbarga', 1319),
(234, 'Hassan', 1319),
(235, 'Haveri District', 1319),
(236, 'Kodagu', 1319),
(237, 'Kolar', 1319),
(238, 'Koppal', 1319),
(239, 'Mandya', 1319),
(240, 'Mysore', 1319),
(241, 'Raichur', 1319),
(242, 'Shimoga', 1319),
(243, 'Tumkur', 1319),
(244, 'Udupi', 1319),
(245, 'Uttara Kannada', 1319),
(246, 'Ramanagara', 1319),
(248, 'Yadagiri', 1319),
(249, 'Alappuzha', 1313),
(250, 'Ernakulam', 1313),
(251, 'Idukki', 1313),
(252, 'Kollam', 1313),
(253, 'Kannur', 1313),
(254, 'Kasaragod', 1313),
(255, 'Kottayam', 1313),
(256, 'Kozhikode', 1313),
(257, 'Malappuram', 1313),
(258, 'Palakkad', 1313),
(259, 'Pathanamthitta', 1313),
(260, 'Thrissur', 1313),
(261, 'Thiruvananthapuram', 1313),
(262, 'Wayanad', 1313),
(263, 'Alirajpur', 1315),
(264, 'Anuppur', 1315),
(265, 'Ashok Nagar', 1315),
(266, 'Balaghat', 1315),
(267, 'Barwani', 1315),
(268, 'Betul', 1315),
(269, 'Bhind', 1315),
(270, 'Bhopal', 1315),
(271, 'Burhanpur', 1315),
(272, 'Chhatarpur', 1315),
(273, 'Chhindwara', 1315),
(274, 'Damoh', 1315),
(275, 'Datia', 1315),
(276, 'Dewas', 1315),
(277, 'Dhar', 1315),
(278, 'Dindori', 1315),
(279, 'Guna', 1315),
(280, 'Gwalior', 1315),
(281, 'Harda', 1315),
(282, 'Hoshangabad', 1315),
(283, 'Indore', 1315),
(284, 'Jabalpur', 1315),
(285, 'Jhabua', 1315),
(286, 'Katni', 1315),
(287, 'Khandwa', 1315),
(288, 'Khargone', 1315),
(289, 'Mandla', 1315),
(290, 'Mandsaur', 1315),
(291, 'Morena', 1315),
(292, 'Narsinghpur', 1315),
(293, 'Neemuch', 1315),
(294, 'Panna', 1315),
(295, 'Rewa', 1315),
(296, 'Rajgarh', 1315),
(297, 'Ratlam', 1315),
(298, 'Raisen', 1315),
(299, 'Sagar', 1315),
(300, 'Satna', 1315),
(301, 'Sehore', 1315),
(302, 'Seoni', 1315),
(303, 'Shahdol', 1315),
(304, 'Shajapur', 1315),
(305, 'Sheopur', 1315),
(306, 'Shivpuri', 1315),
(307, 'Sidhi', 1315),
(308, 'Singrauli', 1315),
(309, 'Tikamgarh', 1315),
(310, 'Ujjain', 1315),
(311, 'Umaria', 1315),
(312, 'Vidisha', 1315),
(313, 'Ahmednagar', 1316),
(314, 'Akola', 1316),
(315, 'Amrawati', 1316),
(316, 'Aurangabad', 1316),
(317, 'Bhandara', 1316),
(318, 'Beed', 1316),
(319, 'Buldhana', 1316),
(320, 'Chandrapur', 1316),
(321, 'Dhule', 1316),
(322, 'Gadchiroli', 1316),
(323, 'Gondiya', 1316),
(324, 'Hingoli', 1316),
(325, 'Jalgaon', 1316),
(326, 'Jalna', 1316),
(327, 'Kolhapur', 1316),
(328, 'Latur', 1316),
(329, 'Mumbai City', 1316),
(330, 'Mumbai suburban', 1316),
(331, 'Nandurbar', 1316),
(332, 'Nanded', 1316),
(333, 'Nagpur', 1316),
(334, 'Nashik', 1316),
(335, 'Osmanabad', 1316),
(336, 'Parbhani', 1316),
(337, 'Pune', 1316),
(338, 'Raigad', 1316),
(339, 'Ratnagiri', 1316),
(340, 'Sindhudurg', 1316),
(341, 'Sangli', 1316),
(342, 'Solapur', 1316),
(343, 'Satara', 1316),
(344, 'Thane', 1316),
(345, 'Wardha', 1316),
(346, 'Washim', 1316),
(347, 'Yavatmal', 1316),
(348, 'Bishnupur', 1317),
(349, 'Churachandpur', 1317),
(350, 'Chandel', 1317),
(351, 'Imphal East', 1317),
(352, 'Senapati', 1317),
(353, 'Tamenglong', 1317),
(354, 'Thoubal', 1317),
(355, 'Ukhrul', 1317),
(356, 'Imphal West', 1317),
(357, 'East Garo Hills', 1318),
(358, 'East Khasi Hills', 1318),
(359, 'Jaintia Hills', 1318),
(360, 'Ri-Bhoi', 1318),
(361, 'South Garo Hills', 1318),
(362, 'West Garo Hills', 1318),
(363, 'West Khasi Hills', 1318),
(364, 'Aizawl', 1331),
(365, 'Champhai', 1331),
(366, 'Kolasib', 1331),
(367, 'Lawngtlai', 1331),
(368, 'Lunglei', 1331),
(369, 'Mamit', 1331),
(370, 'Saiha', 1331),
(371, 'Serchhip', 1331),
(372, 'Dimapur', 1320),
(373, 'Kohima', 1320),
(374, 'Mokokchung', 1320),
(375, 'Mon', 1320),
(376, 'Phek', 1320),
(377, 'Tuensang', 1320),
(378, 'Wokha', 1320),
(379, 'Zunheboto', 1320),
(380, 'Angul', 1321),
(381, 'Boudh', 1321),
(382, 'Bhadrak', 1321),
(383, 'Bolangir', 1321),
(384, 'Bargarh', 1321),
(385, 'Baleswar', 1321),
(386, 'Cuttack', 1321),
(387, 'Debagarh', 1321),
(388, 'Dhenkanal', 1321),
(389, 'Ganjam', 1321),
(390, 'Gajapati', 1321),
(391, 'Jharsuguda', 1321),
(392, 'Jajapur', 1321),
(393, 'Jagatsinghpur', 1321),
(394, 'Khordha', 1321),
(395, 'Kendujhar', 1321),
(396, 'Kalahandi', 1321),
(397, 'Kandhamal', 1321),
(398, 'Koraput', 1321),
(399, 'Kendrapara', 1321),
(400, 'Malkangiri', 1321),
(401, 'Mayurbhanj', 1321),
(402, 'Nabarangpur', 1321),
(403, 'Nuapada', 1321),
(404, 'Nayagarh', 1321),
(405, 'Puri', 1321),
(406, 'Rayagada', 1321),
(407, 'Sambalpur', 1321),
(408, 'Subarnapur', 1321),
(409, 'Sundargarh', 1321),
(414, 'Amritsar', 1323),
(415, 'Bathinda', 1323),
(416, 'Firozpur', 1323),
(417, 'Faridkot', 1323),
(418, 'Fatehgarh Sahib', 1323),
(419, 'Gurdaspur', 1323),
(420, 'Hoshiarpur', 1323),
(421, 'Jalandhar', 1323),
(422, 'Kapurthala', 1323),
(423, 'Ludhiana', 1323),
(424, 'Mansa', 1323),
(425, 'Moga', 1323),
(426, 'Mukatsar', 1323),
(427, 'Nawan Shehar', 1323),
(428, 'Patiala', 1323),
(429, 'Rupnagar', 1323),
(430, 'Sangrur', 1323),
(431, 'Ajmer', 1324),
(432, 'Alwar', 1324),
(433, 'Bikaner', 1324),
(434, 'Barmer', 1324),
(435, 'Banswara', 1324),
(436, 'Bharatpur', 1324),
(437, 'Baran', 1324),
(438, 'Bundi', 1324),
(439, 'Bhilwara', 1324),
(440, 'Churu', 1324),
(441, 'Chittorgarh', 1324),
(442, 'Dausa', 1324),
(443, 'Dholpur', 1324),
(444, 'Dungapur', 1324),
(445, 'Ganganagar', 1324),
(446, 'Hanumangarh', 1324),
(447, 'Juhnjhunun', 1324),
(448, 'Jalore', 1324),
(449, 'Jodhpur', 1324),
(450, 'Jaipur', 1324),
(451, 'Jaisalmer', 1324),
(452, 'Jhalawar', 1324),
(453, 'Karauli', 1324),
(454, 'Kota', 1324),
(455, 'Nagaur', 1324),
(456, 'Pali', 1324),
(457, 'Pratapgarh', 1324),
(458, 'Rajsamand', 1324),
(459, 'Sikar', 1324),
(460, 'Sawai Madhopur', 1324),
(461, 'Sirohi', 1324),
(462, 'Tonk', 1324),
(463, 'Udaipur', 1324),
(464, 'East Sikkim', 1329),
(465, 'North Sikkim', 1329),
(466, 'South Sikkim', 1329),
(467, 'West Sikkim', 1329),
(468, 'Ariyalur', 1325),
(469, 'Chennai', 1325),
(470, 'Coimbatore', 1325),
(471, 'Cuddalore', 1325),
(472, 'Dharmapuri', 1325),
(473, 'Dindigul', 1325),
(474, 'Erode', 1325),
(475, 'Kanchipuram', 1325),
(476, 'Kanyakumari', 1325),
(477, 'Karur', 1325),
(478, 'Madurai', 1325),
(479, 'Nagapattinam', 1325),
(480, 'The Nilgiris', 1325),
(481, 'Namakkal', 1325),
(482, 'Perambalur', 1325),
(483, 'Pudukkottai', 1325),
(484, 'Ramanathapuram', 1325),
(485, 'Salem', 1325),
(486, 'Sivagangai', 1325),
(487, 'Tiruppur', 1325),
(488, 'Tiruchirappalli', 1325),
(489, 'Theni', 1325),
(490, 'Tirunelveli', 1325),
(491, 'Thanjavur', 1325),
(492, 'Thoothukudi', 1325),
(493, 'Thiruvallur', 1325),
(494, 'Thiruvarur', 1325),
(495, 'Tiruvannamalai', 1325),
(496, 'Vellore', 1325),
(497, 'Villupuram', 1325),
(498, 'Dhalai', 1326),
(499, 'North Tripura', 1326),
(500, 'South Tripura', 1326),
(501, 'West Tripura', 1326),
(502, 'Almora', 1339),
(503, 'Bageshwar', 1339),
(504, 'Chamoli', 1339),
(505, 'Champawat', 1339),
(506, 'Dehradun', 1339),
(507, 'Haridwar', 1339),
(508, 'Nainital', 1339),
(509, 'Pauri Garhwal', 1339),
(510, 'Pithoragharh', 1339),
(511, 'Rudraprayag', 1339),
(512, 'Tehri Garhwal', 1339),
(513, 'Udham Singh Nagar', 1339),
(514, 'Uttarkashi', 1339),
(515, 'Agra', 1327),
(516, 'Allahabad', 1327),
(517, 'Aligarh', 1327),
(518, 'Ambedkar Nagar', 1327),
(519, 'Auraiya', 1327),
(520, 'Azamgarh', 1327),
(521, 'Barabanki', 1327),
(522, 'Badaun', 1327),
(523, 'Bagpat', 1327),
(524, 'Bahraich', 1327),
(525, 'Bijnor', 1327),
(526, 'Ballia', 1327),
(527, 'Banda', 1327),
(528, 'Balrampur', 1327),
(529, 'Bareilly', 1327),
(530, 'Basti', 1327),
(531, 'Bulandshahr', 1327),
(532, 'Chandauli', 1327),
(533, 'Chitrakoot', 1327),
(534, 'Deoria', 1327),
(535, 'Etah', 1327),
(536, 'Kanshiram Nagar', 1327),
(537, 'Etawah', 1327),
(538, 'Firozabad', 1327),
(539, 'Farrukhabad', 1327),
(540, 'Fatehpur', 1327),
(541, 'Faizabad', 1327),
(542, 'Gautam Buddha Nagar', 1327),
(543, 'Gonda', 1327),
(544, 'Ghazipur', 1327),
(545, 'Gorkakhpur', 1327),
(546, 'Ghaziabad', 1327),
(547, 'Hamirpur', 1327),
(548, 'Hardoi', 1327),
(549, 'Mahamaya Nagar', 1327),
(550, 'Jhansi', 1327),
(551, 'Jalaun', 1327),
(552, 'Jyotiba Phule Nagar', 1327),
(553, 'Jaunpur District', 1327),
(554, 'Kanpur Dehat', 1327),
(555, 'Kannauj', 1327),
(556, 'Kanpur Nagar', 1327),
(557, 'Kaushambi', 1327),
(558, 'Kushinagar', 1327),
(559, 'Lalitpur', 1327),
(560, 'Lakhimpur Kheri', 1327),
(561, 'Lucknow', 1327),
(562, 'Mau', 1327),
(563, 'Meerut', 1327),
(564, 'Maharajganj', 1327),
(565, 'Mahoba', 1327),
(566, 'Mirzapur', 1327),
(567, 'Moradabad', 1327),
(568, 'Mainpuri', 1327),
(569, 'Mathura', 1327),
(570, 'Muzaffarnagar', 1327),
(571, 'Pilibhit', 1327),
(572, 'Pratapgarh', 1327),
(573, 'Rampur', 1327),
(574, 'Rae Bareli', 1327),
(575, 'Saharanpur', 1327),
(576, 'Sitapur', 1327),
(577, 'Shahjahanpur', 1327),
(578, 'Sant Kabir Nagar', 1327),
(579, 'Siddharthnagar', 1327),
(580, 'Sonbhadra', 1327),
(581, 'Sant Ravidas Nagar', 1327),
(582, 'Sultanpur', 1327),
(583, 'Shravasti', 1327),
(584, 'Unnao', 1327),
(585, 'Varanasi', 1327),
(586, 'Ranga Reddy', 3894),
(588, 'Alipurduar', 3893),
(591, 'Bankura', 3893),
(592, 'Bardhaman', 3893),
(593, 'Birbhum', 3893),
(594, 'Cooch Behar', 3893),
(595, 'Darjeeling', 3893),
(596, 'East Midnapore', 3893),
(597, 'Hooghly', 3893),
(598, 'Howrah', 3893),
(599, 'Jalpaiguri', 3893),
(600, 'Kolkata', 3893),
(601, 'Malda', 3893),
(602, 'Murshidabad', 3893),
(603, 'Nadia', 3893),
(604, 'North 24 Parganas', 3893),
(605, 'North Dinajpur', 3893),
(606, 'Purulia', 3893),
(607, 'South 24 Parganas', 3893),
(608, 'South Dinajpur', 3893),
(609, 'West Midnapore', 3893),
(610, 'Barnala', 1323),
(611, 'Fazilka ', 1323),
(612, 'Mohali', 1323),
(613, 'Sri Muktsar Sahib', 1323),
(614, 'Nawan Shahr', 1323),
(615, 'Pathankot', 1323),
(616, 'Tarn Taran', 1323),
(617, 'Changlang ', 1330),
(618, 'Dibang Valley ', 1330),
(619, 'East Kameng ', 1330),
(620, 'East Siang', 1330),
(621, 'Kurung Kumey', 1330),
(622, 'Lohit', 1330),
(623, 'Lower Dibang Valley', 1330),
(624, 'Lower Subansiri', 1330),
(625, 'Papum Pare', 1330),
(626, 'Tawang', 1330),
(627, 'Tirap', 1330),
(628, 'Upper Siang', 1330),
(629, 'Upper Subansiri', 1330),
(630, 'West Kameng', 1330),
(631, 'West Siang', 1330),
(632, 'Delhi', 1308),
(633, 'New Delhi ', 1308),
(634, 'Doda ', 1312),
(635, 'Jammu ', 1312),
(636, 'Kathua ', 1312),
(637, 'Kishtwar ', 1312),
(638, 'Poonch ', 1312),
(639, 'Rajouri ', 1312),
(640, 'Ramban ', 1312),
(641, 'Reasi ', 1312),
(642, 'Samba ', 1312),
(643, 'Udhampur ', 1312),
(644, 'Anantnag ', 1312),
(645, 'Bandipora ', 1312),
(646, 'Baramulla ', 1312),
(647, 'Budgam ', 1312),
(648, 'Ganderbal ', 1312),
(649, 'Kulgam ', 1312),
(650, 'Kupwara ', 1312),
(651, 'Pulwama ', 1312),
(652, 'Shopian ', 1312),
(653, 'Srinagar ', 1312),
(656, 'Chandigarh', 1323),
(657, 'Karailkal', 1322),
(659, 'Pondicherry', 1322),
(660, 'Virudhunagar', 1325),
(661, 'Kamrup', 1304),
(662, 'Guwahati', 1304),
(663, 'Itanagar', 1330),
(664, 'Shillong', 1318),
(665, 'Port Blair', 1302),
(666, 'Dadra and Nagar Haveli', 1307),
(667, 'Kargil ', 1312),
(668, 'Leh', 1312),
(669, 'Chikballapura', 1319),
(670, 'Yanam', 1322),
(671, 'Mahe', 1322),
(672, 'shahid bhagat singh nagar', 1323),
(673, 'Krishnagiri', 1325),
(674, 'Budaun', 1327),
(675, 'Sambhal', 1327),
(676, 'Amethi', 1327),
(677, 'Hathras', 1327),
(678, 'Ladakh', 1312),
(679, 'bhubaneswar', 1321),
(680, 'Rourkela', 1321),
(682, 'Mangalore', 1319),
(683, 'Kashipur', 1339),
(684, 'Greater Noida', 1327),
(685, 'Kharagpur', 3893),
(686, 'Roorkee', 1339);

-- --------------------------------------------------------

--
-- Table structure for table `vid_doubt_table`
--

CREATE TABLE IF NOT EXISTS `vid_doubt_table` (
`d_id` int(11) NOT NULL,
  `qun_id` int(50) NOT NULL,
  `stud_id` int(50) NOT NULL,
  `schuduled_id` int(50) NOT NULL,
  `exam_id` int(50) NOT NULL,
  `add_date` date NOT NULL,
  `solved_date` date NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `vid_doubt_table`
--

INSERT INTO `vid_doubt_table` (`d_id`, `qun_id`, `stud_id`, `schuduled_id`, `exam_id`, `add_date`, `solved_date`, `status`) VALUES
(1, 216, 318, 290, 133, '2018-04-13', '0000-00-00', '0'),
(2, 217, 318, 290, 133, '2018-04-13', '0000-00-00', '0'),
(3, 217, 319, 290, 133, '2018-04-13', '0000-00-00', '0');

-- --------------------------------------------------------

--
-- Table structure for table `vid_exam`
--

CREATE TABLE IF NOT EXISTS `vid_exam` (
`exam_id` int(11) NOT NULL,
  `author_id` int(11) DEFAULT NULL,
  `course_id` int(11) NOT NULL,
  `exam_name` varchar(50) NOT NULL,
  `no_of_question` int(11) NOT NULL,
  `submitdate` datetime NOT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1' COMMENT '0-inactive,1-active'
) ENGINE=InnoDB AUTO_INCREMENT=145 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `vid_exam`
--

INSERT INTO `vid_exam` (`exam_id`, `author_id`, `course_id`, `exam_name`, `no_of_question`, `submitdate`, `active`) VALUES
(129, 123456789, 11, 'Summer Exam', 15, '2018-04-13 11:12:15', '1'),
(133, 123456789, 11, 'pratik testing', 11, '2018-04-13 11:17:50', '1'),
(134, 123456789, 12, 'Winter Jee', 10, '2018-04-13 11:50:09', '1'),
(144, 123456789, 11, 'testing exam', 10, '2018-04-13 15:22:52', '0');

-- --------------------------------------------------------

--
-- Table structure for table `vid_exam_chapter_questions`
--

CREATE TABLE IF NOT EXISTS `vid_exam_chapter_questions` (
`ecq_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `chapter_id` int(11) NOT NULL,
  `no_of_ques` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=391 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `vid_exam_chapter_questions`
--

INSERT INTO `vid_exam_chapter_questions` (`ecq_id`, `exam_id`, `chapter_id`, `no_of_ques`) VALUES
(104, 79, 323, 10),
(106, 78, 323, 10),
(107, 80, 323, 10),
(170, 79, 323, 10),
(188, 82, 323, 10),
(239, 83, 323, 10),
(299, 84, 285, 5),
(300, 84, 286, 20),
(301, 84, 287, 5),
(302, 86, 286, 25),
(315, 87, 286, 10),
(325, 88, 286, 10),
(326, 98, 323, 11),
(327, 101, 286, 10),
(328, 106, 285, 10),
(329, 109, 323, 10),
(330, 81, 324, 5),
(331, 81, 325, 20),
(337, 118, 286, 10),
(339, 119, 286, 10),
(340, 120, 286, 10),
(341, 117, 286, 10),
(342, 77, 285, 5),
(343, 77, 287, 5),
(358, 121, 285, 5),
(359, 121, 286, 5),
(360, 121, 287, 5),
(361, 121, 290, 4),
(362, 121, 291, 1),
(365, 122, 285, 10),
(366, 123, 285, 11),
(367, 124, 285, 11),
(368, 125, 285, 11),
(369, 126, 285, 11),
(370, 127, 285, 11),
(371, 128, 285, 11),
(373, 129, 285, 5),
(374, 129, 287, 5),
(375, 129, 303, 5),
(376, 130, 285, 11),
(377, 131, 285, 11),
(378, 132, 285, 11),
(379, 133, 285, 11),
(380, 134, 323, 10),
(381, 135, 285, 10),
(382, 136, 285, 10),
(383, 137, 285, 10),
(384, 138, 285, 10),
(385, 139, 285, 10),
(386, 140, 285, 10),
(387, 141, 285, 10),
(388, 142, 285, 10),
(389, 143, 285, 10),
(390, 144, 285, 10);

-- --------------------------------------------------------

--
-- Table structure for table `vid_exam_questions`
--

CREATE TABLE IF NOT EXISTS `vid_exam_questions` (
`exam_ques_id` int(11) NOT NULL,
  `ques_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `vid_exam_schedule`
--

CREATE TABLE IF NOT EXISTS `vid_exam_schedule` (
`schedule_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `sub_group_id` int(11) DEFAULT NULL,
  `exam_date` date DEFAULT NULL,
  `exam_duration` int(11) NOT NULL,
  `fee` bigint(100) DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `exam_mode` enum('0','1','2') NOT NULL COMMENT 'online=0,offline=1,both=2',
  `submit_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=309 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `vid_exam_schedule`
--

INSERT INTO `vid_exam_schedule` (`schedule_id`, `exam_id`, `sub_group_id`, `exam_date`, `exam_duration`, `fee`, `start_time`, `end_time`, `exam_mode`, `submit_date`) VALUES
(270, 129, 2, '2018-04-13', 10, 10, NULL, NULL, '0', '2018-04-16 09:56:46'),
(285, 129, 1, '2018-04-16', 10, 10, NULL, NULL, '0', '2018-04-16 09:52:52'),
(290, 133, 1, '2018-04-16', 11, 11, NULL, NULL, '0', '2018-04-16 09:52:58'),
(291, 134, 6, '2018-04-13', 10, 10, NULL, NULL, '0', '2018-04-13 06:20:09'),
(308, 144, 3, '2018-04-13', 22, 22, NULL, NULL, '0', '2018-04-13 09:52:52');

-- --------------------------------------------------------

--
-- Table structure for table `vid_fbconcern`
--

CREATE TABLE IF NOT EXISTS `vid_fbconcern` (
  `concern_id` int(11) NOT NULL,
  `concern` varchar(500) NOT NULL,
  `submit_date` datetime DEFAULT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1' COMMENT '0-inactive,1-active'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `vid_fbdetails`
--

CREATE TABLE IF NOT EXISTS `vid_fbdetails` (
`feedback_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `concern_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `fb_msg` varchar(1000) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `receiver_type` varchar(100) NOT NULL,
  `reply_msg` varchar(500) DEFAULT NULL,
  `fbsubmit_date` datetime NOT NULL,
  `fbreply_date` datetime DEFAULT NULL,
  `fbactive` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `vid_fbdetails`
--

INSERT INTO `vid_fbdetails` (`feedback_id`, `course_id`, `concern_id`, `student_id`, `fb_msg`, `receiver_id`, `receiver_type`, `reply_msg`, `fbsubmit_date`, `fbreply_date`, `fbactive`) VALUES
(1, 0, 0, 318, 'asdf', 0, '', NULL, '2018-03-16 17:06:07', NULL, '1'),
(2, 0, 0, 318, 'asdfasdf', 0, '', NULL, '2018-03-16 17:56:11', NULL, '1'),
(3, 0, 0, 318, 'asdfsdfgfjkjk', 0, '', NULL, '2018-03-16 19:14:23', NULL, '1'),
(4, 0, 0, 318, 'asdfasdf', 0, '', NULL, '2018-03-20 11:09:35', NULL, '1'),
(5, 0, 0, 318, '849', 0, '', NULL, '2018-03-30 16:54:49', NULL, '1'),
(6, 0, 0, 319, 'sdfsds', 0, '', NULL, '2018-04-12 16:23:31', NULL, '1'),
(7, 0, 0, 346, '&nbsp', 0, '', NULL, '2018-04-12 17:19:52', NULL, '1'),
(8, 0, 0, 346, '<p></p>', 0, '', NULL, '2018-04-12 17:20:19', NULL, '1'),
(9, 0, 0, 353, 'Hello admin', 0, '', NULL, '2018-04-13 17:17:03', NULL, '1'),
(10, 0, 0, 353, 'Where does it come from?\nContrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of "de Finibus Bonorum et Malorum" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, "Lorem ipsum dolor sit amet..", comes from a line in section 1.10.32.\n\nThe standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those interested. Sections 1.10.32 and 1.10.33 from "de Finibus Bonorum et Malorum" by Cicero are also reproduced in their exact or', 0, '', NULL, '2018-04-13 17:18:31', NULL, '1');

-- --------------------------------------------------------

--
-- Table structure for table `vid_forget_password`
--

CREATE TABLE IF NOT EXISTS `vid_forget_password` (
`forget_id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `type` int(11) NOT NULL,
  `token` varchar(50) NOT NULL,
  `submitdate` datetime NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '0' COMMENT '0-pending,1-complete'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `vid_forum`
--

CREATE TABLE IF NOT EXISTS `vid_forum` (
`forum_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `description` longtext NOT NULL,
  `senddate` datetime NOT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1' COMMENT '0-incomplete,1-complete'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `vid_master`
--

CREATE TABLE IF NOT EXISTS `vid_master` (
  `master_id` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1' COMMENT '0-inactive,1-active'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `vid_master`
--

INSERT INTO `vid_master` (`master_id`, `email`, `password`, `active`) VALUES
(2, 'admin2@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '1'),
(123456789, 'admin@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '1');

-- --------------------------------------------------------

--
-- Table structure for table `vid_notes`
--

CREATE TABLE IF NOT EXISTS `vid_notes` (
`note_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `submitdate` datetime NOT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1' COMMENT '0-inactive,1-active'
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `vid_notes`
--

INSERT INTO `vid_notes` (`note_id`, `course_id`, `title`, `submitdate`, `active`) VALUES
(11, 11, 'Circular motion', '2018-03-27 18:24:57', '1'),
(12, 14, 'asdf', '2018-03-27 19:39:11', '1'),
(13, 12, 'asdf', '2018-03-28 13:20:08', '1'),
(14, 12, 'asdf', '2018-03-28 13:20:15', '1'),
(15, 12, 'asdf', '2018-03-28 13:24:17', '1'),
(16, 12, 'asdf', '2018-03-28 13:24:32', '1'),
(17, 11, 'awer', '2018-03-28 13:25:27', '1'),
(18, 11, 'asdf', '2018-03-28 13:32:32', '1'),
(19, 11, 'asdf', '2018-03-28 13:33:01', '1');

-- --------------------------------------------------------

--
-- Table structure for table `vid_notes_path`
--

CREATE TABLE IF NOT EXISTS `vid_notes_path` (
`note_path_id` int(11) NOT NULL,
  `note_id` int(11) NOT NULL,
  `file_name` varchar(100) DEFAULT NULL,
  `note_path` varchar(100) NOT NULL,
  `type` varchar(50) NOT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1' COMMENT '0-inactive,1-active'
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `vid_notes_path`
--

INSERT INTO `vid_notes_path` (`note_path_id`, `note_id`, `file_name`, `note_path`, `type`, `active`) VALUES
(13, 12, 'Student_HallTicket (7).pdf', 'images/notes/docs/12_5aba50875afa2.pdf', 'doc', '1'),
(14, 13, 'Student_HallTicket (25).pdf', 'images/notes/docs/13_5abb4930bbec4.pdf', 'doc', '1'),
(15, 14, 'Student_HallTicket (25).pdf', 'images/notes/docs/14_5abb493758dcb.pdf', 'doc', '1'),
(16, 15, 'Student_HallTicket (25).pdf', 'images/notes/docs/15_5abb4a29de1d9.pdf', 'doc', '1'),
(17, 16, 'Student_HallTicket (25).pdf', 'images/notes/docs/16_5abb4a385af84.pdf', 'doc', '1'),
(18, 17, 'Student_HallTicket (3).pdf', 'images/notes/docs/17_5abb4a6f27fa2.pdf', 'doc', '1'),
(19, 18, 'Student_HallTicket (20).pdf', 'images/notes/18_5abb4c182ea50.pdf', 'doc', '1'),
(20, 19, 'Student_HallTicket (20).pdf', 'images/notes/19_5abb4c3599804.pdf', 'doc', '1');

-- --------------------------------------------------------

--
-- Table structure for table `vid_prepair_test`
--

CREATE TABLE IF NOT EXISTS `vid_prepair_test` (
`pid` int(11) NOT NULL,
  `stud_id` int(11) NOT NULL,
  `chapter_id` int(11) NOT NULL,
  `topic_id` int(11) DEFAULT NULL,
  `course_id` int(11) NOT NULL,
  `last_attempt` int(11) DEFAULT '0',
  `attempt_question` int(11) DEFAULT '0',
  `total_attempt` int(11) NOT NULL DEFAULT '0',
  `submitdate` datetime NOT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1' COMMENT '0-inactive,1-active'
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `vid_prepair_test`
--

INSERT INTO `vid_prepair_test` (`pid`, `stud_id`, `chapter_id`, `topic_id`, `course_id`, `last_attempt`, `attempt_question`, `total_attempt`, `submitdate`, `active`) VALUES
(1, 318, 287, NULL, 0, 0, 0, 1, '2018-01-02 18:09:06', '1'),
(2, 331, 285, NULL, 0, 0, 0, 0, '2018-01-08 15:38:11', '1'),
(3, 318, 293, NULL, 0, 0, 0, 0, '2018-01-08 15:47:00', '1'),
(4, 318, 286, NULL, 0, 0, 0, 0, '2018-01-09 19:19:25', '1'),
(5, 318, 288, NULL, 0, 0, 0, 0, '2018-01-09 19:26:19', '1'),
(6, 318, 285, NULL, 0, 0, 0, 2, '2018-01-10 10:50:40', '1'),
(7, 318, 292, NULL, 0, 0, 0, 0, '2018-02-14 16:10:30', '1'),
(8, 318, 290, NULL, 0, 0, 0, 0, '2018-02-14 16:10:53', '1'),
(9, 318, 301, NULL, 0, 0, 0, 0, '2018-02-26 13:01:01', '1'),
(10, 319, 285, NULL, 0, 0, 0, 1, '2018-04-12 16:22:16', '1');

-- --------------------------------------------------------

--
-- Table structure for table `vid_question`
--

CREATE TABLE IF NOT EXISTS `vid_question` (
`ques_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `chapter_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `topic_id` int(11) DEFAULT NULL,
  `is_final` int(5) NOT NULL DEFAULT '0',
  `ques_text` longtext CHARACTER SET utf8 NOT NULL,
  `ques_type` enum('0','1') DEFAULT '0' COMMENT '0-normal,1-paragraph',
  `paraghaph_id` int(11) NOT NULL DEFAULT '0',
  `is_sequence` enum('0','1') NOT NULL DEFAULT '1' COMMENT '0-random,1-sequence',
  `submitdate` datetime NOT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1' COMMENT '0-inactive,1-active',
  `qun_mark` int(11) NOT NULL,
  `qun_neg_mark` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=237 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `vid_question`
--

INSERT INTO `vid_question` (`ques_id`, `author_id`, `chapter_id`, `course_id`, `topic_id`, `is_final`, `ques_text`, `ques_type`, `paraghaph_id`, `is_sequence`, `submitdate`, `active`, `qun_mark`, `qun_neg_mark`) VALUES
(14, 123456789, 285, 11, NULL, 0, 'A large parallel plate capacitor of capacitance 5&micro;C is being charged at a rate of 25 V/s. Then the displacement current at this instant is', '0', 0, '0', '2017-12-08 17:34:01', '1', 1, 1),
(15, 123456789, 285, 11, NULL, 0, 'If for a material, Y and K are Young&rsquo;s modulus and bulk modulus then :', '0', 0, '0', '2017-12-08 17:34:01', '1', 1, 1),
(16, 123456789, 286, 11, NULL, 0, 'SI unit of damping constant is', '0', 0, '0', '2017-12-08 17:34:01', '1', 0, 0),
(17, 123456789, 287, 11, NULL, 0, 'The solid NaCl is a bad conductor of electricity since', '0', 0, '0', '2017-12-08 17:36:14', '1', 1, 1),
(18, 123456789, 287, 11, NULL, 0, 'The maximum radius of sphere that can be fitted in theoctahedral hole of cubical closed packing of sphere of radiusr is', '0', 0, '0', '2017-12-08 17:36:14', '1', 1, 1),
(19, 123456789, 287, 11, NULL, 0, 'The fraction of total volume occupied by the atoms presentin a simple cube is', '0', 0, '0', '2017-12-08 17:36:14', '1', 1, 1),
(20, 123456789, 287, 11, NULL, 0, 'The number of unit cells in 58.5g of NaCl is nearly', '0', 0, '1', '2017-12-08 17:36:14', '1', 2, 1),
(21, 123456789, 290, 11, NULL, 0, 'Negation is &ldquo;2 + 3 = 5 and 8 &lt; 10&rdquo; is', '0', 0, '0', '2017-12-08 17:39:34', '1', 0, 0),
(23, 123456789, 290, 11, NULL, 0, '(p ^ ~ q) ^ (~ p ^ q ) is equal', '0', 0, '0', '2017-12-08 17:39:34', '1', 0, 0),
(28, 123456789, 290, 11, NULL, 0, '290 qun testing', '0', 0, '0', '2017-12-20 11:07:52', '1', 0, 0),
(30, 123456789, 323, 22, NULL, 0, 'asdfasd', '0', 0, '0', '2017-12-25 17:33:48', '1', 0, 0),
(31, 123456789, 323, 22, NULL, 0, 'asdf', '0', 0, '0', '2017-12-25 17:34:06', '1', 0, 0),
(32, 123456789, 323, 22, NULL, 0, 'asdf', '0', 0, '0', '2017-12-25 17:38:53', '1', 0, 0),
(33, 123456789, 323, 22, NULL, 0, 'gwegw', '0', 0, '0', '2017-12-25 17:39:31', '1', 0, 0),
(34, 123456789, 323, 22, NULL, 0, 'erty', '0', 0, '0', '2017-12-25 17:39:59', '1', 0, 0),
(35, 123456789, 323, 22, NULL, 0, 'hjtrj', '0', 0, '0', '2017-12-25 17:40:14', '1', 0, 0),
(36, 123456789, 323, 22, NULL, 0, 'jrjr', '0', 0, '0', '2017-12-25 17:40:28', '1', 0, 0),
(37, 123456789, 323, 22, NULL, 0, 'rtrt', '0', 0, '0', '2017-12-25 17:40:42', '1', 0, 0),
(38, 123456789, 323, 22, NULL, 0, 'igkfg', '0', 0, '0', '2017-12-25 17:40:57', '1', 0, 0),
(39, 123456789, 323, 22, NULL, 0, 'gkghjk', '0', 0, '0', '2017-12-25 17:41:13', '1', 0, 0),
(75, 0, 328, 12, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 12:04:57', '1', 5, 4),
(76, 0, 328, 12, NULL, 0, 'it is demo test questionsdf', '0', 0, '0', '2018-03-09 12:04:57', '1', 4, 5),
(77, 0, 328, 12, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 12:04:57', '1', 5, 4),
(78, 0, 328, 12, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 12:04:57', '1', 5, 4),
(79, 0, 328, 12, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 12:04:57', '1', 5, 4),
(80, 0, 328, 12, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 12:04:57', '1', 5, 4),
(81, 0, 328, 12, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 12:04:57', '1', 5, 4),
(82, 0, 328, 12, NULL, 0, 'it is demo test questionsdf', '0', 0, '0', '2018-03-09 12:04:57', '1', 4, 5),
(83, 0, 328, 12, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 12:04:57', '1', 5, 4),
(84, 0, 328, 12, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 12:04:57', '1', 5, 4),
(85, 0, 328, 12, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 12:04:57', '1', 5, 4),
(86, 0, 328, 12, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 12:04:57', '1', 5, 4),
(87, 0, 328, 12, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 12:04:57', '1', 5, 4),
(88, 0, 328, 12, NULL, 0, 'it is demo test questionsdf', '0', 0, '0', '2018-03-09 12:04:57', '1', 4, 5),
(89, 0, 328, 12, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 12:04:57', '1', 5, 4),
(90, 0, 328, 12, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 12:04:57', '1', 5, 4),
(91, 0, 328, 12, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 12:04:57', '1', 5, 4),
(92, 0, 328, 12, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 12:04:57', '1', 5, 4),
(93, 0, 328, 12, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 12:04:57', '1', 5, 4),
(94, 0, 328, 12, NULL, 0, 'it is demo test questionsdf', '0', 0, '0', '2018-03-09 12:04:57', '1', 4, 5),
(95, 0, 328, 12, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 12:04:57', '1', 5, 4),
(96, 0, 328, 12, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 12:04:57', '1', 5, 4),
(97, 0, 328, 12, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 12:04:57', '1', 5, 4),
(98, 0, 328, 12, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 12:04:57', '1', 5, 4),
(99, 0, 286, 11, NULL, 0, 'qwer', '0', 0, '1', '2018-03-09 12:09:00', '1', 11, 11),
(100, 0, 286, 11, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 12:09:47', '1', 5, 4),
(101, 0, 286, 11, NULL, 0, 'it is demo test questionsdf', '0', 0, '0', '2018-03-09 12:09:47', '1', 4, 5),
(102, 0, 286, 11, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 12:09:47', '1', 5, 4),
(103, 0, 286, 11, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 12:09:47', '1', 5, 4),
(104, 0, 286, 11, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 12:09:47', '1', 5, 4),
(105, 0, 286, 11, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 12:09:47', '1', 5, 4),
(106, 0, 286, 11, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 12:09:47', '1', 5, 4),
(107, 0, 286, 11, NULL, 0, 'it is demo test questionsdf', '0', 0, '0', '2018-03-09 12:09:47', '1', 4, 5),
(108, 0, 286, 11, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 12:09:47', '1', 5, 4),
(109, 0, 286, 11, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 12:09:47', '1', 5, 4),
(110, 0, 286, 11, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 12:09:47', '1', 5, 4),
(111, 0, 286, 11, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 12:09:47', '1', 5, 4),
(112, 0, 286, 11, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 12:09:47', '1', 5, 4),
(113, 0, 286, 11, NULL, 0, 'it is demo test questionsdf', '0', 0, '0', '2018-03-09 12:09:47', '1', 5, 4),
(114, 0, 286, 11, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 12:09:47', '1', 5, 4),
(115, 0, 286, 11, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 12:09:47', '1', 5, 4),
(116, 0, 286, 11, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 12:09:47', '1', 5, 4),
(117, 0, 286, 11, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 12:09:47', '1', 5, 4),
(118, 0, 286, 11, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 12:09:47', '1', 5, 4),
(119, 0, 286, 11, NULL, 0, 'it is demo test questionsdf', '0', 0, '0', '2018-03-09 12:09:47', '1', 4, 5),
(120, 0, 286, 11, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 12:09:47', '1', 5, 4),
(121, 0, 286, 11, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 12:09:47', '1', 5, 4),
(122, 0, 286, 11, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 12:09:47', '1', 5, 4),
(124, 0, 329, 14, NULL, 0, 'demo qun<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/Capture.PNG" style="height:72px; width:200px" />asdfasdfadfs', '0', 0, '0', '2018-03-09 17:03:31', '1', 4, 2),
(125, 0, 329, 14, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 17:07:33', '1', 5, 4),
(126, 0, 329, 14, NULL, 0, 'it is demo test questionsdf', '0', 0, '0', '2018-03-09 17:07:33', '1', 4, 4),
(127, 0, 329, 14, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 17:07:33', '1', 5, 4),
(128, 0, 329, 14, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 17:07:33', '1', 5, 4),
(129, 0, 329, 14, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 17:07:33', '1', 5, 4),
(130, 0, 329, 14, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 17:07:33', '1', 5, 4),
(131, 0, 329, 14, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 17:07:33', '1', 5, 4),
(132, 0, 329, 14, NULL, 0, 'it is demo test questionsdf', '0', 0, '0', '2018-03-09 17:07:33', '1', 4, 4),
(133, 0, 329, 14, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 17:07:33', '1', 5, 4),
(134, 0, 329, 14, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 17:07:33', '1', 5, 4),
(135, 0, 329, 14, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 17:07:33', '1', 5, 4),
(136, 0, 329, 14, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 17:07:33', '1', 5, 4),
(137, 0, 329, 14, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 17:07:33', '1', 5, 4),
(138, 0, 329, 14, NULL, 0, 'it is demo test questionsdf', '0', 0, '0', '2018-03-09 17:07:33', '1', 4, 4),
(139, 0, 329, 14, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 17:07:33', '1', 5, 4),
(140, 0, 329, 14, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 17:07:33', '1', 5, 4),
(141, 0, 329, 14, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 17:07:33', '1', 5, 4),
(142, 0, 329, 14, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 17:07:33', '1', 5, 4),
(143, 0, 329, 14, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 17:07:33', '1', 5, 4),
(144, 0, 329, 14, NULL, 0, 'it is demo test questionsdf', '0', 0, '0', '2018-03-09 17:07:33', '1', 4, 4),
(145, 0, 329, 14, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 17:07:33', '1', 5, 4),
(146, 0, 329, 14, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 17:07:33', '1', 5, 4),
(147, 0, 329, 14, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 17:07:33', '1', 5, 4),
(148, 0, 329, 14, NULL, 0, 'it is demo test question', '0', 0, '0', '2018-03-09 17:07:33', '1', 5, 4),
(149, 0, 331, 17, NULL, 0, 'sdsdf', '0', 0, '0', '2018-03-12 16:30:39', '1', 0, 1),
(150, 0, 331, 17, NULL, 0, 'sdfsdf', '0', 0, '0', '2018-03-12 17:08:43', '1', 1, 1),
(151, 0, 291, 11, NULL, 1, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/mQue(9)/Q.9.JPG" style="height:94px; width:371px" />', '0', 0, '0', '2018-03-14 15:16:27', '1', 4, 2),
(152, 0, 285, 11, NULL, 0, 'this is testing qun', '0', 0, '0', '2018-03-14 17:26:05', '1', 1, 1),
(153, 0, 285, 11, NULL, 0, 'asdf', '0', 0, '0', '2018-03-14 17:26:27', '1', 1, 1),
(154, 0, 285, 11, NULL, 0, 'asdfsdf', '0', 0, '0', '2018-03-27 11:32:21', '1', 1, 2),
(157, 0, 285, 11, NULL, 0, 'aaaaaaaaa', '0', 0, '1', '2018-03-27 11:54:10', '1', 4, 2),
(158, 0, 285, 11, NULL, 0, '456456', '0', 0, '1', '2018-03-30 17:17:32', '1', 4, 1),
(159, 0, 323, 12, NULL, 0, '4545', '0', 0, '1', '2018-03-30 18:01:04', '1', 4, 1),
(160, 0, 323, 12, NULL, 0, '4545', '0', 0, '1', '2018-03-30 18:03:43', '1', 4, 1),
(161, 0, 323, 12, NULL, 0, '4545', '0', 0, '1', '2018-03-30 18:05:54', '1', 4, 1),
(162, 0, 323, 12, NULL, 0, '4545', '0', 0, '1', '2018-03-30 18:13:47', '1', 4, 1),
(163, 0, 323, 12, NULL, 0, '4545', '0', 0, '1', '2018-03-30 18:14:22', '1', 4, 1),
(164, 0, 323, 12, NULL, 0, '4545', '0', 0, '1', '2018-03-30 18:16:09', '1', 4, 1),
(165, 0, 323, 12, NULL, 0, '4545', '0', 0, '1', '2018-03-30 18:16:48', '1', 4, 1),
(216, 0, 285, 11, NULL, 1, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/pQue_(1)/Q.1.JPG" style="height:147px; width:455px" />', '0', 0, '1', '2018-04-12 12:31:22', '1', 2, 1),
(217, 0, 285, 11, NULL, 1, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/pQue_(10)/Q.10.JPG" style="height:87px; width:493px" />', '0', 0, '1', '2018-04-12 12:33:23', '1', 2, 1),
(218, 0, 285, 11, NULL, 1, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/pQue_(11)/Q.11.JPG" style="height:113px; width:495px" />', '0', 0, '1', '2018-04-12 12:35:50', '1', 2, 1),
(219, 0, 285, 11, NULL, 1, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/pQue_(12)/Q.12.JPG" style="height:176px; width:484px" />', '0', 0, '1', '2018-04-12 12:37:41', '1', 2, 1),
(220, 0, 285, 11, NULL, 1, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/pQue_(13)/Q.13.JPG" style="height:115px; width:453px" />', '0', 0, '1', '2018-04-12 12:55:18', '1', 2, 1),
(221, 0, 286, 11, NULL, 1, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/p2Que_(50)/Q.50.JPG" style="height:99px; width:443px" />', '0', 0, '1', '2018-04-12 13:12:02', '1', 2, 1),
(222, 0, 286, 11, NULL, 1, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/p2Que_(6)/Q.6.JPG" style="height:86px; width:461px" />', '0', 0, '1', '2018-04-12 13:12:55', '1', 2, 1),
(223, 0, 286, 11, NULL, 1, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/p2Que_(7)/Q.7.JPG" style="height:62px; width:443px" />', '0', 0, '1', '2018-04-12 13:13:56', '1', 2, 1),
(224, 0, 286, 11, NULL, 1, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/p2Que_(8)/Q.8.JPG" style="height:144px; width:473px" />', '0', 0, '1', '2018-04-12 13:14:56', '1', 2, 1),
(225, 0, 286, 11, NULL, 1, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/p2Que_(9)/Q.9.JPG" style="height:143px; width:461px" />', '0', 0, '1', '2018-04-12 13:17:45', '1', 2, 1),
(226, 0, 287, 11, NULL, 0, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/cQue_(50)/Q.50.JPG" style="height:46px; width:348px" />', '0', 0, '1', '2018-04-12 13:20:13', '1', 2, 1),
(227, 0, 287, 11, NULL, 1, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/cQue_(6)/Q.6.JPG" style="height:49px; width:348px" />', '0', 0, '1', '2018-04-12 13:22:20', '1', 2, 1),
(228, 0, 287, 11, NULL, 1, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/cQue_(7)/Q.7.JPG" style="height:25px; width:225px" />', '0', 0, '1', '2018-04-12 14:14:40', '1', 2, 1),
(229, 0, 287, 11, NULL, 1, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/cQue_(8)/Q.8.JPG" style="height:44px; width:333px" />', '0', 0, '1', '2018-04-12 14:15:36', '1', 2, 1),
(230, 0, 287, 11, NULL, 1, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/cQue_(9)/Q.9.JPG" style="height:50px; width:343px" />', '0', 0, '1', '2018-04-12 14:16:59', '1', 2, 1),
(231, 0, 290, 11, NULL, 1, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/mQue(50)/Q.50.JPG" style="height:76px; width:370px" />', '0', 0, '1', '2018-04-12 14:19:32', '1', 4, 2),
(232, 0, 290, 11, NULL, 1, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/mQue(6)/Q.6.JPG" style="height:71px; width:376px" />', '0', 0, '1', '2018-04-12 14:20:24', '1', 4, 2),
(233, 0, 290, 11, NULL, 1, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/mQue(7)/Q.7.JPG" style="height:244px; width:386px" />', '0', 0, '1', '2018-04-12 14:21:15', '1', 4, 2),
(234, 0, 290, 11, NULL, 1, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/mQue(8)/Q.8.JPG" style="height:76px; width:360px" />', '0', 0, '1', '2018-04-12 14:23:01', '1', 4, 2),
(235, 0, 290, 11, NULL, 0, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/mQue(9)/Q.9.JPG" style="height:94px; width:371px" /><img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/mQue(9)/opt_9.1.JPG" style="height:21px; width:34px" />', '0', 0, '1', '2018-04-12 14:24:35', '1', 4, 2),
(236, 0, 287, 11, NULL, 1, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/cQue_(6)/Q.6.JPG" style="height:49px; width:348px" />', '0', 0, '1', '2018-04-12 15:27:21', '1', 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `vid_question_correct_answer`
--

CREATE TABLE IF NOT EXISTS `vid_question_correct_answer` (
`ques_correct_ans_id` int(11) NOT NULL,
  `ques_id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL,
  `ans_explanation` longtext CHARACTER SET utf8,
  `img_path` varchar(200) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=375 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `vid_question_correct_answer`
--

INSERT INTO `vid_question_correct_answer` (`ques_correct_ans_id`, `ques_id`, `option_id`, `ans_explanation`, `img_path`) VALUES
(102, 23, 413, '321', NULL),
(104, 21, 422, '321', NULL),
(109, 28, 440, 'trtr', NULL),
(115, 30, 468, 'asdf', NULL),
(116, 31, 472, 'wewdgf', NULL),
(117, 32, 476, '<h1>asdfw</h1>\nsf', NULL),
(118, 33, 484, 'wefw', NULL),
(119, 34, 488, 'yrty', NULL),
(120, 35, 489, 'ert', NULL),
(121, 36, 493, 'tete', NULL),
(122, 37, 497, 'ryry', NULL),
(123, 38, 501, 'fgk', NULL),
(124, 39, 505, 'puiop', NULL),
(131, 14, 535, 'asdf', NULL),
(135, 15, 550, 'asdf', NULL),
(136, 16, 555, 'asdf', NULL),
(204, 75, 878, 'demo explaination', NULL),
(205, 77, 888, 'demo explaination', NULL),
(206, 78, 893, 'demo explaination', NULL),
(207, 79, 898, 'demo explaination', NULL),
(208, 80, 903, 'demo explaination', NULL),
(209, 81, 908, 'demo explaination', NULL),
(210, 83, 918, 'demo explaination', NULL),
(211, 84, 923, 'demo explaination', NULL),
(212, 85, 928, 'demo explaination', NULL),
(213, 86, 933, 'demo explaination', NULL),
(214, 87, 938, 'demo explaination', NULL),
(215, 89, 948, 'demo explaination', NULL),
(216, 90, 953, 'demo explaination', NULL),
(217, 91, 958, 'demo explaination', NULL),
(218, 92, 963, 'demo explaination', NULL),
(219, 93, 968, 'demo explaination', NULL),
(220, 95, 978, 'demo explaination', NULL),
(221, 96, 983, 'demo explaination', NULL),
(222, 97, 988, 'demo explaination', NULL),
(223, 98, 993, 'demo explaination', NULL),
(224, 99, 1000, '11', NULL),
(225, 100, 1003, 'demo explaination', NULL),
(226, 102, 1013, 'demo explaination', NULL),
(227, 103, 1018, 'demo explaination', NULL),
(228, 104, 1023, 'demo explaination', NULL),
(229, 105, 1028, 'demo explaination', NULL),
(230, 106, 1033, 'demo explaination', NULL),
(231, 108, 1043, 'demo explaination', NULL),
(232, 109, 1048, 'demo explaination', NULL),
(233, 110, 1053, 'demo explaination', NULL),
(234, 111, 1058, 'demo explaination', NULL),
(235, 112, 1063, 'demo explaination', NULL),
(236, 114, 1073, 'demo explaination', NULL),
(237, 115, 1078, 'demo explaination', NULL),
(238, 116, 1083, 'demo explaination', NULL),
(239, 117, 1088, 'demo explaination', NULL),
(240, 118, 1093, 'demo explaination', NULL),
(241, 120, 1103, 'demo explaination', NULL),
(242, 121, 1108, 'demo explaination', NULL),
(243, 122, 1113, 'demo explaination', NULL),
(246, 124, 1128, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/Capture.PNG" style="height:185px; width:310px" />', NULL),
(247, 125, 1133, 'demo explaination', NULL),
(248, 127, 1143, 'demo explaination', NULL),
(249, 128, 1148, 'demo explaination', NULL),
(250, 129, 1153, 'demo explaination', NULL),
(251, 130, 1158, 'demo explaination', NULL),
(252, 131, 1163, 'demo explaination', NULL),
(253, 133, 1173, 'demo explaination', NULL),
(254, 134, 1178, 'demo explaination', NULL),
(255, 135, 1183, 'demo explaination', NULL),
(256, 136, 1188, 'demo explaination', NULL),
(257, 137, 1193, 'demo explaination', NULL),
(258, 139, 1203, 'demo explaination', NULL),
(259, 140, 1208, 'demo explaination', NULL),
(260, 141, 1213, 'demo explaination', NULL),
(261, 142, 1218, 'demo explaination', NULL),
(262, 143, 1223, 'demo explaination', NULL),
(263, 145, 1233, 'demo explaination', NULL),
(264, 146, 1238, 'demo explaination', NULL),
(265, 147, 1243, 'demo explaination', NULL),
(266, 148, 1248, 'demo explaination', NULL),
(267, 149, 1251, 'asdasd', NULL),
(269, 150, 1263, 'sdfsdfsdf', NULL),
(271, 113, 1268, 'asdf', NULL),
(274, 19, 1284, 'asdf', NULL),
(275, 18, 1286, 'asdf', NULL),
(276, 17, 1291, 'asdf', NULL),
(277, 152, 1293, 'asdf', NULL),
(280, 153, 1305, '', NULL),
(281, 154, 1309, 'adf', NULL),
(284, 157, 1326, 'Easdfasdf', NULL),
(285, 158, 1332, '456456', NULL),
(286, 159, 1336, '456', NULL),
(287, 161, 1344, '456', NULL),
(288, 162, 1349, '456', NULL),
(289, 163, 1354, '456', NULL),
(290, 164, 1359, '456', NULL),
(291, 165, 1363, '456', NULL),
(344, 217, 1575, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/pQue_(10)/Expl_10.JPG" style="height:146px; width:495px" />', NULL),
(346, 216, 1583, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/pQue_(1)/Expl_1.JPG" style="height:75px; width:407px" />', NULL),
(347, 218, 1587, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/pQue_(11)/Expl_11.JPG" style="height:498px; width:659px" />', NULL),
(348, 219, 1588, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/pQue_(13)/Expl_13.JPG" style="height:398px; width:427px" />', NULL),
(349, 220, 1592, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/pQue_(13)/Expl_13.JPG" style="height:398px; width:427px" />', NULL),
(350, 221, 1598, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/p2Que_(50)/Expl_50.JPG" style="height:157px; width:458px" />', NULL),
(351, 222, 1603, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/p2Que_(6)/Expl_6.JPG" style="height:109px; width:436px" />', NULL),
(352, 223, 1606, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/p2Que_(7)/Expl_7.JPG" style="height:169px; width:322px" />', NULL),
(353, 224, 1608, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/p2Que_(8)/Expl_8.JPG" style="height:137px; width:408px" />', NULL),
(354, 225, 1612, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/p2Que_(9)/Expl_9.JPG" style="height:338px; width:360px" />', NULL),
(355, 20, 1618, 'asdf', NULL),
(356, 226, 1623, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/cQue_(50)/Expl_50.JPG" style="height:166px; width:387px" />', NULL),
(357, 227, 1626, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/cQue_(6)/cor_ans.6.JPG" style="height:31px; width:28px" />', NULL),
(358, 228, 1631, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/cQue_(7)/Expl_7.JPG" style="height:137px; width:361px" />', NULL),
(359, 229, 1635, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/cQue_(8)/Expl_8.JPG" style="height:110px; width:370px" />', NULL),
(360, 230, 1636, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/cQue_(9)/Expl_9.JPG" style="height:105px; width:388px" />', NULL),
(368, 236, 1670, 'C is correct Answer.....', NULL),
(369, 235, 1673, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/mQue(9)/Expl_9.JPG" style="height:259px; width:387px" />', NULL),
(370, 234, 1678, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/mQue(8)/Expl_8.JPG" style="height:183px; width:393px" />', NULL),
(371, 233, 1683, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/mQue(7)/cort_opt_7.JPG" style="height:28px; width:43px" />', NULL),
(372, 232, 1684, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/mQue(6)/Expl_6.JPG" style="height:54px; width:285px" />', NULL),
(373, 231, 1689, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/mQue(50)/Expl_50.JPG" style="height:101px; width:331px" />', NULL),
(374, 151, 1693, '2<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/mQue(9)/Expl_9.JPG" style="height:259px; width:387px" />', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vid_question_options`
--

CREATE TABLE IF NOT EXISTS `vid_question_options` (
`option_id` int(11) NOT NULL,
  `ques_id` int(11) NOT NULL,
  `option_text` text CHARACTER SET utf8,
  `img_path` varchar(200) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1696 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `vid_question_options`
--

INSERT INTO `vid_question_options` (`option_id`, `ques_id`, `option_text`, `img_path`) VALUES
(412, 23, '512/513', NULL),
(413, 23, '105/512', NULL),
(414, 23, '100/153', NULL),
(415, 23, '10^C^6', NULL),
(420, 21, '2 + 3 &sup1; 5 and &lt; 10', NULL),
(421, 21, '2 + 3 = 5 and 8 ≮ 10', NULL),
(422, 21, '2 + 3 &sup1; 5 or 8 ≮ 10', NULL),
(423, 21, 'None of these', NULL),
(440, 28, 'wert', NULL),
(441, 28, 'sdfg', NULL),
(442, 28, 'gdg', NULL),
(443, 28, 'erte', NULL),
(468, 30, 'asdf', NULL),
(469, 30, 'asdf', NULL),
(470, 30, 'asdf', NULL),
(471, 30, 'asdf', NULL),
(472, 31, 'asdgf', NULL),
(473, 31, 'asdf', NULL),
(474, 31, 'asdf', NULL),
(475, 31, 'asdw', NULL),
(476, 32, 'asdfqw', NULL),
(477, 32, 'sf', NULL),
(478, 32, 'asdf', NULL),
(479, 32, 'sdf', NULL),
(480, 33, 'weg', NULL),
(481, 33, 'wesd<sub>2</sub>', NULL),
(482, 33, 'wef<sup>3</sup>', NULL),
(483, 33, 'wefsd', NULL),
(484, 33, 'wewe', NULL),
(485, 34, 'erte', NULL),
(486, 34, 'ert', NULL),
(487, 34, 'ert', NULL),
(488, 34, 'ert', NULL),
(489, 35, 'ertws', NULL),
(490, 35, 'ert', NULL),
(491, 35, 'gegt', NULL),
(492, 35, 'tert', NULL),
(493, 36, 'rtr', NULL),
(494, 36, 'rrty', NULL),
(495, 36, 'aasdfg', NULL),
(496, 36, 'ertyu', NULL),
(497, 37, 'mf', NULL),
(498, 37, 'rty', NULL),
(499, 37, 'rty', NULL),
(500, 37, 'rtyr', NULL),
(501, 38, 'fgk', NULL),
(502, 38, 'fgk', NULL),
(503, 38, 'fgh', NULL),
(504, 38, 'fk', NULL),
(505, 39, 'yky', NULL),
(506, 39, 'gb', NULL),
(507, 39, 'gjtui', NULL),
(508, 39, 'kyky', NULL),
(534, 14, '25 &micro;A', NULL),
(535, 14, '125&micro;A', NULL),
(536, 14, '50&micro;A', NULL),
(537, 14, '5&micro;A', NULL),
(550, 15, 'Y &lt; 3K', NULL),
(551, 15, 'Y = 3K', NULL),
(552, 15, 'Y &gt; 3K', NULL),
(553, 15, '3Y = K', NULL),
(554, 16, 'kg/s2', NULL),
(555, 16, 'kg/s', NULL),
(556, 16, 'kg-m/s', NULL),
(557, 16, 'kg-m/s2', NULL),
(876, 75, 'asdf', NULL),
(877, 75, 'asdf', NULL),
(878, 75, 'asdf', NULL),
(879, 75, 'asdf', NULL),
(880, 75, 'option 5 asdf`', NULL),
(881, 76, 'asdf', NULL),
(882, 76, 'Asdfaqsd', NULL),
(883, 76, 'asdfas', NULL),
(884, 76, 'asdf', NULL),
(885, 76, 'asdf', NULL),
(886, 77, 'asdf', NULL),
(887, 77, 'asdf', NULL),
(888, 77, 'asdf', NULL),
(889, 77, 'asdf', NULL),
(890, 77, 'option 5 asdf`', NULL),
(891, 78, 'asdf', NULL),
(892, 78, 'asdf', NULL),
(893, 78, 'asdf', NULL),
(894, 78, 'asdf', NULL),
(895, 78, 'option 5 asdf`', NULL),
(896, 79, 'asdf', NULL),
(897, 79, 'asdf', NULL),
(898, 79, 'asdf', NULL),
(899, 79, 'asdf', NULL),
(900, 79, 'option 5 asdf`', NULL),
(901, 80, 'asdf', NULL),
(902, 80, 'asdf', NULL),
(903, 80, 'asdf', NULL),
(904, 80, 'asdf', NULL),
(905, 80, 'option 5 asdf`', NULL),
(906, 81, 'asdf', NULL),
(907, 81, 'asdf', NULL),
(908, 81, 'asdf', NULL),
(909, 81, 'asdf', NULL),
(910, 81, 'option 5 asdf`', NULL),
(911, 82, 'asdf', NULL),
(912, 82, 'Asdfaqsd', NULL),
(913, 82, 'asdfas', NULL),
(914, 82, 'asdf', NULL),
(915, 82, 'asdf', NULL),
(916, 83, 'asdf', NULL),
(917, 83, 'asdf', NULL),
(918, 83, 'asdf', NULL),
(919, 83, 'asdf', NULL),
(920, 83, 'option 5 asdf`', NULL),
(921, 84, 'asdf', NULL),
(922, 84, 'asdf', NULL),
(923, 84, 'asdf', NULL),
(924, 84, 'asdf', NULL),
(925, 84, 'option 5 asdf`', NULL),
(926, 85, 'asdf', NULL),
(927, 85, 'asdf', NULL),
(928, 85, 'asdf', NULL),
(929, 85, 'asdf', NULL),
(930, 85, 'option 5 asdf`', NULL),
(931, 86, 'asdf', NULL),
(932, 86, 'asdf', NULL),
(933, 86, 'asdf', NULL),
(934, 86, 'asdf', NULL),
(935, 86, 'option 5 asdf`', NULL),
(936, 87, 'asdf', NULL),
(937, 87, 'asdf', NULL),
(938, 87, 'asdf', NULL),
(939, 87, 'asdf', NULL),
(940, 87, 'option 5 asdf`', NULL),
(941, 88, 'asdf', NULL),
(942, 88, 'Asdfaqsd', NULL),
(943, 88, 'asdfas', NULL),
(944, 88, 'asdf', NULL),
(945, 88, 'asdf', NULL),
(946, 89, 'asdf', NULL),
(947, 89, 'asdf', NULL),
(948, 89, 'asdf', NULL),
(949, 89, 'asdf', NULL),
(950, 89, 'option 5 asdf`', NULL),
(951, 90, 'asdf', NULL),
(952, 90, 'asdf', NULL),
(953, 90, 'asdf', NULL),
(954, 90, 'asdf', NULL),
(955, 90, 'option 5 asdf`', NULL),
(956, 91, 'asdf', NULL),
(957, 91, 'asdf', NULL),
(958, 91, 'asdf', NULL),
(959, 91, 'asdf', NULL),
(960, 91, 'option 5 asdf`', NULL),
(961, 92, 'asdf', NULL),
(962, 92, 'asdf', NULL),
(963, 92, 'asdf', NULL),
(964, 92, 'asdf', NULL),
(965, 92, 'option 5 asdf`', NULL),
(966, 93, 'asdf', NULL),
(967, 93, 'asdf', NULL),
(968, 93, 'asdf', NULL),
(969, 93, 'asdf', NULL),
(970, 93, 'option 5 asdf`', NULL),
(971, 94, 'asdf', NULL),
(972, 94, 'Asdfaqsd', NULL),
(973, 94, 'asdfas', NULL),
(974, 94, 'asdf', NULL),
(975, 94, 'asdf', NULL),
(976, 95, 'asdf', NULL),
(977, 95, 'asdf', NULL),
(978, 95, 'asdf', NULL),
(979, 95, 'asdf', NULL),
(980, 95, 'option 5 asdf`', NULL),
(981, 96, 'asdf', NULL),
(982, 96, 'asdf', NULL),
(983, 96, 'asdf', NULL),
(984, 96, 'asdf', NULL),
(985, 96, 'option 5 asdf`', NULL),
(986, 97, 'asdf', NULL),
(987, 97, 'asdf', NULL),
(988, 97, 'asdf', NULL),
(989, 97, 'asdf', NULL),
(990, 97, 'option 5 asdf`', NULL),
(991, 98, 'asdf', NULL),
(992, 98, 'asdf', NULL),
(993, 98, 'asdf', NULL),
(994, 98, 'asdf', NULL),
(995, 98, 'option 5 asdf`', NULL),
(996, 99, '11', NULL),
(997, 99, '11', NULL),
(998, 99, '11', NULL),
(999, 99, '11', NULL),
(1000, 99, '11', NULL),
(1001, 100, 'asdf', NULL),
(1002, 100, 'asdf', NULL),
(1003, 100, 'asdf', NULL),
(1004, 100, 'asdf', NULL),
(1005, 100, 'option 5 asdf`', NULL),
(1006, 101, 'asdf', NULL),
(1007, 101, 'Asdfaqsd', NULL),
(1008, 101, 'asdfas', NULL),
(1009, 101, 'asdf', NULL),
(1010, 101, 'asdf', NULL),
(1011, 102, 'asdf', NULL),
(1012, 102, 'asdf', NULL),
(1013, 102, 'asdf', NULL),
(1014, 102, 'asdf', NULL),
(1015, 102, 'option 5 asdf`', NULL),
(1016, 103, 'asdf', NULL),
(1017, 103, 'asdf', NULL),
(1018, 103, 'asdf', NULL),
(1019, 103, 'asdf', NULL),
(1020, 103, 'option 5 asdf`', NULL),
(1021, 104, 'asdf', NULL),
(1022, 104, 'asdf', NULL),
(1023, 104, 'asdf', NULL),
(1024, 104, 'asdf', NULL),
(1025, 104, 'option 5 asdf`', NULL),
(1026, 105, 'asdf', NULL),
(1027, 105, 'asdf', NULL),
(1028, 105, 'asdf', NULL),
(1029, 105, 'asdf', NULL),
(1030, 105, 'option 5 asdf`', NULL),
(1031, 106, 'asdf', NULL),
(1032, 106, 'asdf', NULL),
(1033, 106, 'asdf', NULL),
(1034, 106, 'asdf', NULL),
(1035, 106, 'option 5 asdf`', NULL),
(1036, 107, 'asdf', NULL),
(1037, 107, 'Asdfaqsd', NULL),
(1038, 107, 'asdfas', NULL),
(1039, 107, 'asdf', NULL),
(1040, 107, 'asdf', NULL),
(1041, 108, 'asdf', NULL),
(1042, 108, 'asdf', NULL),
(1043, 108, 'asdf', NULL),
(1044, 108, 'asdf', NULL),
(1045, 108, 'option 5 asdf`', NULL),
(1046, 109, 'asdf', NULL),
(1047, 109, 'asdf', NULL),
(1048, 109, 'asdf', NULL),
(1049, 109, 'asdf', NULL),
(1050, 109, 'option 5 asdf`', NULL),
(1051, 110, 'asdf', NULL),
(1052, 110, 'asdf', NULL),
(1053, 110, 'asdf', NULL),
(1054, 110, 'asdf', NULL),
(1055, 110, 'option 5 asdf`', NULL),
(1056, 111, 'asdf', NULL),
(1057, 111, 'asdf', NULL),
(1058, 111, 'asdf', NULL),
(1059, 111, 'asdf', NULL),
(1060, 111, 'option 5 asdf`', NULL),
(1061, 112, 'asdf', NULL),
(1062, 112, 'asdf', NULL),
(1063, 112, 'asdf', NULL),
(1064, 112, 'asdf', NULL),
(1065, 112, 'option 5 asdf`', NULL),
(1071, 114, 'asdf', NULL),
(1072, 114, 'asdf', NULL),
(1073, 114, 'asdf', NULL),
(1074, 114, 'asdf', NULL),
(1075, 114, 'option 5 asdf`', NULL),
(1076, 115, 'asdf', NULL),
(1077, 115, 'asdf', NULL),
(1078, 115, 'asdf', NULL),
(1079, 115, 'asdf', NULL),
(1080, 115, 'option 5 asdf`', NULL),
(1081, 116, 'asdf', NULL),
(1082, 116, 'asdf', NULL),
(1083, 116, 'asdf', NULL),
(1084, 116, 'asdf', NULL),
(1085, 116, 'option 5 asdf`', NULL),
(1086, 117, 'asdf', NULL),
(1087, 117, 'asdf', NULL),
(1088, 117, 'asdf', NULL),
(1089, 117, 'asdf', NULL),
(1090, 117, 'option 5 asdf`', NULL),
(1091, 118, 'asdf', NULL),
(1092, 118, 'asdf', NULL),
(1093, 118, 'asdf', NULL),
(1094, 118, 'asdf', NULL),
(1095, 118, 'option 5 asdf`', NULL),
(1096, 119, 'asdf', NULL),
(1097, 119, 'Asdfaqsd', NULL),
(1098, 119, 'asdfas', NULL),
(1099, 119, 'asdf', NULL),
(1100, 119, 'asdf', NULL),
(1101, 120, 'asdf', NULL),
(1102, 120, 'asdf', NULL),
(1103, 120, 'asdf', NULL),
(1104, 120, 'asdf', NULL),
(1105, 120, 'option 5 asdf`', NULL),
(1106, 121, 'asdf', NULL),
(1107, 121, 'asdf', NULL),
(1108, 121, 'asdf', NULL),
(1109, 121, 'asdf', NULL),
(1110, 121, 'option 5 asdf`', NULL),
(1111, 122, 'asdf', NULL),
(1112, 122, 'asdf', NULL),
(1113, 122, 'asdf', NULL),
(1114, 122, 'asdf', NULL),
(1115, 122, 'option 5 asdf`', NULL),
(1126, 124, 'option 1', NULL),
(1127, 124, 'option 2', NULL),
(1128, 124, 'option 3', NULL),
(1129, 124, 'option 4', NULL),
(1130, 124, 'option 5', NULL),
(1131, 125, 'asdf', NULL),
(1132, 125, 'asdf', NULL),
(1133, 125, 'asdf', NULL),
(1134, 125, 'asdf', NULL),
(1135, 125, 'option 5 asdf`', NULL),
(1136, 126, 'asdf', NULL),
(1137, 126, 'Asdfaqsd', NULL),
(1138, 126, 'asdfas', NULL),
(1139, 126, 'asdf', NULL),
(1140, 126, 'asdf', NULL),
(1141, 127, 'asdf', NULL),
(1142, 127, 'asdf', NULL),
(1143, 127, 'asdf', NULL),
(1144, 127, 'asdf', NULL),
(1145, 127, 'option 5 asdf`', NULL),
(1146, 128, 'asdf', NULL),
(1147, 128, 'asdf', NULL),
(1148, 128, 'asdf', NULL),
(1149, 128, 'asdf', NULL),
(1150, 128, 'option 5 asdf`', NULL),
(1151, 129, 'asdf', NULL),
(1152, 129, 'asdf', NULL),
(1153, 129, 'asdf', NULL),
(1154, 129, 'asdf', NULL),
(1155, 129, 'option 5 asdf`', NULL),
(1156, 130, 'asdf', NULL),
(1157, 130, 'asdf', NULL),
(1158, 130, 'asdf', NULL),
(1159, 130, 'asdf', NULL),
(1160, 130, 'option 5 asdf`', NULL),
(1161, 131, 'asdf', NULL),
(1162, 131, 'asdf', NULL),
(1163, 131, 'asdf', NULL),
(1164, 131, 'asdf', NULL),
(1165, 131, 'option 5 asdf`', NULL),
(1166, 132, 'asdf', NULL),
(1167, 132, 'Asdfaqsd', NULL),
(1168, 132, 'asdfas', NULL),
(1169, 132, 'asdf', NULL),
(1170, 132, 'asdf', NULL),
(1171, 133, 'asdf', NULL),
(1172, 133, 'asdf', NULL),
(1173, 133, 'asdf', NULL),
(1174, 133, 'asdf', NULL),
(1175, 133, 'option 5 asdf`', NULL),
(1176, 134, 'asdf', NULL),
(1177, 134, 'asdf', NULL),
(1178, 134, 'asdf', NULL),
(1179, 134, 'asdf', NULL),
(1180, 134, 'option 5 asdf`', NULL),
(1181, 135, 'asdf', NULL),
(1182, 135, 'asdf', NULL),
(1183, 135, 'asdf', NULL),
(1184, 135, 'asdf', NULL),
(1185, 135, 'option 5 asdf`', NULL),
(1186, 136, 'asdf', NULL),
(1187, 136, 'asdf', NULL),
(1188, 136, 'asdf', NULL),
(1189, 136, 'asdf', NULL),
(1190, 136, 'option 5 asdf`', NULL),
(1191, 137, 'asdf', NULL),
(1192, 137, 'asdf', NULL),
(1193, 137, 'asdf', NULL),
(1194, 137, 'asdf', NULL),
(1195, 137, 'option 5 asdf`', NULL),
(1196, 138, 'asdf', NULL),
(1197, 138, 'Asdfaqsd', NULL),
(1198, 138, 'asdfas', NULL),
(1199, 138, 'asdf', NULL),
(1200, 138, 'asdf', NULL),
(1201, 139, 'asdf', NULL),
(1202, 139, 'asdf', NULL),
(1203, 139, 'asdf', NULL),
(1204, 139, 'asdf', NULL),
(1205, 139, 'option 5 asdf`', NULL),
(1206, 140, 'asdf', NULL),
(1207, 140, 'asdf', NULL),
(1208, 140, 'asdf', NULL),
(1209, 140, 'asdf', NULL),
(1210, 140, 'option 5 asdf`', NULL),
(1211, 141, 'asdf', NULL),
(1212, 141, 'asdf', NULL),
(1213, 141, 'asdf', NULL),
(1214, 141, 'asdf', NULL),
(1215, 141, 'option 5 asdf`', NULL),
(1216, 142, 'asdf', NULL),
(1217, 142, 'asdf', NULL),
(1218, 142, 'asdf', NULL),
(1219, 142, 'asdf', NULL),
(1220, 142, 'option 5 asdf`', NULL),
(1221, 143, 'asdf', NULL),
(1222, 143, 'asdf', NULL),
(1223, 143, 'asdf', NULL),
(1224, 143, 'asdf', NULL),
(1225, 143, 'option 5 asdf`', NULL),
(1226, 144, 'asdf', NULL),
(1227, 144, 'Asdfaqsd', NULL),
(1228, 144, 'asdfas', NULL),
(1229, 144, 'asdf', NULL),
(1230, 144, 'asdf', NULL),
(1231, 145, 'asdf', NULL),
(1232, 145, 'asdf', NULL),
(1233, 145, 'asdf', NULL),
(1234, 145, 'asdf', NULL),
(1235, 145, 'option 5 asdf`', NULL),
(1236, 146, 'asdf', NULL),
(1237, 146, 'asdf', NULL),
(1238, 146, 'asdf', NULL),
(1239, 146, 'asdf', NULL),
(1240, 146, 'option 5 asdf`', NULL),
(1241, 147, 'asdf', NULL),
(1242, 147, 'asdf', NULL),
(1243, 147, 'asdf', NULL),
(1244, 147, 'asdf', NULL),
(1245, 147, 'option 5 asdf`', NULL),
(1246, 148, 'asdf', NULL),
(1247, 148, 'asdf', NULL),
(1248, 148, 'asdf', NULL),
(1249, 148, 'asdf', NULL),
(1250, 148, 'option 5 asdf`', NULL),
(1251, 149, 'asdasd', NULL),
(1252, 149, 'asd', NULL),
(1253, 149, 'asd', NULL),
(1254, 149, 'asd', NULL),
(1260, 150, 'sdf', NULL),
(1261, 150, 'sdfsdf', NULL),
(1262, 150, 'sdfsdf', NULL),
(1263, 150, 'sdfsdf', NULL),
(1268, 113, 'asdf', NULL),
(1269, 113, 'Asdfaqsd', NULL),
(1270, 113, 'asdfas', NULL),
(1271, 113, 'asdf', NULL),
(1272, 113, 'asdf', NULL),
(1281, 19, '&pi;/6', NULL),
(1282, 19, '&pi;/( 3&radic;3)', NULL),
(1283, 19, '&pi;/( 4&radic;2)', NULL),
(1284, 19, '&pi;/4', NULL),
(1285, 18, '0.732 r', NULL),
(1286, 18, '0.414 r', NULL),
(1287, 18, '0.225 r', NULL),
(1288, 18, '0.155 r', NULL),
(1289, 17, 'In solid NaClthere are no ions', NULL),
(1290, 17, 'Solid NaCl is covalent', NULL),
(1291, 17, 'In solid NaClthere is no velocity of ions.', NULL),
(1292, 17, 'In solid NaCl there are no electrons In solid NaCl there are no electrons', NULL),
(1293, 152, 'asdf', NULL),
(1294, 152, 'asdf', NULL),
(1295, 152, 'asdf', NULL),
(1296, 152, 'asd', NULL),
(1305, 153, 'asdf', NULL),
(1306, 153, 'asdf', NULL),
(1307, 153, 'asdf', NULL),
(1308, 153, 'asd', NULL),
(1309, 154, 'sdf', NULL),
(1310, 154, 'asd', NULL),
(1311, 154, 'asd', NULL),
(1312, 154, 'asd', NULL),
(1323, 157, '11111111', NULL),
(1324, 157, 'optrion 222222', NULL),
(1325, 157, 'option 3', NULL),
(1326, 157, 'option 444444', NULL),
(1327, 157, 'option 5555', NULL),
(1328, 158, '456', NULL),
(1329, 158, '456', NULL),
(1330, 158, '56', NULL),
(1331, 158, '456', NULL),
(1332, 158, '456', NULL),
(1333, 159, '456', NULL),
(1334, 159, '456', NULL),
(1335, 159, '456', NULL),
(1336, 159, '456', NULL),
(1337, 160, '456', NULL),
(1338, 160, '456', NULL),
(1339, 160, '456', NULL),
(1340, 160, '456', NULL),
(1341, 161, '456', NULL),
(1342, 161, '456', NULL),
(1343, 161, '456', NULL),
(1344, 161, '456', NULL),
(1345, 161, 'vcdvfd', NULL),
(1346, 162, '456', NULL),
(1347, 162, '456', NULL),
(1348, 162, '456', NULL),
(1349, 162, '456', NULL),
(1350, 163, '456', NULL),
(1351, 163, '456', NULL),
(1352, 163, '456', NULL),
(1353, 163, '456', NULL),
(1354, 163, 'ghgh', NULL),
(1355, 164, '456', NULL),
(1356, 164, '456', NULL),
(1357, 164, '456', NULL),
(1358, 164, '456', NULL),
(1359, 164, 'ghgh', NULL),
(1360, 165, '456', NULL),
(1361, 165, '456', NULL),
(1362, 165, '456', NULL),
(1363, 165, '456', NULL),
(1572, 217, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/pQue_(10)/opt_10.1.JPG" style="height:36px; width:165px" />', NULL),
(1573, 217, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/pQue_(10)/opt_10.2.JPG" style="height:40px; width:153px" />', NULL),
(1574, 217, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/pQue_(10)/opt_10.3.JPG" style="height:32px; width:159px" />', NULL),
(1575, 217, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/pQue_(10)/opt_10.4.JPG" style="height:31px; width:148px" />', NULL),
(1580, 216, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/pQue_(1)/opt_1.1.JPG" style="height:39px; width:78px" />', NULL),
(1581, 216, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/pQue_(1)/opt_1.2.JPG" style="height:29px; width:55px" />', NULL),
(1582, 216, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/pQue_(1)/opt_1.3.JPG" style="height:41px; width:74px" />', NULL),
(1583, 216, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/pQue_(1)/opt_1.4.JPG" style="height:46px; width:77px" />', NULL),
(1584, 218, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/pQue_(11)/opt_11.1.JPG" style="height:27px; width:75px" />', NULL),
(1585, 218, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/pQue_(11)/opt_11.2.JPG" style="height:30px; width:69px" />', NULL),
(1586, 218, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/pQue_(11)/opt_11.3.JPG" style="height:36px; width:72px" />', NULL),
(1587, 218, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/pQue_(11)/opt_11.4.JPG" style="height:31px; width:74px" />', NULL),
(1588, 219, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/pQue_(13)/Q.13.JPG" style="height:115px; width:453px" />', NULL),
(1589, 219, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/pQue_(13)/opt_13.2.JPG" style="height:54px; width:51px" />', NULL),
(1590, 219, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/pQue_(13)/opt_13.3.JPG" style="height:69px; width:52px" />', NULL),
(1591, 219, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/pQue_(13)/opt_13.4.JPG" style="height:72px; width:50px" />', NULL),
(1592, 220, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/pQue_(13)/opt_13.1.JPG" style="height:66px; width:47px" />', NULL),
(1593, 220, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/pQue_(13)/opt_13.2.JPG" style="height:54px; width:51px" />', NULL),
(1594, 220, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/pQue_(13)/opt_13.3.JPG" style="height:69px; width:52px" />', NULL),
(1595, 220, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/pQue_(13)/opt_13.4.JPG" style="height:72px; width:50px" />', NULL),
(1596, 221, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/p2Que_(50)/opt_50.1.JPG" style="height:124px; width:148px" />', NULL),
(1597, 221, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/p2Que_(50)/opt_50.2.JPG" style="height:129px; width:155px" />', NULL),
(1598, 221, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/p2Que_(50)/opt_50.3.JPG" style="height:153px; width:132px" />', NULL),
(1599, 221, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/p2Que_(50)/opt_50.4.JPG" style="height:155px; width:137px" />', NULL),
(1600, 222, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/p2Que_(6)/opt_6.1.JPG" style="height:55px; width:76px" />', NULL),
(1601, 222, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/p2Que_(6)/opt_6.2.JPG" style="height:54px; width:82px" />', NULL),
(1602, 222, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/p2Que_(6)/opt_6.3.JPG" style="height:59px; width:81px" />', NULL),
(1603, 222, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/p2Que_(6)/opt_6.4.JPG" style="height:63px; width:88px" />', NULL),
(1604, 223, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/p2Que_(7)/opt_7.1.JPG" style="height:55px; width:68px" />', NULL),
(1605, 223, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/p2Que_(7)/opt_7.2.JPG" style="height:61px; width:74px" />', NULL),
(1606, 223, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/p2Que_(7)/opt_7.3.JPG" style="height:39px; width:54px" />', NULL),
(1607, 223, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/p2Que_(7)/opt_7.4.JPG" style="height:43px; width:99px" />', NULL),
(1608, 224, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/p2Que_(8)/opt_8.1.JPG" style="height:39px; width:78px" />', NULL),
(1609, 224, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/p2Que_(8)/opt_8.2.JPG" style="height:40px; width:54px" />', NULL),
(1610, 224, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/p2Que_(8)/opt_8.3.JPG" style="height:33px; width:76px" />', NULL),
(1611, 224, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/p2Que_(8)/opt_8.4.JPG" style="height:37px; width:61px" />', NULL),
(1612, 225, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/p2Que_(9)/opt_9.1.JPG" style="height:28px; width:45px" />', NULL),
(1613, 225, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/p2Que_(9)/opt_9.2.JPG" style="height:30px; width:50px" />', NULL),
(1614, 225, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/p2Que_(9)/opt_9.3.JPG" style="height:33px; width:46px" />', NULL),
(1615, 225, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/p2Que_(9)/opt_9.4.JPG" style="height:34px; width:57px" />', NULL),
(1616, 20, '6 &times; l0^20', NULL),
(1617, 20, '3&times; l0^22', NULL),
(1618, 20, 'l.5 &times; l0^23', NULL),
(1619, 20, '0.5&times; l0^24', NULL),
(1620, 226, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/cQue_(50)/opt_50.1.JPG" style="height:21px; width:350px" />', NULL),
(1621, 226, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/cQue_(50)/opt_50.2.JPG" style="height:20px; width:327px" />', NULL),
(1622, 226, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/cQue_(50)/opt_50.3.JPG" style="height:24px; width:225px" />', NULL),
(1623, 226, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/cQue_(50)/opt_50.4.JPG" style="height:81px; width:317px" />', NULL),
(1624, 227, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/cQue_(6)/opt_6.1.JPG" style="height:30px; width:43px" />', NULL),
(1625, 227, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/cQue_(6)/opt_6.2.JPG" style="height:27px; width:45px" />', NULL),
(1626, 227, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/cQue_(6)/opt_6.3.JPG" style="height:29px; width:38px" />', NULL),
(1627, 227, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/cQue_(6)/opt_6.4.JPG" style="height:29px; width:50px" />', NULL),
(1628, 228, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/cQue_(7)/opt_7.1.JPG" style="height:23px; width:47px" />', NULL),
(1629, 228, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/cQue_(7)/opt_7.2.JPG" style="height:28px; width:58px" />', NULL),
(1630, 228, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/cQue_(7)/opt_7.3.JPG" style="height:25px; width:72px" />', NULL),
(1631, 228, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/cQue_(7)/opt_7.4.JPG" style="height:26px; width:73px" />', NULL),
(1632, 229, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/cQue_(8)/opt_8.1.JPG" style="height:19px; width:55px" />', NULL),
(1633, 229, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/cQue_(8)/opt_8.2.JPG" style="height:27px; width:33px" />', NULL),
(1634, 229, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/cQue_(8)/opt_8.3.JPG" style="height:26px; width:42px" />', NULL),
(1635, 229, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/cQue_(8)/opt_8.4.JPG" style="height:28px; width:39px" />', NULL),
(1636, 230, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/cQue_(9)/opt_9.1.JPG" style="height:47px; width:330px" />', NULL),
(1637, 230, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/cQue_(9)/opt_9.1.JPG" style="height:47px; width:330px" />', NULL),
(1638, 230, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/cQue_(9)/opt_9.3.JPG" style="height:44px; width:346px" />', NULL),
(1639, 230, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/cQue_(9)/opt_9.4.JPG" style="height:75px; width:345px" />', NULL),
(1668, 236, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/cQue_(6)/opt_6.1.JPG" style="height:30px; width:43px" />', NULL),
(1669, 236, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/cQue_(6)/opt_6.2.JPG" style="height:27px; width:45px" />', NULL),
(1670, 236, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/cQue_(6)/opt_6.3.JPG" style="height:29px; width:38px" />', NULL),
(1671, 236, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/cQue_(6)/opt_6.4.JPG" style="height:29px; width:50px" />', NULL),
(1672, 235, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/mQue(9)/opt_9.1.JPG" style="height:21px; width:34px" />', NULL),
(1673, 235, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/mQue(9)/opt_9.2.JPG" style="height:23px; width:36px" />​​​​​​​', NULL),
(1674, 235, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/mQue(9)/opt_9.3.JPG" style="height:19px; width:45px" />', NULL),
(1675, 235, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/mQue(9)/opt_9.4.JPG" style="height:25px; width:125px" />', NULL),
(1676, 234, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/mQue(8)/opt_8.1.JPG" style="height:25px; width:45px" />', NULL),
(1677, 234, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/mQue(8)/opt_8.2.JPG" style="height:29px; width:44px" />', NULL),
(1678, 234, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/mQue(8)/opt_8.3.JPG" style="height:34px; width:43px" />', NULL),
(1679, 234, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/mQue(8)/opt_8.4.JPG" style="height:33px; width:41px" />', NULL),
(1680, 233, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/mQue(7)/opt_7.1.JPG" style="height:26px; width:71px" />', NULL),
(1681, 233, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/mQue(7)/opt_7.2.JPG" style="height:29px; width:76px" />', NULL),
(1682, 233, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/mQue(7)/opt_7.3.JPG" style="height:28px; width:66px" />', NULL),
(1683, 233, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/mQue(7)/opt_7.4.JPG" style="height:30px; width:113px" />', NULL),
(1684, 232, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/mQue(6)/opt_6.1.JPG" style="height:28px; width:46px" />', NULL),
(1685, 232, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/mQue(6)/opt_6.2.JPG" style="height:37px; width:42px" />', NULL),
(1686, 232, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/mQue(6)/opt_6.3.JPG" style="height:60px; width:38px" />', NULL),
(1687, 232, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/mQue(6)/opt_6.4.JPG" style="height:59px; width:44px" />', NULL),
(1688, 231, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/mQue(50)/opt_50.1.JPG" style="height:18px; width:27px" />', NULL),
(1689, 231, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/mQue(50)/opt_50.2.JPG" style="height:28px; width:43px" />', NULL),
(1690, 231, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/mQue(50)/opt_50.3.JPG" style="height:36px; width:53px" />', NULL),
(1691, 231, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/mQue(50)/opt_50.4.JPG" style="height:25px; width:115px" />', NULL),
(1692, 151, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/mQue(9)/opt_9.1.JPG" style="height:21px; width:34px" />', NULL),
(1693, 151, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/mQue(9)/opt_9.2.JPG" style="height:23px; width:36px" />', NULL),
(1694, 151, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/mQue(9)/opt_9.3.JPG" style="height:19px; width:45px" />', NULL),
(1695, 151, '<img alt="" src="/vidyarthimitra/ckeditor/kcfinder/upload/images/mQue(9)/opt_9.4.JPG" style="height:25px; width:125px" />', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vid_states`
--

CREATE TABLE IF NOT EXISTS `vid_states` (
`state_id` int(11) NOT NULL,
  `county_id` int(11) NOT NULL,
  `state_name` varchar(100) NOT NULL,
  `time_zone` int(100) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3896 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `vid_states`
--

INSERT INTO `vid_states` (`state_id`, `county_id`, `state_name`, `time_zone`) VALUES
(1302, 356, 'Andaman and Nicobar Islands', 0),
(1303, 356, 'Andhra Pradesh', 0),
(1304, 356, 'Assam', 0),
(1305, 356, 'Bihar', 0),
(1306, 356, 'Chandigarh', 0),
(1307, 356, 'Dadra and Nagar Haveli', 0),
(1308, 356, 'Delhi', 0),
(1309, 356, 'Gujarat', 0),
(1310, 356, 'Haryana', 0),
(1311, 356, 'Himachal Pradesh', 0),
(1312, 356, 'Jammu and Kashmir', 0),
(1313, 356, 'Kerala', 0),
(1314, 356, 'Lakshadweep', 0),
(1315, 356, 'Madhya Pradesh ', 0),
(1316, 356, 'Maharashtra', 0),
(1317, 356, 'Manipur', 0),
(1318, 356, 'Meghalaya', 0),
(1319, 356, 'Karnataka', 0),
(1320, 356, 'Nagaland', 0),
(1321, 356, 'Orissa', 0),
(1322, 356, 'Puducherry', 0),
(1323, 356, 'Punjab', 0),
(1324, 356, 'Rajasthan', 0),
(1325, 356, 'Tamil Nadu', 0),
(1326, 356, 'Tripura', 0),
(1327, 356, 'Uttar Pradesh', 0),
(1328, 356, 'Bengal', 0),
(1329, 356, 'Sikkim', 0),
(1330, 356, 'Arunachal Pradesh', 0),
(1331, 356, 'Mizoram', 0),
(1332, 356, 'Daman and Diu', 0),
(1333, 356, 'Goa', 0),
(1337, 356, 'Chhattisgarh', 0),
(1338, 356, 'Jharkhand', 0),
(1339, 356, 'Uttarakhand', 0),
(3893, 356, 'West Bengal', 0),
(3894, 356, 'Telangana', 0),
(3895, 356, 'Rajasthan', 0);

-- --------------------------------------------------------

--
-- Table structure for table `vid_student`
--

CREATE TABLE IF NOT EXISTS `vid_student` (
`stud_id` int(11) NOT NULL,
  `stud_name` varchar(100) NOT NULL,
  `gender` enum('Male','Female','Other') DEFAULT 'Male',
  `dob` date DEFAULT NULL,
  `stud_email` varchar(50) NOT NULL,
  `stud_password` varchar(50) DEFAULT NULL,
  `username` varchar(20) DEFAULT NULL,
  `stud_contact` varchar(15) NOT NULL,
  `mother_name` varchar(50) DEFAULT NULL,
  `stud_edu_summary` longtext,
  `socialid` varchar(50) DEFAULT NULL,
  `country` enum('India','Other') NOT NULL DEFAULT 'India',
  `address` varchar(200) DEFAULT NULL,
  `other_country` varchar(50) DEFAULT NULL,
  `district` int(50) DEFAULT NULL,
  `state` int(50) DEFAULT NULL,
  `pin_code` varchar(50) DEFAULT NULL,
  `standard` text,
  `college_name` text,
  `college_district` int(50) DEFAULT NULL,
  `college_taluka` varchar(50) DEFAULT NULL,
  `author_id` int(50) DEFAULT NULL,
  `prof_pic` varchar(100) DEFAULT '',
  `register_type` int(5) NOT NULL DEFAULT '0',
  `verify_flag` enum('0','1') NOT NULL DEFAULT '0' COMMENT '0-notverify,1-verify',
  `email_flag` enum('0','1') NOT NULL DEFAULT '0' COMMENT '0-notverified,1-verified',
  `otp` int(11) DEFAULT NULL,
  `forget_flag` enum('0','1') NOT NULL DEFAULT '0' COMMENT '0-incative,1-active',
  `submitdate` date NOT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1' COMMENT '0-inactive,1-active',
  `coordinator_name` varchar(50) NOT NULL,
  `how_to_know` varchar(100) NOT NULL,
  `mailStatus` varchar(5) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=355 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `vid_student`
--

INSERT INTO `vid_student` (`stud_id`, `stud_name`, `gender`, `dob`, `stud_email`, `stud_password`, `username`, `stud_contact`, `mother_name`, `stud_edu_summary`, `socialid`, `country`, `address`, `other_country`, `district`, `state`, `pin_code`, `standard`, `college_name`, `college_district`, `college_taluka`, `author_id`, `prof_pic`, `register_type`, `verify_flag`, `email_flag`, `otp`, `forget_flag`, `submitdate`, `active`, `coordinator_name`, `how_to_know`, `mailStatus`) VALUES
(318, 'Pratik  Pawarasd', 'Male', '2018-03-04', 'pratik@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', NULL, '8208142416', 'Anita', NULL, NULL, 'India', 'Near-Post Office,Pimpalgon', '', 665, 1302, '423202', '12th', 'M.S.G. College Malegaon Camp,Malegaon Dist nashik', NULL, NULL, NULL, 'images/student/_5a9e75ba88935.jpg', 0, '1', '0', 687081, '1', '2017-12-08', '1', 'Sonavane classes', 'frnd', '1'),
(319, 'SachinG', 'Male', '2018-03-06', 'assdsdf@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', NULL, '8055535049', 'asdf', NULL, NULL, 'India', 'Pune', 'fghfh', 0, 0, '423205', '11th', 'asdf', NULL, NULL, 2, 'images/student/_5aa916956a504.jpg', 0, '1', '0', 932406, '1', '2018-03-08', '1', 'asdf', 'asdf', '0'),
(320, 'Mohanish', 'Male', '2018-03-08', 'monty@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', NULL, '9028852043', 'v', NULL, NULL, 'India', 'qwerty', '', 320, 1316, '111111', '12', 'qwerty', NULL, NULL, NULL, '', 0, '1', '0', 284158, '0', '2018-03-12', '1', 'asd', 'dsaa', '0'),
(321, 'Sachin', 'Female', '2018-03-12', 'sachuin@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', NULL, '1212121212', 's', NULL, NULL, 'Other', 'fff', 'ffff', NULL, NULL, NULL, 'f', 'f', NULL, NULL, 2, '', 0, '0', '0', NULL, '0', '2018-03-12', '1', '', '', '0'),
(336, 'Pramoda', 'Male', '1990-01-01', 'prersesofd@gmaeil.cwom', 'e10adc3949ba59abbe56e057f20f883e', NULL, '9673554359', 'Demo Name', NULL, NULL, 'India', 'DemoAddress', NULL, NULL, NULL, '666666', '12 th', 'Demo College Name', NULL, NULL, 2, '', 0, '1', '0', 123456, '0', '2018-03-13', '1', 'Cordinator Name', '', '0'),
(337, 'testing reg', 'Male', NULL, 'test@gmail.com', NULL, NULL, '8888888888', NULL, NULL, NULL, 'India', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 0, '0', '0', 793786, '0', '2018-03-26', '1', '', '', '0'),
(338, 'pratik jio testing preparation test', 'Male', NULL, 'pratikjio@gmail.com', 'd9b6048301ae6d7775dcda9279dd7664', NULL, '820832416', NULL, NULL, NULL, 'India', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 0, '1', '0', 680627, '0', '2018-03-26', '1', '', '', '1'),
(339, 'pramod.da', 'Male', NULL, 'asdf@sdf.dfg', 'e10adc3949ba59abbe56e057f20f883e', NULL, '8972873421', NULL, NULL, NULL, 'India', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 0, '1', '0', 931033, '0', '2018-03-26', '1', '', '', '0'),
(340, 'Sachin Gaherwar', 'Male', NULL, 'walunjkarshahuraj@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', NULL, '8796589348', NULL, NULL, NULL, 'India', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 0, '1', '0', 827404, '0', '2018-04-12', '1', '', '', '0'),
(341, 'Rahul Pawar', 'Male', NULL, 'rahul@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', NULL, '9545474193', NULL, NULL, NULL, 'India', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 0, '1', '0', 720892, '0', '2018-04-12', '1', '', '', '0'),
(342, 'Ameya Waghmare', 'Male', NULL, 'ameyawaghmare91@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', NULL, '9096089910', NULL, NULL, NULL, 'India', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 0, '1', '0', 713009, '0', '2018-04-12', '1', '', '', '0'),
(343, 'Sumit', 'Male', NULL, 'sumit.deshpande10@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', NULL, '9867009208', NULL, NULL, NULL, 'India', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 0, '1', '0', 922299, '0', '2018-04-12', '1', '', '', '0'),
(344, 'Aniket Salve', 'Male', NULL, 'aniket.salve100@gmail.com', 'bbc7c65a1df649f96bc8d21f98d5a611', NULL, '8380082280', NULL, NULL, NULL, 'India', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 0, '1', '0', 801284, '0', '2018-04-12', '1', '', '', '1'),
(345, 'Priyanka Nalawade', 'Male', NULL, 'priyankanalawade@gmail.com', NULL, NULL, '9955224411', NULL, NULL, NULL, 'India', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 0, '0', '0', 383090, '0', '2018-04-12', '1', '', '', '0'),
(346, 'Ganesh Thorat', 'Male', '1990-08-30', 'ganesh.thorat71@gmail.com', 'cfcd208495d565ef66e7dff9f98764da', NULL, '9890637105', '', NULL, NULL, 'India', 'thythg', '', 337, 1316, '413102', 'MCA', 'VIIT', NULL, NULL, NULL, 'images/student/_5acf48788c7c5.jpg', 0, '1', '0', 566561, '0', '2018-04-12', '1', '', '<p></p>', '0'),
(347, 'Ganesh Shelke', 'Male', NULL, 'ganesh.shelke@siddhiglobal.com', 'cfcd208495d565ef66e7dff9f98764da', NULL, '7072631005', NULL, NULL, NULL, 'India', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 0, '1', '0', 406326, '0', '2018-04-12', '1', '', '', '0'),
(348, 'Priyanka Nalawade', 'Female', '2018-04-04', 'piyu@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', NULL, '8379096676', 'Chhaya', NULL, NULL, 'India', 'kothrud', '', 343, 1316, '415537', '12th', 'KPIT kolhapur', NULL, NULL, NULL, '', 0, '1', '0', 284432, '0', '2018-04-12', '1', '', '', '0'),
(349, 'Sachin', 'Male', NULL, 'sachin@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', NULL, '9834047524', NULL, NULL, NULL, 'India', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 0, '1', '0', 905764, '0', '2018-04-12', '1', '', '', '0'),
(350, 'pramod Deore', 'Male', NULL, 'pramod@gmail.com', NULL, NULL, '7777777777', NULL, NULL, NULL, 'India', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 0, '0', '0', 116973, '0', '2018-04-12', '1', '', '', '0'),
(351, 'Jyoti pawar', 'Male', NULL, 'jyotipawar38@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', NULL, '9172214541', NULL, NULL, NULL, 'India', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 0, '1', '0', 194454, '0', '2018-04-12', '1', '', '', '0'),
(352, 'pramod deore', 'Male', NULL, 'pramod.deore@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', NULL, '9623541750', NULL, NULL, NULL, 'India', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 0, '1', '0', 763821, '0', '2018-04-12', '1', '', '', '0'),
(353, 'Hritik Pawar', 'Male', NULL, 'hriktik@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', NULL, '1234567892', NULL, NULL, NULL, 'India', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 0, '1', '0', 216674, '0', '2018-04-13', '1', '', '', '0'),
(354, 'sdfsdf', '', '1930-03-03', 'demdfdfo@mail.com', '3811ba77a88bcd2cc789df34c5379055', NULL, '8888884488', 'Demo Name', NULL, NULL, 'India', 'DemoAddress', NULL, NULL, NULL, '666666', '12 th', 'Demo College Name', NULL, NULL, 2, '', 0, '0', '0', NULL, '0', '2018-04-13', '1', 'Cordinator Name', '', '0');

-- --------------------------------------------------------

--
-- Table structure for table `vid_student_buy_exam`
--

CREATE TABLE IF NOT EXISTS `vid_student_buy_exam` (
`stud_course_batch_id` int(50) NOT NULL,
  `roll_no` int(55) NOT NULL,
  `stud_id` int(50) NOT NULL,
  `course_id` int(11) NOT NULL,
  `exam_schedule_id` int(11) NOT NULL,
  `is_payment` enum('0','1') NOT NULL DEFAULT '0' COMMENT '0-notpay,1-pay',
  `payment_id` int(11) NOT NULL DEFAULT '0',
  `submitdate` date NOT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1' COMMENT '0-inctive,1-active'
) ENGINE=InnoDB AUTO_INCREMENT=117 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `vid_student_buy_exam`
--

INSERT INTO `vid_student_buy_exam` (`stud_course_batch_id`, `roll_no`, `stud_id`, `course_id`, `exam_schedule_id`, `is_payment`, `payment_id`, `submitdate`, `active`) VALUES
(84, 1102400001, 318, 11, 240, '1', 225, '2018-03-23', '1'),
(85, 1102400002, 318, 12, 240, '1', 226, '2018-03-23', '1'),
(86, 1102680001, 318, 11, 268, '1', 245, '2018-04-04', '1'),
(89, 1102370001, 318, 11, 237, '1', 254, '2018-04-09', '1'),
(90, 1102430001, 319, 11, 244, '1', 255, '2018-04-11', '1'),
(91, 1102420001, 318, 11, 243, '1', 256, '2018-04-11', '1'),
(92, 1102700001, 319, 11, 270, '1', 265, '2018-04-12', '1'),
(93, 1102380001, 319, 11, 238, '1', 269, '2018-04-12', '1'),
(94, 1102420001, 319, 11, 242, '1', 270, '2018-04-12', '1'),
(95, 1102700002, 343, 11, 270, '1', 272, '2018-04-12', '1'),
(96, 1102700003, 344, 11, 270, '1', 274, '2018-04-12', '1'),
(97, 1102700004, 349, 11, 270, '1', 275, '2018-04-12', '1'),
(98, 1102700005, 347, 11, 270, '1', 276, '2018-04-12', '1'),
(99, 1102700006, 342, 11, 270, '1', 279, '2018-04-12', '1'),
(100, 1102700007, 346, 11, 270, '1', 278, '2018-04-12', '1'),
(101, 1102700008, 351, 11, 270, '1', 280, '2018-04-12', '1'),
(102, 1102700009, 352, 11, 270, '1', 282, '2018-04-12', '1'),
(103, 1102700010, 348, 11, 270, '1', 283, '2018-04-12', '1'),
(104, 1102700011, 318, 11, 270, '1', 285, '2018-04-12', '1'),
(105, 1102380002, 318, 11, 238, '1', 287, '2018-04-13', '1'),
(106, 1102830001, 318, 11, 283, '1', 287, '2018-04-13', '1'),
(107, 1102390001, 318, 11, 239, '1', 288, '2018-04-13', '1'),
(108, 1102500001, 318, 11, 250, '1', 288, '2018-04-13', '1'),
(109, 1102850001, 319, 11, 285, '1', 289, '2018-04-13', '1'),
(110, 1102850002, 318, 11, 285, '1', 290, '2018-04-13', '1'),
(111, 1102900001, 318, 11, 290, '1', 290, '2018-04-13', '1'),
(112, 1202910001, 319, 12, 291, '1', 291, '2018-04-13', '1'),
(113, 1102900002, 319, 11, 290, '1', 293, '2018-04-13', '1'),
(114, 1102850003, 353, 11, 285, '1', 295, '2018-04-13', '1'),
(115, 1202910002, 353, 12, 291, '1', 296, '2018-04-13', '1'),
(116, 1102850004, 354, 11, 285, '1', 297, '2018-04-13', '1');

-- --------------------------------------------------------

--
-- Table structure for table `vid_student_course_temp`
--

CREATE TABLE IF NOT EXISTS `vid_student_course_temp` (
`temp_id` int(11) NOT NULL,
  `schedule_id` int(11) DEFAULT NULL,
  `studentid` int(11) DEFAULT NULL,
  `submitdate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `vid_student_exam`
--

CREATE TABLE IF NOT EXISTS `vid_student_exam` (
`stud_exam_id` int(11) NOT NULL,
  `stud_id` int(11) NOT NULL,
  `exam_schedule_id` int(11) NOT NULL,
  `roll_no` int(11) NOT NULL,
  `start_time` time DEFAULT NULL,
  `total_submit_time` int(11) DEFAULT NULL,
  `iscomplete_flag` enum('0','1') NOT NULL DEFAULT '0' COMMENT '0-incomplete,1-complete',
  `submitdate` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `vid_student_exam`
--

INSERT INTO `vid_student_exam` (`stud_exam_id`, `stud_id`, `exam_schedule_id`, `roll_no`, `start_time`, `total_submit_time`, `iscomplete_flag`, `submitdate`) VALUES
(1, 319, 270, 1102700001, '15:51:43', NULL, '1', '2018-04-12 15:51:43'),
(2, 343, 270, 1102700002, '17:01:58', NULL, '1', '2018-04-12 17:01:58'),
(3, 344, 270, 1102700003, '17:02:01', NULL, '1', '2018-04-12 17:02:01'),
(4, 349, 270, 1102700004, '17:02:55', NULL, '1', '2018-04-12 17:02:55'),
(5, 347, 270, 1102700005, '17:05:20', NULL, '1', '2018-04-12 17:05:20'),
(6, 346, 270, 1102700007, '17:08:22', NULL, '1', '2018-04-12 17:08:22'),
(7, 351, 270, 1102700008, '17:08:38', NULL, '1', '2018-04-12 17:08:38'),
(8, 352, 270, 1102700009, '17:09:27', NULL, '1', '2018-04-12 17:09:27'),
(9, 342, 270, 1102700006, '17:12:42', NULL, '1', '2018-04-12 17:12:42'),
(10, 348, 270, 1102700010, '17:26:00', NULL, '1', '2018-04-12 17:26:00'),
(11, 318, 270, 1102700011, '18:45:20', NULL, '1', '2018-04-12 18:45:20'),
(12, 319, 285, 1102850001, '13:06:51', NULL, '1', '2018-04-13 13:06:51'),
(13, 318, 290, 1102900001, '13:17:49', NULL, '1', '2018-04-13 13:17:49'),
(14, 319, 291, 1202910001, '13:31:05', NULL, '0', '2018-04-13 13:31:05'),
(15, 319, 290, 1102900002, '13:35:26', NULL, '1', '2018-04-13 13:35:26'),
(16, 353, 285, 1102850003, '17:05:40', NULL, '1', '2018-04-13 17:05:40');

-- --------------------------------------------------------

--
-- Table structure for table `vid_student_exam_result`
--

CREATE TABLE IF NOT EXISTS `vid_student_exam_result` (
`stud_exam_result_id` int(11) NOT NULL,
  `stud_exam_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `correct_option_id` int(11) NOT NULL,
  `ques_option_id` int(11) DEFAULT NULL,
  `time_taken` int(11) DEFAULT NULL COMMENT 'time in seconds'
) ENGINE=InnoDB AUTO_INCREMENT=251 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `vid_student_exam_result`
--

INSERT INTO `vid_student_exam_result` (`stud_exam_result_id`, `stud_exam_id`, `question_id`, `correct_option_id`, `ques_option_id`, `time_taken`) VALUES
(1, 1, 217, 1575, 1572, 5),
(2, 1, 219, 1588, 1589, 4),
(3, 1, 218, 1587, 1585, 3),
(4, 1, 216, 1583, 1582, 3),
(5, 1, 220, 1592, NULL, 2),
(6, 1, 223, 1606, 1605, 4),
(7, 1, 222, 1603, NULL, 2),
(8, 1, 224, 1608, 1608, 3),
(9, 1, 225, 1612, 1613, 3),
(10, 1, 221, 1598, 1597, 5),
(11, 1, 229, 1635, 1634, 3),
(12, 1, 230, 1636, 1638, 3),
(13, 1, 228, 1631, 1630, 3),
(14, 1, 227, 1626, 1626, 3),
(15, 1, 236, 1670, 1669, 3),
(16, 1, 231, 1689, 1690, 32),
(17, 1, 234, 1678, 1677, 3),
(18, 1, 233, 1683, 1681, 6),
(19, 1, 232, 1684, 1686, 3),
(20, 1, 151, 1693, 1695, 8),
(21, 2, 217, 1575, 1572, 7),
(22, 2, 216, 1583, 1581, 38),
(23, 2, 218, 1587, 1586, 10),
(24, 2, 219, 1588, 1590, 9),
(25, 2, 220, 1592, 1595, 7),
(26, 2, 225, 1612, NULL, 6),
(27, 2, 223, 1606, 1607, 48),
(28, 2, 224, 1608, 1609, 50),
(29, 2, 222, 1603, 1601, 4),
(30, 2, 221, 1598, 1596, 11),
(31, 2, 228, 1631, NULL, 5),
(32, 2, 230, 1636, 1636, 8),
(33, 2, 227, 1626, 1625, 6),
(34, 2, 236, 1670, 1670, 259),
(35, 2, 229, 1635, 1633, 22),
(36, 2, 233, 1683, 1683, 9),
(37, 2, 231, 1689, NULL, 2),
(38, 2, 232, 1684, 1684, 207),
(39, 2, 234, 1678, 1676, 12),
(40, 2, 151, 1693, 1694, 290),
(41, 3, 216, 1583, 1583, 9),
(42, 3, 220, 1592, 1593, 4),
(43, 3, 218, 1587, 1585, 10),
(44, 3, 219, 1588, 1588, 8),
(45, 3, 217, 1575, 1572, 4),
(46, 3, 224, 1608, 1608, 14),
(47, 3, 221, 1598, 1599, 8),
(48, 3, 222, 1603, 1602, 12),
(49, 3, 225, 1612, 1613, 22),
(50, 3, 223, 1606, 1604, 12),
(51, 3, 227, 1626, 1625, 11),
(52, 3, 230, 1636, 1637, 4),
(53, 3, 228, 1631, 1628, 3),
(54, 3, 229, 1635, 1633, 12),
(55, 3, 236, 1670, 1669, 3),
(56, 3, 234, 1678, 1676, 4),
(57, 3, 233, 1683, 1680, 4),
(58, 3, 232, 1684, 1686, 6),
(59, 3, 231, 1689, 1689, 4),
(60, 3, 151, 1693, 1695, 7),
(61, 4, 219, 1588, 1588, 5),
(62, 4, 220, 1592, 1593, 3),
(63, 4, 216, 1583, 1582, 3),
(64, 4, 218, 1587, 1586, 2),
(65, 4, 217, 1575, 1574, 4),
(66, 4, 225, 1612, 1615, 6),
(67, 4, 222, 1603, 1601, 26),
(68, 4, 223, 1606, 1606, 2),
(69, 4, 224, 1608, 1609, 2),
(70, 4, 221, 1598, 1597, 8),
(71, 4, 227, 1626, 1627, 3),
(72, 4, 229, 1635, 1635, 3),
(73, 4, 228, 1631, 1631, 2),
(74, 4, 236, 1670, 1671, 2),
(75, 4, 230, 1636, 1639, 3),
(76, 4, 232, 1684, 1687, 7),
(77, 4, 234, 1678, 1679, 2),
(78, 4, 231, 1689, 1691, 2),
(79, 4, 233, 1683, 1683, 2),
(80, 4, 151, 1693, 1694, 4),
(81, 5, 216, 1583, 1581, 10),
(82, 5, 219, 1588, 1589, 3),
(83, 5, 220, 1592, 1595, 2),
(84, 5, 218, 1587, 1585, 2),
(85, 5, 217, 1575, 1574, 3),
(86, 5, 224, 1608, 1610, 3),
(87, 5, 225, 1612, 1613, 2),
(88, 5, 223, 1606, 1607, 2),
(89, 5, 222, 1603, 1602, 3),
(90, 5, 221, 1598, 1597, 3),
(91, 5, 229, 1635, 1633, 16),
(92, 5, 236, 1670, 1670, 3),
(93, 5, 230, 1636, 1638, 3),
(94, 5, 227, 1626, 1625, 2),
(95, 5, 228, 1631, 1631, 2),
(96, 5, 233, 1683, 1681, 4),
(97, 5, 234, 1678, 1678, 2),
(98, 5, 232, 1684, 1687, 4),
(99, 5, 231, 1689, 1689, 3),
(100, 5, 151, 1693, 1695, 12),
(101, 6, 218, 1587, 1587, 98),
(102, 6, 219, 1588, 1588, 58),
(103, 6, 217, 1575, 1573, 17),
(104, 6, 216, 1583, 1580, 21),
(105, 6, 220, 1592, NULL, 4),
(106, 6, 224, 1608, NULL, 28),
(107, 6, 223, 1606, NULL, 54),
(108, 6, 222, 1603, NULL, 6),
(109, 6, 221, 1598, 1598, 19),
(110, 6, 225, 1612, 1614, 68),
(111, 6, 228, 1631, 1631, 54),
(112, 6, 236, 1670, 1671, 8),
(113, 6, 229, 1635, 1633, 3),
(114, 6, 227, 1626, 1624, 3),
(115, 6, 230, 1636, 1637, 2),
(116, 6, 233, 1683, 1681, 2),
(117, 6, 232, 1684, 1686, 3),
(118, 6, 234, 1678, 1679, 3),
(119, 6, 231, 1689, 1690, 2),
(120, 6, 151, 1693, 1692, 5),
(121, 7, 217, 1575, 1572, 32),
(122, 7, 220, 1592, 1592, 4),
(123, 7, 218, 1587, 1585, 3),
(124, 7, 219, 1588, 1590, 5),
(125, 7, 216, 1583, 1580, 4),
(126, 7, 222, 1603, 1602, 4),
(127, 7, 224, 1608, 1608, 3),
(128, 7, 221, 1598, 1596, 8),
(129, 7, 223, 1606, NULL, 6),
(130, 7, 225, 1612, NULL, 4),
(131, 7, 230, 1636, 1637, 9),
(132, 7, 228, 1631, 1629, 5),
(133, 7, 227, 1626, 1626, 8),
(134, 7, 236, 1670, 1670, 18),
(135, 7, 229, 1635, 1634, 6),
(136, 7, 233, 1683, 1682, 2),
(137, 7, 234, 1678, 1677, 3),
(138, 7, 231, 1689, NULL, 3),
(139, 7, 232, 1684, 1686, 3),
(140, 7, 151, 1693, 1692, 3),
(141, 8, 220, 1592, NULL, 13),
(142, 8, 217, 1575, 1573, 14),
(143, 8, 216, 1583, 1582, 18),
(144, 8, 218, 1587, 1587, 11),
(145, 8, 219, 1588, NULL, 4),
(146, 8, 222, 1603, 1602, 12),
(147, 8, 223, 1606, 1604, 10),
(148, 8, 224, 1608, 1611, 12),
(149, 8, 221, 1598, 1596, 9),
(150, 8, 225, 1612, NULL, 3),
(151, 8, 236, 1670, 1668, 11),
(152, 8, 230, 1636, 1639, 6),
(153, 8, 228, 1631, 1628, 5),
(154, 8, 229, 1635, 1635, 4),
(155, 8, 227, 1626, 1627, 8),
(156, 8, 231, 1689, NULL, 4),
(157, 8, 234, 1678, 1676, 13),
(158, 8, 232, 1684, 1687, 5),
(159, 8, 233, 1683, 1680, 5),
(160, 8, 151, 1693, 1695, 7),
(161, 9, 218, 1587, 1584, 24),
(162, 9, 216, 1583, 1583, 25),
(163, 9, 219, 1588, 1588, 31),
(164, 9, 220, 1592, 1593, 23),
(165, 9, 217, 1575, 1574, 19),
(166, 9, 222, 1603, 1602, 14),
(167, 9, 224, 1608, NULL, 5),
(168, 9, 221, 1598, 1596, 22),
(169, 9, 225, 1612, 1613, 21),
(170, 9, 223, 1606, 1605, 10),
(171, 9, 228, 1631, NULL, 5),
(172, 9, 236, 1670, 1670, 13),
(173, 9, 230, 1636, 1638, 13),
(174, 9, 227, 1626, NULL, 6),
(175, 9, 229, 1635, 1633, 11),
(176, 9, 231, 1689, 1690, 8),
(177, 9, 232, 1684, 1686, 12),
(178, 9, 234, 1678, 1678, 26),
(179, 9, 233, 1683, 1683, 20),
(180, 9, 151, 1693, 1695, 7),
(181, 10, 218, 1587, 1587, 23),
(182, 10, 217, 1575, NULL, 12),
(183, 10, 216, 1583, 1580, 18),
(184, 10, 219, 1588, NULL, 16),
(185, 10, 220, 1592, NULL, 209),
(186, 10, 225, 1612, NULL, 20),
(187, 10, 221, 1598, 1599, 27),
(188, 10, 222, 1603, NULL, 9),
(189, 10, 223, 1606, NULL, 9),
(190, 10, 224, 1608, NULL, 3),
(191, 10, 230, 1636, NULL, 1),
(192, 10, 227, 1626, NULL, 38),
(193, 10, 236, 1670, NULL, 3),
(194, 10, 229, 1635, NULL, NULL),
(195, 10, 228, 1631, NULL, 29),
(196, 10, 231, 1689, NULL, 6),
(197, 10, 233, 1683, NULL, NULL),
(198, 10, 234, 1678, NULL, 7),
(199, 10, 232, 1684, NULL, NULL),
(200, 10, 151, 1693, NULL, NULL),
(201, 11, 220, 1592, 1592, 13),
(202, 11, 218, 1587, 1585, 4),
(203, 11, 219, 1588, 1588, 2),
(204, 11, 217, 1575, 1574, 2),
(205, 11, 216, 1583, 1581, 2),
(206, 11, 224, 1608, NULL, 1),
(207, 11, 223, 1606, NULL, 1),
(208, 11, 222, 1603, NULL, 5),
(209, 11, 225, 1612, NULL, NULL),
(210, 11, 221, 1598, NULL, 8),
(211, 11, 228, 1631, NULL, 3),
(212, 11, 236, 1670, NULL, NULL),
(213, 11, 227, 1626, NULL, NULL),
(214, 11, 230, 1636, NULL, NULL),
(215, 11, 229, 1635, NULL, NULL),
(216, 11, 231, 1689, NULL, NULL),
(217, 11, 232, 1684, NULL, NULL),
(218, 11, 234, 1678, NULL, NULL),
(219, 11, 233, 1683, NULL, NULL),
(220, 11, 151, 1693, NULL, NULL),
(221, 12, 220, 1592, 1595, 1878),
(222, 12, 219, 1588, 1588, 1792),
(223, 12, 216, 1583, 1580, 1364),
(224, 12, 218, 1587, 1585, 956),
(225, 12, 217, 1575, 1573, 1292),
(226, 12, 229, 1635, NULL, 725),
(227, 12, 230, 1636, NULL, 533),
(228, 12, 236, 1670, NULL, 362),
(229, 12, 228, 1631, NULL, 203),
(230, 12, 227, 1626, 1625, 55),
(231, 13, 216, 1583, NULL, 233),
(232, 13, 217, 1575, NULL, 2),
(233, 13, 218, 1587, NULL, 1),
(234, 13, 220, 1592, NULL, NULL),
(235, 13, 219, 1588, NULL, NULL),
(236, 15, 217, 1575, 1575, 6),
(237, 15, 219, 1588, 1589, 2),
(238, 15, 218, 1587, 1587, 2),
(239, 15, 216, 1583, 1583, 2),
(240, 15, 220, 1592, 1595, 5),
(241, 16, 217, 1575, 1572, 4),
(242, 16, 220, 1592, 1593, 3),
(243, 16, 219, 1588, 1589, 3),
(244, 16, 216, 1583, 1582, 2),
(245, 16, 218, 1587, 1585, 3),
(246, 16, 227, 1626, 1626, 4),
(247, 16, 228, 1631, 1630, 9),
(248, 16, 230, 1636, 1636, 3),
(249, 16, 229, 1635, 1633, 2),
(250, 16, 236, 1670, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vid_student_final_result`
--

CREATE TABLE IF NOT EXISTS `vid_student_final_result` (
`sr_id` int(11) NOT NULL,
  `sr_stud_id` int(10) NOT NULL,
  `sr_stud_roll_no` int(20) NOT NULL,
  `sr_course_id` int(10) NOT NULL,
  `sr_schedule_id` int(10) NOT NULL,
  `sr_exam_id` int(10) NOT NULL,
  `sr_stud_exam_id` int(10) NOT NULL,
  `sr_total_que` int(10) NOT NULL DEFAULT '0',
  `sr_attempt_que` int(10) NOT NULL DEFAULT '0',
  `sr_correct_que` int(10) NOT NULL DEFAULT '0',
  `sr_wrong_que` int(10) NOT NULL DEFAULT '0',
  `sr_total_marks` float NOT NULL DEFAULT '0',
  `sr_neg_marks` float NOT NULL DEFAULT '0',
  `sr_total_score` float NOT NULL DEFAULT '0',
  `sr_total_time` int(10) DEFAULT '0',
  `sr_result_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sr_exam_max_marks` int(10) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `vid_student_final_result`
--

INSERT INTO `vid_student_final_result` (`sr_id`, `sr_stud_id`, `sr_stud_roll_no`, `sr_course_id`, `sr_schedule_id`, `sr_exam_id`, `sr_stud_exam_id`, `sr_total_que`, `sr_attempt_que`, `sr_correct_que`, `sr_wrong_que`, `sr_total_marks`, `sr_neg_marks`, `sr_total_score`, `sr_total_time`, `sr_result_date`, `sr_exam_max_marks`) VALUES
(1, 318, 1102420001, 11, 243, 129, 46, 10, 8, 2, 6, 2, 6, -4, 24, '2018-04-12 06:33:59', 13),
(2, 319, 1102700001, 11, 270, 129, 1, 20, 18, 2, 16, 4, 21, -17, 101, '2018-04-12 10:15:48', 50),
(3, 349, 1102700004, 11, 270, 129, 4, 20, 20, 5, 15, 12, 19, 10, 91, '2018-04-12 11:34:22', 50),
(4, 344, 1102700003, 11, 270, 129, 3, 20, 20, 4, 16, 10, 20, -10, 161, '2018-04-12 11:34:38', 50),
(5, 347, 1102700005, 11, 270, 129, 5, 20, 20, 4, 16, 12, 19, 10, 84, '2018-04-12 11:36:54', 50),
(6, 351, 1102700008, 11, 270, 129, 7, 20, 17, 4, 13, 8, 17, 5, 133, '2018-04-12 11:40:48', 50),
(7, 352, 1102700009, 11, 270, 129, 8, 20, 16, 2, 14, 4, 18, -14, 174, '2018-04-12 11:42:24', 50),
(8, 346, 1102700007, 11, 270, 129, 6, 20, 16, 4, 12, 8, 17, -9, 458, '2018-04-12 11:46:12', 50),
(9, 342, 1102700006, 11, 270, 129, 9, 20, 17, 5, 12, 14, 15, 5, 315, '2018-04-12 11:47:54', 50),
(10, 343, 1102700002, 11, 270, 129, 2, 20, 17, 4, 13, 12, 15, -3, 1010, '2018-04-12 11:56:43', 50),
(11, 348, 1102700010, 11, 270, 129, 10, 20, 3, 1, 2, 2, 2, 0, 430, '2018-04-12 12:05:56', 50),
(12, 318, 1102700011, 11, 270, 129, 11, 20, 5, 2, 3, 4, 3, 1, 41, '2018-04-12 13:16:40', 50),
(13, 319, 1102850001, 11, 285, 129, 12, 10, 6, 1, 5, 2, 5, -3, 9160, '2018-04-13 07:43:36', 20),
(14, 318, 1102900001, 11, 290, 129, 13, 5, 0, 0, 0, 0, 0, 0, 236, '2018-04-13 08:05:14', 10),
(15, 319, 1102900002, 11, 290, 129, 15, 5, 5, 3, 2, 6, 2, 4, 17, '2018-04-13 08:05:52', 10),
(16, 353, 1102850003, 11, 285, 129, 16, 10, 9, 2, 7, 4, 7, -3, 33, '2018-04-13 11:36:14', 20);

-- --------------------------------------------------------

--
-- Table structure for table `vid_student_payment_details`
--

CREATE TABLE IF NOT EXISTS `vid_student_payment_details` (
`payment_id` int(11) NOT NULL,
  `txnid` varchar(50) NOT NULL,
  `stud_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `discount_amount` int(11) NOT NULL DEFAULT '0',
  `course_id` int(11) DEFAULT '0',
  `payment_date` datetime NOT NULL,
  `update_date` datetime DEFAULT NULL,
  `status` enum('0','1','2') NOT NULL DEFAULT '0' COMMENT '0-pendding,1-success,2-fail',
  `payment_type` varchar(20) NOT NULL DEFAULT 'Paytm'
) ENGINE=InnoDB AUTO_INCREMENT=298 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `vid_student_payment_details`
--

INSERT INTO `vid_student_payment_details` (`payment_id`, `txnid`, `stud_id`, `amount`, `discount_amount`, `course_id`, `payment_date`, `update_date`, `status`, `payment_type`) VALUES
(55, '2e0440dd6d1919155d3a', 318, 2018, 0, 0, '2017-12-28 11:55:55', NULL, '0', 'Paytm'),
(56, '7c2718078df507da368b', 318, 2018, 0, 0, '2017-12-28 11:56:41', NULL, '0', 'Paytm'),
(57, 'a0263655f1d07c686463', 318, 101, 0, 0, '2017-12-28 11:57:13', '2017-12-28 11:57:25', '1', 'Paytm'),
(58, '1e833824ef329f3820a7', 318, 101, 0, 0, '2017-12-28 13:03:51', '2017-12-28 13:04:00', '1', 'Paytm'),
(59, 'c26af2e1bb03c14761a8', 318, 101, 0, 0, '2017-12-28 13:06:52', '2017-12-28 13:07:06', '1', 'Paytm'),
(60, 'e5587b79a09a6e4acfd5', 318, 101, 0, 0, '2017-12-28 13:10:17', '2017-12-28 13:10:41', '1', 'Paytm'),
(61, '7d86d11c3ebd4325af5c', 318, 101, 0, 0, '2017-12-28 13:12:46', NULL, '0', 'Paytm'),
(62, '32b693a5005b1fa6b570', 318, 101, 0, 0, '2017-12-28 13:13:25', '2017-12-28 13:13:42', '1', 'Paytm'),
(63, '6eb6734494bd146997ad', 318, 101, 0, 0, '2017-12-28 16:29:43', '2017-12-28 16:29:51', '1', 'Paytm'),
(64, '11bf4e112bd9667fb871', 318, 101, 0, 0, '2017-12-28 16:38:47', NULL, '0', 'Paytm'),
(65, 'd4c00fde852438b86c7b', 318, 101, 0, 0, '2017-12-28 16:39:21', '2017-12-28 16:39:28', '1', 'Paytm'),
(66, '4b771c541591067ecbb6', 318, 101, 0, 0, '2017-12-28 16:39:22', NULL, '0', 'Paytm'),
(67, '6185d1836c470ba63764', 318, 101, 0, 0, '2017-12-29 12:05:04', '2017-12-29 12:05:13', '1', 'Paytm'),
(68, 'f6cf7b26af10b916d817', 318, 206, 0, 0, '2017-12-29 12:36:52', NULL, '0', 'Paytm'),
(69, 'db2866cc2236951a557a', 318, 206, 0, 0, '2017-12-29 12:37:26', NULL, '0', 'Paytm'),
(70, '6fc91c91b66be8bcc5b9', 318, 105, 0, 0, '2017-12-29 12:37:48', '2017-12-29 12:38:01', '1', 'Paytm'),
(71, '90d39150ebcfc88fab58', 318, 101, 0, 0, '2018-01-05 11:37:39', '2018-01-05 11:38:25', '1', 'Paytm'),
(72, '2b919423955df46b237a', 331, 104, 0, 0, '2018-01-08 15:34:07', '2018-01-08 15:37:13', '1', 'Paytm'),
(73, '4ebbe6ac8920f44b90b3', 318, 101, 0, 0, '2018-01-08 15:46:10', NULL, '0', 'Paytm'),
(74, '8a2cb673eaaacb82da7a', 318, 102, 0, 0, '2018-01-12 14:58:31', NULL, '0', 'Paytm'),
(75, 'dfcce24428043d48b9a6', 318, 2018, 0, 0, '2018-02-05 14:13:40', NULL, '0', 'Paytm'),
(76, '488e25d5277366ce0e58', 318, 2018, 0, 0, '2018-02-05 14:14:09', NULL, '0', 'Paytm'),
(77, '722ec553f6b31cad6a37', 318, 2018, 0, 0, '2018-02-05 14:15:17', NULL, '0', 'Paytm'),
(78, 'd1476d439e7b9b3fdc45', 318, 2018, 0, 0, '2018-02-05 14:30:58', '2018-02-05 14:31:35', '1', 'Paytm'),
(79, '3d39deb494a8322979a9', 318, 2018, 0, 0, '2018-02-05 14:31:55', NULL, '0', 'Paytm'),
(80, '6d1581763e1c6f8f5ff0', 318, 108, 0, 0, '2018-02-05 14:33:40', '2018-02-05 14:33:52', '1', 'Paytm'),
(81, '4f42fbd72c404b79caed', 318, 228, 0, 0, '2018-02-05 14:35:03', '2018-02-05 14:35:21', '1', 'Paytm'),
(82, 'f6d13858832c2b02a0cb', 318, 228, 0, 0, '2018-02-13 14:56:03', NULL, '0', 'Paytm'),
(83, 'fadfed7d09e0d08b76c7', 318, 228, 0, 0, '2018-02-13 14:59:51', NULL, '0', 'Paytm'),
(84, 'caeb3eae748cff8167d9', 318, 228, 0, 0, '2018-02-13 15:17:33', NULL, '0', 'Paytm'),
(85, 'c51cfc23621d0febf113', 318, 228, 0, 0, '2018-02-13 15:55:01', '2018-02-13 15:55:22', '1', 'Paytm'),
(86, 'c5a3f92acfe209392fed', 318, 228, 0, 0, '2018-02-13 16:10:04', '2018-02-13 16:10:12', '1', 'Paytm'),
(87, '8d350d0ec7fda098a72e', 318, 108, 0, 0, '2018-02-14 11:48:23', '2018-02-14 11:48:29', '1', 'Paytm'),
(88, 'c2665cf06519d453bbc5', 318, 108, 0, 0, '2018-02-14 13:33:41', '2018-02-14 13:33:48', '1', 'Paytm'),
(89, 'b07a487cb77191968c8e', 318, 108, 0, 0, '2018-02-14 14:07:45', '2018-02-14 14:07:51', '1', 'Paytm'),
(90, '8a4dacf705a7cd2c12ec', 318, 108, 0, 0, '2018-02-24 13:37:54', '2018-02-24 13:38:11', '1', 'Paytm'),
(91, '1d7ddacae9929f5babbb', 503, 108, 0, 0, '2018-02-24 16:08:59', '2018-02-24 16:13:06', '1', 'Paytm'),
(92, '7deaad8479fb4e2b607a', 318, 108, 0, 0, '2018-02-26 13:10:05', '2018-02-26 13:10:13', '1', 'Paytm'),
(93, '928a148f542c362afd12', 318, 105, 0, 0, '2018-03-01 19:15:46', NULL, '0', 'Paytm'),
(94, '4fc77be5dd46d4e27c73', 318, 105, 0, 0, '2018-03-01 19:17:31', NULL, '0', 'Paytm'),
(95, '0af7f2182e92ba1174f1', 318, 605, 0, 0, '2018-03-01 19:18:37', NULL, '0', 'Paytm'),
(96, '4d7ad8442137499899ef', 318, 605, 0, 0, '2018-03-01 19:20:08', NULL, '0', 'Paytm'),
(97, 'a318ac2981ba0cb154aa', 318, 605, 0, 0, '2018-03-01 19:26:37', '2018-03-01 19:26:46', '1', 'Paytm'),
(98, 'b4363b8fdae56ff9827d', 318, 600, 0, 0, '2018-03-01 19:30:09', '2018-03-01 19:30:14', '1', 'Paytm'),
(99, '817c617b55dabe0eecd8', 318, 600, 0, 0, '2018-03-01 19:34:46', '2018-03-01 19:35:32', '1', 'Paytm'),
(100, '17f92653b6c1f853fd71', 318, 500, 0, 0, '2018-03-05 12:40:15', '2018-03-05 12:40:37', '1', 'Paytm'),
(106, 'd609f7342175b385a646', 318, 500, 0, 0, '2018-03-05 12:51:04', '2018-03-05 12:51:19', '1', 'Paytm'),
(107, 'e258ba591e56c0ab0ad7', 318, 105, 0, 0, '2018-03-05 12:53:07', '2018-03-05 12:53:24', '1', 'Paytm'),
(108, '99c69b60c9e817840d10', 318, 500, 0, 0, '2018-03-05 12:55:59', NULL, '0', 'Paytm'),
(109, '85a73967f17310d6bacf', 318, 500, 0, 0, '2018-03-05 14:36:48', '2018-03-05 14:36:57', '1', 'Paytm'),
(110, '064449040e39a0010d2f', 318, 500, 0, 0, '2018-03-05 14:37:23', NULL, '0', 'Paytm'),
(111, 'acc12aeacc256d867bd9', 318, 500, 0, 0, '2018-03-05 14:40:13', NULL, '0', 'Paytm'),
(112, '3cfd97025ed33d78d175', 318, 500, 0, 0, '2018-03-05 14:46:22', NULL, '0', 'Paytm'),
(113, 'd4ce4df72208a5207c27', 318, 500, 0, 0, '2018-03-05 15:04:25', NULL, '0', 'Paytm'),
(114, 'd30d040e5394b457f31f', 318, 500, 0, 0, '2018-03-05 15:07:33', NULL, '0', 'Paytm'),
(115, '20924959878cd8009b0d', 318, 500, 0, 0, '2018-03-05 15:08:22', NULL, '0', 'Paytm'),
(116, '8e797dc2b49ee762d26f', 318, 500, 0, 0, '2018-03-05 15:10:01', NULL, '0', 'Paytm'),
(117, 'af460e70538193447eba', 318, 500, 0, 0, '2018-03-05 15:16:09', NULL, '0', 'Paytm'),
(118, 'a96da101ca4ba44f7493', 318, 500, 0, 0, '2018-03-05 15:16:59', NULL, '0', 'Paytm'),
(119, '92df90156795fe436943', 318, 500, 0, 0, '2018-03-05 15:18:05', NULL, '0', 'Paytm'),
(120, '4642bf0010a78f01fcb3', 318, 500, 0, 0, '2018-03-05 15:18:26', NULL, '0', 'Paytm'),
(121, 'cd9a1478dcb36ad26687', 318, 500, 0, 0, '2018-03-05 15:28:18', NULL, '0', 'Paytm'),
(122, '60d3e3760e21d408d238', 318, 500, 0, 0, '2018-03-05 15:28:49', NULL, '0', 'Paytm'),
(123, 'a6c85244dfe8d2c870ed', 318, 500, 0, 0, '2018-03-05 15:29:17', NULL, '0', 'Paytm'),
(124, 'e627a4c4f7bc051d4f18', 318, 500, 0, 0, '2018-03-05 15:31:12', NULL, '0', 'Paytm'),
(125, '086956d328e3667e6e75', 318, 500, 0, 0, '2018-03-05 15:31:32', NULL, '0', 'Paytm'),
(126, '138dcfd68f31b061f9b9', 318, 500, 0, 0, '2018-03-05 16:01:16', NULL, '0', 'Paytm'),
(127, '380f151dcc74e0929bed', 318, 500, 0, 0, '2018-03-05 16:02:55', NULL, '0', 'Paytm'),
(128, '661b55cf0cc75f9d1110', 318, 500, 0, 0, '2018-03-05 16:03:33', NULL, '0', 'Paytm'),
(129, '881ca050481021d81be4', 318, 500, 0, 0, '2018-03-05 16:11:39', NULL, '0', 'Paytm'),
(130, '82847926d22b15ac58fe', 318, 500, 0, 0, '2018-03-05 16:12:27', NULL, '0', 'Paytm'),
(131, '582f7a92605f18e83a01', 318, 500, 0, 0, '2018-03-05 16:12:59', NULL, '0', 'Paytm'),
(132, 'f7f21cd3d74e64918651', 318, 500, 0, 0, '2018-03-05 16:24:36', NULL, '0', 'Paytm'),
(133, 'd4d417ce815c9437aaf7', 318, 500, 0, 0, '2018-03-05 17:49:58', NULL, '0', 'Paytm'),
(134, '6f399180f61c06aef5ef', 318, 500, 0, 0, '2018-03-05 18:49:25', NULL, '0', 'Paytm'),
(135, '07033e274f1814140592', 318, 500, 0, 0, '2018-03-05 18:50:56', NULL, '0', 'Paytm'),
(136, '60e5a1da1e20928de495', 318, 500, 0, 0, '2018-03-05 18:51:13', NULL, '0', 'Paytm'),
(137, 'be54f89f898970bd0026', 318, 500, 0, 0, '2018-03-05 18:52:17', NULL, '0', 'Paytm'),
(138, 'b003f7744c17e4127bb9', 318, 500, 0, 0, '2018-03-05 18:55:47', NULL, '0', 'Paytm'),
(139, 'fb32d86e40ae65887ca3', 318, 105, 0, 0, '2018-03-05 18:59:58', NULL, '0', 'Paytm'),
(140, 'c6f19bd7943ed1eedcf3', 318, 105, 0, 0, '2018-03-05 19:00:22', NULL, '0', 'Paytm'),
(141, '93c9a707059fe0049baa', 318, 105, 0, 0, '2018-03-05 19:02:57', NULL, '0', 'Paytm'),
(142, '8ce57c9aed847caef3e9', 318, 105, 0, 0, '2018-03-05 19:10:25', NULL, '0', 'Paytm'),
(143, 'be8534f080f4d95b48f2', 318, 105, 0, 0, '2018-03-05 19:27:40', NULL, '0', 'Paytm'),
(144, 'b1bb939847002a5a67d6', 318, 105, 0, 0, '2018-03-05 19:28:03', '2018-03-05 19:37:58', '1', 'Paytm'),
(145, '369a1d99966e5b9709d3', 318, 105, 0, 0, '2018-03-05 19:39:41', '2018-03-05 19:39:49', '1', 'Paytm'),
(146, '96b310b1dc858da4e3bc', 318, 600, 0, 0, '2018-03-05 19:41:47', '2018-03-05 19:41:53', '1', 'Paytm'),
(147, 'db165114bf0463e5735d', 318, 600, 0, 0, '2018-03-05 19:45:43', '2018-03-05 19:45:48', '1', 'Paytm'),
(148, '2de4a75a6741f7f66c1b', 318, 500, 0, 0, '2018-03-05 19:48:11', '2018-03-05 19:49:22', '1', 'Paytm'),
(149, '160e95687f86da27aca7', 318, 500, 0, 0, '2018-03-06 11:15:39', NULL, '0', 'Paytm'),
(150, 'e652a7e678f07b1be154', 318, 500, 0, 0, '2018-03-06 11:19:19', NULL, '0', 'Paytm'),
(151, '48f683fa42a8a14833f1', 318, 500, 0, 0, '2018-03-06 11:19:51', NULL, '0', 'Paytm'),
(152, 'bb7590f36de7d9be4a36', 318, 500, 0, 0, '2018-03-06 11:22:42', '2018-03-06 11:23:07', '1', 'Paytm'),
(153, '1b6c0b0602674044f23e', 318, 1, 0, 0, '2018-03-06 11:23:55', NULL, '0', 'Paytm'),
(154, '4382aa6c5f282ee523a3', 318, 1, 0, 0, '2018-03-06 11:24:07', NULL, '0', 'Paytm'),
(155, '9f4dd69841e80f3c1c1c', 318, 10, 0, 0, '2018-03-06 11:24:24', NULL, '0', 'Paytm'),
(156, 'c3b2df7d6df4982799c8', 318, 100, 0, 0, '2018-03-06 11:24:59', NULL, '0', 'Paytm'),
(157, 'b3f86c8bfa3a5552985a', 318, 100, 0, 0, '2018-03-06 11:25:14', NULL, '0', 'Paytm'),
(158, 'a944769235cbb468d44f', 318, 100, 0, 0, '2018-03-06 11:25:54', NULL, '0', 'Paytm'),
(159, 'a40e5d0edeb428b68ad4', 318, 2, 0, 0, '2018-03-06 11:26:53', NULL, '0', 'Paytm'),
(160, 'c07c61b31f5bfabe4d23', 522, 2, 0, 0, '2018-03-06 11:31:49', NULL, '0', 'Paytm'),
(161, '9ca57bd9d72d1603a143', 522, 2, 0, 0, '2018-03-06 11:47:18', NULL, '0', 'Paytm'),
(162, '9596c06e0b0786863b18', 522, 2, 0, 0, '2018-03-06 11:53:47', NULL, '0', 'Paytm'),
(163, '650ad6514b96bab80934', 318, 2, 0, 0, '2018-03-06 12:12:10', NULL, '0', 'Paytm'),
(164, '8dd548e09bd2667f4b32', 318, 2, 0, 0, '2018-03-06 12:42:34', NULL, '0', 'Paytm'),
(165, '6a18202e8229cb4dc8b2', 318, 2, 0, 0, '2018-03-06 12:48:01', '2018-03-06 12:51:04', '1', 'Paytm'),
(166, 'd03d6aa54ae6b8753164', 318, 2, 0, 0, '2018-03-06 12:53:36', NULL, '0', 'Paytm'),
(167, '6c4f80256dd66c051b60', 318, 2, 0, 0, '2018-03-06 18:46:35', NULL, '0', 'Paytm'),
(168, 'da063bfa89cf465ecfc3', 318, 2, 0, 0, '2018-03-07 16:17:42', NULL, '0', 'Paytm'),
(169, '0ed0d6c3f733da64a460', 318, 2, 0, 0, '2018-03-07 16:20:55', NULL, '0', 'Paytm'),
(170, '3df5047ca2da48a8aef2', 318, 2, 0, 0, '2018-03-07 16:29:33', NULL, '0', 'Paytm'),
(171, '181685dfa6ddfe4cdd8e', 318, 2, 0, 0, '2018-03-07 16:30:42', NULL, '0', 'Paytm'),
(172, 'e0cb8984d20dadbf5d7f', 318, 2, 0, 0, '2018-03-07 16:31:37', '2018-03-07 16:32:11', '1', 'Paytm'),
(173, '6900264309bde2fb8730', 318, 2, 0, 0, '2018-03-07 16:32:38', '2018-03-07 16:32:45', '1', 'Paytm'),
(174, '9507f98f50b8c1d13fe9', 318, 2, 0, 0, '2018-03-07 16:34:10', NULL, '0', 'Paytm'),
(175, '3b4ddf747f4edd414dbf', 318, 2, 0, 0, '2018-03-07 16:35:54', NULL, '0', 'Paytm'),
(176, '497971aa3394d24e37a4', 318, 2, 0, 0, '2018-03-07 16:38:11', NULL, '0', 'Paytm'),
(177, 'e4d918994fea677b9827', 318, 2, 0, 0, '2018-03-07 16:38:35', NULL, '0', 'Paytm'),
(178, '4f22f8d6a7606cc6fa04', 318, 2, 0, 0, '2018-03-07 16:40:37', NULL, '0', 'Paytm'),
(179, '1795bec8ae328ade05f4', 318, 2, 0, 0, '2018-03-07 16:41:31', '2018-03-07 16:43:08', '1', 'Paytm'),
(180, 'c3f1c5a6b050ac0823ab', 318, 105, 0, 0, '2018-03-08 09:00:00', '2018-03-08 09:00:18', '1', 'Paytm'),
(181, 'aa485fe56a3dc479be02', 318, 550, 0, 0, '2018-03-08 14:53:59', '2018-03-08 14:54:04', '1', 'Paytm'),
(182, 'fb1028f7e9d5e4540fcf', 318, 550, 0, 0, '2018-03-09 17:24:23', '2018-03-09 17:24:30', '1', 'Paytm'),
(183, 'a0e425da1c23dac547a5', 318, 222, 0, 0, '2018-03-12 13:00:24', NULL, '0', 'Paytm'),
(184, '3c4978010792f116988f', 318, 222, 0, 0, '2018-03-12 13:01:26', '2018-03-12 13:01:38', '1', 'Paytm'),
(185, '199b1ef0470155ad3dc6', 320, 100, 0, 0, '2018-03-12 14:38:04', NULL, '0', 'Paytm'),
(186, '1a6101fad4fe71909c56', 320, 100, 0, 0, '2018-03-12 14:38:55', NULL, '0', 'Paytm'),
(187, '4a68e9e581ca1967648e', 323, 12, 0, 0, '2018-03-13 14:18:50', NULL, '0', 'Paytm'),
(188, '50b1c231773016171683', 324, 12, 0, 0, '2018-03-13 14:22:29', NULL, '0', 'Paytm'),
(189, 'f0fd7dcbcd9d2f16f690', 325, 99, 0, 0, '2018-03-13 14:25:31', NULL, '0', 'Paytm'),
(190, '4e855372494e71fdf81b', 326, 99, 0, 0, '2018-03-13 14:27:17', NULL, '0', 'Paytm'),
(191, 'dc395f438af19b68f2c0', 327, 99, 0, 12, '2018-03-13 14:35:41', NULL, '0', 'Admin'),
(192, 'ef101f21d307099842fb', 328, 99, 0, 12, '2018-03-13 14:39:55', NULL, '0', 'Admin'),
(193, '0c929c2327c717c89e8f', 329, 99, 0, 12, '2018-03-13 14:51:44', NULL, '0', 'Admin'),
(194, 'c5c7883b66b542a3a49b', 330, 99, 0, 12, '2018-03-13 15:01:44', NULL, '1', 'Admin'),
(195, 'f33351c6114872d72139', 331, 99, 0, 12, '2018-03-13 15:04:58', NULL, '1', 'Admin'),
(196, 'd3150341982e9033df93', 332, 99, 0, 12, '2018-03-13 15:05:59', NULL, '1', 'Admin'),
(197, 'a9cba3ca75d2c41627d8', 333, 99, 0, 12, '2018-03-13 15:06:02', NULL, '1', 'Admin'),
(198, '56b0f20e8c75d4945454', 334, 99, 0, 12, '2018-03-13 15:25:21', NULL, '1', 'Admin'),
(199, '7f524b3722e08e09e4cb', 335, 99, 0, 12, '2018-03-13 15:25:24', NULL, '1', 'Admin'),
(200, '047db046c1d6bdd85ce0', 336, 99, 0, 12, '2018-03-13 15:32:35', NULL, '1', 'Admin'),
(201, 'c389ba340b3bef0543e2', 337, 99, 0, 12, '2018-03-13 15:32:37', NULL, '1', 'Admin'),
(202, 'b400ab4c19911a948f19', 338, 99, 0, 12, '2018-03-13 15:40:41', NULL, '1', 'Admin'),
(203, '180e32746da95a322bb9', 339, 99, 0, 12, '2018-03-13 15:40:44', NULL, '1', 'Admin'),
(204, '45f96ad38b67196de20a', 340, 99, 0, 12, '2018-03-13 15:51:27', NULL, '1', 'Admin'),
(205, '609ebac450d099493dcc', 341, 99, 0, 12, '2018-03-13 15:51:29', NULL, '1', 'Admin'),
(206, '240579e3f012326613e1', 318, 100, 0, 0, '2018-03-14 12:55:31', '2018-03-14 12:55:38', '1', 'Paytm'),
(207, '6723320b01db590ec381', 318, 100, 0, 0, '2018-03-14 15:06:05', '2018-03-14 15:06:20', '1', 'Paytm'),
(208, '42e1c11b66a4ebc4a30e', 318, 100, 0, 0, '2018-03-14 16:01:54', NULL, '0', 'Paytm'),
(209, 'e2d766179eaae48c3867', 318, 100, 0, 0, '2018-03-14 16:02:31', '2018-03-14 16:02:46', '1', 'Paytm'),
(210, 'bc600389628e0f236ed2', 319, 100, 0, 0, '2018-03-14 18:02:07', '2018-03-14 18:02:17', '1', 'Paytm'),
(211, '45c115513105f024d1f3', 318, 100, 0, 0, '2018-03-14 18:09:31', '2018-03-14 18:09:45', '1', 'Paytm'),
(212, '1505c4b61c78c0b9b791', 318, 100, 0, 0, '2018-03-14 19:53:52', '2018-03-14 19:54:07', '1', 'Paytm'),
(213, '4d24dc0bc204a7795b51', 319, 100, 0, 0, '2018-03-15 14:43:04', NULL, '0', 'Paytm'),
(214, 'ebb14c0f2d9f2c56bc51', 318, 731, 0, 0, '2018-03-16 12:14:20', '2018-03-16 12:14:37', '1', 'Paytm'),
(215, 'a44e9cd5f6c9c8d51155', 318, 631, 0, 0, '2018-03-16 12:17:15', '2018-03-16 12:17:21', '1', 'Paytm'),
(216, 'c059568b1386ac97530b', 318, 100, 0, 0, '2018-03-16 12:37:10', '2018-03-16 12:37:16', '1', 'Paytm'),
(217, '3b4da0dbbe62d10347ff', 337, 32, 0, 0, '2018-03-21 11:41:20', '2018-03-21 11:41:39', '1', 'Paytm'),
(218, '5d6fd1dba777207e1fea', 337, 599, 0, 0, '2018-03-21 11:41:56', '2018-03-21 11:42:13', '1', 'Paytm'),
(219, 'f3ee979f6f49d5ef127d', 337, 399, 0, 0, '2018-03-21 11:43:13', '2018-03-21 11:43:18', '1', 'Paytm'),
(220, 'd37b7fb97ce0ea556a65', 318, 32, 0, 0, '2018-03-23 11:05:20', '2018-03-23 11:05:30', '1', 'Paytm'),
(221, '01731253616373100883', 318, 599, 0, 0, '2018-03-23 11:19:22', '2018-03-23 11:19:28', '1', 'Paytm'),
(222, '4529b66794a6174078ae', 318, 599, 0, 0, '2018-03-23 11:20:42', '2018-03-23 11:21:13', '1', 'Paytm'),
(223, '4cda3ab67006fdbb8676', 319, 599, 0, 0, '2018-03-23 15:37:43', '2018-03-23 15:37:50', '1', 'Paytm'),
(224, '21acea49ec193762f348', 336, 599, 0, 0, '2018-03-23 15:39:41', '2018-03-23 15:39:45', '1', 'Paytm'),
(225, '8c10d66f6a0ce391c41b', 336, 599, 0, 0, '2018-03-23 16:16:19', '2018-03-23 16:16:29', '1', 'Paytm'),
(226, 'a4a48991fe5651cd8435', 318, 599, 0, 0, '2018-03-23 16:21:09', '2018-03-23 16:21:12', '1', 'Paytm'),
(227, 'b305f19884e60db3d529', 319, 599, 0, 0, '2018-03-23 17:21:44', NULL, '0', 'Paytm'),
(228, '489084b60a89c9d82535', 319, 599, 0, 0, '2018-03-23 17:21:50', NULL, '0', 'Paytm'),
(229, 'bbdaf012fa8d829a20f5', 319, 599, 0, 0, '2018-03-23 17:21:56', NULL, '0', 'Paytm'),
(230, 'e733c6dd5e6d436dc505', 319, 599, 0, 0, '2018-03-23 17:22:23', NULL, '0', 'Paytm'),
(231, '2124503863771d9d3d68', 319, 599, 0, 0, '2018-03-23 17:23:03', NULL, '0', 'Paytm'),
(232, '4a2faf8acff19b5bac81', 319, 599, 0, 0, '2018-03-23 17:23:06', NULL, '0', 'Paytm'),
(233, '001cb8d4622c32d10563', 319, 631, 0, 0, '2018-03-23 17:24:10', NULL, '0', 'Paytm'),
(234, '701b8a4b65de9f4ceade', 319, 631, 0, 0, '2018-03-23 17:25:55', NULL, '0', 'Paytm'),
(235, '966bf8eb24093c50ac64', 319, 631, 0, 0, '2018-03-23 17:28:38', NULL, '0', 'Paytm'),
(236, 'a0a47ccabc17bbf73610', 319, 631, 0, 0, '2018-03-23 18:04:47', NULL, '0', 'Paytm'),
(237, '738d24318704df8c2fa9', 319, 631, 0, 0, '2018-03-23 18:08:44', NULL, '0', 'Paytm'),
(238, '4d86c7c5ecb7ce512412', 319, 631, 0, 0, '2018-03-23 18:08:49', NULL, '0', 'Paytm'),
(239, '6712671a1057a27f2fe1', 338, 1111, 0, 0, '2018-03-26 11:11:47', NULL, '0', 'Paytm'),
(240, 'dd4e007a946133565ee8', 338, 1510, 0, 0, '2018-03-27 12:19:23', NULL, '0', 'Paytm'),
(241, 'e87aeb86cafc5ac7d054', 319, 12, 0, 0, '2018-03-31 17:46:08', NULL, '0', 'Paytm'),
(242, '9e89caae77cfa158b0e8', 318, 1212, 0, 0, '2018-04-04 13:53:08', NULL, '0', 'Paytm'),
(243, '8c77d6e29f679452983f', 318, 1212, 0, 0, '2018-04-04 13:53:49', NULL, '0', 'Paytm'),
(244, 'b0f5b2023737a8493827', 318, 1212, 0, 0, '2018-04-04 13:56:01', NULL, '0', 'Paytm'),
(245, 'a023f55542f3e4faf6cf', 318, 1212, 0, 0, '2018-04-04 13:57:21', '2018-04-04 13:57:33', '1', 'Paytm'),
(246, '2364a173eb523928e1f0', 319, 1224, 0, 0, '2018-04-05 14:21:04', NULL, '0', 'Paytm'),
(247, 'fc9377ec847cb93f8e12', 319, 1224, 0, 0, '2018-04-05 14:21:27', '2018-04-05 14:22:44', '1', 'Paytm'),
(248, 'd4fca9f31adb63adcd98', 318, 120, 0, 0, '2018-04-09 15:23:00', NULL, '0', 'Paytm'),
(249, '05065ace22b54b2580fa', 318, 120, 0, 0, '2018-04-09 16:02:48', NULL, '0', 'Paytm'),
(250, '9025a44d5c94be4c70f4', 318, 120, 0, 0, '2018-04-09 16:05:52', NULL, '0', 'Paytm'),
(251, 'c24b17900d7516dcc446', 318, 120, 0, 0, '2018-04-09 16:06:34', NULL, '0', 'Paytm'),
(252, '2fa44d8875f43ac84c2d', 318, 120, 0, 0, '2018-04-09 16:09:46', NULL, '0', 'Paytm'),
(253, '8625ae47a8fb27e8defb', 318, 120, 0, 0, '2018-04-09 16:10:52', NULL, '0', 'Paytm'),
(254, '9214b8363da1022a36e8', 318, 120, 0, 0, '2018-04-09 16:15:42', '2018-04-09 16:29:38', '1', 'Paytm'),
(255, '7db9d2a3a8c1508b473d', 319, 200, 0, 0, '2018-04-11 14:43:19', '2018-04-11 14:43:32', '1', 'Paytm'),
(256, 'a1a330e1963ab935f4d7', 318, 100, 0, 0, '2018-04-11 14:58:37', '2018-04-11 14:58:54', '1', 'Paytm'),
(257, 'ec7680a406819f541d44', 319, 2, 0, 0, '2018-04-12 13:21:32', NULL, '0', 'Paytm'),
(258, 'afd6dd93ecf9a104290c', 319, 2, 0, 0, '2018-04-12 14:35:06', NULL, '0', 'Paytm'),
(259, 'ba625a90abb6c4712d00', 318, 1234, 0, 0, '2018-04-12 14:46:00', NULL, '0', 'Paytm'),
(260, 'db8bab3fb311c91e75a6', 318, 1234, 0, 0, '2018-04-12 14:47:04', NULL, '0', 'Paytm'),
(261, '250fe2cc1eea0abda380', 318, 1234, 0, 0, '2018-04-12 14:48:05', NULL, '0', 'Paytm'),
(262, 'e2cfc70e7910fe085009', 318, 1234, 0, 0, '2018-04-12 14:49:51', NULL, '0', 'Paytm'),
(263, 'e4d23e3b6326fde08dc3', 318, 1234, 0, 0, '2018-04-12 14:51:58', NULL, '0', 'Paytm'),
(264, '2c8eafb6de3091d5d51c', 318, 1234, 0, 0, '2018-04-12 14:53:41', NULL, '0', 'Paytm'),
(265, 'e7576262f18106b6136e', 319, 2, 0, 0, '2018-04-12 14:56:51', '2018-04-12 15:02:14', '1', 'Paytm'),
(266, '4acd0c78d9aeaf596fb2', 319, 222, 0, 0, '2018-04-12 15:54:57', NULL, '0', 'Paytm'),
(267, '1afb04f46d71a5d1c164', 319, 222, 0, 0, '2018-04-12 15:55:59', NULL, '0', 'Paytm'),
(268, 'a49ca2f08e4b3117677d', 319, 222, 0, 0, '2018-04-12 15:57:43', NULL, '0', 'Paytm'),
(269, 'b78fe5b376b3d7cb9e36', 319, 222, 0, 0, '2018-04-12 15:58:56', '2018-04-12 15:59:49', '1', 'Paytm'),
(270, 'e5c847212bf7a62618a4', 319, 100, 0, 0, '2018-04-12 16:01:02', '2018-04-12 16:01:11', '1', 'Paytm'),
(271, '6357f571e9c6976c1426', 342, 2, 0, 0, '2018-04-12 16:59:20', NULL, '0', 'Paytm'),
(272, '714f9aeeebe0f7bef2a8', 343, 2, 0, 0, '2018-04-12 16:59:59', '2018-04-12 17:01:26', '1', 'Paytm'),
(273, 'ce1d3e98bc3e9f08838f', 342, 2, 0, 0, '2018-04-12 17:00:02', NULL, '0', 'Paytm'),
(274, '905e95934ca2ea2c01d4', 344, 2, 0, 0, '2018-04-12 17:00:03', '2018-04-12 17:01:28', '1', 'Paytm'),
(275, '008bdc2df9221886fa84', 349, 2, 0, 0, '2018-04-12 17:02:35', '2018-04-12 17:02:46', '1', 'Paytm'),
(276, '8bee4fdef61cd7324097', 347, 2, 0, 0, '2018-04-12 17:03:58', '2018-04-12 17:05:09', '1', 'Paytm'),
(277, '948d41cb5803a0dc9821', 342, 2, 0, 0, '2018-04-12 17:05:05', NULL, '0', 'Paytm'),
(278, '79a1447818d33b8765cb', 346, 2, 0, 0, '2018-04-12 17:06:13', '2018-04-12 17:08:04', '1', 'Paytm'),
(279, '1156504fc8ea12d2ae73', 342, 2, 0, 0, '2018-04-12 17:06:19', '2018-04-12 17:07:34', '1', 'Paytm'),
(280, 'cb4087dd3f54abef0b9d', 351, 2, 0, 0, '2018-04-12 17:07:25', '2018-04-12 17:08:23', '1', 'Paytm'),
(281, '1eb19913ce8bc09d1050', 352, 2, 0, 0, '2018-04-12 17:07:49', NULL, '0', 'Paytm'),
(282, 'c9d84e73c83adb94d8f9', 352, 2, 0, 0, '2018-04-12 17:09:00', '2018-04-12 17:09:17', '1', 'Paytm'),
(283, 'd3742919bcf35d13b507', 348, 2, 0, 0, '2018-04-12 17:11:25', '2018-04-12 17:12:27', '1', 'Paytm'),
(284, 'f2348bf61f9b658aca1c', 348, 1234, 0, 0, '2018-04-12 17:17:47', NULL, '0', 'Paytm'),
(285, 'd74d7bb27bf32626002c', 318, 2, 0, 0, '2018-04-12 18:44:55', '2018-04-12 18:45:09', '1', 'Paytm'),
(286, '7a09f7d72d0d01d2706d', 318, 444, 0, 0, '2018-04-13 10:47:42', NULL, '0', 'Paytm'),
(287, '41956dd4023085c130e3', 318, 444, 0, 0, '2018-04-13 10:49:24', '2018-04-13 10:51:01', '1', 'Paytm'),
(288, '3b063fcc7be98726d390', 318, 1266, 0, 0, '2018-04-13 10:59:15', '2018-04-13 11:00:06', '1', 'Paytm'),
(289, '00630537e42d48d4cfa0', 319, 10, 0, 0, '2018-04-13 11:14:04', '2018-04-13 11:14:18', '1', 'Paytm'),
(290, 'e0e52b7fb6c6224cd731', 318, 21, 0, 0, '2018-04-13 12:46:04', '2018-04-13 12:46:12', '1', 'Paytm'),
(291, '31c6150f531c0867146f', 319, 10, 0, 0, '2018-04-13 13:30:38', '2018-04-13 13:30:49', '1', 'Paytm'),
(292, '9f6cf33795a6f014180b', 319, 11, 0, 0, '2018-04-13 13:31:36', NULL, '0', 'Paytm'),
(293, 'b96124af5253d4aab33c', 319, 11, 0, 0, '2018-04-13 13:33:23', '2018-04-13 13:33:34', '1', 'Paytm'),
(294, '237485cba04e4137019b', 353, 10, 0, 0, '2018-04-13 17:01:37', NULL, '0', 'Paytm'),
(295, 'e992db0c62660a657b18', 353, 10, 0, 0, '2018-04-13 17:02:03', '2018-04-13 17:02:07', '1', 'Paytm'),
(296, 'eb29fee71a7f54f27144', 353, 10, 0, 0, '2018-04-13 17:15:28', '2018-04-13 17:15:38', '1', 'Paytm'),
(297, 'a5a6272c892e8193dd52', 354, 0, 0, 11, '2018-04-13 17:58:41', NULL, '1', 'Admin');

-- --------------------------------------------------------

--
-- Table structure for table `vid_studfeedback`
--

CREATE TABLE IF NOT EXISTS `vid_studfeedback` (
`sf_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `contact` int(11) NOT NULL,
  `message` text NOT NULL,
  `city` varchar(50) DEFAULT NULL,
  `feedback_date` date NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `vid_studfeedback`
--

INSERT INTO `vid_studfeedback` (`sf_id`, `name`, `email`, `contact`, `message`, `city`, `feedback_date`, `status`) VALUES
(1, 'asdf', 'asdf@gasd.sdf', 2147483647, 'asdf', 'asdfasdf', '2018-02-27', '0'),
(3, 'pratik pawar', 'pratik@gmail.cco', 2147483647, 'This is testing mail for feedback check', 'asdf', '2018-03-07', '1'),
(4, 'Pratik Pawar', 'pratikwe@gmail.com', 2147483647, 'Great forum for aspirants like me who seek a flexible schedule and up to date material. The live tests will gave us an actual test experience and helped us fight nervous breakdown. And the All India Ranking will helped to gauge position in the competition. Overall,', NULL, '2018-03-31', '1'),
(5, 'Pramod Deore', 'pramoddeore@gmail.com', 2147483647, 'This great website comes with topic-wise examples and sample tests for every topic. The solved examples are the best to understand tricky concepts. This will surely be a great tool for IIT aspirants for recent exam.Best of Luck to All..!!', NULL, '2018-03-31', '1'),
(6, 'Sachin Gaherwar', 'sachin@gmail.com', 2147483647, 'Mockexam.vidyarthimitra.org will be a great way to know and resolve your common mistakes. I personally feel it is a great step towards helping students like me who aim to be in the IITs.I really appreciate effort to provide the online education for all categories of exams.', NULL, '2018-03-31', '1'),
(7, 'Aniket Salve', 'aniket@gmail.com', 2147483647, 'As an author & editor, vidyarthimitra giving an opportunity to student to Learn & earn knowledge simultaneously. vidyarthimitra is doing a great job for students to gain confidence before actual exam by providing them with large number of Mocktests.', NULL, '2018-03-31', '1'),
(8, 'Mohanish Upse', 'asdf@sf.ddfg', 2147483647, 'this is new testimonial for testing.', NULL, '2018-03-31', '1'),
(9, 'Monty', 'monty@gmail.com', 2147483647, 'The classic “Lorem ipsum dolor sit amet…” passage is attributed to a remixing of the Roman philosopher Cicero''s 45 BC text De Finibus Bonorum et Malorum (“On the Extremes of Good and Evil”). More specifically', NULL, '2018-03-31', '1'),
(10, 'eggtr', 'trgtrgtr@ftrg.yjh', 2147483647, 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry''s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the', NULL, '2018-03-31', '1'),
(11, 'sachin new message', 'asdfasd@sdf.sdf', 2147483647, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillu', NULL, '2018-03-31', '1');

-- --------------------------------------------------------

--
-- Table structure for table `vid_subject`
--

CREATE TABLE IF NOT EXISTS `vid_subject` (
`subject_id` int(11) NOT NULL,
  `level_id` int(11) DEFAULT NULL,
  `author_id` int(11) NOT NULL,
  `subject_name` varchar(100) NOT NULL,
  `subject_description` varchar(300) DEFAULT NULL,
  `weightage` float DEFAULT NULL,
  `submitdate` datetime NOT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1' COMMENT '0-inactive,1-active',
  `course_id` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `vid_subject`
--

INSERT INTO `vid_subject` (`subject_id`, `level_id`, `author_id`, `subject_name`, `subject_description`, `weightage`, `submitdate`, `active`, `course_id`) VALUES
(1, 0, 123456789, 'Physics', 'Phyasdfasdfasf asdfasdf asdfasdfasdf asdfasdf asas asdf asf', NULL, '2017-11-30 15:45:45', '1', 11),
(2, 0, 123456789, 'Chemistry', 'chem', NULL, '2017-11-30 15:45:09', '1', 11),
(3, 0, 123456789, 'Math', 'm', NULL, '2017-11-30 15:45:21', '1', 11),
(4, 0, 123456789, 'Biology', 'bio', NULL, '2017-11-30 15:45:29', '1', 11),
(5, 0, 123456789, 'Physics I', 'asda', NULL, '2017-12-11 18:24:06', '0', 14),
(6, 0, 123456789, 'chemistry I', 'sdf', NULL, '2017-12-11 18:24:15', '0', 14),
(7, 0, 123456789, 'fvd', 'dfdf', NULL, '2017-12-14 12:35:58', '1', 15),
(8, 0, 123456789, 'Physics Demo chapter 1', 'Physics Demo chapter 1 Desscription', NULL, '2017-12-25 17:30:05', '1', 12),
(9, 0, 123456789, 'sample 2', 'sample 2', NULL, '2017-12-25 17:30:21', '1', 12),
(10, 0, 123456789, 'sample 3', 'sample 3', NULL, '2017-12-25 17:30:31', '1', 12),
(11, 0, 2, 'sub 1', 'sub 1 deexcsdf', NULL, '2018-03-08 17:00:01', '1', 14),
(12, 0, 2, 'sub 2', 'asdf', NULL, '2018-03-08 17:00:07', '1', 14),
(13, 0, 2, 'sub 3', 'asdfasf', NULL, '2018-03-08 17:00:16', '1', 14),
(14, 0, 123456789, 'Subjectsdss', 'sdsdssds', NULL, '2018-03-12 16:25:41', '0', 17),
(15, 0, 123456789, 'secsdsd', 'sdsvbcvb hfghf g f gh fgh', NULL, '2018-03-12 16:26:08', '1', 17),
(16, 0, 2, 'df', 'df', NULL, '2018-03-31 15:19:57', '1', 18),
(17, 0, 123456789, '145645', '45645645645646456464562456', NULL, '2018-03-31 17:15:31', '1', 13);

-- --------------------------------------------------------

--
-- Table structure for table `vid_subject_group`
--

CREATE TABLE IF NOT EXISTS `vid_subject_group` (
`subject_group_id` int(11) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL,
  `subject_group_name` varchar(100) NOT NULL,
  `submitdate` datetime NOT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1' COMMENT '0-inactive,1-active',
  `updatedate` datetime DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `vid_subject_group`
--

INSERT INTO `vid_subject_group` (`subject_group_id`, `course_id`, `author_id`, `subject_group_name`, `submitdate`, `active`, `updatedate`) VALUES
(1, 11, 123456789, 'PCM', '2017-11-30 15:46:13', '1', '2018-03-22 11:37:14'),
(2, 11, 123456789, 'PCB', '2017-11-30 15:46:23', '1', '2018-03-21 14:47:12'),
(3, 11, 123456789, 'PCMB', '2017-11-30 15:46:37', '1', NULL),
(4, 14, 123456789, 'PC', '2017-12-11 18:24:55', '0', NULL),
(5, 11, 123456789, 'drd', '2017-12-14 12:39:39', '0', NULL),
(6, 12, 123456789, 'sample Aieee 1', '2017-12-25 17:42:56', '1', NULL),
(7, 14, 2, 'temp sub group', '2018-03-08 17:11:37', '1', NULL),
(8, 14, 2, 'PCM  (JEE 2018)', '2018-03-09 17:16:10', '1', NULL),
(9, 15, 123456789, 'PCSMD', '2018-03-12 17:21:10', '1', '2018-03-12 17:21:23'),
(10, 12, 123456789, 'pcm', '2018-03-12 17:23:07', '1', NULL),
(11, 12, NULL, 'dd', '2018-03-20 18:10:15', '1', '2018-03-20 18:11:13'),
(12, 15, NULL, 'cvxcx', '2018-03-20 19:11:03', '0', '2018-03-20 19:51:03'),
(13, 11, NULL, 'asdf', '2018-03-30 14:13:36', '1', NULL),
(14, 11, NULL, 'asdf', '2018-03-30 14:13:44', '1', NULL),
(15, 14, NULL, 'SDDSD', '2018-03-31 16:57:46', '1', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vid_subject_group_sub`
--

CREATE TABLE IF NOT EXISTS `vid_subject_group_sub` (
`sub_group_sub_id` int(11) NOT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `sub_group_id` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=151 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `vid_subject_group_sub`
--

INSERT INTO `vid_subject_group_sub` (`sub_group_sub_id`, `subject_id`, `sub_group_id`) VALUES
(7, 1, 3),
(8, 2, 3),
(9, 3, 3),
(10, 4, 3),
(11, 5, 4),
(12, 6, 4),
(13, 1, 5),
(14, 2, 5),
(15, 3, 5),
(16, 4, 5),
(17, 8, 6),
(18, 11, 7),
(19, 12, 7),
(20, 13, 7),
(21, 11, 8),
(22, 12, 8),
(23, 13, 8),
(26, 7, 9),
(27, 8, 10),
(28, 9, 10),
(29, 10, 10),
(84, 1, 11),
(85, 2, 11),
(86, 4, 11),
(132, 7, 12),
(136, 1, 2),
(137, 2, 2),
(138, 4, 2),
(144, 3, 1),
(145, 1, 1),
(146, 2, 1),
(147, 1, 13),
(148, 2, 13),
(149, 2, 14),
(150, 11, 15);

-- --------------------------------------------------------

--
-- Table structure for table `vid_subscriber`
--

CREATE TABLE IF NOT EXISTS `vid_subscriber` (
`subscriber_id` int(11) NOT NULL,
  `subscriber_contact` bigint(20) NOT NULL,
  `subscriber_email` varchar(100) NOT NULL,
  `subscribe_date` date NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `vid_subscriber`
--

INSERT INTO `vid_subscriber` (`subscriber_id`, `subscriber_contact`, `subscriber_email`, `subscribe_date`) VALUES
(1, 9637960396, 'pratik@gmail.com', '2018-04-19'),
(2, 9226124695, 'pratikpawar@gmail.com', '2018-04-19'),
(3, 0, 'emailsdsasadassa', '2018-04-19'),
(4, 0, 'emailsdsasadassa', '2018-04-19'),
(5, 0, 'emailsdsasadassa', '2018-04-19'),
(6, 0, 'emailsdsasadassa', '2018-04-19'),
(7, 0, 'emailsdsasadassa', '2018-04-19'),
(8, 0, 'emailsdsasadassa', '2018-04-19'),
(9, 0, 'emailsdsasadassa', '2018-04-19'),
(10, 0, 'emailsdsasadassa', '2018-04-19'),
(11, 0, 'emailsdsasadassa', '2018-04-19'),
(12, 0, 'emailsdsasadassa', '2018-04-19'),
(13, 0, 'emailsdsasadassa', '2018-04-19'),
(14, 0, 'emailsdsasadassa', '2018-04-19'),
(15, 0, 'emailsdsasadassa', '2018-04-19'),
(16, 0, 'emailsdsasadassa', '2018-04-19'),
(17, 0, 'emailsdsasadassa', '2018-04-19'),
(18, 0, 'emailsdsasadassa', '2018-04-19'),
(19, 0, 'emailsdsasadassa', '2018-04-19'),
(20, 0, 'emailsdsasadassa', '2018-04-19'),
(21, 0, 'emailsdsasadassa', '2018-04-19'),
(22, 0, 'emailsdsasadassa', '2018-04-19'),
(23, 0, 'emailsdsasadassa', '2018-04-19'),
(24, 0, 'emailsdsasadassa', '2018-04-19'),
(25, 0, 'emailsdsasadassa', '2018-04-19'),
(26, 0, 'emailsdsasadassa', '2018-04-19'),
(27, 0, 'emailsdsasadassa', '2018-04-19'),
(28, 0, 'emailsdsasadassa', '2018-04-19'),
(29, 0, 'emailsdsasadassa', '2018-04-19'),
(30, 0, 'emailsdsasadassa', '2018-04-19'),
(31, 0, 'emailsdsasadassa', '2018-04-19'),
(32, 0, 'emailsdsasadassa', '2018-04-19'),
(33, 0, 'emailsdsasadassa', '2018-04-19'),
(34, 0, 'emailsdsasadassa', '2018-04-19'),
(35, 0, 'emailsdsasadassa', '2018-04-19'),
(36, 0, 'emailsdsasadassa', '2018-04-19'),
(37, 0, 'emailsdsasadassa', '2018-04-19'),
(38, 0, 'emailsdsasadassa', '2018-04-19'),
(39, 0, 'emailsdsasadassa', '2018-04-19'),
(40, 0, 'emailsdsasadassa', '2018-04-19'),
(41, 0, 'emailsdsasadassa', '2018-04-19'),
(42, 0, 'emailsdsasadassa', '2018-04-19'),
(43, 0, 'emailsdsasadassa', '2018-04-19'),
(44, 0, 'emailsdsasadassa', '2018-04-20'),
(45, 0, 'emailsdsasadassa', '2018-04-20'),
(46, 0, 'emailsdsasadassa', '2018-04-20'),
(47, 0, 'emailsdsasadassa', '2018-04-20'),
(48, 0, 'emailsdsasadassa', '2018-04-20'),
(49, 0, 'emailsdsasadassa', '2018-04-20'),
(50, 0, 'emailsdsasadassa', '2018-04-20'),
(51, 0, 'emailsdsasadassa', '2018-04-20'),
(52, 0, 'emailsdsasadassa', '2018-04-20'),
(53, 0, 'emailsdsasadassa', '2018-04-20'),
(54, 0, 'emailsdsasadassa', '2018-04-20');

-- --------------------------------------------------------

--
-- Table structure for table `vid_usersession`
--

CREATE TABLE IF NOT EXISTS `vid_usersession` (
  `usersessionid` varchar(50) NOT NULL,
  `userid` int(11) NOT NULL,
  `usertype` tinyint(1) NOT NULL,
  `createddate` date NOT NULL,
  `logoutdate` datetime DEFAULT NULL,
  `login_flag` enum('0','1') NOT NULL COMMENT '0-logout,1-login'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `vid_usersession`
--

INSERT INTO `vid_usersession` (`usersessionid`, `userid`, `usertype`, `createddate`, `logoutdate`, `login_flag`) VALUES
('fa66b0106f53ad701ed33efb761906d2', 123456789, 1, '2017-03-30', NULL, '0'),
('a628d7899c7d88efc96174298ef034f6', 155, 3, '2017-03-31', NULL, '0'),
('14c84a60472bf5ef6f4333b0fe64895d', 156, 3, '2017-04-03', NULL, '0'),
('a6e421d8c308a9787136d98d9bf0ac7c', 157, 3, '2017-04-04', NULL, '0'),
('e97f00e91b832da5d7080782c4f53bee', 158, 3, '2017-04-04', NULL, '0'),
('0fa0993563c6fdd2da2621facebab612', 159, 3, '2017-04-05', NULL, '0'),
('b4a72fefa3950bd22e2e86bfd3da423d', 160, 3, '2017-04-11', NULL, '0'),
('64f17f291a32e54ccf20a7efc05fe83c', 161, 3, '2017-04-11', NULL, '1'),
('e8560f9c08438f292ad7855fbbcb1671', 162, 3, '2017-05-12', NULL, '0'),
('20eb7cf322650fd263fc5a8296671e97', 163, 3, '2017-05-15', NULL, '0'),
('0592b32afee73192e666d40cdd48e48d', 164, 3, '2017-05-21', NULL, '0'),
('c6eedfbf612aced717f4cf4b57fc6496', 165, 3, '2017-05-22', NULL, '0'),
('361b207c1252a5dfc9bbfa57ea354e15', 166, 3, '2017-05-22', NULL, '0'),
('90ef106c92dfcc1335aeb980584e7cc6', 167, 3, '2017-06-03', NULL, '0'),
('17d2ed71ae8ab93be2af361ea4bc631b', 168, 3, '2017-06-17', NULL, '1'),
('124fff02a5cdda4509a5851e0de5e011', 169, 3, '2017-06-20', NULL, '0'),
('c06c0c23fbe44acb5a5858669d344abf', 170, 3, '2017-07-20', NULL, '0'),
('5eb8ee1366eb10844de27b8a9f8e4815', 171, 3, '2017-07-20', NULL, '1'),
('89fb92579784648cb9ba1aec77d5118e', 172, 3, '2017-07-20', NULL, '1'),
('99c93fc8578ece05230564e114325dd8', 173, 3, '2017-07-25', NULL, '1'),
('ce9c6be940bac6a659438000e3c03d15', 174, 3, '2017-07-25', NULL, '1'),
('4b4f294c92064a6c5c500e66ed19285d', 175, 3, '2017-07-25', NULL, '0'),
('3d3b4089574715b1b26f29e3d6c9906c', 176, 3, '2017-07-25', NULL, '0'),
('fef944dbc27845728c7d27b2d7503067', 177, 3, '2017-07-25', NULL, '1'),
('9e37c40aee191a7da49861cd5838b4bf', 178, 3, '2017-07-28', NULL, '0'),
('b1afc9416bcfb363d3cb5829d0820b19', 179, 3, '2017-07-28', NULL, '1'),
('0be55f408541bcd5fdc93f8b9f14d53a', 180, 3, '2017-07-28', NULL, '0'),
('7a7f7ff5cfe2441241081792a048a7d3', 181, 3, '2017-07-28', NULL, '1'),
('4dab8918657f5286a0b9fc85103f49bd', 182, 3, '2017-07-28', NULL, '0'),
('5081af09da34ecd3c4b190a84a4b8925', 183, 3, '2017-07-28', NULL, '1'),
('3e9256e11675da5047c4b05d478d2e67', 184, 3, '2017-07-28', NULL, '1'),
('a5b3aab4f497a84f16879455c6a8052f', 185, 3, '2017-07-28', NULL, '0'),
('4ba2ad7d3a651eefa5a009bd607b383e', 186, 3, '2017-07-28', NULL, '0'),
('e7c254ecd5f1a48981fbc74d22974958', 187, 3, '2017-07-28', NULL, '0'),
('6fdbfd6d4ebedb64355f887b6ec8a9be', 188, 3, '2017-07-28', NULL, '0'),
('2a9c8e0e1faca1bcd3f08fc2c2a74bcd', 189, 3, '2017-07-28', NULL, '1'),
('60cd9abebf93a6163e005b84f6174c47', 190, 3, '2017-07-29', NULL, '1'),
('75ed92f18e87334510acf3445b20a80b', 191, 3, '2017-07-31', NULL, '0'),
('9e2d2f84f8543f590db41567b3fac432', 192, 3, '2017-07-31', NULL, '0'),
('4d8691bf412f0b310235a04271266555', 193, 3, '2017-07-31', NULL, '0'),
('08a9f2a00a00e6c519d9ad3448a50787', 194, 3, '2017-07-31', NULL, '0'),
('bd695ac28e8cf7ad14caf1147b265c30', 195, 3, '2017-07-31', NULL, '0'),
('f993028fd0df63fd967a9ece8c5f46b2', 196, 3, '2017-08-01', NULL, '0'),
('41e48a3ab1502e528ef1da6d3cbf5a8d', 197, 3, '2017-08-01', NULL, '0'),
('e8ccfb278f1f17fece7a40ccf8f2c31c', 198, 3, '2017-08-01', NULL, '0'),
('5aa84a55201119d73f761adf36f742e8', 199, 3, '2017-08-01', NULL, '1'),
('451cb716056cdf3f54de66d2f8cef173', 200, 3, '2017-08-02', NULL, '0'),
('342b662241d4ffc2e3b879cdfa4351b7', 201, 3, '2017-08-02', NULL, '1'),
('9fbbc1dd7348c29490e75e587383792f', 202, 3, '2017-08-02', NULL, '0'),
('ee36fe38fd41da0ac4e6d16a3025b16d', 203, 3, '2017-08-08', NULL, '0'),
('dec3fd65a077554d6e819a690048792c', 204, 3, '2017-08-08', NULL, '1'),
('ee0853876787f18781acb68a2f4055b1', 205, 3, '2017-08-08', NULL, '1'),
('c4e3b62f138bcfd4faa87d8d073ac008', 206, 3, '2017-08-09', NULL, '0'),
('35634e51cd460cc8bb5ea584becde206', 207, 3, '2017-08-10', NULL, '0'),
('741269c4dd31575a32a91da4ebda4c98', 208, 3, '2017-08-10', NULL, '1'),
('9c87168fbfcfa6339661e9e572fe7d65', 209, 3, '2017-08-11', NULL, '0'),
('999e89bb2907a23ef695c988d324b063', 210, 3, '2017-08-16', NULL, '0'),
('b227ef3239f9bcfec96829df4569bfa5', 211, 3, '2017-08-18', NULL, '1'),
('45c9258dea9ad8f3f9bfde7d9a19202f', 212, 3, '2017-08-22', NULL, '0'),
('5a5bd56b8d6b04d9e08f3e058becdd8e', 213, 3, '2017-08-22', NULL, '0'),
('c36eb163c2c7b43e44b489c85cc8de69', 214, 3, '2017-08-22', NULL, '0'),
('288b40d7586c1c46970fc096fa117e9d', 215, 3, '2017-08-23', NULL, '1'),
('b2baf7a87104f0aa6ab57bab7f900e23', 216, 3, '2017-08-23', NULL, '0'),
('297e0d168a55db22f6c981c4a8bcc7a6', 217, 3, '2017-08-24', NULL, '0'),
('3a1d068acf2fa7ae612e1a97dfd4b2c2', 218, 3, '2017-08-28', NULL, '0'),
('e7184a45679099849f23420c1e28eea9', 219, 3, '2017-08-28', NULL, '0'),
('94a6ac34d55418bf3f0b7792b21219c7', 220, 3, '2017-08-28', NULL, '0'),
('7faa918c42e94fee38d4a84d0f8d7393', 221, 3, '2017-08-28', NULL, '0'),
('b4176c63d16ad256a98ff14c8d831c48', 222, 3, '2017-08-31', NULL, '0'),
('b5adfac54ed0a0f6af8c2e09022ca6e9', 223, 3, '2017-08-31', NULL, '0'),
('5f34ed43c225131832c39ce4bb66458a', 224, 3, '2017-09-01', NULL, '0'),
('00caa858e5a7c0e97be0e7cf30e10d3c', 225, 3, '2017-09-06', NULL, '0'),
('937dbdcaf8201d292c4b799ba62705a9', 226, 3, '2017-09-06', NULL, '1'),
('5bdf6041a0d95175c658a30af5df8a2f', 227, 3, '2017-09-06', NULL, '0'),
('c4d6d696bde1b3c74222ec7bb667f271', 228, 3, '2017-09-09', NULL, '0'),
('519695312ce869e14e270e2e8b0a09af', 229, 3, '2017-09-13', NULL, '0'),
('519959f790173393beb90a30d4196b02', 230, 3, '2017-09-16', NULL, '0'),
('6db5364e338c38af71e2897ccf20db43', 231, 3, '2017-09-18', NULL, '0'),
('566eec48a0e7a4a8e6f798a8a40c9623', 232, 3, '2017-10-05', NULL, '0'),
('ca8d7134e11b02b77e91b550ab83b74b', 233, 3, '2017-10-09', NULL, '1'),
('f72359fadf9dc5f3659c8c88fd5a5674', 234, 3, '2017-10-09', NULL, '0'),
('62832526cc66d3eac0047a960783eaac', 235, 3, '2017-10-10', NULL, '1'),
('53e6b29a4868d755e57ddb7006488ca8', 236, 3, '2017-10-14', NULL, '0'),
('c03b4c4830b3424fa219da92538ff44c', 237, 3, '2017-10-14', NULL, '0'),
('f681536779da327988ff6a29850df5d8', 238, 3, '2017-10-14', NULL, '1'),
('5640250c825e94f7b2d1dd5f102c4133', 239, 3, '2017-10-16', NULL, '1'),
('40d50e3ee0882ca3dd40aa4891c08473', 240, 3, '2017-10-16', NULL, '0'),
('ee63ab388c29ff04713ac5d6175ea798', 241, 3, '2017-10-17', NULL, '0'),
('4154e04c1afabbe65778c1ca496ec239', 242, 3, '2017-10-24', NULL, '0'),
('407b00d9f1d961cf00afda976fecdb5e', 243, 3, '2017-10-24', NULL, '0'),
('a5bde8d2234d5770caf5f4c825705284', 244, 3, '2017-10-24', NULL, '0'),
('6ed21008fa977b406b0ce4bae73072d9', 249, 3, '2017-10-24', NULL, '0'),
('77145e94ea955eea10f5ed5e67a9503f', 250, 3, '2017-10-24', NULL, '0'),
('07f6ea29ed549de1d818e1730f9f1c99', 251, 3, '2017-10-24', NULL, '0'),
('79d6291e5001be7d5e4d67b335fec1f3', 252, 3, '2017-10-24', NULL, '0'),
('3971e5f95ac219c7c8ef26a5810fa021', 254, 3, '2017-10-24', NULL, '0'),
('52c3aa25291694a2da29015e9eed336b', 255, 3, '2017-10-24', NULL, '0'),
('2534e2382cc7c196738dce85f6900783', 256, 3, '2017-10-24', NULL, '0'),
('7d78183280e32f23448e905b78f29809', 257, 3, '2017-10-24', NULL, '0'),
('1d01895fff311e129df7fb8a61263031', 258, 3, '2017-10-24', NULL, '1'),
('302cf62c409ec29c46b2801225e27fe9', 259, 3, '2017-10-25', NULL, '0'),
('2ddd71758bd4e0d570796e2500fe6bb3', 260, 3, '2017-10-25', NULL, '0'),
('3a8a827bce6d6db238c375abd626e279', 261, 3, '2017-10-25', NULL, '0'),
('8d6921c28fcce1718c74c6c1fcc25b95', 262, 3, '2017-10-27', NULL, '0'),
('443eff627557641e212e3e91b48993d0', 263, 3, '2017-11-01', NULL, '0'),
('67962ad52910251d6b878773c47ef950', 264, 3, '2017-11-02', NULL, '0'),
('7d0c89830730c4ab34e57b5031f88f34', 265, 3, '2017-11-02', NULL, '0'),
('1628c77e0c93837184e2c8f7dacddcc8', 266, 3, '2017-11-03', NULL, '0'),
('c2dd207354e238bcfb6d26c5a924fe1a', 267, 3, '2017-11-03', NULL, '0'),
('5dbec415e3aeefa508c8ba840468c076', 268, 3, '2017-11-03', NULL, '1'),
('e97d21b14a5e7dc48cc899a9b74abf9e', 269, 3, '2017-11-03', NULL, '1'),
('061f96bc6b2d2beac7de304d1719056f', 270, 3, '2017-11-03', NULL, '1'),
('5bda5aa0eeb39eadd8da1a0867c521d3', 271, 3, '2017-11-03', NULL, '0'),
('e8e7e1b98d259c95edfa08e52f29b6ef', 272, 3, '2017-11-03', NULL, '0'),
('66e26694cf308028f085c909401e2be7', 273, 3, '2017-11-03', NULL, '0'),
('6a5f2eff3f27a66af405a85fdc179adc', 274, 3, '2017-11-03', NULL, '1'),
('259063bd384ba2ebc5e0971cf270b37e', 275, 3, '2017-11-03', NULL, '1'),
('aba09467cf8fe287070cfc56a062b0c5', 276, 3, '2017-11-03', NULL, '0'),
('c8ec229918b7fa7d827cfc6b769b9a2b', 277, 3, '2017-11-03', NULL, '0'),
('0cfa58174b4962405ac32f70c42a0e0e', 278, 3, '2017-11-03', NULL, '0'),
('23c77c8ab55675a1a5f552987cb7b5de', 279, 3, '2017-11-03', NULL, '0'),
('28c5c67ca595be7a991c88d92b5fcd82', 280, 3, '2017-11-03', NULL, '0'),
('bea91314816b36528b1d5c621aa973fd', 281, 3, '2017-11-07', NULL, '0'),
('1d2b6d5fb642a529a952a9962472d5cd', 282, 3, '2017-11-07', NULL, '0'),
('231d48825a43fe126c7d235f7d53d54e', 283, 3, '2017-11-07', NULL, '0'),
('fcf3e2e10925c6f87966134cc56bcb82', 284, 3, '2017-11-08', NULL, '1'),
('6f40c985b645603b5eea093643994b36', 285, 3, '2017-11-09', NULL, '0'),
('76fe0f8d7e1b9e833c4ddd3f74141ae3', 286, 3, '2017-11-09', NULL, '0'),
('125c391a5e9598b35f43a25029425f13', 287, 3, '2017-11-09', NULL, '0'),
('57e3bb6f447942bffb6852e0dfb5e7fd', 288, 3, '2017-11-09', NULL, '1'),
('bef2b79a7ec635541ea5d5511b341f22', 314, 3, '2017-11-23', NULL, '0'),
('afcf46b31b3e0a0d51441b5cca8f07da', 315, 3, '2017-11-23', NULL, '0'),
('247ee6c210ebf298d55e17c39cc4e0e9', 316, 3, '2017-11-23', NULL, '1'),
('1a6524d29fd27f2315d179e92ed77431', 317, 3, '2017-11-23', NULL, '1'),
('39ecc488243106d0dd0b6a48cc26735e', 318, 3, '2017-12-08', NULL, '0'),
('5d23cd2f93500a838089a5efa4e66685', 319, 3, '2017-12-14', NULL, '0'),
('64108950d681031d4040a5cddcebd0ca', 320, 3, '2017-12-14', NULL, '0'),
('ab98763513ac18e97d667acfec5e3c30', 321, 3, '2017-12-14', NULL, '0'),
('fe9a6e6f1c560aa3d43d151ecf20ac8f', 322, 3, '2017-12-14', NULL, '0'),
('98903c2d511b09ad228ba34473a8f1dc', 323, 3, '2017-12-14', NULL, '0'),
('02c1d00f6e65f5da8643ff1845f24add', 324, 3, '2017-12-21', NULL, '0'),
('686100574527ececf666073ef448ba05', 325, 3, '2018-01-03', NULL, '0'),
('0e4bf77c823624fe2d25bfc2340d517e', 326, 3, '2018-01-03', NULL, '0'),
('ceeffa0e06f5d825e7fcc9a227d900d0', 327, 3, '2018-01-03', NULL, '0'),
('73d3669c9e3b700525b1f08bd4cc0232', 328, 3, '2018-01-03', NULL, '0'),
('212ceefb0eb80b83aab84cab2600fcda', 329, 3, '2018-01-03', NULL, '0'),
('c5a40321934b9b505a2269bcabe0cbc6', 330, 3, '2018-01-03', NULL, '0'),
('c5a40321934b9b505a2269bcabe0cbc6', 330, 3, '2018-01-08', NULL, '0'),
('68c7c39ccf1a58800392747b35485812', 331, 3, '2018-01-08', NULL, '0'),
('faa8326c2951948c4936eee8296ce16e', 351, 3, '2018-02-10', NULL, '0'),
('8f4b243ad309a4feb93d3248d91ef7dd', 367, 3, '2018-02-14', NULL, '0'),
('51d71580a7052b170c2e8d74e93a2594', 368, 3, '2018-02-22', NULL, '0'),
('1e9562c13862921efb041286f38d908d', 369, 3, '2018-02-22', NULL, '0'),
('44a31f2a6ee27b8b45d297eb840eca15', 370, 3, '2018-02-22', NULL, '0'),
('36690e969e5d52f10125f65c9891463f', 371, 3, '2018-02-22', NULL, '0'),
('6df3f7546b68268cf8cf36ccde3ec93e', 372, 3, '2018-02-22', NULL, '0'),
('ebca66eaf2e23d9e3d165f625fa22a57', 373, 3, '2018-02-22', NULL, '0'),
('c033192b973b03bc3dcc478fccb9bd4d', 374, 3, '2018-02-22', NULL, '0'),
('7b9f5fc8d1bb800aebcdd98181fbddf3', 0, 3, '2018-02-22', NULL, '0'),
('47541482fd892e1b0d6d879586058e18', 0, 3, '2018-02-22', NULL, '0'),
('926412ac067dfc54fa9e60f24795a649', 501, 3, '2018-02-24', NULL, '0'),
('dcf024d0e03d0a25b6c8c3034b20cd0b', 502, 3, '2018-02-24', NULL, '0'),
('6653dda100e522294603644fd7fcada4', 503, 3, '2018-02-24', NULL, '0'),
('e88990ea44d8c8cfd721002ffba7c252', 504, 3, '2018-02-24', NULL, '0'),
('e18f4db54d6a6894c3368ec49729cc50', 505, 3, '2018-02-25', NULL, '0'),
('9981bb58f2145278d77a98ee5924aa48', 506, 3, '2018-02-25', NULL, '0'),
('e892e42ae71f818dab840b6ee9ff4218', 507, 3, '2018-02-25', NULL, '0'),
('0a2668f42601c4ab141842aec2c47fc9', 508, 3, '2018-02-25', NULL, '0'),
('5f4d1386d2972ad6e6bd8ddab3fe5ed2', 509, 3, '2018-02-25', NULL, '0'),
('0fab3f7f37c3e23ff0be5e7895010084', 510, 3, '2018-02-25', NULL, '0'),
('00d4c5f8d54db081a3e6a396cc22da11', 511, 3, '2018-02-25', NULL, '0'),
('2bb14edd87342240db4d75459db665b6', 512, 3, '2018-02-25', NULL, '0'),
('c44c7f09e5553ff694ea1b925b31e36d', 513, 3, '2018-02-25', NULL, '0'),
('23a1f77d2e18013350b2bbf52a0180b7', 514, 3, '2018-02-25', NULL, '0'),
('d273327f5ac1d642ebd5a191a5727d34', 515, 3, '2018-02-25', NULL, '0'),
('830b9f8fbd879e999bce16c36a7c1cec', 516, 3, '2018-02-25', NULL, '0'),
('f28613d4ad97ea8d35510ac284924748', 517, 3, '2018-02-26', NULL, '0'),
('40ceefb522613374fdadbe839f3532cf', 518, 3, '2018-02-26', NULL, '0'),
('9f651ea0866805b0b4ce424c03955ac3', 519, 3, '2018-02-26', NULL, '0'),
('b3559f2fbccb40575056c59d825c87e1', 520, 3, '2018-02-28', NULL, '0'),
('afad448409f385953277c8e7cfcdf01f', 521, 3, '2018-02-28', NULL, '0'),
('deb931011fa37c200ad3d96f5d038cd6', 522, 3, '2018-03-01', NULL, '0'),
('2909771ccda9fadec05fb0e25a8c3dd7', 523, 3, '2018-03-01', NULL, '0'),
('5ed99397bc33f5603b7a786bc832b82e', 0, 3, '2018-03-01', NULL, '0'),
('622377c1088027104997eba7c66ca801', 524, 3, '2018-03-01', NULL, '0'),
('59f9591d09605ff2ce4d3d885da299b7', 2, 1, '2018-03-05', NULL, '0'),
('59f9591d09605ff2ce4d3d885da299b7', 2, 1, '2018-03-05', NULL, '0'),
('e4abf74de213681051cc56bee97346f0', 525, 3, '2018-03-06', NULL, '1'),
('45eb37765884a26b6536d18dcb32ff05', 526, 3, '2018-03-06', NULL, '0'),
('c04196876ce589b5a6ca56b78f0ddcc8', 527, 3, '2018-03-06', NULL, '0'),
('5d23cd2f93500a838089a5efa4e66685', 319, 3, '2018-03-08', NULL, '0'),
('64108950d681031d4040a5cddcebd0ca', 320, 3, '2018-03-12', NULL, '0'),
('c3301a0124e0a2f891c83ca1a1b869ad', 321, 3, '2018-03-12', NULL, '0'),
('85ed4bf7cb3afa1808f69414481dc114', 322, 3, '2018-03-12', NULL, '0'),
('541eb754d0790cbe1320c7af7e45b366', 323, 3, '2018-03-13', NULL, '0'),
('6570ff80e47e94b0ff7f8ce138baec84', 328, 3, '2018-03-13', NULL, '0'),
('9986bb85970bc2ae1b1ba45ed78a2b8b', 329, 3, '2018-03-13', NULL, '0'),
('7b3856487b67f2617fe6cc41d22b99d9', 330, 3, '2018-03-13', NULL, '0'),
('00bd04d6ca21902d7294542143d7036a', 331, 3, '2018-03-13', NULL, '0'),
('4e861694e804ad4b0514a5f2b22cb4d8', 332, 3, '2018-03-13', NULL, '0'),
('09d6621c7d138f5029484a10d7b6221b', 333, 3, '2018-03-13', NULL, '0'),
('d70c82b4475b3714082504e108c08c46', 334, 3, '2018-03-13', NULL, '0'),
('6680391e88f14709ff632fd76bf51f81', 335, 3, '2018-03-13', NULL, '0'),
('0de5d6001bc39d64755b2703c3f31e5e', 336, 3, '2018-03-13', NULL, '0'),
('a6802a457fe735451d4c85f470c36fa0', 337, 3, '2018-03-13', NULL, '0'),
('9dd8042d867f7dea539f78c50d6e1235', 338, 3, '2018-03-13', NULL, '0'),
('8cff2ff6b739101d291da79b21a219ea', 339, 3, '2018-03-13', NULL, '0'),
('050af1ce3b9ba538d1c98b83c7410211', 340, 3, '2018-03-13', NULL, '1'),
('e039c513bf863031ca21bfa276ec5ab3', 341, 3, '2018-03-13', NULL, '0'),
('a6802a457fe735451d4c85f470c36fa0', 337, 3, '2018-03-21', NULL, '0'),
('69dee32c31ff8bebef4331f54199627e', 337, 3, '2018-03-26', NULL, '0'),
('9dd8042d867f7dea539f78c50d6e1235', 338, 3, '2018-03-26', NULL, '0'),
('8cff2ff6b739101d291da79b21a219ea', 339, 3, '2018-03-26', NULL, '0'),
('050af1ce3b9ba538d1c98b83c7410211', 340, 3, '2018-04-12', NULL, '1'),
('e039c513bf863031ca21bfa276ec5ab3', 341, 3, '2018-04-12', NULL, '0'),
('5d508e33715b462d7898a1623f9a40c6', 342, 3, '2018-04-12', NULL, '0'),
('819f69b00c8add50eb4b2eb27f49c989', 343, 3, '2018-04-12', NULL, '0'),
('b7fc1884bbd90ca3e93799235e7e57b6', 344, 3, '2018-04-12', NULL, '0'),
('f91f622ad062d85f3b829c129f853559', 345, 3, '2018-04-12', NULL, '0'),
('64ad52a43a2a36289a0477b2eea0ee31', 346, 3, '2018-04-12', NULL, '0'),
('b5faad81bc84aed8dbf0ac6620df809d', 347, 3, '2018-04-12', NULL, '0'),
('75f14c7392ddd2679d710813762f4d19', 348, 3, '2018-04-12', NULL, '0'),
('17c6b91654d0883cb068ddb7a6d48b39', 349, 3, '2018-04-12', NULL, '0'),
('aa53069b0ae74610162f8f61b7d94887', 350, 3, '2018-04-12', NULL, '0'),
('faa8326c2951948c4936eee8296ce16e', 351, 3, '2018-04-12', NULL, '0'),
('dd62225ce9d9d4f753bae37d94091787', 352, 3, '2018-04-12', NULL, '0'),
('c1267186bd129bfe6c9c715014e0284e', 353, 3, '2018-04-13', NULL, '0'),
('4e058d8dda2b54afe17903d8122de00a', 354, 3, '2018-04-13', NULL, '0');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `category`
--
ALTER TABLE `category`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `plandetails`
--
ALTER TABLE `plandetails`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
 ADD PRIMARY KEY (`pid`), ADD KEY `categoryid` (`categoryid`), ADD KEY `fmeaid` (`fmeaid`);

--
-- Indexes for table `productfmea`
--
ALTER TABLE `productfmea`
 ADD PRIMARY KEY (`id`), ADD KEY `productid` (`productid`);

--
-- Indexes for table `riskclass`
--
ALTER TABLE `riskclass`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `userplans`
--
ALTER TABLE `userplans`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `usersession`
--
ALTER TABLE `usersession`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vid_chapter`
--
ALTER TABLE `vid_chapter`
 ADD PRIMARY KEY (`chapter_id`), ADD KEY `sub_id` (`subject_id`);

--
-- Indexes for table `vid_contact_us`
--
ALTER TABLE `vid_contact_us`
 ADD PRIMARY KEY (`contact_id`);

--
-- Indexes for table `vid_course`
--
ALTER TABLE `vid_course`
 ADD PRIMARY KEY (`course_id`), ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `vid_course_category`
--
ALTER TABLE `vid_course_category`
 ADD PRIMARY KEY (`category_id`), ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `vid_demo_test_student`
--
ALTER TABLE `vid_demo_test_student`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vid_district`
--
ALTER TABLE `vid_district`
 ADD PRIMARY KEY (`district_id`), ADD KEY `state_id_fk` (`state_id`);

--
-- Indexes for table `vid_doubt_table`
--
ALTER TABLE `vid_doubt_table`
 ADD PRIMARY KEY (`d_id`);

--
-- Indexes for table `vid_exam`
--
ALTER TABLE `vid_exam`
 ADD PRIMARY KEY (`exam_id`), ADD KEY `chapter_id` (`course_id`), ADD KEY `exam_id` (`exam_id`);

--
-- Indexes for table `vid_exam_chapter_questions`
--
ALTER TABLE `vid_exam_chapter_questions`
 ADD PRIMARY KEY (`ecq_id`), ADD KEY `exam` (`exam_id`), ADD KEY `chapterid` (`chapter_id`);

--
-- Indexes for table `vid_exam_questions`
--
ALTER TABLE `vid_exam_questions`
 ADD PRIMARY KEY (`exam_ques_id`), ADD KEY `ques_id` (`ques_id`,`exam_id`), ADD KEY `exam_id` (`exam_id`);

--
-- Indexes for table `vid_exam_schedule`
--
ALTER TABLE `vid_exam_schedule`
 ADD PRIMARY KEY (`schedule_id`);

--
-- Indexes for table `vid_fbdetails`
--
ALTER TABLE `vid_fbdetails`
 ADD PRIMARY KEY (`feedback_id`);

--
-- Indexes for table `vid_forget_password`
--
ALTER TABLE `vid_forget_password`
 ADD PRIMARY KEY (`forget_id`);

--
-- Indexes for table `vid_forum`
--
ALTER TABLE `vid_forum`
 ADD PRIMARY KEY (`forum_id`);

--
-- Indexes for table `vid_master`
--
ALTER TABLE `vid_master`
 ADD PRIMARY KEY (`master_id`);

--
-- Indexes for table `vid_notes`
--
ALTER TABLE `vid_notes`
 ADD PRIMARY KEY (`note_id`);

--
-- Indexes for table `vid_notes_path`
--
ALTER TABLE `vid_notes_path`
 ADD PRIMARY KEY (`note_path_id`), ADD KEY `note_path_id` (`note_path_id`);

--
-- Indexes for table `vid_prepair_test`
--
ALTER TABLE `vid_prepair_test`
 ADD PRIMARY KEY (`pid`), ADD KEY `pid` (`pid`), ADD KEY `stud_id` (`stud_id`), ADD KEY `chapter_id` (`chapter_id`), ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `vid_question`
--
ALTER TABLE `vid_question`
 ADD PRIMARY KEY (`ques_id`), ADD KEY `inst_id` (`chapter_id`), ADD KEY `chapter_id` (`chapter_id`), ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `vid_question_correct_answer`
--
ALTER TABLE `vid_question_correct_answer`
 ADD PRIMARY KEY (`ques_correct_ans_id`), ADD KEY `ques_id` (`ques_id`), ADD KEY `option_id` (`option_id`), ADD KEY `ques_correct_ans_id` (`ques_correct_ans_id`);

--
-- Indexes for table `vid_question_options`
--
ALTER TABLE `vid_question_options`
 ADD PRIMARY KEY (`option_id`), ADD KEY `ques_id` (`ques_id`);

--
-- Indexes for table `vid_states`
--
ALTER TABLE `vid_states`
 ADD PRIMARY KEY (`state_id`);

--
-- Indexes for table `vid_student`
--
ALTER TABLE `vid_student`
 ADD PRIMARY KEY (`stud_id`), ADD KEY `username` (`username`);

--
-- Indexes for table `vid_student_buy_exam`
--
ALTER TABLE `vid_student_buy_exam`
 ADD PRIMARY KEY (`stud_course_batch_id`), ADD KEY `stud_id` (`stud_id`), ADD KEY `course_id` (`exam_schedule_id`);

--
-- Indexes for table `vid_student_course_temp`
--
ALTER TABLE `vid_student_course_temp`
 ADD PRIMARY KEY (`temp_id`), ADD KEY `schudule` (`schedule_id`,`studentid`), ADD KEY `student table` (`studentid`);

--
-- Indexes for table `vid_student_exam`
--
ALTER TABLE `vid_student_exam`
 ADD PRIMARY KEY (`stud_exam_id`), ADD KEY `stud_id` (`stud_id`), ADD KEY `exam_id` (`exam_schedule_id`);

--
-- Indexes for table `vid_student_exam_result`
--
ALTER TABLE `vid_student_exam_result`
 ADD PRIMARY KEY (`stud_exam_result_id`), ADD KEY `stud_exam_id` (`stud_exam_id`), ADD KEY `ques_option_id` (`ques_option_id`);

--
-- Indexes for table `vid_student_final_result`
--
ALTER TABLE `vid_student_final_result`
 ADD PRIMARY KEY (`sr_id`);

--
-- Indexes for table `vid_student_payment_details`
--
ALTER TABLE `vid_student_payment_details`
 ADD PRIMARY KEY (`payment_id`), ADD KEY `student table` (`stud_id`);

--
-- Indexes for table `vid_studfeedback`
--
ALTER TABLE `vid_studfeedback`
 ADD PRIMARY KEY (`sf_id`);

--
-- Indexes for table `vid_subject`
--
ALTER TABLE `vid_subject`
 ADD PRIMARY KEY (`subject_id`), ADD KEY `subject_id` (`subject_id`), ADD KEY `level_id` (`level_id`), ADD KEY `author_id` (`author_id`);

--
-- Indexes for table `vid_subject_group`
--
ALTER TABLE `vid_subject_group`
 ADD PRIMARY KEY (`subject_group_id`), ADD KEY `subject_id` (`subject_group_id`), ADD KEY `course_id` (`course_id`), ADD KEY `author_id` (`author_id`);

--
-- Indexes for table `vid_subject_group_sub`
--
ALTER TABLE `vid_subject_group_sub`
 ADD PRIMARY KEY (`sub_group_sub_id`), ADD KEY `subject_id` (`sub_group_sub_id`), ADD KEY `course_id` (`subject_id`), ADD KEY `author_id` (`sub_group_id`);

--
-- Indexes for table `vid_subscriber`
--
ALTER TABLE `vid_subscriber`
 ADD PRIMARY KEY (`subscriber_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
MODIFY `pid` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `productfmea`
--
ALTER TABLE `productfmea`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `riskclass`
--
ALTER TABLE `riskclass`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT for table `userplans`
--
ALTER TABLE `userplans`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `usersession`
--
ALTER TABLE `usersession`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `vid_chapter`
--
ALTER TABLE `vid_chapter`
MODIFY `chapter_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=333;
--
-- AUTO_INCREMENT for table `vid_contact_us`
--
ALTER TABLE `vid_contact_us`
MODIFY `contact_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=31;
--
-- AUTO_INCREMENT for table `vid_course`
--
ALTER TABLE `vid_course`
MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=28;
--
-- AUTO_INCREMENT for table `vid_course_category`
--
ALTER TABLE `vid_course_category`
MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `vid_demo_test_student`
--
ALTER TABLE `vid_demo_test_student`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=34;
--
-- AUTO_INCREMENT for table `vid_district`
--
ALTER TABLE `vid_district`
MODIFY `district_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=687;
--
-- AUTO_INCREMENT for table `vid_doubt_table`
--
ALTER TABLE `vid_doubt_table`
MODIFY `d_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `vid_exam`
--
ALTER TABLE `vid_exam`
MODIFY `exam_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=145;
--
-- AUTO_INCREMENT for table `vid_exam_chapter_questions`
--
ALTER TABLE `vid_exam_chapter_questions`
MODIFY `ecq_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=391;
--
-- AUTO_INCREMENT for table `vid_exam_questions`
--
ALTER TABLE `vid_exam_questions`
MODIFY `exam_ques_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vid_exam_schedule`
--
ALTER TABLE `vid_exam_schedule`
MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=309;
--
-- AUTO_INCREMENT for table `vid_fbdetails`
--
ALTER TABLE `vid_fbdetails`
MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `vid_forget_password`
--
ALTER TABLE `vid_forget_password`
MODIFY `forget_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vid_forum`
--
ALTER TABLE `vid_forum`
MODIFY `forum_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vid_notes`
--
ALTER TABLE `vid_notes`
MODIFY `note_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=20;
--
-- AUTO_INCREMENT for table `vid_notes_path`
--
ALTER TABLE `vid_notes_path`
MODIFY `note_path_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=21;
--
-- AUTO_INCREMENT for table `vid_prepair_test`
--
ALTER TABLE `vid_prepair_test`
MODIFY `pid` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `vid_question`
--
ALTER TABLE `vid_question`
MODIFY `ques_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=237;
--
-- AUTO_INCREMENT for table `vid_question_correct_answer`
--
ALTER TABLE `vid_question_correct_answer`
MODIFY `ques_correct_ans_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=375;
--
-- AUTO_INCREMENT for table `vid_question_options`
--
ALTER TABLE `vid_question_options`
MODIFY `option_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1696;
--
-- AUTO_INCREMENT for table `vid_states`
--
ALTER TABLE `vid_states`
MODIFY `state_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3896;
--
-- AUTO_INCREMENT for table `vid_student`
--
ALTER TABLE `vid_student`
MODIFY `stud_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=355;
--
-- AUTO_INCREMENT for table `vid_student_buy_exam`
--
ALTER TABLE `vid_student_buy_exam`
MODIFY `stud_course_batch_id` int(50) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=117;
--
-- AUTO_INCREMENT for table `vid_student_course_temp`
--
ALTER TABLE `vid_student_course_temp`
MODIFY `temp_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vid_student_exam`
--
ALTER TABLE `vid_student_exam`
MODIFY `stud_exam_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=17;
--
-- AUTO_INCREMENT for table `vid_student_exam_result`
--
ALTER TABLE `vid_student_exam_result`
MODIFY `stud_exam_result_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=251;
--
-- AUTO_INCREMENT for table `vid_student_final_result`
--
ALTER TABLE `vid_student_final_result`
MODIFY `sr_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=17;
--
-- AUTO_INCREMENT for table `vid_student_payment_details`
--
ALTER TABLE `vid_student_payment_details`
MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=298;
--
-- AUTO_INCREMENT for table `vid_studfeedback`
--
ALTER TABLE `vid_studfeedback`
MODIFY `sf_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT for table `vid_subject`
--
ALTER TABLE `vid_subject`
MODIFY `subject_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT for table `vid_subject_group`
--
ALTER TABLE `vid_subject_group`
MODIFY `subject_group_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT for table `vid_subject_group_sub`
--
ALTER TABLE `vid_subject_group_sub`
MODIFY `sub_group_sub_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=151;
--
-- AUTO_INCREMENT for table `vid_subscriber`
--
ALTER TABLE `vid_subscriber`
MODIFY `subscriber_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=55;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `vid_chapter`
--
ALTER TABLE `vid_chapter`
ADD CONSTRAINT `vid_chapter_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `vid_subject` (`subject_id`);

--
-- Constraints for table `vid_course`
--
ALTER TABLE `vid_course`
ADD CONSTRAINT `vid_course_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `vid_course_category` (`category_id`);

--
-- Constraints for table `vid_district`
--
ALTER TABLE `vid_district`
ADD CONSTRAINT `vid_district_ibfk_1` FOREIGN KEY (`state_id`) REFERENCES `vid_states` (`state_id`);

--
-- Constraints for table `vid_exam_questions`
--
ALTER TABLE `vid_exam_questions`
ADD CONSTRAINT `fk_exam_ques_id` FOREIGN KEY (`ques_id`) REFERENCES `vid_question` (`ques_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_ques_exam_id` FOREIGN KEY (`exam_id`) REFERENCES `vid_exam` (`exam_id`);

--
-- Constraints for table `vid_question`
--
ALTER TABLE `vid_question`
ADD CONSTRAINT `fk_ques_chapter` FOREIGN KEY (`chapter_id`) REFERENCES `vid_chapter` (`chapter_id`);

--
-- Constraints for table `vid_question_correct_answer`
--
ALTER TABLE `vid_question_correct_answer`
ADD CONSTRAINT `fk_option_id` FOREIGN KEY (`option_id`) REFERENCES `vid_question_options` (`option_id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `fk_qoestion_id` FOREIGN KEY (`ques_id`) REFERENCES `vid_question` (`ques_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `vid_question_options`
--
ALTER TABLE `vid_question_options`
ADD CONSTRAINT `fk_option_ques_id` FOREIGN KEY (`ques_id`) REFERENCES `vid_question` (`ques_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `vid_subject_group_sub`
--
ALTER TABLE `vid_subject_group_sub`
ADD CONSTRAINT `vid_subject_group_sub_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `vid_subject` (`subject_id`),
ADD CONSTRAINT `vid_subject_group_sub_ibfk_2` FOREIGN KEY (`sub_group_id`) REFERENCES `vid_subject_group` (`subject_group_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
