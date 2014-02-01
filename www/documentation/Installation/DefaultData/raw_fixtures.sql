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
-- Table structure for table `variable`
--

DROP TABLE IF EXISTS `variable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `variable` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `variable`
--

LOCK TABLES `variable` WRITE;
/*!40000 ALTER TABLE `variable` DISABLE KEYS */;
INSERT INTO `variable` VALUES (11,'FC','FC'),(12,'FE','FE'),(13,'CL','CL'),(14,'SOMCPH','SOMCPH'),(16,'FRNAPH','FRNAPH'),(17,'FRNAPH.I','FRNAPH.I'),(18,'FRNAPH.II','FRNAPH.II'),(19,'FRNAPH.III','FRNAPH.III'),(20,'FRNAPH.IV','FRNAPH.IV'),(21,'RYC2056','RYC2056'),(26,'DiE','DiE'),(27,'FM-FS','FM-FS'),(28,'Hir','Hir'),(29,'DiC','DiC'),(30,'ECP','ECP'),(32,'GA17','GA17'),(36,'HBSA.Y','HBSA.Y'),(37,'HBSA.T','HBSA.T');
/*!40000 ALTER TABLE `variable` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `season`
--

DROP TABLE IF EXISTS `season`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `season` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `content` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=118 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `season`
--

LOCK TABLES `season` WRITE;
/*!40000 ALTER TABLE `season` DISABLE KEYS */;
INSERT INTO `season` VALUES (79,'envFC-Estiu.txt',NULL,'2013-11-09','2013-11-09','# Envelliment FC Estiu (3 assajos)\r\n\r\n0    5.61 \r\n24   4.81\r\n48   5.34\r\n72   3.58\r\n168  2.30\r\n\r\n\r\n0    5.38\r\n24   5.23\r\n72   4.20\r\n144  3.59\r\n192  2.41\r\n\r\n\r\n0    6.59\r\n24   5.60\r\n72   4.20\r\n144  3.97\r\n168  2.80\r\n'),(80,'envFC-Hivern.txt',NULL,'2013-11-09','2013-11-09','# Envelliment FC Hivern (6 assajos)\r\n0    5.45 \r\n24   5.40\r\n48   5.34\r\n72   5.11\r\n168  3.74\r\n216  3.70\r\n240  3.65\r\n\r\n\r\n0    5.54\r\n24   5.48\r\n48   5.43\r\n120  3.91\r\n144  3.86\r\n168  3.70\r\n265  3.53\r\n\r\n\r\n0    6.41\r\n48   5.61\r\n192  4.85\r\n336  4.04\r\n\r\n\r\n0    5.99\r\n48   5.95\r\n144  5.00\r\n288  4.91\r\n360  3.60\r\n\r\n\r\n0    6.18\r\n48   6.32\r\n72   5.76\r\n96   4.92\r\n168  5.06\r\n216  3.60\r\n\r\n\r\n0    5.47\r\n24   5.25\r\n48   5.49\r\n72   4.94\r\n96   4.77\r\n168  3.08\r\n'),(81,'envFE-Estiu.txt',NULL,'2013-11-09','2013-11-09','# Envelliment FC Estiu (7 assajos)\r\n\r\n0    4.34 \r\n24   3.80\r\n48   3.00\r\n72   2.00\r\n\r\n0    4.63 \r\n24   4.04\r\n48   3.00\r\n72   2.90\r\n\r\n0    4.65 \r\n24   4.23\r\n72   3.38\r\n168  2.78\r\n\r\n0    4.26\r\n24   3.54\r\n72   2.26\r\n192  2.23\r\n\r\n0    5.50\r\n24   4.60\r\n72   3.91\r\n144  3.17\r\n168  3.04\r\n\r\n0    4.71 \r\n24   4.51\r\n48   2.78\r\n72   2.49\r\n\r\n0    4.70 \r\n24   3.70\r\n48   3.13\r\n72   2.41\r\n'),(82,'envFE-Hivern.txt',NULL,'2013-11-09','2013-11-09','# Envelliment FE Hivern (6 assajos)\r\n# He fet un fit lineal dels 4 primers, doncs els altres 2 ténen pujades\r\n\r\n0    4.60 \r\n24   4.40\r\n48   4.28\r\n72   4.04\r\n168  3.18\r\n240  2.62\r\n\r\n\r\n0    4.94\r\n24   4.40\r\n264  3.23\r\n\r\n\r\n0    4.94\r\n48   4.90\r\n144  4.56\r\n288  4.28\r\n360  3.23\r\n\r\n\r\n0    4.29\r\n24   4.04\r\n48   4.18\r\n72   3.88\r\n96   3.82\r\n168  2.40\r\n\r\n\r\n# 0    5.02\r\n# 48   4.17\r\n# 72   4.42\r\n# 96   3.94\r\n# 168  3.84\r\n# 216  1.70\r\n\r\n\r\n# 0    4.47\r\n# 24   4.05\r\n# 48   3.77\r\n# 72   3.04\r\n# 96   3.28\r\n# 216  2.30\r\n'),(83,'envCL-Estiu.txt',NULL,'2013-11-09','2013-11-09','# Envelliment CL Estiu (4 assajos)\r\n\r\n0    3.76 \r\n24   3.58\r\n48   3.45\r\n72   3.30\r\n144  3.26\r\n192  3.00\r\n\r\n\r\n0    3.70\r\n24   3.62\r\n48   3.38\r\n144  3.15\r\n192  3.00\r\n\r\n\r\n0    4.65\r\n24   4.23\r\n72   3.38\r\n168  2.78\r\n\r\n\r\n0    4.38\r\n24   4.32\r\n72   4.08\r\n144  3.00\r\n192  2.78\r\n'),(84,'envCL-Hivern.txt',NULL,'2013-11-09','2013-11-09','# Envelliment CL Hivern (4 assajos)\r\n\r\n0    4.08 \r\n24   4.04\r\n48   4.00\r\n72   3.95\r\n168  3.65\r\n216  3.15\r\n240  3.00\r\n\r\n\r\n0    3.90\r\n24   3.78\r\n48   3.60\r\n144  3.56\r\n168  3.48\r\n264  2.95\r\n\r\n\r\n0    5.73\r\n48   5.64\r\n192  5.58\r\n336  5.46\r\n\r\n\r\n0    5.34\r\n48   5.15\r\n144  5.00\r\n288  5.00\r\n360  4.69\r\n\r\n'),(85,'envSOMCPH-Estiu.txt',NULL,'2013-11-09','2013-11-09','# Envelliment SOMCPH Estiu (6 assajos)\r\n\r\n0    4.48 \r\n24   4.52\r\n72   4.00\r\n168  3.00\r\n\r\n0    5.19 \r\n24   4.90\r\n72   4.76\r\n144  3.81\r\n192  3.11\r\n\r\n0    5.30 \r\n24   4.92\r\n72   4.81\r\n144  3.98\r\n168  3.46\r\n\r\n0    4.50\r\n24   4.10\r\n72   3.20\r\n168  1.90\r\n\r\n0    4.30 \r\n24   4.30\r\n72   3.90\r\n144  2.90\r\n192  2.70\r\n\r\n0    5.40\r\n24   4.80\r\n48   4.30\r\n72   4.10\r\n96   3.80\r\n168  3.10\r\n\r\n'),(86,'envSOMCPH-Hivern.txt',NULL,'2013-11-09','2013-11-09','# Envelliment SOMCPH Hivern (4 assajos)\r\n\r\n0    6.41 \r\n48   6.41\r\n192  6.08\r\n336  6.00\r\n\r\n\r\n0    5.23\r\n48   5.15\r\n144  5.15\r\n288  4.32\r\n360  4.11\r\n\r\n\r\n0    4.40\r\n24   4.20\r\n72   3.90\r\n168  3.60\r\n\r\n\r\n0    5.30\r\n24   5.10\r\n48   4.90\r\n72   4.70\r\n96   4.60\r\n168  4.40\r\n\r\n'),(89,'envFRNAPH.II-Estiu.txt',NULL,'2013-11-09','2013-11-09','# Envelliment FRNAPH-II Estiu (2 assajos)\r\n\r\n0    3.50 \r\n24   3.10\r\n48   2.70\r\n72   2.20\r\n\r\n0    3.50 \r\n24   3.30\r\n48   2.50\r\n72   2.40\r\n'),(90,'envFRNAPH.II-Hivern.txt',NULL,'2013-11-09','2013-11-09','# Envelliment FRNAPH-II Hivern (2 assajos)\r\n\r\n0    4.00 \r\n48   3.85\r\n144  3.54\r\n288  3.08\r\n360  2.85\r\n\r\n\r\n0    3.80\r\n48   3.66\r\n144  3.37\r\n288  2.94\r\n360  2.72\r\n\r\n'),(91,'envFRNAPH.III-Estiu.txt',NULL,'2013-11-09','2013-11-09','# Envelliment FRNAPH-III Estiu (2 assajos)\r\n\r\n0    3.40 \r\n24   2.90\r\n48   2.30\r\n72   1.80\r\n\r\n0    3.60 \r\n24   2.70\r\n48   2.00\r\n72   1.90\r\n'),(92,'envFRNAPH.III-Hivern.txt',NULL,'2013-11-09','2013-11-09','# Envelliment FRNAPH-III Hivern (2 assajos)\r\n\r\n0    3.60 \r\n48   3.16\r\n144  2.29\r\n288  0.98\r\n360  0.32\r\n\r\n\r\n0    3.70\r\n48   3.27\r\n144  2.40\r\n288  1.11\r\n360  0.46\r\n\r\n'),(93,'envFRNAPH.IV-Estiu.txt',NULL,'2013-11-09','2013-11-09','# Envelliment FRNAPH-IV Estiu (2 assajos)\r\n\r\n0    2.90 \r\n24   2.40\r\n48   1.90\r\n72   1.50\r\n\r\n0    3.00 \r\n24   1.90\r\n48   1.60\r\n72   1.30\r\n'),(94,'envFRNAPH.IV-Hivern.txt',NULL,'2013-11-09','2013-11-09','# Envelliment FRNAPH-IV Hivern (2 assajos)\r\n\r\n0    3.10 \r\n48   2.75\r\n144  2.06\r\n288  1.03\r\n360  0.51\r\n\r\n\r\n0    3.30\r\n48   2.99\r\n144  2.32\r\n288  1.31\r\n360  0.81\r\n\r\n'),(95,'envRYC2056-Estiu.txt',NULL,'2013-11-09','2013-11-09','# Envelliment RYC2056 Estiu (4 assajos)\r\n\r\n0    3.48 \r\n24   3.41\r\n72   3.11\r\n168  1.48\r\n\r\n0    5.62 \r\n24   5.38\r\n72   5.36\r\n144  4.53\r\n192  4.36\r\n\r\n0    4.26 \r\n24   3.52\r\n72   2.90\r\n192  2.60\r\n\r\n0    6.32 \r\n24   6.15\r\n72   5.67\r\n144  5.26\r\n168  4.18\r\n\r\n\r\n\r\n\r\n\r\n'),(96,'envRYC2056-Hivern.txt',NULL,'2013-11-09','2013-11-09','# Envelliment RYC2056 Hivern (1 assaig)\r\n\r\n0    6.62 \r\n48   6.60\r\n144  6.15\r\n288  6.00\r\n360  5.80\r\n'),(97,'envHBSA.Y-Estiu.txt',NULL,'2013-11-09','2013-11-09','# Envelliment SFBIF Estiu (2 assajos)\r\n# Al primer assaig hem posat 1.5 on deia <3 i hem eliminat l\'entrada de 72 hores\r\n\r\n0    5.40 \r\n24   3.00\r\n48   1.50\r\n\r\n0    5.30\r\n24   4.38\r\n\r\n'),(98,'envHBSA.Y-Hivern.txt',NULL,'2013-11-09','2013-11-09','# Envelliment SFBIF Hivern (2 assajos)\r\n\r\n0    5.70 \r\n24   4.00\r\n48   3.67\r\n72   3.18\r\n\r\n\r\n0    5.30\r\n24   4.38\r\n48   3.60\r\n\r\n'),(99,'envGA17-Estiu.txt',NULL,'2013-11-09','2013-11-09','# Envelliment BTHPH Estiu (2 assajos)\r\n\r\n0    3.50 \r\n24   3.10\r\n48   2.60\r\n72   1.90\r\n96   1.50\r\n168  1.00\r\n\r\n\r\n0    3.50\r\n24   2.80\r\n48   2.40\r\n72   2.00\r\n96   1.70\r\n168  1.40\r\n\r\n\r\n'),(100,'envGA17-Hivern.txt',NULL,'2013-11-09','2013-11-09','# Envelliment BTHPH Hivern (2 assajos)\r\n\r\n0    3.50 \r\n24   3.50\r\n48   3.50\r\n72   3.00\r\n96   2.70\r\n168  2.60\r\n\r\n\r\n0    3.60\r\n24   3.60\r\n48   3.50\r\n72   3.10\r\n96   3.00\r\n168  2.60\r\n\r\n\r\n'),(101,'envFRNAPH-Estiu.txt',NULL,'2013-11-09','2013-11-09','# Envelliment FRNAPH Estiu (4 assajos)\r\n# Hem posat 0.5 a on deia <1 (deia <100, però crec que és un error)\r\n\r\n0    4.54 \r\n24   3.00\r\n72   2.48\r\n168  0.50\r\n\r\n0    4.00 \r\n24   3.30\r\n72   3.28\r\n144  2.00\r\n192  0.50\r\n\r\n0    4.26 \r\n24   3.52\r\n72   2.90\r\n\r\n0    4.66 \r\n24   3.57\r\n72   2.30\r\n144  2.00\r\n168  0.50\r\n'),(102,'envFRNAPH-Hivern.txt',NULL,'2013-11-09','2013-11-09','# Envelliment FRNAPH Hivern (2 assajos)\r\n\r\n0    6.67 \r\n48   6.65\r\n192  5.97\r\n336  5.15\r\n\r\n\r\n0    4.98\r\n48   4.92\r\n144  4.83\r\n288  3.94\r\n360  3.72\r\n\r\n'),(103,'envFRNAPH.I-Estiu.txt',NULL,'2013-11-09','2013-11-09','# Envelliment FRNAPH-I Estiu (2 assajos)\r\n\r\n0    3.00 \r\n24   2.70\r\n48   2.60\r\n72   2.00\r\n\r\n0    3.10 \r\n24   2.50\r\n48   2.30\r\n72   2.30\r\n'),(105,'envFRNAPH.I-Hivern.txt',NULL,'2013-11-09','2013-11-09','# Envelliment FRNAPH-I Hivern (2 assajos)\r\n\r\n0    3.20 \r\n48   3.15\r\n144  3.04\r\n288  2.88\r\n360  2.80\r\n\r\n\r\n0    3.10\r\n48   3.04\r\n144  2.93\r\n288  2.75\r\n360  2.67\r\n\r\n'),(106,'envDiE-Estiu.txt',NULL,'2013-11-09','2013-11-09','# Envelliment DiE Estiu (2 assajos)\r\n# ULL que les corbes pujen i no baixen !!!\r\n\r\n0    0.935\r\n24   0.975\r\n72   0.909\r\n\r\n0    0.946\r\n24   0.949\r\n72   0.815\r\n'),(107,'envDiE-Hivern.txt',NULL,'2013-11-09','2013-11-09','# Envelliment DiE Hivern (2 assajos)\r\n\r\n0    0.978\r\n120  0.982\r\n168  0.946\r\n\r\n0    0.982\r\n48   0.938\r\n96   0.906\r\n'),(108,'envFMFS-Estiu.txt',NULL,'2013-11-09','2013-11-09','# Envelliment FMFS Estiu (2 assajos)\r\n\r\n0    70.8 \r\n24   58.8 \r\n72   56.0 \r\n\r\n0    66.6 \r\n24   58.3 \r\n72   54.1 \r\n'),(109,'envFMFS-Hivern.txt',NULL,'2013-11-09','2013-11-09','# Envelliment FMFS Hivern (2 assajos)\r\n# ULL que les corbes pujen i no baixen !!!\r\n\r\n0    54.1 \r\n120  66.6 \r\n168  50.0 \r\n\r\n0    54.1 \r\n48   54.1\r\n96   50.0\r\n\r\n'),(110,'envHiR-Estiu.txt',NULL,'2013-11-09','2013-11-09','# Envelliment HiR Estiu (2 assajos)\r\n# ULL que les corbes pujen i no baixen !!!\r\n\r\n0    8.33 \r\n24   12.5 \r\n72   37.0 \r\n\r\n0    12.5 \r\n24   25.0 \r\n72   45.8 \r\n'),(111,'envHiR-Hivern.txt',NULL,'2013-11-09','2013-11-09','# Envelliment HiR Hivern (2 assajos)\r\n\r\n0    20.8 \r\n120  12.5 \r\n168  33.3 \r\n\r\n0    29.1 \r\n48   29.1\r\n96   45.8\r\n'),(112,'envDiC-Estiu.txt',NULL,'2013-11-09','2013-11-09','# Envelliment DiC Estiu (2 assajos)\r\n# ULL que les corbes pujen i no baixen !!!\r\n\r\n0    0.819\r\n72   0.917\r\n96   0.949\r\n\r\n0    0.866\r\n48   0.946\r\n96   0.964\r\n'),(113,'envDiC-Hivern.txt',NULL,'2013-11-09','2013-11-09','# Envelliment DiC Hivern (2 assajos)\r\n\r\n0    0.833\r\n120  0.960\r\n168  0.957\r\n\r\n0    0.982\r\n120  0.953\r\n168  0.913\r\n'),(114,'envECP-Estiu.txt',NULL,'2013-11-09','2013-11-09','# Envelliment ECP Estiu (2 assajos)\r\n# ULL que les corbes pujen i no baixen !!!\r\n\r\n0    12.5 \r\n24   20.8 \r\n72   16.6 \r\n\r\n0    8.30 \r\n24   33.3 \r\n72   41.6 \r\n'),(115,'envECP-Hivern.txt',NULL,'2013-11-09','2013-11-09','# Envelliment ECP Hivern (2 assajos)\r\n\r\n0    20.8\r\n120  12.5\r\n168  12.5\r\n\r\n0    16.6 \r\n48   20.8 \r\n96   25.0\r\n \r\n'),(116,'envHBSA.T-Estiu.txt',NULL,'2013-11-09','2013-11-09','# Envelliment TBIF Estiu (2 assajos)\r\n# Al primer assaig hem posat 1.5 on deia <3 i hem eliminat l\'entrada de 72 hores\r\n\r\n0    6.40 \r\n24   3.90\r\n48   1.50\r\n\r\n0    5.90\r\n24   4.84\r\n\r\n'),(117,'envHBSA.T-Hivern.txt',NULL,'2013-11-09','2013-11-09','# Envelliment TBIF Hivern (2 assajos)\r\n\r\n0    6.40 \r\n24   4.90\r\n48   4.60\r\n72   4.18\r\n\r\n\r\n0    5.90\r\n24   4.84\r\n48   4.30\r\n120  3.30\r\n\r\n');
/*!40000 ALTER TABLE `season` ENABLE KEYS */;
UNLOCK TABLES;

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

--
-- Table structure for table `matrix`
--

DROP TABLE IF EXISTS `matrix`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `matrix` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `owner_id` int(11) DEFAULT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `IDX_F83341CF7E3C61F9` (`owner_id`),
  CONSTRAINT `FK_F83341CF7E3C61F9` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `matrix`
--

LOCK TABLES `matrix` WRITE;
/*!40000 ALTER TABLE `matrix` DISABLE KEYS */;
INSERT INTO `matrix` VALUES (14,'Cyprus',25,1);
/*!40000 ALTER TABLE `matrix` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `variable_matrix_config`
--

DROP TABLE IF EXISTS `variable_matrix_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `variable_matrix_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `matrix_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `seasonSet` int(11) DEFAULT NULL,
  `variable_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_3504F63BAA000BE7` (`matrix_id`),
  KEY `IDX_3504F63BF6D8A7DD` (`seasonSet`),
  KEY `IDX_3504F63BF3037E8E` (`variable_id`),
  CONSTRAINT `FK_3504F63BAA000BE7` FOREIGN KEY (`matrix_id`) REFERENCES `matrix` (`id`),
  CONSTRAINT `FK_3504F63BF3037E8E` FOREIGN KEY (`variable_id`) REFERENCES `variable` (`id`),
  CONSTRAINT `FK_3504F63BF6D8A7DD` FOREIGN KEY (`seasonSet`) REFERENCES `season_set` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=379 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `variable_matrix_config`
