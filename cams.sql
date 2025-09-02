-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- 主机： 127.0.0.1:3306
-- 生成日期： 2025-09-01 07:39:49
-- 服务器版本： 5.7.26
-- PHP 版本： 7.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `cams`
--
CREATE DATABASE IF NOT EXISTS `cams` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `cams`;

DELIMITER $$
--
-- 存储过程
--
DROP PROCEDURE IF EXISTS `ResetUserPassword`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `ResetUserPassword` (IN `user_id` INT)  BEGIN
    UPDATE userinfo
    SET Password = '000000'
    WHERE UserId = user_id;
END$$

--
-- 函数
--
DROP FUNCTION IF EXISTS `FormatDate`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `FormatDate` (`date_value` DATE) RETURNS VARCHAR(10) CHARSET utf8 NO SQL
RETURN DATE_FORMAT(date_value, '%Y-%m-%d')$$

DELIMITER ;

-- --------------------------------------------------------

--
-- 表的结构 `activities`
--

DROP TABLE IF EXISTS `activities`;
CREATE TABLE IF NOT EXISTS `activities` (
  `ActvtId` int(11) NOT NULL,
  `ActvtTitle` varchar(150) NOT NULL,
  `DeptId` int(11) DEFAULT NULL,
  `ActvtTime` datetime NOT NULL,
  `PlaceId` int(11) NOT NULL,
  `PeopleNumRqrd` int(11) NOT NULL,
  `PeopleNumIn` int(11) NOT NULL DEFAULT '0',
  `Intro` varchar(1000) DEFAULT NULL,
  `OtherRqrments` varchar(1000) DEFAULT NULL,
  `Notes` varchar(500) DEFAULT NULL,
  `Status` int(11) NOT NULL DEFAULT '0',
  `PublishDate` date DEFAULT NULL,
  `AdministrationAuth` tinyint(1) DEFAULT NULL,
  `PublisherId` int(11) NOT NULL,
  PRIMARY KEY (`ActvtId`),
  KEY `FK_ACTIVITI_ACTVT_PLA_PLACES` (`PlaceId`),
  KEY `FK_ACTIVITI_DEPT_ACTV_DEPARTME` (`DeptId`),
  KEY `FK_ACTIVITI_PUBLICATI_ADMINUSE` (`PublisherId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `activities`
--

INSERT INTO `activities` (`ActvtId`, `ActvtTitle`, `DeptId`, `ActvtTime`, `PlaceId`, `PeopleNumRqrd`, `PeopleNumIn`, `Intro`, `OtherRqrments`, `Notes`, `Status`, `PublishDate`, `AdministrationAuth`, `PublisherId`) VALUES
(2, 'TestActvt1', 10, '2025-01-01 00:00:00', 100001, 2, 0, NULL, NULL, NULL, 0, '2024-12-21', NULL, 1),
(10001, 'Aerospace Opening Ceremony Arrangement Conference', 10, '2024-09-01 00:00:00', 100001, 20, 2, NULL, NULL, NULL, 1, NULL, 1, 1),
(10002, 'IT Seminar', 20, '2024-10-15 09:00:00', 200002, 50, 0, NULL, NULL, NULL, 1, NULL, NULL, 1),
(10003, 'Marketing Workshop', 30, '2024-11-01 14:00:00', 300003, 30, 0, NULL, NULL, NULL, 1, NULL, NULL, 1),
(10004, 'HR Training', 40, '2024-12-01 10:00:00', 400004, 100, 0, NULL, NULL, NULL, 1, NULL, NULL, 1),
(10005, 'Finance Meeting', 30, '2025-01-15 08:30:00', 300005, 25, 0, '', '', '', 0, '2024-12-19', 0, 1),
(10007, 'TestActvt0', 10, '2025-01-01 00:00:00', 100001, 1, 1, 'NULL', 'NULL', 'NULL', 0, '2024-12-21', 0, 1);

--
-- 触发器 `activities`
--
DROP TRIGGER IF EXISTS `increment_activity_published`;
DELIMITER $$
CREATE TRIGGER `increment_activity_published` AFTER INSERT ON `activities` FOR EACH ROW UPDATE adminusers
SET NumActivityPublished = NumActivityPublished + 1
WHERE UserId = NEW.PublisherId
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `set_publish_InNum`;
DELIMITER $$
CREATE TRIGGER `set_publish_InNum` BEFORE INSERT ON `activities` FOR EACH ROW IF NEW.PeopleNumIn IS NULL THEN 
  SET NEW.PeopleNumIn = 0;
END IF
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `set_publish_date`;
DELIMITER $$
CREATE TRIGGER `set_publish_date` BEFORE INSERT ON `activities` FOR EACH ROW SET NEW.PublishDate = CURDATE()
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- 表的结构 `adminusers`
--

DROP TABLE IF EXISTS `adminusers`;
CREATE TABLE IF NOT EXISTS `adminusers` (
  `UserId` int(11) NOT NULL,
  `AdminName` varchar(50) NOT NULL,
  `NumActivityPublished` int(11) NOT NULL DEFAULT '0',
  `DeptId` int(11) NOT NULL,
  PRIMARY KEY (`UserId`),
  KEY `FK_ADMINUSE_DEPT_AU_DEPARTME` (`DeptId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `adminusers`
--

INSERT INTO `adminusers` (`UserId`, `AdminName`, `NumActivityPublished`, `DeptId`) VALUES
(1, 'SuperUser', 11, 10),
(22100001, 'DefaultName', 0, 10);

-- --------------------------------------------------------

--
-- 表的结构 `buildings`
--

DROP TABLE IF EXISTS `buildings`;
CREATE TABLE IF NOT EXISTS `buildings` (
  `BuildingId` int(11) NOT NULL,
  `BuildingName` varchar(100) NOT NULL,
  `Location` varchar(100) NOT NULL,
  `BuiltOn` date DEFAULT NULL,
  PRIMARY KEY (`BuildingId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `buildings`
--

INSERT INTO `buildings` (`BuildingId`, `BuildingName`, `Location`, `BuiltOn`) VALUES
(1, 'Building of Administration', 'University Road No 1', NULL),
(3, 'Library', 'University Road No 3', NULL),
(10, 'Building of Aerospace', 'Engineering Road No 12', NULL),
(11, 'Building of Science', 'Engineering Road No 1', NULL),
(12, 'Building of Engineering', 'Engineering Road No 5', NULL),
(15, 'Building of CSSE', 'Engineering Road No 9', NULL),
(19, 'Building of Finance and Economy', 'University Road No 7', NULL),
(20, 'Building of Art', 'University Road No 6', NULL),
(30, 'Multifunctional Building', 'University Road No 10', NULL);

-- --------------------------------------------------------

--
-- 表的结构 `class`
--

DROP TABLE IF EXISTS `class`;
CREATE TABLE IF NOT EXISTS `class` (
  `ClassId` int(11) NOT NULL,
  `ClassName` varchar(30) NOT NULL,
  `ClassGrade` int(11) NOT NULL,
  `DeptId` int(11) NOT NULL,
  PRIMARY KEY (`ClassId`),
  KEY `FK_CLASS_CLASS_DEP_DEPARTME` (`DeptId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `class`
--

INSERT INTO `class` (`ClassId`, `ClassName`, `ClassGrade`, `DeptId`) VALUES
(10001, 'Aircraft Design 01', 2022, 10),
(20001, 'Software Engineering 01', 2022, 20),
(30001, 'Economy Expr. Class', 2022, 30),
(40001, 'Enterprise Management 01', 2022, 40);

-- --------------------------------------------------------

--
-- 表的结构 `commonusers`
--

DROP TABLE IF EXISTS `commonusers`;
CREATE TABLE IF NOT EXISTS `commonusers` (
  `UserId` int(11) NOT NULL,
  `UserName` varchar(50) NOT NULL,
  `ClassId` int(11) NOT NULL,
  `Gender_male` tinyint(1) NOT NULL,
  `ActivityParticipation` int(11) NOT NULL DEFAULT '0',
  `Misbehavior` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`UserId`),
  UNIQUE KEY `Index_Name` (`UserName`),
  KEY `FK_COMMONUS_COMMUSERS_CLASS` (`ClassId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `commonusers`
--

INSERT INTO `commonusers` (`UserId`, `UserName`, `ClassId`, `Gender_male`, `ActivityParticipation`, `Misbehavior`) VALUES
(123456, 'testabc', 10001, 1, 1, 0),
(22100001, 'TestUser1', 10001, 1, 2, 0),
(22100002, 'TestUser2', 10001, 0, 0, 0),
(22100005, 'a', 10001, 1, 1, 0),
(22100010, 'testu', 10001, 1, 1, 0);

-- --------------------------------------------------------

--
-- 表的结构 `departments`
--

DROP TABLE IF EXISTS `departments`;
CREATE TABLE IF NOT EXISTS `departments` (
  `DeptId` int(11) NOT NULL,
  `DeptName` varchar(100) NOT NULL,
  `DeptLeader` int(11) DEFAULT NULL,
  PRIMARY KEY (`DeptId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `departments`
--

INSERT INTO `departments` (`DeptId`, `DeptName`, `DeptLeader`) VALUES
(1, 'University\'s Administration', NULL),
(10, 'Aerospace Dept.', NULL),
(20, 'Computer Science and Software Engineering Dept.', NULL),
(30, 'Economy and Finance Dept.', NULL),
(40, 'Management Dept.', NULL);

-- --------------------------------------------------------

--
-- 表的结构 `participation`
--

DROP TABLE IF EXISTS `participation`;
CREATE TABLE IF NOT EXISTS `participation` (
  `UserId` int(11) NOT NULL,
  `ActvtId` int(11) NOT NULL,
  `Status` int(11) NOT NULL,
  PRIMARY KEY (`UserId`,`ActvtId`),
  KEY `FK_PARTICIP_PARTICIPA_ACTIVITI` (`ActvtId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `participation`
--

INSERT INTO `participation` (`UserId`, `ActvtId`, `Status`) VALUES
(123456, 10007, 1);

--
-- 触发器 `participation`
--
DROP TRIGGER IF EXISTS `activity_participation_status_change`;
DELIMITER $$
CREATE TRIGGER `activity_participation_status_change` AFTER UPDATE ON `participation` FOR EACH ROW IF OLD.Status = 0 AND NEW.Status = 1 THEN
  -- 从申请状态到批准状态
  -- 获取对应的activities记录的PeopleNumIn和PeopleNumRqrd
  SET 
  @ActvtPeopleNumIn = (SELECT PeopleNumIn FROM activities WHERE ActvtId = NEW.ActvtId),
  @ActvtPeopleNumRqrd = (SELECT PeopleNumRqrd FROM activities WHERE ActvtId = NEW.ActvtId);

  -- 检查PeopleNumIn是否小于PeopleNumRqrd
  IF @ActvtPeopleNumIn < @ActvtPeopleNumRqrd THEN
    -- 如果人数未满，则增加PeopleNumIn和ActivityParticipation
    UPDATE activities
    SET PeopleNumIn = PeopleNumIn + 1
    WHERE ActvtId = NEW.ActvtId;
      
    UPDATE commonusers
    SET ActivityParticipation = ActivityParticipation + 1
    WHERE UserId = NEW.UserId;
  ELSE
    -- 如果人数已满，则将status改为5
    UPDATE participation
    SET Status = 5
    WHERE ActvtId = NEW.ActvtId AND UserId = NEW.UserId;
  END IF;
ELSEIF OLD.Status = 1 AND NEW.Status = 10 THEN
  -- 从批准状态到未参加状态
  UPDATE commonusers
  SET ActivityParticipation = ActivityParticipation - 1,
    Misbehavior = Misbehavior + 1
  WHERE UserId = NEW.UserId;
ELSEIF OLD.Status = 1 AND NEW.Status = 11 THEN
  -- 撤回申请
  UPDATE activities
  SET PeopleNumIn = PeopleNumIn - 1
  WHERE ActvtId = NEW.ActvtId;
  
  UPDATE commonusers
  SET ActivityParticipation = ActivityParticipation - 1
  WHERE UserId = NEW.UserId;
END IF
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- 表的结构 `places`
--

DROP TABLE IF EXISTS `places`;
CREATE TABLE IF NOT EXISTS `places` (
  `PlaceId` int(11) NOT NULL,
  `PlaceName` varchar(80) NOT NULL,
  `BuildingId` int(11) NOT NULL,
  `DeptId` int(11) NOT NULL,
  PRIMARY KEY (`PlaceId`),
  KEY `FK_PLACES_DEPTINCHA_DEPARTME` (`DeptId`),
  KEY `FK_PLACES_RELATIONS_BUILDING` (`BuildingId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `places`
--

INSERT INTO `places` (`PlaceId`, `PlaceName`, `BuildingId`, `DeptId`) VALUES
(100001, 'Aerospace FG Conference Room', 10, 10),
(200002, 'IT Conference Room', 15, 20),
(300003, 'Marketing Hall', 19, 30),
(300005, 'Finance Office', 19, 30),
(400004, 'HR Training Room', 30, 40);

-- --------------------------------------------------------

--
-- 表的结构 `userinfo`
--

DROP TABLE IF EXISTS `userinfo`;
CREATE TABLE IF NOT EXISTS `userinfo` (
  `UserId` int(11) NOT NULL,
  `isAdmin` tinyint(1) NOT NULL,
  `Password` varchar(30) NOT NULL,
  `PhoneNo` varchar(11) NOT NULL,
  `Email` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`UserId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `userinfo`
--

INSERT INTO `userinfo` (`UserId`, `isAdmin`, `Password`, `PhoneNo`, `Email`) VALUES
(1, 1, '123456', '12345678901', 'SuperUser@a'),
(123456, 0, '000000', '12345678901', 'a@a'),
(22100001, 1, '000000', '12345678900', '22100001@camsuser'),
(22100002, 0, '000000', '12345678901', NULL),
(22100005, 0, '000000', '12345678901', 'a@a'),
(22100010, 0, '000000', '12345678901', '10@a');

-- --------------------------------------------------------

--
-- 替换视图以便查看 `view_activities_details`
-- （参见下面的实际视图）
--
DROP VIEW IF EXISTS `view_activities_details`;
CREATE TABLE IF NOT EXISTS `view_activities_details` (
`ActvtId` int(11)
,`ActvtTitle` varchar(150)
,`DeptName` varchar(100)
,`ActvtTime` datetime
,`PlaceName` varchar(80)
,`PeopleNumRqrd` int(11)
,`PeopleNumIn` int(11)
,`Intro` varchar(1000)
,`OtherRqrments` varchar(1000)
,`Notes` varchar(500)
,`Status` int(11)
,`PublisherId` int(11)
,`AdminName` varchar(50)
);

-- --------------------------------------------------------

--
-- 替换视图以便查看 `view_adminusersinfo`
-- （参见下面的实际视图）
--
DROP VIEW IF EXISTS `view_adminusersinfo`;
CREATE TABLE IF NOT EXISTS `view_adminusersinfo` (
`UserId` int(11)
,`AdminName` varchar(50)
,`NumActivityPublished` int(11)
,`DeptId` int(11)
,`isAdmin` tinyint(1)
,`PhoneNo` varchar(11)
,`Email` varchar(256)
);

-- --------------------------------------------------------

--
-- 替换视图以便查看 `view_commonusersinfo`
-- （参见下面的实际视图）
--
DROP VIEW IF EXISTS `view_commonusersinfo`;
CREATE TABLE IF NOT EXISTS `view_commonusersinfo` (
`UserId` int(11)
,`UserName` varchar(50)
,`ClassId` int(11)
,`Gender_male` tinyint(1)
,`ActivityParticipation` int(11)
,`Misbehavior` smallint(6)
,`isAdmin` tinyint(1)
,`PhoneNo` varchar(11)
,`Email` varchar(256)
);

-- --------------------------------------------------------

--
-- 视图结构 `view_activities_details`
--
DROP TABLE IF EXISTS `view_activities_details`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_activities_details`  AS  select `a`.`ActvtId` AS `ActvtId`,`a`.`ActvtTitle` AS `ActvtTitle`,`d`.`DeptName` AS `DeptName`,`a`.`ActvtTime` AS `ActvtTime`,`p`.`PlaceName` AS `PlaceName`,`a`.`PeopleNumRqrd` AS `PeopleNumRqrd`,`a`.`PeopleNumIn` AS `PeopleNumIn`,`a`.`Intro` AS `Intro`,`a`.`OtherRqrments` AS `OtherRqrments`,`a`.`Notes` AS `Notes`,`a`.`Status` AS `Status`,`a`.`PublisherId` AS `PublisherId`,`au`.`AdminName` AS `AdminName` from (((`activities` `a` join `departments` `d` on((`a`.`DeptId` = `d`.`DeptId`))) join `places` `p` on((`a`.`PlaceId` = `p`.`PlaceId`))) join `adminusers` `au` on((`a`.`PublisherId` = `au`.`UserId`))) ;

-- --------------------------------------------------------

--
-- 视图结构 `view_adminusersinfo`
--
DROP TABLE IF EXISTS `view_adminusersinfo`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_adminusersinfo`  AS  select `au`.`UserId` AS `UserId`,`au`.`AdminName` AS `AdminName`,`au`.`NumActivityPublished` AS `NumActivityPublished`,`au`.`DeptId` AS `DeptId`,`ui`.`isAdmin` AS `isAdmin`,`ui`.`PhoneNo` AS `PhoneNo`,`ui`.`Email` AS `Email` from (`adminusers` `au` join `userinfo` `ui` on((`au`.`UserId` = `ui`.`UserId`))) ;

-- --------------------------------------------------------

--
-- 视图结构 `view_commonusersinfo`
--
DROP TABLE IF EXISTS `view_commonusersinfo`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_commonusersinfo`  AS  select `cu`.`UserId` AS `UserId`,`cu`.`UserName` AS `UserName`,`cu`.`ClassId` AS `ClassId`,`cu`.`Gender_male` AS `Gender_male`,`cu`.`ActivityParticipation` AS `ActivityParticipation`,`cu`.`Misbehavior` AS `Misbehavior`,`ui`.`isAdmin` AS `isAdmin`,`ui`.`PhoneNo` AS `PhoneNo`,`ui`.`Email` AS `Email` from (`commonusers` `cu` join `userinfo` `ui` on((`cu`.`UserId` = `ui`.`UserId`))) ;

--
-- 限制导出的表
--

--
-- 限制表 `activities`
--
ALTER TABLE `activities`
  ADD CONSTRAINT `FK_ACTIVITI_ACTVT_PLA_PLACES` FOREIGN KEY (`PlaceId`) REFERENCES `places` (`PlaceId`),
  ADD CONSTRAINT `FK_ACTIVITI_DEPT_ACTV_DEPARTME` FOREIGN KEY (`DeptId`) REFERENCES `departments` (`DeptId`),
  ADD CONSTRAINT `FK_ACTIVITI_PUBLICATI_ADMINUSE` FOREIGN KEY (`PublisherId`) REFERENCES `adminusers` (`UserId`);

--
-- 限制表 `adminusers`
--
ALTER TABLE `adminusers`
  ADD CONSTRAINT `FK_ADMINUSE_DEPT_AU_DEPARTME` FOREIGN KEY (`DeptId`) REFERENCES `departments` (`DeptId`),
  ADD CONSTRAINT `FK_ADMINUSE_UI_AU_USERINFO` FOREIGN KEY (`UserId`) REFERENCES `userinfo` (`UserId`);

--
-- 限制表 `class`
--
ALTER TABLE `class`
  ADD CONSTRAINT `FK_CLASS_CLASS_DEP_DEPARTME` FOREIGN KEY (`DeptId`) REFERENCES `departments` (`DeptId`);

--
-- 限制表 `commonusers`
--
ALTER TABLE `commonusers`
  ADD CONSTRAINT `FK_COMMONUS_COMMUSERS_CLASS` FOREIGN KEY (`ClassId`) REFERENCES `class` (`ClassId`),
  ADD CONSTRAINT `FK_COMMONUS_UI_CU_USERINFO` FOREIGN KEY (`UserId`) REFERENCES `userinfo` (`UserId`);

--
-- 限制表 `participation`
--
ALTER TABLE `participation`
  ADD CONSTRAINT `FK_PARTICIP_PARTICIPA_ACTIVITI` FOREIGN KEY (`ActvtId`) REFERENCES `activities` (`ActvtId`),
  ADD CONSTRAINT `FK_PARTICIP_PARTICIPA_USERINFO` FOREIGN KEY (`UserId`) REFERENCES `userinfo` (`UserId`);

--
-- 限制表 `places`
--
ALTER TABLE `places`
  ADD CONSTRAINT `FK_PLACES_DEPTINCHA_DEPARTME` FOREIGN KEY (`DeptId`) REFERENCES `departments` (`DeptId`),
  ADD CONSTRAINT `FK_PLACES_RELATIONS_BUILDING` FOREIGN KEY (`BuildingId`) REFERENCES `buildings` (`BuildingId`);

DELIMITER $$
--
-- 事件
--
DROP EVENT `update_status_event`$$
CREATE DEFINER=`root`@`localhost` EVENT `update_status_event` ON SCHEDULE EVERY 1 MINUTE STARTS '2024-12-21 00:00:00' ENDS '2025-01-30 00:00:00' ON COMPLETION PRESERVE ENABLE DO UPDATE activities
SET Status = 1
WHERE ActvtTime < NOW() AND Status = 0$$

DROP EVENT `delete_status_11_event`$$
CREATE DEFINER=`root`@`localhost` EVENT `delete_status_11_event` ON SCHEDULE EVERY 1 MINUTE STARTS '2024-12-21 00:00:00' ENDS '2025-01-30 00:00:00' ON COMPLETION PRESERVE ENABLE DO DELETE FROM participation WHERE Status = 11$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
