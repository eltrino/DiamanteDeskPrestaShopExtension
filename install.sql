/**
 * Copyright (c) 2014 Eltrino LLC (http://eltrino.com)
 *
 * Licensed under the Open Software License (OSL 3.0).
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://opensource.org/licenses/osl-3.0.php
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@eltrino.com so we can send you a copy immediately.
 */

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