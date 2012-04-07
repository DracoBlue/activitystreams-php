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
  KEY `verb` (`verb`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activities`
--

LOCK TABLES `activities` WRITE;
/*!40000 ALTER TABLE `activities` DISABLE KEYS */;
INSERT INTO `activities` VALUES ('374023a036f4c6a1b932a092f7c60bad','432edc733d6412755bc625f9d89752b3','739d8b2f6f7d5be6cb538a4831de744e','2012-04-07 18:49:50','post','4c383a8d30603098a57f4100a6358ceb',NULL,NULL,'I posted a (public) new link'),('a8ef3775db510d0516769df5532699b4','551f5cba3a865a22285d7e996f91dbe9','739d8b2f6f7d5be6cb538a4831de744e','2012-04-07 18:49:51','post','961ff3e9321735042e666d660720fba2',NULL,'{\"bar_attribute\":\"1337\"}','I posted a (private) new link');
/*!40000 ALTER TABLE `activities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `objects`
--

DROP TABLE IF EXISTS `objects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `objects` (
  `id` varchar(32) NOT NULL,
  `object_type` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `values` longtext,
  PRIMARY KEY (`id`),
  KEY `object_type` (`object_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `objects`
--

LOCK TABLES `objects` WRITE;
/*!40000 ALTER TABLE `objects` DISABLE KEYS */;
INSERT INTO `objects` VALUES ('4c383a8d30603098a57f4100a6358ceb','blog','http://dracoblue.net',NULL),('739d8b2f6f7d5be6cb538a4831de744e',NULL,NULL,'{\"displayName\":\"User1\",\"foo_attribute\":\"!23\",\"objectType\":\"person\"}'),('961ff3e9321735042e666d660720fba2',NULL,'http://webdevberlin.com',NULL);
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
  `name` varchar(255) NOT NULL,
  `update_timestamp` datetime DEFAULT NULL,
  `auto_subscribe` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `streams`
--

LOCK TABLES `streams` WRITE;
/*!40000 ALTER TABLE `streams` DISABLE KEYS */;
INSERT INTO `streams` VALUES ('432edc733d6412755bc625f9d89752b3','Public TestStream',NULL,1),('551f5cba3a865a22285d7e996f91dbe9','Private TestStream',NULL,0);
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
  `stream_id` varchar(32) NOT NULL,
  `object_id` varchar(32) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `stream_id` (`stream_id`),
  KEY `object_id` (`object_id`)
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
  `stream_id` varchar(32) NOT NULL,
  `object_id` varchar(32) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `stream_id` (`stream_id`),
  KEY `object_id` (`object_id`)
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
