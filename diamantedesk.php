<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

include_once(dirname(__FILE__) . '/Api.php');
include_once(dirname(__FILE__) . '/Config.php');
include_once(dirname(__FILE__) . '/Ticket.php');

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
        $this->registerHook('displayBackOfficeTop');
        $this->registerHook('customerAccount');
        $this->registerHook('displayMyAccountBlock');

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
        $this->unregisterHook('displayBackOfficeTop');
        $this->unregisterHook('customerAccount');
        $this->unregisterHook('displayMyAccountBlock');

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

    public function hookDisplayBackOfficeTop($params)
    {
        /** @var Smarty_Internal_Template $tpl */
        $tpl = $this->context->smarty->createTemplate(dirname(__FILE__) . '/views/templates/admin/diamante_desk/configuration.tpl');
        $tpl->assign('diamantedesk_server_address', Configuration::get('DIAMANTEDESK_SERVER_ADDRESS'));
        $tpl->display();
    }

    public function hookCustomerAccount($params)
    {
        return $this->display(dirname(__FILE__), '/views/my-account.tpl');
    }

    public function hookDisplayMyAccountBlock($params)
    {
        return $this->hookCustomerAccount($params);
    }
}