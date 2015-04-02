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
class AdminDiamanteDeskController extends ModuleAdminController
{
    const TOTAL_RESULT_HEADER = 'X-Total';
    const CONFIGURATION_FILTER_VALUE = 'configurationFilter';
    const CREATED_AT_FIELD = 'createdAt';

    const API_DATE_FROM_VALUE = 'createdAfter';
    const API_DATE_TO_VALUE = 'createdBefore';

    /** @var DiamanteDesk_Api */
    protected $_api;

    protected function _init()
    {
        if (!$this->_api) {
            $this->_api = getDiamanteDeskApi();
            $this->_api->init();
        }
    }

    public function __construct()
    {
        $this->bootstrap = true;

        $this->className = 'Ticket';
        $this->lang = false;

        $this->addRowAction('view');

        $this->context = Context::getContext();
        $this->fields_list = array(
            'id' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'filter' => false,
                'search' => false
            ),
            'subject' => array(
                'title' => $this->l('Subject')
            ),
            'email' => array(
                'title' => $this->l('Email'),
                'orderby' => false,
                'filter' => false,
                'search' => false,
            ),
            'createdAt' => array(
                'title' => $this->l('Created At'),
                'type' => 'datetime',
            ),
            'priority' => array(
                'title' => $this->l('Priority'),
                'align' => 'center',
                'class' => 'fixed-width-sm',
                'color' => 'color',
                'type' => 'select',
                'list' => array(
                    'low' => 'Low',
                    'medium' => 'Medium',
                    'high' => 'High',
                ),
                'filter_key' => 'priority',
                'order_key' => 'priority'
            ),
            'status' => array(
                'title' => $this->l('Status'),
                'align' => 'center',
                'class' => 'fixed-width-sm',
                'color' => 'color',
                'type' => 'select',
                'list' => array(
                    'new' => 'New',
                    'open' => 'Open',
                    'pending' => 'Pending',
                    'in_progress' => 'In progress',
                    'closed' => 'closed',
                    'on_hold' => 'On hold',
                ),
                'filter_key' => 'status',
                'order_key' => 'status'
            ),
        );
        parent::__construct();

        if (Tools::isSubmit('onboarding_carrier')) {
            $this->display = 'view';
        }

    }

    public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
    {
        $this->_init();
        $this->addJs(_MODULE_DIR_ . $this->module->name . '/js/blankLink.js');
        $tickets = $this->_api->getTickets();
        $this->_list = array();
        $this->_listTotal = $this->_api->resultHeaders[static::TOTAL_RESULT_HEADER];
        if ($tickets) {
            foreach ($tickets as $ticket) {

                $date = new DateTime($ticket->created_at);
                $createdAt = $date->format('Y-m-d H:i:s');

                $this->_list[] = array(
                    'id_configuration' => $ticket->key,
                    'id' => $ticket->id,
                    'subject' => $ticket->subject,
                    'email' => '',
                    'createdAt' => $createdAt,
                    'priority' => $ticket->priority,
                    'status' => $ticket->status,
                );
            }
        }
    }

    public function renderForm()
    {
        $this->_init();
        $branches = $this->_api->getBranches();
        $users = $this->_api->getUsers();

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
        $this->_init();
        if (!$this->_api->saveTicket($_POST)) {
            $this->errors[] = 'Error was occurred.';
        }
    }

    public function processFilter()
    {
        $this->_init();
        $this->_applyPageSize();
        $this->_applyPage();
        $this->_applySorting();
        $this->_applyFilters();
    }

    protected function _applyPageSize()
    {
        $pageSize = $_POST['configuration_pagination'] ? $_POST['configuration_pagination'] : $this->_default_pagination;
        $this->_api->addFilter('limit', $pageSize);
    }

    protected function _applyPage()
    {
        $page = $_POST['submitFilterconfiguration'] ? $_POST['submitFilterconfiguration'] : 1;
        $this->_api->addFilter('page', $page);
    }

    protected function _applySorting()
    {
        $attribute = $_GET['configurationOrderby'] ? $_GET['configurationOrderby'] : null;
        $dir = $_GET['configurationOrderway'] ? $_GET['configurationOrderway'] : null;

        if (!$attribute || !$dir) {
            return;
        }

        $this->_api
            ->addFilter('sort', $attribute)
            ->addFilter('order', strtoupper($dir));
    }

    protected function _applyFilters()
    {
        if (!isset($_POST)) {
            return $this;
        }

        foreach ($_POST as $key => $value) {

            if (!$value) {
                continue;
            }

            $arr = explode('_', $key);

            if (count($arr) != 2) {
                continue;
            }

            if ($arr[0] != static::CONFIGURATION_FILTER_VALUE) {
                continue;
            }

            if (is_array($value) && $arr[1] == static::CREATED_AT_FIELD) {
                list($from, $to) = $value;

                if ($from) {
                    $from = new DateTime($from, new DateTimezone('UTC'))    ;
                    $this->_api->addFilter(static::API_DATE_FROM_VALUE, $from->format(DateTime::ISO8601));
                }

                if ($to) {
                    $to = new DateTime($to, new DateTimezone('UTC'));
                    $this->_api->addFilter(static::API_DATE_TO_VALUE, $to->format(DateTime::ISO8601));
                }
            }

            if (is_array($value)) {
                return $this;
            }

            $this->_api->addFilter($arr[1], $value);

            return $this;
        }

    }
}