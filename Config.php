<?php

class DiamanteDesk_Config extends DiamanteDesk
{
    public function getContent()
    {
        $output = null;
        if (Tools::isSubmit('submit')) {
            Configuration::updateValue('DIAMANTEDESK_SERVER_ADDRESS', Tools::getValue('DIAMANTEDESK_SERVER_ADDRESS'));
            Configuration::updateValue('DIAMANTEDESK_USERNAME', Tools::getValue('DIAMANTEDESK_USERNAME'));
            Configuration::updateValue('DIAMANTEDESK_API_KEY', Tools::getValue('DIAMANTEDESK_API_KEY'));
            Configuration::updateValue('DIAMANTEDESK_DEFAULT_BRANCH', Tools::getValue('DIAMANTEDESK_DEFAULT_BRANCH'));
            $output .= $this->displayConfirmation($this->l('Settings updated'));
        }
        return $output . $this->renderForm();
    }


    public function renderForm()
    {

        $options = array(
            array(
                'id_option' => 1,
                'name' => 'All'
            ), array(
                'id_option' => 2,
                'name' => 'All 1'
            ), array(
                'id_option' => 3,
                'name' => 'All 2'
            ), array(
                'id_option' => 4,
                'name' => 'All 3'
            ),
        );

        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('DiamanteDesk Settings'),
                    'icon' => 'icon-AdminDiamanteDeskDark'
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Server Address'),
                        'name' => 'DIAMANTEDESK_SERVER_ADDRESS',
                        'required' => true
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Username'),
                        'name' => 'DIAMANTEDESK_USERNAME',
                        'required' => true
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Api Key'),
                        'name' => 'DIAMANTEDESK_API_KEY',
                        'required' => true
                    ),
                    array(
                        'type' => 'select',
                        'lang' => true,
                        'label' => $this->l('Default Branch'),
                        'name' => 'DIAMANTEDESK_DEFAULT_BRANCH',
                        'options' => array(
                            'query' => $options,
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 1;
        $helper->id = (int)Tools::getValue('id_carrier');
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submit';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return $helper->generateForm(array($fields_form));
    }

    public function getConfigFieldsValues()
    {
        return array(
            'DIAMANTEDESK_SERVER_ADDRESS' => Tools::getValue('DIAMANTEDESK_SERVER_ADDRESS', Configuration::get('DIAMANTEDESK_SERVER_ADDRESS')),
            'DIAMANTEDESK_USERNAME' => Tools::getValue('DIAMANTEDESK_USERNAME', Configuration::get('DIAMANTEDESK_USERNAME')),
            'DIAMANTEDESK_API_KEY' => Tools::getValue('DIAMANTEDESK_API_KEY', Configuration::get('DIAMANTEDESK_API_KEY')),
            'DIAMANTEDESK_DEFAULT_BRANCH' => Tools::getValue('DIAMANTEDESK_DEFAULT_BRANCH', Configuration::get('DIAMANTEDESK_DEFAULT_BRANCH')),
        );
    }

}