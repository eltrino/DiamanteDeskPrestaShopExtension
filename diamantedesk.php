<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

include_once(dirname(__FILE__) . '/Api.php');
include_once(dirname(__FILE__) . '/Config.php');

class DiamanteDesk extends Module
{
    public function __construct()
    {
        $this->name = 'diamantedesk';
        $this->tab = 'others';
        $this->version = '1.0.0';
        $this->author = 'Eltrino';
        $this->need_instance = 1;
        $this->ps_versions_compliancy = array(
            'min' => '1.6',
            'max' => _PS_VERSION_
        );
        $this->bootstrap = TRUE;

        parent::__construct();

        $this->displayName = $this->l('DiamanteDesk');
        $this->description = $this->l('DiamanteDesk integration');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

    }

    public function install()
    {
        $parentTab = new Tab();
        // Need a foreach for the language
        $parentTab->name[$this->context->language->id] = $this->l('DiamanteDesk');
        $parentTab->class_name = 'AdminDiamanteDesk';
        $parentTab->id_parent = 0; // Home tab
        $parentTab->module = $this->name;
        $parentTab->add();

        $tab = new Tab();
        // Need a foreach for the language
        $tab->name[$this->context->language->id] = $this->l('All Tickets');
        $tab->class_name = 'AdminDiamanteDesk';
        $tab->id_parent = $parentTab->id;
        $tab->module = $this->name;
        $tab->add();
        $install = parent::install();
        $this->registerHook('displayBackOfficeHeader');
        return $install;
    }

    public function uninstall()
    {

        while ($idTab = Tab::getIdFromClassName('AdminDiamanteDesk')) {
            if ($idTab != 0) {
                $tab = new Tab($idTab);
                $tab->delete();
            }
        }
        $this->unregisterHook('displayBackOfficeHeader');
        return parent::uninstall();
    }

    public function getContent()
    {
        $config = new DiamanteDesk_Config();
        return $config->getContent();
    }

    public function hookDisplayBackOfficeHeader($params)
    {
        $this->context->controller->addCSS(dirname(__FILE__) . '/css/admin.css');
    }
}