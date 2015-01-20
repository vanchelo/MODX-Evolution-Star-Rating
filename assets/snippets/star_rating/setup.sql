CREATE TABLE `{prefix}star_rating` (
  `rid` int(11) unsigned NOT NULL COMMENT 'Resource ID',
  `total` int(5) unsigned DEFAULT '0' COMMENT 'Rating',
  `votes` int(5) unsigned DEFAULT '0' COMMENT 'Total Votes',
  `rating` double unsigned NOT NULL DEFAULT '0' COMMENT 'Avg. Rating',
  PRIMARY KEY (`rid`),
  KEY `rating` (`rating`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{prefix}star_rating_votes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `rid` int(11) unsigned NOT NULL COMMENT 'Resource ID',
  `vote` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Vote',
  `ip` varchar(15) NOT NULL DEFAULT '0.0.0.0' COMMENT 'IP Address',
  `time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Voted Time',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
