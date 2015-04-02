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
class DiamanteDesk_Api
{
    const API_URL_POSTFIX = '/api/rest/v1/';

    const API_RESPONSE_FORMAT = 'json';

    /** @var resource */
    protected $_ch;

    /** @var array */
    protected $_config = array();

    /** @var array */
    protected $allowedStatuses = array(200, 201, 202, 204, 304);

    public $result;

    public $resultHeaders;

    /** @var array */
    protected $_postData = array();

    /** @var array */
    protected $_getData = array();

    /** @var string */
    protected $_url = '';

    /** @var string */
    protected $_httpMethod = 'GET';

    /**
     * @return $this
     */
    public function init()
    {
        $this->initConfig()
            ->initCurl()
            ->setHeaders()
            ->setHttpMethod('GET');
        return $this;
    }

    /**
     * @param null $userName
     * @param null $apiKey
     * @param null $serverAddress
     * @return $this
     */
    public function initConfig($userName = null, $apiKey = null, $serverAddress = null)
    {
        /** Check is config already initialized */
        if (count($this->_config)) return $this;

        $this->_config['userName'] = $userName ? $userName : Configuration::get('DIAMANTEDESK_USERNAME');
        $this->_config['apiKey'] = $apiKey ? $apiKey : Configuration::get('DIAMANTEDESK_API_KEY');
        $this->_config['serverAddress'] = $serverAddress ? $serverAddress : Configuration::get('DIAMANTEDESK_SERVER_ADDRESS');

        return $this;
    }

    /**
     * @return $this
     */
    public function initCurl()
    {
        if ($this->_ch) {
            return $this;
        }
        $this->_ch = curl_init();
        curl_setopt($this->_ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->_ch, CURLOPT_HEADER, true);
        return $this;
    }

    /**
     * @return $this
     */
    public function setHeaders()
    {
        curl_setopt(
            $this->_ch,
            CURLOPT_HTTPHEADER,
            array(
                'Accept: application/' . static::API_RESPONSE_FORMAT,
                'Authorization: WSSE profile="UsernameToken"',
                'X-WSSE: ' . $this->_getWsseHeader()
            )
        );
        return $this;
    }

    /**
     * @return string
     */
    protected function _getWsseHeader()
    {
        $nonce = Tools::passwdGen(10);
        $created = new DateTime('now', new DateTimezone('UTC'));
        $created = $created->format(DateTime::ISO8601);
        $digest = sha1($nonce . $created . $this->_config['apiKey'], true);

        return sprintf(
            'UsernameToken Username="%s", PasswordDigest="%s", Nonce="%s", Created="%s"',
            $this->_config['userName'],
            base64_encode($digest),
            base64_encode($nonce),
            $created
        );
    }

    /**
     * @param $method
     * @return $this
     */
    public function setHttpMethod($method)
    {
        if ($method == 'POST') {
            $this->_httpMethod = 'POST';
            curl_setopt($this->_ch, CURLOPT_POST, 1);
        } elseif ($method == 'GET') {
            $this->_httpMethod = 'GET';
            curl_setopt($this->_ch, CURLOPT_POST, 0);
        }
        return $this;
    }

    /**
     * @param $method
     * @return $this
     */
    public function setMethod($method)
    {
        $this->_url = trim($this->_config['serverAddress'], '/') . static::API_URL_POSTFIX . $method . '.' . static::API_RESPONSE_FORMAT;
        curl_setopt($this->_ch, CURLOPT_URL, $this->_url);
        return $this;
    }

