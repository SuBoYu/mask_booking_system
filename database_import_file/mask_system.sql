-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- 主機： localhost:3306
-- 產生時間： 2021 年 06 月 07 日 07:05
-- 伺服器版本： 5.7.32
-- PHP 版本： 7.4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `mask_system`
--

-- --------------------------------------------------------

--
-- 資料表結構 `mask_order`
--

CREATE TABLE `mask_order` (
  `OID` bigint(20) UNSIGNED NOT NULL,
  `creator_UID` bigint(20) UNSIGNED NOT NULL,
  `finish_UID` bigint(20) UNSIGNED DEFAULT NULL,
  `SID` bigint(20) UNSIGNED NOT NULL,
  `create_time` datetime NOT NULL,
  `end_time` datetime DEFAULT NULL,
  `status` set('finished','unfinished','canceled') NOT NULL,
  `price` int(10) UNSIGNED NOT NULL,
  `amount` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 傾印資料表的資料 `mask_order`
--

INSERT INTO `mask_order` (`OID`, `creator_UID`, `finish_UID`, `SID`, `create_time`, `end_time`, `status`, `price`, `amount`) VALUES
(4, 2, 2, 1, '2021-06-05 13:04:28', '2021-06-07 14:56:36', 'finished', 10, 30),
(5, 2, NULL, 1, '2021-06-05 13:20:21', NULL, 'unfinished', 10, 90);

-- --------------------------------------------------------

--
-- 資料表結構 `members`
--

CREATE TABLE `members` (
  `UID` bigint(20) UNSIGNED NOT NULL,
  `Account` varchar(50) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `password` varchar(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `Phonenumber` int(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 傾印資料表的資料 `members`
--

INSERT INTO `members` (`UID`, `Account`, `password`, `Phonenumber`) VALUES
(2, 'tony', '$2y$10$unxjjorw0X3Re5qWKYhWmeZ4RKQVi4wP1A0o.YtXJHayzzlsmOxsy', 123),
(3, 'jack', '$2y$10$AFgFhF.voI1eUQfGaA4LBO3b44SJS53UDeD9weR6XYcTGGASFHk3a', 123),
(4, 'jenson', '$2y$10$vdrFpiwi2lGlRNHG/wS2eug2kyt9teQ/J/u9.blcKV/O92tNyHxN2', 123),
(5, 'peter', '$2y$10$tuhyCz09s3.aFSav2VgX3OI2elxDVXiXE/KL0BnfcfecwoOgGAEOm', 123);

-- --------------------------------------------------------

--
-- 資料表結構 `staff`
--

CREATE TABLE `staff` (
  `UID` bigint(20) UNSIGNED NOT NULL,
  `SID` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 傾印資料表的資料 `staff`
--

INSERT INTO `staff` (`UID`, `SID`) VALUES
(3, 1);

-- --------------------------------------------------------

--
-- 資料表結構 `store`
--

CREATE TABLE `store` (
  `SID` bigint(20) UNSIGNED NOT NULL,
  `UID` bigint(20) UNSIGNED NOT NULL,
  `name` text CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `amount` int(10) UNSIGNED NOT NULL,
  `price` int(10) UNSIGNED NOT NULL,
  `city` text CHARACTER SET ascii COLLATE ascii_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 傾印資料表的資料 `store`
--

INSERT INTO `store` (`SID`, `UID`, `name`, `amount`, `price`, `city`) VALUES
(1, 2, 'tsmc', 10, 10, 'Taipei'),
(2, 5, 'apple', 10000, 100, 'Taipei');

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `mask_order`
--
ALTER TABLE `mask_order`
  ADD PRIMARY KEY (`OID`),
  ADD KEY `mask_order_ibfk_1` (`creator_UID`),
  ADD KEY `mask_order_ibfk_2` (`SID`),
  ADD KEY `finish_UID` (`finish_UID`);

--
-- 資料表索引 `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`UID`) USING BTREE,
  ADD UNIQUE KEY `account` (`Account`);

--
-- 資料表索引 `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`UID`,`SID`),
  ADD KEY `staff_ibfk_1` (`SID`);

--
-- 資料表索引 `store`
--
ALTER TABLE `store`
  ADD PRIMARY KEY (`SID`),
  ADD UNIQUE KEY `UID` (`UID`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `mask_order`
--
ALTER TABLE `mask_order`
  MODIFY `OID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `members`
--
ALTER TABLE `members`
  MODIFY `UID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `store`
--
ALTER TABLE `store`
  MODIFY `SID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- 已傾印資料表的限制式
--

--
-- 資料表的限制式 `mask_order`
--
ALTER TABLE `mask_order`
  ADD CONSTRAINT `mask_order_ibfk_2` FOREIGN KEY (`creator_UID`) REFERENCES `members` (`UID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `mask_order_ibfk_3` FOREIGN KEY (`SID`) REFERENCES `store` (`SID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `mask_order_ibfk_4` FOREIGN KEY (`finish_UID`) REFERENCES `members` (`UID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 資料表的限制式 `staff`
--
ALTER TABLE `staff`
  ADD CONSTRAINT `staff_ibfk_1` FOREIGN KEY (`UID`) REFERENCES `members` (`UID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `staff_ibfk_2` FOREIGN KEY (`SID`) REFERENCES `store` (`SID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 資料表的限制式 `store`
--
ALTER TABLE `store`
  ADD CONSTRAINT `store_ibfk_1` FOREIGN KEY (`UID`) REFERENCES `members` (`UID`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
