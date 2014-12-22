<?php

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

    /** @var array */
    protected $_postData = array();

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
        $this->_ch = curl_init();
        curl_setopt($this->_ch, CURLOPT_HEADER, true);
        curl_setopt($this->_ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->_ch, CURLOPT_HEADER, false);
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
        curl_setopt($this->_ch, CURLOPT_URL, trim($this->_config['serverAddress'], '/') . static::API_URL_POSTFIX . $method . '.' . static::API_RESPONSE_FORMAT);
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
            rtrim($fieldsString, '&');
            curl_setopt($this->_ch, CURLOPT_POST, count($this->_postData));
            curl_setopt($this->_ch, CURLOPT_POSTFIELDS, $fieldsString);
        }

        $result = curl_exec($this->_ch);
        if ($result) {
            $this->result = json_decode($result);
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
     * @return mixed
     */
    public function getUsers()
    {
        $this->init()
            ->setMethod('users')
            ->doRequest();

        return $this->result;
    }

    public function saveTicket($data)
    {
        $data['source'] = 'web';

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
        return true;

    }

    public function addPostData($key, $value)
    {
        $this->_postData[$key] = $value;
    }

}

/**
 * @return DiamanteDesk_Api
 */
function getDiamanteDeskApi()
{
    return new DiamanteDesk_Api();
}