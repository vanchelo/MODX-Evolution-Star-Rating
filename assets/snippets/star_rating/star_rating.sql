SET NAMES utf8;
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `modx_star_rating`;
CREATE TABLE `modx_star_rating` (
  `rid` int(11) unsigned NOT NULL COMMENT 'Resource ID',
  `total` int(5) unsigned DEFAULT NULL COMMENT 'Rating',
  `votes` int(5) unsigned DEFAULT NULL COMMENT 'Total Votes',
  PRIMARY KEY (`rid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `modx_star_rating_votes`;
CREATE TABLE `modx_star_rating_votes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `rid` int(11) unsigned NOT NULL COMMENT 'Resource ID',
  `vote` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Vote',
  `ip` varchar(15) NOT NULL DEFAULT '0.0.0.0' COMMENT 'IP Address',
  `time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Voted Time',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
