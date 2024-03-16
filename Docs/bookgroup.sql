-- MySQL dump 10.13  Distrib 8.0.25, for Win64 (x86_64)
--
-- Host: localhost    Database: bookgroup
-- ------------------------------------------------------
-- Server version	8.0.25

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `books`
--

DROP TABLE IF EXISTS `books`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `books` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `Title` varchar(128) DEFAULT NULL,
  `Author` varchar(64) DEFAULT NULL,
  `AmazonLink` varchar(1024) DEFAULT NULL,
  `SuggestedBy` int DEFAULT NULL,
  `ChosenBy` int DEFAULT NULL,
  `DateEntered` datetime DEFAULT NULL,
  `Status` tinyint NOT NULL DEFAULT '0',
  `KindlePrice` decimal(4,2) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `IDX_SuggestedBy` (`SuggestedBy`),
  KEY `Books_ibfk_2` (`ChosenBy`),
  CONSTRAINT `books_ibfk_1` FOREIGN KEY (`SuggestedBy`) REFERENCES `members` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `books_ibfk_2` FOREIGN KEY (`ChosenBy`) REFERENCES `members` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=203 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `calendar`
--

DROP TABLE IF EXISTS `calendar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `calendar` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `MeetingDate` datetime DEFAULT NULL,
  `Host` int DEFAULT NULL,
  `ptVenues` int DEFAULT NULL,
  `ptBooks` int DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `IX_Host` (`Host`),
  KEY `IX_ptBooks` (`ptBooks`),
  KEY `IX_ptVenues` (`ptVenues`),
  CONSTRAINT `calendar_ibfk_1` FOREIGN KEY (`Host`) REFERENCES `members` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `calendar_ibfk_2` FOREIGN KEY (`ptBooks`) REFERENCES `books` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `calendar_ibfk_3` FOREIGN KEY (`ptVenues`) REFERENCES `venues` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=130 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `forget_password`
--

DROP TABLE IF EXISTS `forget_password`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `forget_password` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(200) NOT NULL DEFAULT '',
  `temp_key` varchar(200) NOT NULL DEFAULT '',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `logins`
--

DROP TABLE IF EXISTS `logins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `logins` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `ptUser` int NOT NULL,
  `LoggedIn` datetime DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `fk_logins_user` (`ptUser`),
  CONSTRAINT `fk_logins_user` FOREIGN KEY (`ptUser`) REFERENCES `members` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `members`
--

