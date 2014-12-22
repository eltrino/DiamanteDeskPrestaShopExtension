<?php

class Ticket extends ObjectModel
{
    public $content;
    public $title;
    public $position;
    public $active;

    public static $definition = array(
        'table' => 'temp',
        'primary' => 'id_customer',
    );

    public function __construct($id = null)
    {
        parent::__construct();
    }
}