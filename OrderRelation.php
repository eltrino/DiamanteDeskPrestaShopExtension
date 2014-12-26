<?php

class DiamanteDesk_OrderRelation extends ObjectModel
{

    public $id;

    public $ticket_id;

    public $order_id;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'diamantedesk_order_relation',
        'primary' => 'relation_id',
        'fields' => array(
            'ticket_id' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'order_id' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true)
        )
    );

    public function __construct($id = null)
    {
        parent::__construct();
    }

    /**
     * @param $ticketId
     * @param $orderId
     * @return bool
     * @throws PrestaShopDatabaseException
     */
    public function saveRelation($ticketId, $orderId)
    {
        return Db::getInstance()->insert('diamantedesk_order_relation',
            array(
                'ticket_id' => (int)$ticketId,
                'order_id' => (int)$orderId
            )
        );
    }

    /**
     * @param $ticketId
     * @return array
     * @throws PrestaShopDatabaseException
     */
    public function getRelatedOrders($ticketId)
    {
        /** @var PDOStatement $result */
        $result = Db::getInstance()->query('
				SELECT `order_id`
				FROM `' . _DB_PREFIX_ . 'diamantedesk_order_relation`
				WHERE `ticket_id` = ' . (int)$ticketId);
        
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
				SELECT `ticket_id`
				FROM `' . _DB_PREFIX_ . 'diamantedesk_order_relation`
				WHERE `order_id` = ' . (int)$orderId);

        return $result->fetchAll(PDO::FETCH_COLUMN);
    }
}