DROP TABLE IF EXISTS `members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `members` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `password` varchar(255) DEFAULT NULL,
  `Forename` varchar(32) DEFAULT NULL,
  `Surname` varchar(32) DEFAULT NULL,
  `KnownAs` varchar(32) NOT NULL,
  `Login` varchar(16) DEFAULT NULL,
  `Address1` varchar(64) DEFAULT NULL,
  `Address2` varchar(32) DEFAULT NULL,
  `City` varchar(24) DEFAULT NULL,
  `Postcode` varchar(12) DEFAULT NULL,
  `Telephone` varchar(16) DEFAULT NULL,
  `Mobile` varchar(16) DEFAULT NULL,
  `Email` varchar(64) DEFAULT NULL,
  `LastLoggedIn` datetime DEFAULT NULL,
  `DateJoined` datetime DEFAULT NULL,
  `DateLeft` datetime DEFAULT NULL,
  `Deleted` tinyint NOT NULL DEFAULT '0',
  `SuggestionsOnly` tinyint NOT NULL DEFAULT '0',
  `Admin` tinyint NOT NULL DEFAULT '0' COMMENT '0=normal, 1=admin, 2=superadmin',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `IDX_KnownAs` (`KnownAs`),
  UNIQUE KEY `IDX_Login` (`Login`),
  UNIQUE KEY `idxEmail` (`Email`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `members`
--

LOCK TABLES `members` WRITE;
/*!40000 ALTER TABLE `members` DISABLE KEYS */;
INSERT INTO `members` VALUES (1,'$2y$10$UxbWXDIpAyRD4nCAf2h8ieIHCetaXUK564Ot4UIIsRgL/jtIUNHb6','','Admin','Admin','admin','','','','','','','admin@gmail.com',NULL,NULL,NULL,0,0,2);
/*!40000 ALTER TABLE `members` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `venues`
--

DROP TABLE IF EXISTS `venues`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `venues` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `VenueName` varchar(48) DEFAULT NULL,
  `Website` varchar(64) DEFAULT NULL,
  `Address1` varchar(64) DEFAULT NULL,
  `Address2` varchar(32) DEFAULT NULL,
  `City` varchar(24) DEFAULT NULL,
  `Postcode` varchar(12) DEFAULT NULL,
  `Telephone` varchar(16) DEFAULT NULL,
  `Email` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Temporary view structure for view `vwcalendar`
--

DROP TABLE IF EXISTS `vwcalendar`;
/*!50001 DROP VIEW IF EXISTS `vwcalendar`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vwcalendar` AS SELECT 
 1 AS `MeetingDate`,
 1 AS `MeetingDateFmt`,
 1 AS `MeetingTime`,
 1 AS `KnownAs`,
 1 AS `Title`,
 1 AS `Author`,
 1 AS `VenueName`,
 1 AS `ID`,
 1 AS `Website`,
 1 AS `AmazonLink`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vwcalendaradmin`
--

DROP TABLE IF EXISTS `vwcalendaradmin`;
/*!50001 DROP VIEW IF EXISTS `vwcalendaradmin`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vwcalendaradmin` AS SELECT 
 1 AS `ID`,
 1 AS `MeetingDate`,
 1 AS `Host`,
 1 AS `Venue`,
 1 AS `Book`,
 1 AS `SuggestedBy`,
 1 AS `ChosenBy`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vwknownas`
--

DROP TABLE IF EXISTS `vwknownas`;
/*!50001 DROP VIEW IF EXISTS `vwknownas`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vwknownas` AS SELECT 
 1 AS `ID`,
 1 AS `KnownAs`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vwlogins`
--

DROP TABLE IF EXISTS `vwlogins`;
/*!50001 DROP VIEW IF EXISTS `vwlogins`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vwlogins` AS SELECT 
 1 AS `Name`,
 1 AS `LoggedIn`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vwsuggestions`
--

DROP TABLE IF EXISTS `vwsuggestions`;
/*!50001 DROP VIEW IF EXISTS `vwsuggestions`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vwsuggestions` AS SELECT 
 1 AS `ID`,
 1 AS `Title`,
 1 AS `Author`,
 1 AS `AmazonLink`,
 1 AS `DateEntered`,
 1 AS `KnownAs`,
 1 AS `Status`,
 1 AS `KindlePrice`,
 1 AS `SortDate`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vwsuggestionsadmin`
--

DROP TABLE IF EXISTS `vwsuggestionsadmin`;
/*!50001 DROP VIEW IF EXISTS `vwsuggestionsadmin`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vwsuggestionsadmin` AS SELECT 
 1 AS `ID`,
 1 AS `Title`,
 1 AS `Author`,
 1 AS `DateEntered`,
 1 AS `SuggestedBy`,
 1 AS `ChosenBy`,
 1 AS `DateRead`,
 1 AS `Status`*/;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `vwcalendar`
--

