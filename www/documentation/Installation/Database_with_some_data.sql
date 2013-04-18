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
  `name` varchar(255) NOT NULL,
  `status` enum('new','open','closed') DEFAULT 'new',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `matrix`
--

LOCK TABLES `matrix` WRITE;
/*!40000 ALTER TABLE `matrix` DISABLE KEYS */;
INSERT INTO `matrix` VALUES (60,'Welcome test','new','2013-04-13 18:14:21');
/*!40000 ALTER TABLE `matrix` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `project_matrix`
--

DROP TABLE IF EXISTS `project_matrix`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `project_matrix` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `matrix_id` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uc_project_matrix` (`project_id`,`matrix_id`),
  KEY `matrix_id` (`matrix_id`),
  CONSTRAINT `project_matrix_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`),
  CONSTRAINT `project_matrix_ibfk_2` FOREIGN KEY (`matrix_id`) REFERENCES `matrix` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=175 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `project_matrix`
--

LOCK TABLES `project_matrix` WRITE;
/*!40000 ALTER TABLE `project_matrix` DISABLE KEYS */;
INSERT INTO `project_matrix` VALUES (174,172,60,'2013-04-17 10:28:54');
/*!40000 ALTER TABLE `project_matrix` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `creator` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `active` enum('y','n') DEFAULT 'y',
  PRIMARY KEY (`id`),
  KEY `creator` (`creator`),
  CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`creator`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=173 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `projects`
--

LOCK TABLES `projects` WRITE;
/*!40000 ALTER TABLE `projects` DISABLE KEYS */;
INSERT INTO `projects` VALUES (170,1,'New project','2013-04-13 18:13:53','y'),(171,1,'Nuevo proyectos','2013-04-14 07:35:57','y'),(172,1,'New project','2013-04-17 10:28:49','n');
/*!40000 ALTER TABLE `projects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `season`
--

DROP TABLE IF EXISTS `season`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `season` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `single_var_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `notes` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `single_var_id` (`single_var_id`),
  CONSTRAINT `season_ibfk_1` FOREIGN KEY (`single_var_id`) REFERENCES `single_var` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `season`
--

LOCK TABLES `season` WRITE;
/*!40000 ALTER TABLE `season` DISABLE KEYS */;
INSERT INTO `season` VALUES (34,59,'Winter','Nairobi'),(35,59,'Summer','Kenya'),(36,60,'Summer',''),(37,60,'Winter',''),(38,61,'All year',''),(39,61,'Summer','Nairobi'),(40,62,'Winter',''),(41,62,'Summer',''),(42,62,'All year',''),(43,63,'Winter','Barcelona'),(44,63,'Summer','Barcelona'),(45,63,'All year','in Congo');
/*!40000 ALTER TABLE `season` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `single_var`
--

DROP TABLE IF EXISTS `single_var`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `single_var` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `single_var`
--

LOCK TABLES `single_var` WRITE;
/*!40000 ALTER TABLE `single_var` DISABLE KEYS */;
INSERT INTO `single_var` VALUES (59,'ICH01'),(60,'ICH03'),(61,'ICH09'),(62,'E_CHOLI'),(63,'ICHN 40');
/*!40000 ALTER TABLE `single_var` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Nahuel Admin Velazco','nahuelsgk@gmail.com','y','6777ef5d0255bac09c11e1903e079cfd','2013-04-01 10:43:49'),(2,'Nahuel Normal','0','n','816fea807924bec03783b8b0847a09a5','2013-04-17 14:39:42');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `var_configuration`
--

DROP TABLE IF EXISTS `var_configuration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `var_configuration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `var_configuration`
--

LOCK TABLES `var_configuration` WRITE;
/*!40000 ALTER TABLE `var_configuration` DISABLE KEYS */;
INSERT INTO `var_configuration` VALUES (3,60),(4,60),(5,60),(6,60),(7,60),(8,60),(9,60),(10,60),(11,60);
/*!40000 ALTER TABLE `var_configuration` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `var_configuration_single_var`
--

DROP TABLE IF EXISTS `var_configuration_single_var`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `var_configuration_single_var` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `var_conf_id` int(11) NOT NULL,
  `mid` int(11) DEFAULT NULL,
  `svid` int(11) NOT NULL,
  `season_id` int(11) NOT NULL,
  `threshold` decimal(32,16) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `var_configuration_single_var`
--

LOCK TABLES `var_configuration_single_var` WRITE;
/*!40000 ALTER TABLE `var_configuration_single_var` DISABLE KEYS */;
INSERT INTO `var_configuration_single_var` VALUES (8,3,60,59,34,NULL),(9,4,60,61,39,NULL),(10,5,60,61,39,NULL),(11,6,60,60,37,NULL),(12,7,60,60,37,NULL),(13,8,60,59,0,NULL),(14,9,60,62,42,NULL),(15,10,60,59,34,NULL),(16,11,60,63,45,NULL);
/*!40000 ALTER TABLE `var_configuration_single_var` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-04-18 21:10:41
