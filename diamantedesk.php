<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

include_once(dirname(__FILE__) . '/Api.php');
include_once(dirname(__FILE__) . '/Config.php');
include_once(dirname(__FILE__) . '/Ticket.php');
include_once(dirname(__FILE__) . '/OrderRelation.php');

class DiamanteDesk extends Module
{
    const INSTALL_SQL_FILE = 'install.sql';

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

    public function install($keep = true)
    {
        if ($keep) {
            if (!file_exists(dirname(__FILE__) . '/' . self::INSTALL_SQL_FILE))
                return false;
            else if (!$sql = file_get_contents(dirname(__FILE__) . '/' . self::INSTALL_SQL_FILE))
                return false;
            $sql = str_replace(array('PREFIX_', 'ENGINE_TYPE'), array(_DB_PREFIX_, _MYSQL_ENGINE_), $sql);
            $sql = preg_split("/;\s*[\r\n]+/", trim($sql));

            foreach ($sql as $query)
                if (!Db::getInstance()->execute(trim($query)))
                    return false;
        }

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
        $this->registerHook('displayOrderDetail');
        $this->registerHook('actionDispatcher');

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
        $this->unregisterHook('displayOrderDetail');
        $this->unregisterHook('actionDispatcher');

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
        return $tpl->fetch();
    }

    public function hookCustomerAccount($params)
    {
        return $this->display(dirname(__FILE__), '/views/my-account.tpl');
    }

    public function hookDisplayMyAccountBlock($params)
    {
        return $this->hookCustomerAccount($params);
    }

    /**
     * Add submit ticket form & related tickets list
     * To customer order view page
     *
     * @param $params
     * @return string
     */
    public function hookDisplayOrderDetail($params)
    {
        $relation = new DiamanteDesk_OrderRelation();
        $ticketsIds = $relation->getRelatedTickets($params['order']->id);
        $relatedTickets = array();

        //TODO: should be based on API filter
        $tickets = getDiamanteDeskApi()->getTickets();
        foreach ($tickets as $ticket) {
            if (in_array($ticket->id, $ticketsIds)) {
                $relatedTickets[] = $ticket;
            }
        }

        $this->smarty->assign('related_tickets', $relatedTickets);
        return $this->display(dirname(__FILE__), '/views/orderdetails.tpl');
    }


    /**
     * Hook for submit ticket to DiamanteDesk when
     * customer use standard PrestaShop "contact-us" form
     *
     * @param $params
     */
    public function hookActionDispatcher($params)
    {
        if ($params['controller_class'] === 'ContactController' && Tools::isSubmit('submitMessage')) {

            /** @var DiamanteDesk_Api $api */
            $api = getDiamanteDeskApi();

            $data = array(
                'subject' => mb_substr($_POST['message'], 0, 15) . '...',
                'description' => $_POST['message']
            );

            if (isset($_POST['id_order']) && $_POST['id_order']) {
                $data['id_order'] = $_POST['id_order'];
            }

            $api->saveTicket($data);
        }
    }
}