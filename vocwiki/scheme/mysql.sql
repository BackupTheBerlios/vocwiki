# vocwiki sql scheme for mysql

CREATE TABLE `vocwiki_data` (
  `data_id` int(11) unsigned NOT NULL auto_increment,
  `fk_element` int(11) NOT NULL default '0',
  `version` smallint(5) NOT NULL default '1',
  `typ` varchar(30) default NULL,
  `content` text,
  `author` varchar(30) default NULL,
  `lastchange` timestamp(12) NOT NULL,
  `subtype` varchar(30) default NULL,
  PRIMARY KEY  (`data_id`)
) TYPE=MyISAM AUTO_INCREMENT=8099 ;

CREATE TABLE `vocwiki_element` (
  `element_id` int(11) unsigned NOT NULL auto_increment,
  `fk_file` tinyint(5) NOT NULL default '0',
  `lastchange` timestamp(12) NOT NULL,
  `version` smallint(5) NOT NULL default '1',
  PRIMARY KEY  (`element_id`)
) TYPE=MyISAM AUTO_INCREMENT=1066 ;

CREATE TABLE `vocwiki_file` (
  `file_id` tinyint(5) unsigned NOT NULL auto_increment,
  `title` varchar(100) default NULL,
  `original_name` varchar(30) default NULL,
  `original_code` varchar(10) default NULL,
  `translation_name` varchar(30) default NULL,
  `translation_code` varchar(10) default NULL,
  `author` varchar(30) default NULL,
  `lastchange` timestamp(12) NOT NULL,
  PRIMARY KEY  (`file_id`)
) TYPE=MyISAM AUTO_INCREMENT=8 ;

CREATE TABLE `vocwiki_rating` (
  `rating_id` int(11) unsigned NOT NULL auto_increment,
  `fk_user` tinyint(5) unsigned NOT NULL default '0',
  `fk_element` int(11) unsigned NOT NULL default '0',
  `correct` tinyint(5) NOT NULL default '0',
  `allx` tinyint(5) NOT NULL default '0',
  `coeff` float NOT NULL default '0',
  `lastchange` timestamp(14) NOT NULL,
  `username` varchar(80) NOT NULL default '',
  PRIMARY KEY  (`rating_id`)
) TYPE=MyISAM AUTO_INCREMENT=493 ;

CREATE TABLE `vocwiki_users` (
  `name` varchar(80) NOT NULL default '',
  `password` varchar(32) NOT NULL default '',
  `email` varchar(50) NOT NULL default '',
  `motto` text NOT NULL,
  `revisioncount` int(10) unsigned NOT NULL default '20',
  `changescount` int(10) unsigned NOT NULL default '50',
  `doubleclickedit` enum('Y','N') NOT NULL default 'Y',
  `signuptime` datetime NOT NULL default '0000-00-00 00:00:00',
  `show_comments` enum('Y','N') NOT NULL default 'N',
  `last_file` tinyint(5) NOT NULL default '0',
  PRIMARY KEY  (`name`),
  KEY `idx_name` (`name`),
  KEY `idx_signuptime` (`signuptime`)
) TYPE=MyISAM;
    
