CREATE TABLE IF NOT EXISTS `PREFIX_diamantedesk_order_relation` (
  `relation_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ticket_id`   INT(10) UNSIGNED NOT NULL,
  `order_id`    INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`relation_id`),
  KEY `id_product` (`ticket_id`),
  KEY `id_customer` (`order_id`)
)
  ENGINE =ENGINE_TYPE
  DEFAULT CHARSET =utf8;