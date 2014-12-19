<?php

class AdminDiamanteDeskController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;

        $this->className = 'Tickets';
        $this->lang = false;

        $this->addRowAction('view');

        $this->context = Context::getContext();
        $this->fields_list = array(
            'ticket_id' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
            'subject' => array(
                'title' => $this->l('Subject')
            ),
            'email' => array(
                'title' => $this->l('Email'),
            ),
            'created_at' => array(
                'title' => $this->l('Created At'),
                'type' => 'datetime',
            ),
            'priority' => array(
                'title' => $this->l('Priority'),
                'align' => 'center',
                'class' => 'fixed-width-sm',
            ),
            'status' => array(
                'title' => $this->l('Status'),
                'align' => 'center',
                'class' => 'fixed-width-sm',
            ),
        );
        parent::__construct();

        if (Tools::isSubmit('onboarding_carrier')) {
            $this->display = 'view';
        }

    }

    public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
    {
        $this->addJs(_MODULE_DIR_ . $this->module->name . '/js/blankLink.js');
        $tickets = getDiamanteDeskApi()->getTickets();
        $this->_list = array();
        $this->_listTotal = count($tickets);
        if ($tickets) {
            foreach ($tickets as $ticket) {

                $date = new DateTime($ticket->created_at);
                $createdAt = $date->format('Y-m-d H:i:s');

                $this->_list[] = array(
                    'id_configuration' => $ticket->key,
                    'ticket_id' => $ticket->id,
                    'subject' => $ticket->subject,
                    'email' => '',
                    'created_at' => $createdAt,
                    'priority' => $ticket->priority,
                    'status' => $ticket->status,
                );
            }
        }
    }
}