/*!50001 DROP VIEW IF EXISTS `vwcalendar`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_520_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50001 VIEW `vwcalendar` AS select `c`.`MeetingDate` AS `MeetingDate`,date_format(`c`.`MeetingDate`,'%d/%m/%y') AS `MeetingDateFmt`,date_format(`c`.`MeetingDate`,'%H:%i') AS `MeetingTime`,`m`.`KnownAs` AS `KnownAs`,`b`.`Title` AS `Title`,`b`.`Author` AS `Author`,`v`.`VenueName` AS `VenueName`,`c`.`ID` AS `ID`,`v`.`Website` AS `Website`,`b`.`AmazonLink` AS `AmazonLink` from (((`calendar` `c` left join `members` `m` on((`c`.`Host` = `m`.`ID`))) left join `books` `b` on((`c`.`ptBooks` = `b`.`ID`))) left join `venues` `v` on((`c`.`ptVenues` = `v`.`ID`))) order by `c`.`MeetingDate` desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vwcalendaradmin`
--

/*!50001 DROP VIEW IF EXISTS `vwcalendaradmin`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_520_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50001 VIEW `vwcalendaradmin` AS select `c`.`ID` AS `ID`,`c`.`MeetingDate` AS `MeetingDate`,(select `members`.`KnownAs` from `members` where (`members`.`ID` = `c`.`Host`)) AS `Host`,(select `venues`.`VenueName` from `venues` where (`venues`.`ID` = `c`.`ptVenues`)) AS `Venue`,`b`.`Title` AS `Book`,`m`.`KnownAs` AS `SuggestedBy`,`m2`.`KnownAs` AS `ChosenBy` from (((`calendar` `c` left join `books` `b` on((`c`.`ptBooks` = `b`.`ID`))) left join `members` `m` on((`b`.`SuggestedBy` = `m`.`ID`))) left join `members` `m2` on((`b`.`ChosenBy` = `m2`.`ID`))) where ((year(now()) - year(`c`.`MeetingDate`)) <= 1) order by `c`.`MeetingDate` desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vwknownas`
--

/*!50001 DROP VIEW IF EXISTS `vwknownas`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_520_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50001 VIEW `vwknownas` AS select `members`.`ID` AS `ID`,`members`.`KnownAs` AS `KnownAs` from `members` where (`members`.`Deleted` = 0) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vwlogins`
--

/*!50001 DROP VIEW IF EXISTS `vwlogins`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_520_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50001 VIEW `vwlogins` AS select concat_ws(' ',`m`.`Forename`,`m`.`Surname`) AS `Name`,`l`.`LoggedIn` AS `LoggedIn` from (`logins` `l` join `members` `m` on((`l`.`ptUser` = `m`.`ID`))) order by `l`.`LoggedIn` desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vwsuggestions`
--

/*!50001 DROP VIEW IF EXISTS `vwsuggestions`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_520_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50001 VIEW `vwsuggestions` AS select `b`.`ID` AS `ID`,`b`.`Title` AS `Title`,`b`.`Author` AS `Author`,`b`.`AmazonLink` AS `AmazonLink`,date_format(`b`.`DateEntered`,'%d/%m/%y') AS `DateEntered`,`m`.`KnownAs` AS `KnownAs`,`b`.`Status` AS `Status`,`b`.`KindlePrice` AS `KindlePrice`,`b`.`DateEntered` AS `SortDate` from (`books` `b` left join `members` `m` on((`b`.`SuggestedBy` = `m`.`ID`))) where ((`b`.`Status` < 2) and ((to_days(now()) - to_days(`b`.`DateEntered`)) < 730) and (`b`.`ChosenBy` is null)) order by unix_timestamp(`b`.`DateEntered`) desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vwsuggestionsadmin`
--

/*!50001 DROP VIEW IF EXISTS `vwsuggestionsadmin`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_520_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50001 VIEW `vwsuggestionsadmin` AS select `b`.`ID` AS `ID`,`b`.`Title` AS `Title`,`b`.`Author` AS `Author`,`b`.`DateEntered` AS `DateEntered`,`m1`.`KnownAs` AS `SuggestedBy`,`m2`.`KnownAs` AS `ChosenBy`,`c`.`MeetingDate` AS `DateRead`,`b`.`Status` AS `Status` from (((`books` `b` left join `members` `m1` on((`b`.`SuggestedBy` = `m1`.`ID`))) left join `members` `m2` on((`b`.`ChosenBy` = `m2`.`ID`))) left join `calendar` `c` on((`b`.`ID` = `c`.`ptBooks`))) order by unix_timestamp(`b`.`DateEntered`) desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-03-16  8:53:58
