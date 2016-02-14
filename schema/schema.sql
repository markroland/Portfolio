# Create database
#CREATE DATABASE portfolio CHARACTER SET = utf8 COLLATE = utf8_general_ci;

# Create user
#CREATE USER 'portfolio_user'@'localhost' IDENTIFIED BY 'portfolio_pass';
#GRANT SELECT, INSERT, UPDATE, DELETE, EXECUTE ON portfolio.* TO 'portfolio_user'@'localhost';

# Import
# [root@localhost ~]# mysql portfolio < /vagrant/php-libs/portfolio/schema/schema.sql

# ------------------------------------------------------------
# ------------------------------------------------------------

# discipline
# ------------------------------------------------------------

CREATE TABLE `discipline` (
  `discipline_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `discipline` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`discipline_id`),
  UNIQUE KEY `discipline` (`discipline`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# keyword
# ------------------------------------------------------------

CREATE TABLE `keyword` (
  `keyword_id` smallint(3) unsigned NOT NULL AUTO_INCREMENT,
  `keyword` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`keyword_id`),
  UNIQUE KEY `keyword` (`keyword`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# medium
# ------------------------------------------------------------

CREATE TABLE `medium` (
  `medium_id` tinyint(11) unsigned NOT NULL AUTO_INCREMENT,
  `medium` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`medium_id`),
  UNIQUE KEY `medium` (`medium`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# project
# ------------------------------------------------------------

CREATE TABLE `project` (
  `project_id` smallint(3) unsigned NOT NULL AUTO_INCREMENT,
  `publish` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `grade` float(3,2) NOT NULL DEFAULT '0.00',
  `start_date` date DEFAULT NULL,
  `completion_date` date DEFAULT NULL,
  `title` varchar(64) DEFAULT NULL,
  `url_safe_title` varchar(32) DEFAULT NULL,
  `synopsis` text,
  `description` text,
  `tutorial` text,
  `open_source` tinyint(1) NOT NULL DEFAULT '0',
  `location` varchar(32) DEFAULT NULL,
  `width_inches` float(7,4) unsigned DEFAULT '0.0000',
  `height_inches` float(7,4) unsigned DEFAULT '0.0000',
  `depth_inches` float(7,4) unsigned DEFAULT '0.0000',
  `weight_lbs` float(7,4) unsigned NOT NULL DEFAULT '0.0000',
  PRIMARY KEY (`project_id`),
  UNIQUE KEY `url_safe_title` (`url_safe_title`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# project_discipline
# ------------------------------------------------------------

CREATE TABLE `project_discipline` (
  `project_id` smallint(3) unsigned NOT NULL DEFAULT '0',
  `discipline_id` tinyint(3) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# project_hits
# ------------------------------------------------------------

CREATE TABLE `project_hits` (
  `project_id` smallint(3) unsigned NOT NULL,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `hits` smallint(5) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# project_item
# ------------------------------------------------------------

CREATE TABLE `project_item` (
  `item_id` smallint(3) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` smallint(3) unsigned NOT NULL DEFAULT '0',
  `rank` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `purpose` varchar(32) NOT NULL,
  `media_type` varchar(32) NOT NULL DEFAULT '',
  `URL` varchar(120) NOT NULL DEFAULT '',
  `width` int(11) DEFAULT '0',
  `height` int(11) DEFAULT '0',
  `title` varchar(64) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`item_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# project_keyword
# ------------------------------------------------------------

CREATE TABLE `project_keyword` (
  `project_id` tinyint(11) unsigned NOT NULL DEFAULT '0',
  `keyword_id` tinyint(11) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# project_medium
# ------------------------------------------------------------

CREATE TABLE `project_medium` (
  `project_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `medium_id` tinyint(3) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# related_projects
# ------------------------------------------------------------

CREATE TABLE `related_projects` (
  `project_id_A` smallint(3) unsigned NOT NULL DEFAULT '0',
  `project_id_B` smallint(3) unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY `project_id_A` (`project_id_A`,`project_id_B`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
