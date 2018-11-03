DROP TABLE IF EXISTS `PREFIXbeezup_harvest_reporting`;

CREATE TABLE `PREFIXbeezup_harvest_reporting` (
  `id_harvest_client` int(10) unsigned NOT NULL auto_increment,
  `execution_id` varchar(64) default NULL,
  `creation_utc_date` datetime NOT NULL,
  `last_update_utc_date` datetime NOT NULL,
  `error_message` varchar(64) default NULL,
  `total_order_count` int(10) unsigned NOT NULL default '0',
  `processing_status` varchar(64) default NULL,
  `begin_period_utc_date` datetime NOT NULL,
  `end_period_utc_date` datetime NOT NULL,
  `entries_per_page` int(10) unsigned NOT NULL default '0',
  `beezup_api_token` varchar(64) default NULL,
  `beezup_user_id` varchar(64) default NULL,
  `remaining_page_count` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY (`id_harvest_client`),
  KEY `execution_id` (`execution_id`),
  KEY `creation_utc_date` (`creation_utc_date`),
  KEY `last_update_utc_date` (`last_update_utc_date`),
  KEY `processing_status` (`processing_status`),
  KEY `remaining_page_count` (`remaining_page_count`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `PREFIXbeezup_harvest_order`;

CREATE TABLE `PREFIXbeezup_harvest_order` (
  `id_harvest_order` int(10) unsigned NOT NULL auto_increment,
  `execution_id` varchar(64) default NULL,
  `creation_utc_date` datetime NOT NULL,
  `last_update_utc_date` datetime NOT NULL,
  `error_message` varchar(64) default NULL,
  `processing_status` varchar(64) default NULL,
  `beezup_api_token` varchar(64) default NULL,
  `beezup_user_id` varchar(64) default NULL,
  `beezup_order_uuid` varchar(64) default NULL,
  `account_id` varchar(64) default NULL,
  `etag` varchar(64) default NULL,
  `marketplace_technical_code` varchar(64) default NULL,
  `http_status` varchar(64) default NULL,
  `order_detail_json` TEXT default NULL,
  `last_modification_utc_date` datetime default NULL,
  PRIMARY KEY (`id_harvest_order`),
  KEY `execution_id` (`execution_id`),
  KEY `beezup_order_uuid` (`beezup_order_uuid`),
  KEY `creation_utc_date` (`creation_utc_date`),
  KEY `last_update_utc_date` (`last_update_utc_date`),
  KEY `processing_status` (`processing_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `PREFIXbeezup_order`;

CREATE TABLE `PREFIXbeezup_order` (
  `id_beezup_order` int(10) unsigned NOT NULL auto_increment,
  `account_id` varchar(64) default NULL,
  `marketplace_technical_code` varchar(64) default NULL,
  `beezup_order_uuid` varchar(64) default NULL,
  `id_order` int(10) unsigned NOT NULL,
  `etag` varchar(128) default NULL,
  `order_json` longtext default NULL,
  `infos_json` longtext default NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
PRIMARY KEY (`id_beezup_order`),
  KEY `id_order` (`id_order`),
  KEY `account_id` (`account_id`),
  KEY `marketplace_technical_code` (`marketplace_technical_code`),
  KEY `beezup_order_uuid` (`beezup_order_uuid`),
  KEY  `date_add` ( `date_add`),
  KEY  `date_upd` ( `date_upd`),
  KEY  `etag` ( `etag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

