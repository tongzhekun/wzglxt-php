-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主机： 127.0.0.1
-- 生成日期： 2024-11-22 02:32:37
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
-- 表的结构 `calculate_distribution`
--

CREATE TABLE `calculate_distribution` (
  `id` int(11) NOT NULL,
  `serialId` varchar(100) NOT NULL COMMENT '批次号',
  `sku` varchar(100) NOT NULL COMMENT '编号',
  `cig_name` varchar(100) NOT NULL COMMENT '卷烟名称',
  `stocks_sale` decimal(11,2) NOT NULL COMMENT '投放量',
  `is_sale` varchar(5) NOT NULL COMMENT '是否投放',
  `stocks_pre` decimal(11,2) NOT NULL COMMENT '预估销量',
  `level_30_num` int(11) NOT NULL,
  `level_29_num` int(11) NOT NULL,
  `level_28_num` int(11) NOT NULL,
  `level_27_num` int(11) NOT NULL,
  `level_26_num` int(11) NOT NULL,
  `level_25_num` int(11) NOT NULL,
  `level_24_num` int(11) NOT NULL,
  `level_23_num` int(11) NOT NULL,
  `level_22_num` int(11) NOT NULL,
  `level_21_num` int(11) NOT NULL,
  `level_20_num` int(11) NOT NULL,
  `level_19_num` int(11) NOT NULL,
  `level_18_num` int(11) NOT NULL,
  `level_17_num` int(11) NOT NULL,
  `level_16_num` int(11) NOT NULL,
  `level_15_num` int(11) NOT NULL,
  `level_14_num` int(11) NOT NULL,
  `level_13_num` int(11) NOT NULL,
  `level_12_num` int(11) NOT NULL,
  `level_11_num` int(11) NOT NULL,
  `level_10_num` int(11) NOT NULL,
  `level_9_num` int(11) NOT NULL,
  `level_8_num` int(11) NOT NULL,
  `level_7_num` int(11) NOT NULL,
  `level_6_num` int(11) NOT NULL,
  `level_5_num` int(11) NOT NULL,
  `level_4_num` int(11) NOT NULL,
  `level_3_num` int(11) NOT NULL,
  `level_2_num` int(11) NOT NULL,
  `level_1_num` int(11) NOT NULL,
  `update_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='各个档位烟数分配表';

--
-- 转存表中的数据 `calculate_distribution`
--

INSERT INTO `calculate_distribution` (`id`, `serialId`, `sku`, `cig_name`, `stocks_sale`, `is_sale`, `stocks_pre`, `level_30_num`, `level_29_num`, `level_28_num`, `level_27_num`, `level_26_num`, `level_25_num`, `level_24_num`, `level_23_num`, `level_22_num`, `level_21_num`, `level_20_num`, `level_19_num`, `level_18_num`, `level_17_num`, `level_16_num`, `level_15_num`, `level_14_num`, `level_13_num`, `level_12_num`, `level_11_num`, `level_10_num`, `level_9_num`, `level_8_num`, `level_7_num`, `level_6_num`, `level_5_num`, `level_4_num`, `level_3_num`, `level_2_num`, `level_1_num`, `update_date`) VALUES
(21079, '20241108', 'SKU001', '利群(休闲细支)', 20.41, '0', 20.10, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21080, '20241108', 'SKU002', '利群(休闲)', 38.96, '0', 39.14, 3, 3, 2, 2, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21081, '20241108', 'SKU003', '利群(红利)', 22.22, '0', 24.33, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21082, '20241108', 'SKU004', '利群(阳光)', 461.54, '0', 458.98, 12, 11, 10, 9, 9, 8, 7, 7, 6, 6, 5, 5, 4, 4, 4, 3, 3, 3, 3, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, '2024-11-21'),
(21083, '20241108', 'SKU005', '利群(阳光尊中支)', 5.21, '0', 5.28, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21084, '20241108', 'SKU006', '利群(阳光尊细支)', 3.33, '0', 3.17, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21085, '20241108', 'SKU007', '利群(软长嘴)', 454.55, '0', 453.63, 12, 11, 10, 9, 9, 8, 7, 7, 6, 6, 5, 5, 4, 4, 4, 3, 3, 2, 3, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, '2024-11-21'),
(21086, '20241108', 'SKU008', '利群(西子阳光)', 96.39, '0', 95.17, 4, 4, 3, 3, 2, 2, 2, 2, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21087, '20241108', 'SKU009', '利群(长嘴)', 707.07, '0', 706.01, 17, 16, 15, 15, 14, 13, 12, 12, 11, 11, 10, 10, 9, 4, 4, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, '2024-11-21'),
(21088, '20241108', 'SKU010', '利群(软红长嘴)', 454.55, '0', 453.63, 12, 11, 10, 9, 9, 8, 7, 7, 6, 6, 5, 5, 4, 4, 4, 3, 3, 2, 3, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, '2024-11-21'),
(21089, '20241108', 'SKU011', '云烟(云端)', 0.00, '0', 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21090, '20241108', 'SKU012', '云烟(细支大重九)', 0.00, '0', 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21091, '20241108', 'SKU013', '云烟(中支云端)', 0.00, '0', 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21092, '20241108', 'SKU014', '云烟(小云端)', 0.00, '0', 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21093, '20241108', 'SKU015', '云烟(中支乌镇之恋)', 166.67, '0', 162.24, 6, 5, 5, 4, 4, 4, 3, 1, 1, 2, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, '2024-11-21'),
(21094, '20241108', 'SKU016', '云烟(呼伦贝尔天之韵)', 111.11, '0', 111.46, 5, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, '2024-11-21'),
(21095, '20241108', 'SKU017', '软玉溪', 229.89, '0', 220.49, 7, 6, 6, 5, 5, 4, 4, 3, 3, 3, 2, 2, 2, 2, 2, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, '2024-11-21'),
(21096, '20241108', 'SKU018', '玉溪(创客)', 257.14, '0', 254.63, 8, 7, 6, 6, 5, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, '2024-11-21'),
(21097, '20241108', 'SKU019', '云烟(硬云龙)', 10.31, '0', 12.69, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21098, '20241108', 'SKU020', '云烟(紫)', 15.31, '0', 15.86, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21099, '20241108', 'SKU021', '红塔山(硬经典100)', 15.31, '0', 15.86, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21100, '20241108', 'SKU022', '南京(软九五)', 15.79, '0', 15.86, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21101, '20241108', 'SKU023', '南京(十二钗烤烟)', 39.68, '0', 39.14, 3, 3, 2, 2, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21102, '20241108', 'SKU024', '苏烟(五星红杉树)', 85.11, '0', 89.70, 4, 4, 3, 3, 2, 2, 2, 2, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21103, '20241108', 'SKU025', '南京(红)', 102.04, '0', 111.46, 5, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, '2024-11-21'),
(21104, '20241108', 'SKU026', '七匹狼(观海中支)', 3.33, '0', 3.17, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21105, '20241108', 'SKU027', '七匹狼(银中支)', 18.99, '0', 20.10, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21106, '20241108', 'SKU028', '七匹狼(豪运)', 0.00, '0', 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21107, '20241108', 'SKU029', '七匹狼(豪迈)', 0.00, '0', 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21108, '20241108', 'SKU030', '七匹狼(蓝)', 8.25, '0', 9.51, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21109, '20241108', 'SKU031', '双喜(硬世纪经典)', 28.57, '0', 28.58, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21110, '20241108', 'SKU032', '双喜(百年经典)', 33.33, '0', 34.89, 3, 3, 2, 2, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21111, '20241108', 'SKU033', '双喜(硬经典1906)', 15.15, '0', 15.86, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21112, '20241108', 'SKU034', '泰山(颜悦)', 16.18, '0', 20.10, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21113, '20241108', 'SKU035', '泰山(儒风双中支)', 1.67, '0', 1.06, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21114, '20241108', 'SKU036', '泰山(硬红八喜)', 31.58, '0', 34.89, 3, 3, 2, 2, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21115, '20241108', 'SKU037', '娇子(宽窄1024)', 10.00, '0', 12.69, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21116, '20241108', 'SKU038', '娇子(五粮浓香中支)', 13.79, '0', 15.86, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21117, '20241108', 'SKU039', '娇子(宽窄平安中支)', 500.00, '0', 458.98, 12, 11, 10, 9, 9, 8, 7, 7, 6, 6, 5, 5, 4, 4, 4, 3, 3, 3, 3, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, '2024-11-21'),
(21118, '20241108', 'SKU040', '娇子(五粮醇香)', 285.71, '0', 283.21, 8, 7, 6, 6, 5, 5, 4, 4, 3, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, '2024-11-21'),
(21119, '20241108', 'SKU041', '娇子(宽窄好运细支)', 214.29, '0', 209.88, 7, 6, 6, 5, 5, 4, 4, 3, 3, 3, 2, 2, 2, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, '2024-11-21'),
(21120, '20241108', 'SKU042', '娇子(时代阳光)', 258.82, '0', 257.81, 8, 7, 6, 6, 5, 3, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, '2024-11-21'),
(21121, '20241108', 'SKU043', '娇子(X)', 0.00, '0', 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21122, '20241108', 'SKU044', '长城(醇雅薄荷)', 0.00, '0', 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21123, '20241108', 'SKU045', '真龙(海韵中支)', 0.00, '0', 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21124, '20241108', 'SKU046', '真龙(美人香草)', 15.31, '0', 15.86, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21125, '20241108', 'SKU047', '真龙(娇子)', 94.74, '0', 95.17, 4, 4, 3, 3, 2, 2, 2, 2, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21126, '20241108', 'SKU048', '长白山(777)', 0.00, '0', 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21127, '20241108', 'SKU049', '兰州(硬精品)', 3.16, '0', 3.17, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21128, '20241108', 'SKU050', '好猫(长乐九美)', 0.00, '0', 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21129, '20241108', 'SKU051', '好猫(细支长乐)', 5.49, '0', 5.28, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21130, '20241108', 'SKU052', '中华(金中支)', 0.00, '0', 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21131, '20241108', 'SKU053', '中华(金细支)', 0.00, '0', 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21132, '20241108', 'SKU054', '中华(软)', 285.71, '0', 283.21, 8, 7, 6, 6, 5, 5, 4, 4, 3, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, '2024-11-21'),
(21133, '20241108', 'SKU055', '中华(硬)', 305.88, '0', 304.38, 9, 8, 7, 7, 6, 5, 5, 4, 4, 3, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, '2024-11-21'),
(21134, '20241108', 'SKU056', '中华(全开式)', 20.00, '0', 20.10, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21135, '20241108', 'SKU057', '中华(双中支)', 60.61, '0', 63.48, 4, 4, 3, 3, 2, 2, 2, 2, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21136, '20241108', 'SKU058', '中华(细支)', 9.09, '0', 9.51, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21137, '20241108', 'SKU059', '牡丹(软)', 101.01, '0', 111.46, 5, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, '2024-11-21'),
(21138, '20241108', 'SKU060', '中南海(冰耀中支)', 11.63, '0', 12.69, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21139, '20241108', 'SKU061', '中南海(典8)', 40.00, '0', 43.38, 3, 3, 2, 2, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21140, '20241108', 'SKU062', '红双喜(硬)', 55.56, '0', 59.24, 4, 4, 3, 3, 2, 2, 2, 2, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21141, '20241108', 'SKU063', '红双喜(硬8mg)', 612.24, '0', 610.68, 15, 14, 14, 13, 12, 12, 11, 10, 10, 9, 9, 9, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, '2024-11-21'),
(21142, '20241108', 'SKU064', '白沙(和天下)', 11.76, '0', 12.69, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21143, '20241108', 'SKU065', '白沙(软和天下)', 27.17, '0', 28.58, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21144, '20241108', 'SKU066', '芙蓉王(硬中支)', 37.97, '0', 39.14, 3, 3, 2, 2, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21145, '20241108', 'SKU067', '白沙(硬新精品二代)', 75.76, '0', 78.33, 4, 4, 3, 3, 2, 2, 2, 2, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21146, '20241108', 'SKU068', '黄鹤楼(视界)', 0.00, '0', 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21147, '20241108', 'SKU069', '黄鹤楼(硬峡谷柔情)', 104.17, '0', 111.46, 5, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, '2024-11-21'),
(21148, '20241108', 'SKU070', '黄山(徽商新概念细支)', 0.00, '0', 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21149, '20241108', 'SKU071', '黄山(皖烟中支)', 0.00, '0', 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21150, '20241108', 'SKU072', '黄山(硬记忆)', 0.00, '0', 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21151, '20241108', 'SKU073', '贵烟(国酒香30)', 0.00, '0', 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21152, '20241108', 'SKU074', '贵烟(跨越)', 30.12, '0', 32.82, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21153, '20241108', 'SKU075', '黄果树(佳品)', 151.52, '0', 148.46, 6, 5, 5, 4, 4, 2, 2, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, '2024-11-21'),
(21154, '20241108', 'SKU076', '黄金叶(天叶)', 30.61, '0', 32.82, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21155, '20241108', 'SKU077', '黄金叶(国色双中支)', 0.00, '0', 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21156, '20241108', 'SKU078', '黄金叶(乐途中支)', 38.89, '0', 39.14, 3, 3, 2, 2, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21157, '20241108', 'SKU079', '黄金叶(硬帝豪)', 101.01, '0', 111.46, 5, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, '2024-11-21'),
(21158, '20241108', 'SKU080', '金圣(硬滕王阁)', 40.40, '0', 43.38, 3, 3, 2, 2, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21159, '20241108', 'SKU081', '天子(千里江山细支)', 28.57, '0', 28.58, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21160, '20241108', 'SKU082', '天子(中支)', 62.50, '0', 63.48, 4, 4, 3, 3, 2, 2, 2, 2, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21161, '20241108', 'SKU083', '人民大会堂(红细支)', 0.00, '0', 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21162, '20241108', 'SKU084', '人民大会堂(中支)', 20.00, '0', 20.10, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21163, '20241108', 'SKU085', '555(金天越)', 0.00, '0', 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21164, '20241108', 'SKU086', '555(金锐)', 0.00, '0', 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21165, '20241108', 'SKU087', '555(双冰)', 0.00, '0', 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21166, '20241108', 'SKU088', '555冰炫', 0.00, '0', 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21167, '20241108', 'SKU089', '555(国际版)', 0.00, '0', 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21168, '20241108', 'SKU090', '万宝路(硬红2.0)', 0.00, '0', 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21169, '20241108', 'SKU091', '万宝路(软红2.0)', 0.00, '0', 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21170, '20241108', 'SKU092', '万宝路(软金3.0)', 0.00, '0', 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21171, '20241108', 'SKU093', '万宝路(硬金3.0)', 0.00, '0', 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21172, '20241108', 'SKU094', '万宝路(硬冰爵2.0)', 0.00, '0', 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21173, '20241108', 'SKU095', '建牌(薄荷黄冰)', 0.00, '0', 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21174, '20241108', 'SKU096', '建牌(薄荷紫冰)', 0.00, '0', 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21175, '20241108', 'SKU097', '红双喜(龙凤)', 0.00, '0', 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21176, '20241108', 'SKU098', '红双喜(龙凤紫)', 0.00, '0', 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21177, '20241108', 'SKU099', '红双喜(爱国绿中支)', 0.00, '0', 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21178, '20241108', 'SKU100', '爱喜薄荷', 0.00, '0', 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21179, '20241108', 'SKU101', '爱喜(幻变)', 0.00, '0', 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21180, '20241108', 'SKU102', '宝亨6毫克', 0.00, '0', 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21'),
(21181, '20241108', 'SKU103', '爱喜(幻变倍享)', 0.00, '0', 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2024-11-21');

--
-- 转储表的索引
--

--
-- 表的索引 `calculate_distribution`
--
ALTER TABLE `calculate_distribution`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `calculate_distribution`
--
ALTER TABLE `calculate_distribution`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21182;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
