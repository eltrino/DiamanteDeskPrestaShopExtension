<?php

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
class DiamanteDesk_OrderRelation extends ObjectModel
{

    public $id;

    public $ticket_key;

    public $order_id;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'diamantedesk_order_relation',
        'primary' => 'relation_id',
        'fields' => array(
            'ticket_key' => array('type' => self::TYPE_STRING, 'required' => true),
            'order_id' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true)
        )
    );

    public function __construct($id = null)
    {
        parent::__construct();
    }

    /**
     * @param $ticketKey
     * @param $orderId
     * @return bool
     * @throws PrestaShopDatabaseException
     */
    public function saveRelation($ticketKey, $orderId)
    {
        return Db::getInstance()->insert('diamantedesk_order_relation',
            array(
                'ticket_key' => $ticketKey,
                'order_id' => (int)$orderId
            )
        );
    }

    /**
     * @param $ticketKey
     * @return array
     * @throws PrestaShopDatabaseException
     */
    public function getRelatedOrders($ticketKey)
    {
        /** @var PDOStatement $result */
        $result = Db::getInstance()->query('
				SELECT `order_id`
				FROM `' . _DB_PREFIX_ . 'diamantedesk_order_relation`
				WHERE `ticket_key` = ' . $ticketKey);

        return $result->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * @param $orderId
     * @return array
     * @throws PrestaShopDatabaseException
     */
    public function getRelatedTickets($orderId)
    {
        /** @var PDOStatement $result */
        $result = Db::getInstance()->query('
				SELECT `ticket_key`
				FROM `' . _DB_PREFIX_ . 'diamantedesk_order_relation`
				WHERE `order_id` = ' . (int)$orderId);

        return $result->fetchAll(PDO::FETCH_COLUMN);
    }
}

/**
 * @return DiamanteDesk_OrderRelation
 */
function getOrderRelationModel()
{
    return new DiamanteDesk_OrderRelation();
}