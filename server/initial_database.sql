-- MySQL dump 10.13  Distrib 5.1.59, for apple-darwin10.8.0 (i386)
--
-- Host: localhost    Database: activitystreams
-- ------------------------------------------------------
-- Server version	5.1.59-log

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
-- Table structure for table `activities`
--

DROP TABLE IF EXISTS `activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activities` (
  `id` varchar(32) NOT NULL,
  `application_id` varchar(32) NOT NULL,
  `stream_id` varchar(32) NOT NULL,
  `actor_id` varchar(32) DEFAULT NULL,
  `published` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `verb` varchar(32) NOT NULL,
  `object_id` varchar(255) DEFAULT NULL,
  `target_id` varchar(32) DEFAULT NULL,
  `values` longtext,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `stream_id` (`stream_id`),
  KEY `actor_id` (`actor_id`),
  KEY `object_id` (`object_id`),
  KEY `target_id` (`target_id`),
  KEY `verb` (`verb`),
  KEY `application_id` (`application_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activities`
--

LOCK TABLES `activities` WRITE;
/*!40000 ALTER TABLE `activities` DISABLE KEYS */;
/*!40000 ALTER TABLE `activities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `applications`
--

DROP TABLE IF EXISTS `applications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `applications` (
  `id` varchar(32) NOT NULL,
  `name` varchar(255) NOT NULL,
  `secret` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `applications`
--

LOCK TABLES `applications` WRITE;
/*!40000 ALTER TABLE `applications` DISABLE KEYS */;
/*!40000 ALTER TABLE `applications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `objects`
--

DROP TABLE IF EXISTS `objects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `objects` (
  `id` varchar(32) NOT NULL,
  `application_id` varchar(32) NOT NULL,
  `object_type` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `values` longtext,
  PRIMARY KEY (`id`,`application_id`),
  KEY `object_type` (`object_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `objects`
--

LOCK TABLES `objects` WRITE;
/*!40000 ALTER TABLE `objects` DISABLE KEYS */;
/*!40000 ALTER TABLE `objects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `streams`
--

DROP TABLE IF EXISTS `streams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `streams` (
  `id` varchar(32) NOT NULL,
  `application_id` varchar(32) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `update_timestamp` datetime DEFAULT NULL,
  `auto_subscribe` tinyint(1) DEFAULT NULL,
  KEY `id` (`id`,`application_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `streams`
--

LOCK TABLES `streams` WRITE;
/*!40000 ALTER TABLE `streams` DISABLE KEYS */;
/*!40000 ALTER TABLE `streams` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subscriptions`
--

DROP TABLE IF EXISTS `subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subscriptions` (
  `id` varchar(32) NOT NULL,
  `application_id` varchar(32) NOT NULL,
  `stream_id` varchar(32) NOT NULL,
  `object_id` varchar(32) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `stream_id` (`stream_id`),
  KEY `object_id` (`object_id`),
  KEY `application_id` (`application_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subscriptions`
--

LOCK TABLES `subscriptions` WRITE;
/*!40000 ALTER TABLE `subscriptions` DISABLE KEYS */;
/*!40000 ALTER TABLE `subscriptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `unsubscriptions`
--

DROP TABLE IF EXISTS `unsubscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unsubscriptions` (
  `id` varchar(32) NOT NULL,
  `application_id` varchar(32) NOT NULL,
  `stream_id` varchar(32) NOT NULL,
  `object_id` varchar(32) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `stream_id` (`stream_id`),
  KEY `object_id` (`object_id`),
  KEY `application_id` (`application_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `unsubscriptions`
--

LOCK TABLES `unsubscriptions` WRITE;
/*!40000 ALTER TABLE `unsubscriptions` DISABLE KEYS */;
/*!40000 ALTER TABLE `unsubscriptions` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-04-09 20:18:22
