CREATE TABLE IF NOT EXISTS `::DB_PREFIX::beezup_configuration` (
	`id_configuration` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`id_shop_group` INT(11) UNSIGNED DEFAULT NULL DEFAULT 0 ,
	`id_shop` INT(11) UNSIGNED DEFAULT NULL DEFAULT 0 ,
	`name` VARCHAR( 12 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT "default",
	`disable_disabled_product` BOOL NOT NULL DEFAULT 0 ,
	`disable_not_available` BOOL NOT NULL DEFAULT 0 ,
	`disable_oos_product` BOOL NOT NULL DEFAULT 0 ,
	`id_carrier` INT NOT NULL DEFAULT 0 ,
	`id_zone` INT NOT NULL DEFAULT 0 ,
	`image_type` VARCHAR(75) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL  DEFAULT "",
	`id_default_lang` INT NOT NULL DEFAULT 0 ,
	`force_product_tax` BOOL NOT NULL DEFAULT 0 ,
	`set_attributes_as_product` BOOL NOT NULL  DEFAULT 0 ,
	KEY `name` (`name`),
	KEY `id_shop` (`id_shop`),
	KEY `id_shop_group` (`id_shop_group`)
) ENGINE = innoDB ;

CREATE TABLE IF NOT EXISTS `::DB_PREFIX::beezup_field` (
	`id_field` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`id_configuration` INT NOT NULL DEFAULT 0 ,
	`active` BOOL NOT NULL DEFAULT 0 ,
	`forced` BOOL NOT NULL DEFAULT 0  ,
	`editable` BOOL NOT NULL DEFAULT 0  ,
	`free_field` BOOL NOT NULL DEFAULT 0 ,
	`default` VARCHAR( 75 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL  DEFAULT "" ,
	`id_feature` INT NULL DEFAULT 0  ,
	`id_attribute_group` INT NULL DEFAULT 0  ,
	`values_list` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL  DEFAULT "" ,
	`balise` VARCHAR( 75 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT "",
	`function` VARCHAR( 75 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT "",
	`fields_group` VARCHAR(75) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL  DEFAULT "",
	CONSTRAINT fk_configuration_field
		FOREIGN KEY (`id_configuration`)
		REFERENCES `::DB_PREFIX::beezup_configuration`(`id_configuration`)
		ON UPDATE CASCADE
		ON DELETE CASCADE
) ENGINE = InnoDB ;


CREATE TABLE IF NOT EXISTS `::DB_PREFIX::beezupom_log` (
  `id_beezupom_log` int(255) not null auto_increment,
  `beezup_order_id` varchar(100) not null,
  `message_type` varchar(50) not null,
  `message` varchar(350) not null,
  `date` datetime not null,
  primary key(id_beezupom_log)
) ENGINE = InnoDB;


CREATE TABLE IF NOT EXISTS `::DB_PREFIX::beezup_order_status` (
			`id_beezup_order_status`  INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
			`id_order` INT(11) UNSIGNED DEFAULT NULL DEFAULT 0 ,
			`id_order_status` INT(11) UNSIGNED DEFAULT NULL DEFAULT 0 ,
			KEY `id_order` (`id_order`),
			KEY `id_order_status` (`id_order_status`)
			) ENGINE=INNODB;



CREATE TABLE IF NOT EXISTS `::DB_PREFIX::beezupom_product_template` (
            id_beezupom_product_template int(255) not null auto_increment,
            field_type varchar(30) not null,
            search_value varchar(100) not null,
            replace_value varchar(100) not null,
            marketplace varchar(350) not null,
            primary key(id_beezupom_product_template)
        ) ENGINE=INNODB;