    /**
     * @return $this
     */
    public function doRequest()
    {

        /** add post data before do request */
        if ($this->_httpMethod == 'POST') {
            $fieldsString = '';
            foreach ($this->_postData as $key => $value) {
                $fieldsString .= $key . '=' . $value . '&';
            }
            $fieldsString = rtrim($fieldsString, '&');
            curl_setopt($this->_ch, CURLOPT_POST, count($this->_postData));
            curl_setopt($this->_ch, CURLOPT_POSTFIELDS, $fieldsString);
        }

        /** add get parameters to uri before request */
        if ($this->_httpMethod == 'GET') {
            $fieldsString = '';
            foreach ($this->_getData as $key => $value) {
                $fieldsString .= $key . '=' . $value . '&';
            }
            $fieldsString = rtrim($fieldsString, '&');
            if ($fieldsString) {
                $this->_url = $this->_url . '?' . $fieldsString;
            }
            curl_setopt($this->_ch, CURLOPT_URL, $this->_url);
        }

        $result = curl_exec($this->_ch);

        $header_size = curl_getinfo($this->_ch, CURLINFO_HEADER_SIZE);
        $header = substr($result, 0, $header_size);
        $body = substr($result, $header_size);

        if ($header) {
            $this->resultHeaders = http_parse_headers($header);
        }

        if ($body) {
            $this->result = json_decode($body);
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBranches()
    {
        $this->init()
            ->setMethod('desk/branches')
            ->doRequest();
        return $this->result;
    }

    /**
     * @return mixed
     */
    public function getTickets()
    {
        $this->init()
            ->setMethod('desk/tickets')
            ->doRequest();
        return $this->result;
    }

    /**
     * @param $ticketId
     * @return mixed
     */
    public function getTicket($ticketId)
    {
        $this->init()
            ->setMethod('desk/tickets/' . $ticketId)
            ->doRequest();
        return $this->result;
    }

    /**
     * @return mixed
     */
    public function getUsers()
    {
        $this->init()
            ->setMethod('users')
            ->doRequest();

        return $this->result;
    }

    public function getDefaultUser()
    {
        $this->init()
            ->setMethod('user/filter')
            ->setHttpMethod('GET')
            ->addGetData('username', Configuration::get('DIAMANTEDESK_USERNAME'))
            ->doRequest();

        return $this->result;
    }

    public function getUserById($id)
    {
        $this->init()
            ->setMethod('users/' . $id)
            ->setHttpMethod('GET')
            ->doRequest();

        return $this->result;
    }

    /**
     * @param $data = {
     *      content
     *      ticket
     *      author
     *      ticketStatus
     * }
     *
     * @return mixed
     */
    public function addComment($data)
    {
        if (!isset($data['author'])) {
            $data['author'] = 'oro_' . $this->getDefaultUser()->id;
        }

        $this->init()
            ->setMethod('desk/comments')
            ->setHttpMethod('POST');

        foreach ($data as $key => $value) {
            $this->addPostData($key, $value);
        }

        $this->doRequest();

        if ($this->result && isset($this->result->status) && $this->result->status == 'error') {
            return false;
        }

        return $this->result;
    }

    public function createTicket($data)
    {
        $data['source'] = 'web';

        if (!isset($data['status'])) {
            $data['status'] = 'new';
        }

        if (!isset($data['priority'])) {
            $data['priority'] = 'low';
        }

        if (!isset($data['branch'])) {
            $data['branch'] = Configuration::get('DIAMANTEDESK_DEFAULT_BRANCH');
        }

        if (!isset($data['reporter'])) {
            $user = $this->getDefaultUser();
            if ($user instanceof stdClass) {
                $data['reporter'] = $user->id;
            }
        }

        if ($data['reporter'] == (int)($data['reporter'])) {
            $data['reporter'] = 'oro_' . $data['reporter'];
        }

        if (!isset($data['assignee'])) {
            $branches = $this->getBranches();
            foreach ($branches as $branch) {
                if (!$data['branch']) {
                    $data['branch'] = $branch->id;
                }
                if ($branch->id == (int)$data['branch']) {
                    $data['assignee'] = $branch->default_assignee;
                }
            }
        }

        foreach ($data as $key => $value) {
            $this->addPostData($key, $value);
        }

        $this->init()
            ->setHttpMethod('POST')
            ->setMethod('desk/tickets')
            ->doRequest();

        if ($this->result && isset($this->result->status) && $this->result->status == 'error') {
            return false;
        }

        if (isset($data['id_order'])) {
            $relationModel = getOrderRelationModel();
            $relationModel->saveRelation($this->result->id, $data['id_order']);
        }

        return true;

    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function addPostData($key, $value)
    {
        $this->_postData[$key] = $value;
        return $this;
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function addGetData($key, $value)
    {
        $this->_getData[$key] = $value;
        return $this;
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function addFilter($key, $value)
    {
        $this->addGetData($key, $value);
        return $this;
    }

}

/**
 * @return DiamanteDesk_Api
 */
function getDiamanteDeskApi()
{
    return new DiamanteDesk_Api();
}

if (!function_exists('http_parse_headers')) {
    function http_parse_headers($raw_headers) {
        $headers = array();
        $key = '';

        foreach(explode("\n", $raw_headers) as $i => $h) {
            $h = explode(':', $h, 2);

            if (isset($h[1])) {
                if (!isset($headers[$h[0]]))
                    $headers[$h[0]] = trim($h[1]);
                elseif (is_array($headers[$h[0]])) {
                    $headers[$h[0]] = array_merge($headers[$h[0]], array(trim($h[1])));
                }
                else {
                    $headers[$h[0]] = array_merge(array($headers[$h[0]]), array(trim($h[1])));
                }

                $key = $h[0];
            }
            else {
                if (substr($h[0], 0, 1) == "\t")
                    $headers[$key] .= "\r\n\t".trim($h[0]);
                elseif (!$key)
                    $headers[0] = trim($h[0]);
            }
        }

        return $headers;
    }
}