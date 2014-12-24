<?php

class DiamanteDeskMyTicketsModuleFrontController extends ModuleFrontController
{
    public $ssl = true;
    public $auth = true;
    public $page_name = 'My Tickets';

    public function __construct()
    {
        parent::__construct();
        $this->context = Context::getContext();
    }

    public function initContent()
    {
        if (Tools::isSubmit('submitTicket')) {
            Tools::safePostVars();
            getDiamanteDeskApi()->saveTicket($_POST);
        }

        $this->display_column_left = false;
        $this->display_column_right = false;

        parent::initContent();

        $api = getDiamanteDeskApi();

        $tickets = $api->getTickets();

        /** format date */
        foreach ($tickets as $ticket) {
            $ticket->created_at = date("U", strtotime($ticket->created_at));
        }

        $this->context->smarty->assign(array(
            'tickets' => $tickets,
            'diamantedesk_url' => Configuration::get('DIAMANTEDESK_SERVER_ADDRESS')
        ));

        $this->setTemplate('mytickets.tpl');
    }
}