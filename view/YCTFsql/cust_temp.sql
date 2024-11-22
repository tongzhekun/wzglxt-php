-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主机： 127.0.0.1
-- 生成日期： 2024-11-22 02:31:50
-- 服务器版本： 10.4.32-MariaDB
-- PHP 版本： 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `wzgl`
--

-- --------------------------------------------------------

--
-- 表的结构 `cust_temp`
--

CREATE TABLE `cust_temp` (
  `gear` int(10) NOT NULL,
  `num` int(10) NOT NULL,
  `time` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='档位投放表客户临时表';

--
-- 转存表中的数据 `cust_temp`
--

INSERT INTO `cust_temp` (`gear`, `num`, `time`) VALUES
(1, 260, '2024-11-21'),
(2, 534, '2024-11-21'),
(3, 532, '2024-11-21'),
(4, 531, '2024-11-21'),
(5, 529, '2024-11-21'),
(6, 792, '2024-11-21'),
(7, 800, '2024-11-21'),
(8, 1068, '2024-11-21'),
(9, 1072, '2024-11-21'),
(10, 1072, '2024-11-21'),
(11, 1096, '2024-11-21'),
(12, 1059, '2024-11-21'),
(13, 1336, '2024-11-21'),
(14, 1367, '2024-11-21'),
(15, 1522, '2024-11-21'),
(16, 1322, '2024-11-21'),
(17, 1329, '2024-11-21'),
(18, 1324, '2024-11-21'),
(19, 1060, '2024-11-21'),
(20, 1060, '2024-11-21'),
(21, 1063, '2024-11-21'),
(22, 1058, '2024-11-21'),
(23, 1059, '2024-11-21'),
(24, 793, '2024-11-21'),
(25, 794, '2024-11-21'),
(26, 530, '2024-11-21'),
(27, 528, '2024-11-21'),
(28, 528, '2024-11-21'),
(29, 528, '2024-11-21'),
(30, 264, '2024-11-21');

--
-- 转储表的索引
--

--
-- 表的索引 `cust_temp`
--
ALTER TABLE `cust_temp`
  ADD PRIMARY KEY (`gear`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
