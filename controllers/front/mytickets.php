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

        if ($_GET['ticket']) {
            $this->initTicketContent();
            return;
        }

        if (Tools::isSubmit('submitTicket')) {
            Tools::safePostVars();

            if (!$_POST['subject'] || !$_POST['description']) {
                $this->errors[] = 'All fields are required. Please fill all fields and try again.';
            } else {
                if (!getDiamanteDeskApi()->saveTicket($_POST)) {
                    $this->errors[] = 'Something went wrong. Please try again later or contact us';
                } else {
                    $this->context->smarty->assign('success', 'Ticket was successfully created.');
                }
            }
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

    public function initTicketContent()
    {

        if (Tools::isSubmit('submitComment')) {
            Tools::safePostVars();
            if (!$_POST['comment']) {
                $this->errors[] = 'All fields are required. Please fill all fields and try again.';
            } else {
                $api = getDiamanteDeskApi();
                $data = $_POST;
                $data['content'] = $data['comment'];
                if (!getDiamanteDeskApi()->addComment($data)) {
                    $this->errors[] = 'Something went wrong. Please try again later or contact us';
                } else {
                    $this->context->smarty->assign('success', 'Comment successfully added');
                }
            }
        }

        $this->display_column_left = false;
        $this->display_column_right = false;

        parent::initContent();

        $api = getDiamanteDeskApi();

        $ticket = $api->getTicket((int)$_GET['ticket']);

        if ($ticket && $ticket->comments) {
            foreach ($ticket->comments as &$comment) {
                $comment->authorData = $api->getUserById($comment->author);
                $comment->created_at = date("U", strtotime($comment->created_at));
            }
        }

        $this->context->smarty->assign(array(
            'ticket' => $ticket
        ));

        $this->setTemplate('ticket.tpl');
    }
}