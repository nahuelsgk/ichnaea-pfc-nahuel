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
-- Table structure for table `season_set`
--

DROP TABLE IF EXISTS `season_set`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `season_set` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `variable_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_7C5A6F0CF3037E8E` (`variable_id`),
  CONSTRAINT `FK_7C5A6F0CF3037E8E` FOREIGN KEY (`variable_id`) REFERENCES `variable` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `season_set`
--

LOCK TABLES `season_set` WRITE;
/*!40000 ALTER TABLE `season_set` DISABLE KEYS */;
INSERT INTO `season_set` VALUES (39,11,'FC Season Set'),(40,12,'FE Season Set'),(41,13,'CL Season Set'),(42,14,'SOMCPH Season Set'),(44,18,'FRNAPH II Default Season Set'),(45,19,'FRNAPH III Default Season Set'),(46,20,'FRNAPH IV Default Season Set'),(47,21,'RYC2056 Season Set'),(48,36,'HBSA-Y Default Season Set'),(49,32,'GA 17 Default Season Set'),(50,16,'FRNAPH Default Season'),(51,17,'FRNAPH I Default Season Set'),(52,26,'DiE Default Season'),(53,27,'FM-FS Default Season'),(54,28,'Hir Default Season'),(55,29,'DiC Default Season'),(56,30,'ECP Default Season'),(57,37,'HBSA-T Default Season');
/*!40000 ALTER TABLE `season_set` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-11-09 21:51:55
