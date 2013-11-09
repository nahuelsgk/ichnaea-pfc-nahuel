-- MySQL dump 10.13  Distrib 5.5.16, for Linux (i686)
--
-- Host: localhost    Database: ichnaea
-- ------------------------------------------------------
-- Server version	5.5.16

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `season_set_comp`
--

DROP TABLE IF EXISTS `season_set_comp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `season_set_comp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `season_id` int(11) DEFAULT NULL,
  `season_set_id` int(11) DEFAULT NULL,
  `type_comp` enum('all_year','summer','winter','spring','autumn') COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_D23B88E94EC001D1` (`season_id`),
  KEY `IDX_D23B88E9680F3AA` (`season_set_id`),
  CONSTRAINT `FK_D23B88E94EC001D1` FOREIGN KEY (`season_id`) REFERENCES `season` (`id`),
  CONSTRAINT `FK_D23B88E9680F3AA` FOREIGN KEY (`season_set_id`) REFERENCES `season_set` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `season_set_comp`
--

LOCK TABLES `season_set_comp` WRITE;
/*!40000 ALTER TABLE `season_set_comp` DISABLE KEYS */;
INSERT INTO `season_set_comp` VALUES (24,79,39,'summer'),(25,80,39,'winter'),(26,81,40,'summer'),(27,82,40,'winter'),(28,83,41,'summer'),(29,84,41,'winter'),(30,85,42,'summer'),(31,86,42,'winter'),(34,89,44,'summer'),(35,90,44,'winter'),(36,91,45,'summer'),(37,92,45,'winter'),(38,93,46,'summer'),(39,94,46,'winter'),(40,95,47,'summer'),(41,96,47,'winter'),(42,97,48,'summer'),(43,98,48,'winter'),(44,99,49,'summer'),(45,100,49,'winter'),(46,101,50,'summer'),(47,102,50,'winter'),(48,103,51,'summer'),(50,105,51,'winter'),(51,106,52,'summer'),(52,107,52,'winter'),(53,108,53,'summer'),(54,109,53,'winter'),(55,110,54,'summer'),(56,111,54,'winter'),(57,112,55,'summer'),(58,113,55,'winter'),(59,114,56,'summer'),(60,115,56,'winter'),(61,116,57,'summer'),(62,117,57,'winter');
/*!40000 ALTER TABLE `season_set_comp` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-11-09 21:52:04