--

LOCK TABLES `variable_matrix_config` WRITE;
/*!40000 ALTER TABLE `variable_matrix_config` DISABLE KEYS */;
INSERT INTO `variable_matrix_config` VALUES (352,14,'FC alias xy',39,11),(353,14,'FE alias bb',40,12),(354,14,'CL alias b',41,13),(355,14,'SOMCPH Alias',42,14),(356,14,'FTOTAL Alias ',NULL,NULL),(357,14,'FRNAPH Alias',50,16),(358,14,'FRNAPH I Alias',51,17),(359,14,'FRNAPH II',44,18),(360,14,'FRNAPH III',45,19),(361,14,'FRNAPH IV',46,20),(362,14,'RYC2056 Alias',47,21),(363,14,'COP Alias',NULL,NULL),(364,14,'ETHYLCOP Alias',NULL,NULL),(365,14,'EPICOP Alias',NULL,NULL),(366,14,'CHOL Alias',NULL,NULL),(367,14,'DiE Alias',52,26),(368,14,'FM-FS Alias',53,27),(369,14,'Hir Alias',54,28),(370,14,'DiC Alias',55,29),(371,14,'ECP Alias',56,30),(372,14,'ECT Alias',NULL,NULL),(373,14,'GA17 Alias',49,32),(374,14,'Dentium Alias',NULL,NULL),(375,14,'Adolescentis Alias',NULL,NULL),(376,14,'DA Alias',NULL,NULL),(377,14,'HBSA-Y Alias',48,36),(378,14,'HBSA-T Alias',57,37);
/*!40000 ALTER TABLE `variable_matrix_config` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-02-01 18:11:00
