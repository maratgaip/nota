#SKD101|kiandastreamdb|25|2015.05.11 12:12:40|4163|104|1953|67|25|5|250|177|15|1|27|2|6|249|150|1025|16|8|4|79

DROP TABLE IF EXISTS `vass_albums`;
CREATE TABLE `vass_albums` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `artist_id` int(8) NOT NULL,
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `name` varchar(50) /*!40101 COLLATE utf8_unicode_ci */ NOT NULL,
  `composer_id` int(11) NOT NULL,
  `descr` text /*!40101 COLLATE utf8_unicode_ci */ NOT NULL,
  `playcount` smallint(8) NOT NULL DEFAULT '0',
  `like` tinyint(9) NOT NULL DEFAULT '0',
  `view` smallint(9) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `artist_id` (`artist_id`)
) ENGINE=MyISAM AUTO_INCREMENT=149 /*!40101 DEFAULT CHARSET=utf8 */ /*!40101 COLLATE=utf8_unicode_ci */;

INSERT INTO `vass_albums` VALUES