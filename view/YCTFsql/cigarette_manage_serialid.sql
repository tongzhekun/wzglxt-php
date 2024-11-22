-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主机： 127.0.0.1
-- 生成日期： 2024-11-22 02:32:29
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
-- 表的结构 `cigarette_manage_serialid`
--

CREATE TABLE `cigarette_manage_serialid` (
  `id` int(11) NOT NULL,
  `serialId` varchar(8) NOT NULL COMMENT '批次号',
  `period_name` varchar(100) NOT NULL COMMENT '所属周期',
  `time` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='选定批次表';

--
-- 转存表中的数据 `cigarette_manage_serialid`
--

INSERT INTO `cigarette_manage_serialid` (`id`, `serialId`, `period_name`, `time`) VALUES
(3, '20241108', '2024-11-04至2024-11-10卷烟拟合数据', '2024-11-21');

--
-- 转储表的索引
--

--
-- 表的索引 `cigarette_manage_serialid`
--
ALTER TABLE `cigarette_manage_serialid`
  ADD PRIMARY KEY (`id`,`serialId`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `cigarette_manage_serialid`
--
ALTER TABLE `cigarette_manage_serialid`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
