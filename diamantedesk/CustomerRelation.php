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
class DiamanteDesk_CustomerRelation extends ObjectModel
{

    public $id;

    public $customer_id;

    public $user_id;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'diamantedesk_customer_relation',
        'primary' => 'relation_id',
        'fields' => array(
            'customer_id' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'user_id' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true)
        )
    );

    public function __construct($id = null)
    {
        parent::__construct();
    }

    /**
     * @param $customerId
     * @param $userId
     * @return bool
     * @throws PrestaShopDatabaseException
     */
    public function saveRelation($customerId, $userId)
    {
        return Db::getInstance()->insert('diamantedesk_customer_relation',
            array(
                'customer_id' => (int)$customerId,
                'user_id' => (int)$userId
            )
        );
    }

    /**
     * @param $customerId
     * @return int
     */
    public function getUserId($customerId)
    {
        /** @var PDOStatement $result */
        $result = Db::getInstance()->query('
				SELECT `user_id`
				FROM `' . _DB_PREFIX_ . 'diamantedesk_customer_relation`
				WHERE `customer_id` = ' . $customerId);

        return $result->fetch(PDO::FETCH_COLUMN);
    }
}

/**
 * @return DiamanteDesk_CustomerRelation
 */
function getCustomerRelationModel()
{
    return new DiamanteDesk_CustomerRelation();
}