-- MySQL dump 10.11
--
-- Host: localhost    Database: trustrankbot
-- ------------------------------------------------------
-- Server version	5.0.51b

CREATE DATABASE `trustrankbot`;

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
-- Table structure for table `links`
--

DROP TABLE IF EXISTS `links`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `links` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `fr` int(11) unsigned default NULL,
  `to` int(10) unsigned default NULL,
  `dt` timestamp NOT NULL default '0000-00-00 00:00:00' on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=23147 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `links`
--

--
-- Table structure for table `urls`
--

DROP TABLE IF EXISTS `urls`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `urls` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `url` varchar(255) default NULL,
  `domain` varchar(128) default NULL,
  `rank` float unsigned default '0',
  `domain_auth` tinyint(4) unsigned default NULL,
  `page_auth` tinyint(4) unsigned default '0',
  `backlinks` int(11) unsigned default '0',
  `dt_last_crawl` timestamp NOT NULL default '0000-00-00 00:00:00' on update CURRENT_TIMESTAMP,
  `dt_added` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `url` (`url`,`domain`)
) ENGINE=MyISAM AUTO_INCREMENT=7723 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `urls`
--
