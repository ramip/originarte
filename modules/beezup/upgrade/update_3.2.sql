DROP TABLE IF EXISTS `::DB_PREFIX::beezup_configuration_category`;
DROP TABLE IF EXISTS `::DB_PREFIX::beezup_configuration_product`;
ALTER TABLE `::DB_PREFIX::beezup_configuration` ADD `id_shop` INT(11) UNSIGNED DEFAULT NULL;
ALTER TABLE `::DB_PREFIX::beezup_configuration` ADD `id_shop_group` INT(11) UNSIGNED DEFAULT NULL;
ALTER TABLE `::DB_PREFIX::beezup_configuration` ADD KEY `name` (`name`);
ALTER TABLE `::DB_PREFIX::beezup_configuration` ADD KEY `id_shop` (`id_shop`);
ALTER TABLE `::DB_PREFIX::beezup_configuration` ADD KEY `id_shop_group` (`id_shop_group`);
DELETE FROM `::DB_PREFIX::beezup_configuration` WHERE id_configuration <= (SELECT id_configuration FROM (SELECT id_configuration FROM `::DB_PREFIX::beezup_configuration`  ORDER BY id_configuration ASC LIMIT 1 OFFSET 1) foo)
UPDATE `::DB_PREFIX::beezup_configuration` SET 	id_configuration = 1;
UPDATE `::DB_PREFIX::beezup_configuration` SET `name` = 'default';