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
-- Table structure for table `matrix`
--

DROP TABLE IF EXISTS `matrix`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `matrix` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `active` enum('y','n') NOT NULL DEFAULT 'y',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`),
  CONSTRAINT `matrix_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `matrix`
--

LOCK TABLES `matrix` WRITE;
/*!40000 ALTER TABLE `matrix` DISABLE KEYS */;
INSERT INTO `matrix` VALUES (3,1,'Matrix','n','2012-12-29 16:30:18'),(4,1,'Matrix','n','2012-12-29 16:30:58'),(5,1,'Hello','n','2012-12-29 18:57:51'),(6,1,'Hello matrix','n','2012-12-29 18:58:37'),(7,1,'Hello matrix','n','2012-12-29 18:59:05'),(8,1,'Hello matrix','n','2012-12-29 18:59:29'),(9,1,'Hello matrix','y','2012-12-29 19:01:11'),(10,1,'Hello matrix','y','2012-12-29 19:02:07'),(11,1,'My very new matrix','y','2012-12-29 19:05:00');
/*!40000 ALTER TABLE `matrix` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_owner` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `projects_key` (`name`,`user_owner`),
  KEY `user_owner` (`user_owner`),
  CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`user_owner`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `projects`
--

LOCK TABLES `projects` WRITE;
/*!40000 ALTER TABLE `projects` DISABLE KEYS */;
INSERT INTO `projects` VALUES (1,1,'Golden Project','0000-00-00 00:00:00'),(2,1,'Apple Project','0000-00-00 00:00:00'),(3,1,'Mi primer proyecto','2012-12-22 12:27:15'),(4,3,'JDEDIOS PROJECT','2012-12-23 08:24:25');
/*!40000 ALTER TABLE `projects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `login` varchar(255) NOT NULL,
  `administrator` enum('y','n') NOT NULL DEFAULT 'n',
  `passwd` varchar(255) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Nahuel Admin Velazco','nahuelsgk@gmail.com','y','6777ef5d0255bac09c11e1903e079cfd','2012-12-21 14:13:19'),(2,'Nahuel Normal Velazco','nahuel@conexionesbcn.net','n','3d3f4b83fcc8617b5c79f2d1d450c75f','2012-12-17 16:44:24'),(3,'Juan de dios','jdedios@upc.edu','n','3d3f4b83fcc8617b5c79f2d1d450c75f','2012-12-23 08:23:12');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `var`
--

DROP TABLE IF EXISTS `var`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `var` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `matrix_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `var`
--

LOCK TABLES `var` WRITE;
/*!40000 ALTER TABLE `var` DISABLE KEYS */;
INSERT INTO `var` VALUES (1,1,'a','2012-12-29 19:02:07'),(2,1,'b','2012-12-29 19:02:07'),(3,1,'SUmas','2012-12-29 19:05:00'),(4,1,'Restas','2012-12-29 19:05:00');
/*!40000 ALTER TABLE `var` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-12-30 18:50:24
