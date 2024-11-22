-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主机： 127.0.0.1
-- 生成日期： 2024-11-22 02:32:02
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
-- 表的结构 `decay_rule_temp`
--

CREATE TABLE `decay_rule_temp` (
  `id` int(11) NOT NULL COMMENT '序列号',
  `stock_min_max` varchar(255) NOT NULL COMMENT '可供量（箱）',
  `level_30_num` int(11) NOT NULL COMMENT '30档定量',
  `decay_rate` decimal(4,2) NOT NULL COMMENT '衰减速度',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `update_date` date NOT NULL COMMENT '更新日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='衰减速度定量规律表';

--
-- 转存表中的数据 `decay_rule_temp`
--

INSERT INTO `decay_rule_temp` (`id`, `stock_min_max`, `level_30_num`, `decay_rate`, `remark`, `update_date`) VALUES
(2620, '0-30', 1, 0.00, '\n', '2024-11-21'),
(2621, '30-50', 3, 0.16, '', '2024-11-21'),
(2622, '50-100', 4, 0.12, '', '2024-11-21'),
(2623, '100-150', 5, 0.12, '', '2024-11-21'),
(2624, '150-200', 6, 0.10, '\r', '2024-11-21'),
(2625, '200-250', 7, 0.10, '\r', '2024-11-21'),
(2626, '250-300', 8, 0.10, '\r', '2024-11-21'),
(2627, '300-350', 9, 0.10, '\r', '2024-11-21'),
(2628, '350-400', 10, 0.10, '\r', '2024-11-21'),
(2629, '400-450', 11, 0.10, '\r', '2024-11-21'),
(2630, '450-500', 12, 0.08, '', '2024-11-21'),
(2631, '500-550', 13, 0.05, '\r', '2024-11-21'),
(2632, '550-600', 14, 0.05, '\r', '2024-11-21'),
(2633, '600-650', 15, 0.05, '\r', '2024-11-21'),
(2634, '650-700', 16, 0.05, '\r', '2024-11-21'),
(2635, '700-750', 17, 0.05, '\r', '2024-11-21'),
(2636, '750-800', 18, 0.05, '\r', '2024-11-21'),
(2637, '800-850', 19, 0.05, '\r', '2024-11-21'),
(2638, '850-900', 20, 0.05, '\r', '2024-11-21'),
(2639, '900-950', 21, 0.05, '\r', '2024-11-21'),
(2640, '950-1000', 22, 0.05, '\r', '2024-11-21'),
(2641, '1000-1200', 26, 0.04, '\r', '2024-11-21'),
(2642, '1200-1400', 30, 0.04, '\r', '2024-11-21'),
(2643, '1400-1600', 34, 0.04, '\r', '2024-11-21'),
(2644, '1600-1800', 38, 0.04, '\r', '2024-11-21'),
(2645, '1800-2000', 42, 0.04, '\r', '2024-11-21'),
(2646, '2000-2200', 46, 0.04, '\r', '2024-11-21'),
(2647, '2200-2400', 50, 0.04, '\r', '2024-11-21'),
(2648, '2400-2600', 54, 0.04, '\r', '2024-11-21'),
(2649, '2600-2800', 58, 0.04, '\r', '2024-11-21'),
(2650, '2800-3000', 62, 0.04, '\r', '2024-11-21'),
(2651, '3000-3200', 66, 0.04, '\r', '2024-11-21'),
(2652, '3200-3400', 70, 0.04, '\r', '2024-11-21'),
(2653, '3400-3600', 74, 0.04, '\r', '2024-11-21');

--
-- 转储表的索引
--

--
-- 表的索引 `decay_rule_temp`
--
ALTER TABLE `decay_rule_temp`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `decay_rule_temp`
--
ALTER TABLE `decay_rule_temp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '序列号', AUTO_INCREMENT=2654;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
