<?php

class AdminDiamanteDeskController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;

        $this->className = 'Ticket';
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

    public function renderForm()
    {
        $api = getDiamanteDeskApi();
        $branches = $api->getBranches();
        $users = $api->getUsers();


        $listBranches = array();
        if ($branches) {
            foreach ($branches as $key => $branch) {
                $listBranches[$key]['branch_id'] = (int)$branch->id;
                $listBranches[$key]['name'] = $branch->name;
            }
        }

        $listUsers = array();
        if ($users) {
            foreach ($users as $key => $user) {
                $listUsers[$key]['user_id'] = (int)$user->id;
                $listUsers[$key]['name'] = $user->firstName . ' ' . $user->lastName . ' (' . $user->email . ')';
            }
        }

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Create DiamanteDesk Ticket'),
                'icon' => 'icon-AdminDiamanteDeskDark'
            ),
            'input' => array(
                array(
                    'type' => 'select',
                    'label' => $this->l('Branch'),
                    'name' => 'branch',
                    'required' => true,
                    'class' => 't',
                    'options' => array(
                        'query' => $listBranches,
                        'id' => 'branch_id',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Subject'),
                    'name' => 'subject',
                    'required' => true,
                    'col' => '2'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Priority'),
                    'name' => 'priority',
                    'required' => true,
                    'col' => '2'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Status'),
                    'name' => 'status',
                    'required' => true,
                    'col' => '2'
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Reporter'),
                    'name' => 'reporter',
                    'required' => true,
                    'class' => 't',
                    'col' => '4',
                    'options' => array(
                        'query' => $listUsers,
                        'id' => 'user_id',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Assigner'),
                    'name' => 'assigner_id',
                    'required' => true,
                    'class' => 't',
                    'col' => '4',
                    'options' => array(
                        'query' => $listUsers,
                        'id' => 'user_id',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Description'),
                    'name' => 'description',
                    'required' => true,
                    'col' => '4',
                ),
            )
        );

        $this->fields_form['submit'] = array(
            'title' => $this->l('Add Ticket'),
        );
        return parent::renderForm();
    }

    public function processAdd()
    {
        $api = getDiamanteDeskApi();
        if (!$api->saveTicket($_POST)) {
            $this->errors[] = 'Error was occurred.';
        }
    }
}