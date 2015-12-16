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
class DiamanteDeskMyTicketsModuleFrontController extends ModuleFrontController
{
    const TICKETS_PER_PAGE = 25;
    const TOTAL_RESULT_HEADER = 'X-Total';
    const FILE_NAME_FIELD = 'attachment';

    public $ssl = true;
    public $auth = true;
    public $page_name = 'My Tickets';

    private $diamanteUsers = array();
    private $oroUsers = array();

    public function __construct()
    {
        parent::__construct();
        $this->context = Context::getContext();
        $api = getDiamanteDeskApi();
        $this->diamanteUsers = $api->getDiamanteUsers();
        $this->oroUsers = $api->getUsers();
    }

    public function initContent()
    {

        if (isset($this->context->cookie->redirect_success_message)){
            $this->context->smarty->assign(array(
                'success' => $this->context->cookie->redirect_success_message,
            ));
            $this->context->cookie->__unset('redirect_success_message');
        }

        if (isset($_GET['ticket'])) {
            $this->initTicketContent();
            return;
        }

        if (Tools::isSubmit('submitTicket')) {
            Tools::safePostVars();

            if (!$_POST['subject'] || !$_POST['description']) {
                $this->errors[] = 'All fields are required. Please fill all fields and try again.';
            } else {
                $data = $_POST;
                $api = getDiamanteDeskApi();
                $diamanteUser = $api->getOrCreateDiamanteUser($this->context->customer);
                $data['reporter'] = DiamanteDesk_Api::TYPE_DIAMANTE_USER . $diamanteUser->id;
                $newTicket = getDiamanteDeskApi()->createTicket($data);
                if (!$newTicket) {
                    $this->errors[] = 'Something went wrong. Please try again later or contact us';
                } else {

                    $this->saveAttachment($newTicket);

                    $this->context->cookie->__set('redirect_success_message', 'Ticket was successfully created.');
                    Tools::redirect(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null);
                    return;
                }
            }
        }

        $this->display_column_left = false;
        $this->display_column_right = false;

        parent::initContent();

        $api = getDiamanteDeskApi();

        // get pagination info
        $currentPage = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        $ticketsPerPage = static::TICKETS_PER_PAGE;
        $api->addFilter('page', $currentPage);
        $api->addFilter('limit', $ticketsPerPage);

        $customer = $this->context->customer;
        $diamanteUser = $api->getOrCreateDiamanteUser($customer);

        if (!$diamanteUser) {
            $this->errors[] = 'Something went wrong. Please try again later or contact us';
            $this->context->smarty->assign(array(
                'start' => 1,
                'stop' => 1,
                'p' => $currentPage,
                'range' => static::TICKETS_PER_PAGE,
                'pages_nb' => 1,
                'tickets' => array(),
                'diamantedesk_url' => Configuration::get('DIAMANTEDESK_SERVER_ADDRESS'),
                'priorityMap' => DiamanteDesk_Api::$_priorities,
                'statusMap' => DiamanteDesk_Api::$_statuses,
            ));
            $this->setTemplate('mytickets.tpl');
            return;
        }

        $api->addFilter('reporter', DiamanteDesk_Api::TYPE_DIAMANTE_USER . $diamanteUser->id);

        $tickets = $api->getTickets();

        $lastPage = ceil($api->resultHeaders[static::TOTAL_RESULT_HEADER] / static::TICKETS_PER_PAGE);
        $lastPage = $lastPage == 0 ? 1 : $lastPage;

        /** format date */
        foreach ($tickets as $ticket) {
            $ticket->created_at = date("U", strtotime($ticket->created_at));
        }

        $this->context->smarty->assign(array(
            'start' => 1,
            'stop' => $lastPage,
            'p' => $currentPage,
            'range' => static::TICKETS_PER_PAGE,
            'pages_nb' => $lastPage,
            'tickets' => $tickets,
            'diamantedesk_url' => Configuration::get('DIAMANTEDESK_SERVER_ADDRESS'),
            'priorityMap' => DiamanteDesk_Api::$_priorities,
            'statusMap' => DiamanteDesk_Api::$_statuses,
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
                $customer = $this->context->customer;
                $diamanteUser = $api->getOrCreateDiamanteUser($customer);
                $data['author'] = DiamanteDesk_Api::TYPE_DIAMANTE_USER . $diamanteUser->id;
                if (!getDiamanteDeskApi()->addComment($data)) {
                    $this->errors[] = 'Something went wrong. Please try again later or contact us';
                } else {
                    $this->context->cookie->__set('redirect_success_message', 'Comment successfully added.');
                    Tools::redirect(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null);
                    return;
                }
            }
        }

        $this->display_column_left = false;
        $this->display_column_right = false;

        parent::initContent();

        $api = getDiamanteDeskApi();

        $ticket = $api->getTicket((int)$_GET['ticket']);

        $customerRelationModel = getCustomerRelationModel();
        $userId = $customerRelationModel->getUserId($this->context->customer->id);

        if ($ticket->reporter !== 'diamante_' . $userId) {
            Tools::redirect('index.php?controller=404');
            return;
        }

        if (isset($ticket->error) && $ticket->error) {
            Tools::redirect('index.php?controller=404');
            return;
        }

        if ($ticket && $ticket->comments) {
            foreach ($ticket->comments as $key => $comment) {
                if ($comment->private === true) {
                    unset ($ticket->comments[$key]);
                    continue;
                }
                $comment->authorName = $this->getAuthor($comment);
                $comment->created_at = date("U", strtotime($comment->created_at));
            }
        }

        foreach(DiamanteDesk_Api::$_statuses as $status) {
            if ($status['status_id'] == $ticket->status) {
                $ticket->status = $status['name'];
                break;
            }
        }

        foreach(DiamanteDesk_Api::$_priorities as $priority) {
            if ($priority['priority_id'] == $ticket->priority) {
                $ticket->priority = $priority['name'];
                break;
            }
        }

        $this->context->smarty->assign(array(
            'ticket' => $ticket
        ));

        $this->setTemplate('ticket.tpl');
    }

    /**
     * @param $comment
     * @return mixed
     */
    public function getAuthor($comment)
    {
        $customerRelationModel = getCustomerRelationModel();
        $customer = $this->context->customer;
        $userId = $customerRelationModel->getUserId($customer->id);

        if ($comment->author->id == 'diamante_' . $userId) {
            return $customer->firstname . ' ' . $customer->lastname;
        }

        return $comment->author->name;
    }

    /**
     * @param $ticket stdClass
     */
    protected function saveAttachment($ticket)
    {
        if (isset($_FILES) && isset($_FILES[self::FILE_NAME_FIELD]) && $_FILES[self::FILE_NAME_FIELD]['size'] !== 0) {
            $api = getDiamanteDeskApi();
            $fileContent = file_get_contents($_FILES[self::FILE_NAME_FIELD]['tmp_name']);
            $fileName = $_FILES[self::FILE_NAME_FIELD]['name'];

            $api->addAttachmentToTicket(array(
                'ticket_id' => $ticket->id,
                'filename'  => $fileName,
                'content'   => base64_encode($fileContent)
            ));

        }
    }
}