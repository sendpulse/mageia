<?php
/**
 * SendPulse_Mageia Magento component
 *
 * @category    SendPulse
 * @package     SendPulse_Mageia
 * @author      SendPulse Team <info@sendpulse.com>
 * @copyright   SendPulse (http://sendpulse.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace SendPulse\Mageia\Model;

class SPAPI
{
    protected $_version = "1.0";
    protected $_timeout = 300;
    protected $_chunkSize = 8192;
    protected $_apiKey = null;
    protected $_apiSecret = null;
    protected $_secure = false;
    protected $_token = null;
    /**
     * @var \SendPulse\Mageia\Helper\Data|null
     */
    protected $_helper = null;
    /**
     * @var \Magento\Framework\HTTP\Adapter\Curl|null
     */
    protected $_curl = null;

    /**
     * SPAPI constructor.
     *
     * @param \SendPulse\Mageia\Helper\Data        $helper
     * @param \Magento\Framework\HTTP\Adapter\Curl $curl
     */
    public function __construct(
        \SendPulse\Mageia\Helper\Data $helper,
        \Magento\Framework\HTTP\Adapter\Curl $curl,
        \Magento\Newsletter\Model\Session $session,
        \SendPulse\Mageia\Model\Logger\SendPulse $logger
    ) {
        $this->_helper = $helper;
        $this->_curl = $curl;
        $this->_apiKey = $helper->getApiKey();
        $this->_apiSecret = $helper->getApiSecret();
        $this->_secure = false;
        $this->_session = $session;
        $this->_logger = $logger;

        $this->_token = $this->getApiToken();
    }

    public function getApiToken()
    {
        $this->_token = $this->_session->getToken();

        if (!$this->_token) {
            $data = $this->callServer(
                'POST', 'oauth/access_token', null,
                [
                    'grant_type'    => 'client_credentials',
                    'client_id'     => $this->_apiKey,
                    'client_secret' => $this->_apiSecret
                ]
            );

            $this->_logger->debug('Token', [$data]);

            if ($data->access_token) {
                $this->_token = $data->access_token;
                $this->_session->setToken($data->access_token);
                $this->_session->addSuccess('Token received: ' . $data->access_token);
            } else {
                $this->_session->addError('Something went wrong!');
            }
        }

        return $this->_token;
    }

    public function load($apiKey, $secure = false)
    {
        $this->_apiKey = $apiKey;
        $this->_secure = $secure;
        return $this;
    }

    public function setTimeout($seconds)
    {
        if (is_int($seconds)) {
            $this->_timeout = $seconds;
        }
    }

    public function getTimeOut()
    {
        return $this->_timeout;
    }

    protected function useSecure($val)
    {
        if ($val === true) {
            $this->_secure = true;
        } else {
            $this->_secure = false;
        }
        return $this;
    }

    protected function callServer($use = 'GET', $method = null, $params = null,
        $fields = null
    ) {
        $host = $this->getHost($method, $params);
        $curl = $this->_curl;
        $curl->addOption(CURLOPT_POST, false);

        switch ($use) {
            case 'POST':
                $curl->addOption(CURLOPT_POST, count($fields));
                $curl->addOption(CURLOPT_POSTFIELDS, http_build_query($fields));
                break;
            case 'DELETE':
                $curl->addOption(CURLOPT_CUSTOMREQUEST, 'DELETE');
                $curl->addOption(CURLOPT_POSTFIELDS, http_build_query($fields));
                break;
            case 'PATCH':
                $curl->addOption(CURLOPT_CUSTOMREQUEST, 'PATCH');
                $curl->addOption(CURLOPT_POSTFIELDS, http_build_query($fields));
                break;
            case 'PUT':
                $curl->addOption(CURLOPT_CUSTOMREQUEST, 'PUT');
                $curl->addOption(CURLOPT_POSTFIELDS, http_build_query($fields));
                break;
            default:
                break;
        }

        $curl->addOption(CURLOPT_URL, $host);
        $curl->addOption(CURLOPT_USERAGENT, 'Mageia/');
        $curl->addOption(CURLOPT_HEADER, true);

        $headers = [];
//        $headers[] = 'Content-Type: application/json';
//        $headers[] = 'Content-Type: multipart/form-data';
//        $headers[] = 'Content-Type: multipart/x-www-form-urlencoded';
        $headers[] = 'Cache-Control: no-cache';

        if ($this->_token) {
            $headers[] = 'Authorization: Bearer ' . $this->_token;
        }
        $curl->addOption(CURLOPT_HTTPHEADER, $headers);

        $curl->addOption(CURLOPT_RETURNTRANSFER, 1);
        $curl->addOption(CURLOPT_CONNECTTIMEOUT, 30);
        $curl->addOption(CURLOPT_TIMEOUT, $this->_timeout);
        $curl->addOption(CURLOPT_FOLLOWLOCATION, 1);
        $curl->connect($host);
        $response = $curl->read();
        $body = preg_split('/^\r?$/m', $response);
        $responseCode = $curl->getInfo(CURLINFO_HTTP_CODE);
        $curl->close();
        $data = json_decode($body[count($body) - 1]);
//        switch ($use) {
//            case 'DELETE':
//                if ($responseCode != 204) {
//                    throw new \Exception(
//                        'Type: ' . $data->error . ' Code: ' . $data->error_code
//                        . ' Message: ' . $data->message . ' Detail: '
//                        . $data->error_description
//                    );
//                }
//                break;
//            case 'PUT':
//            case 'POST':
//            case 'PATCH':
//                if ($responseCode != 200) {
//                    throw new \Exception(
//                        'Type: ' . $data->error . ' Code: ' . $data->error_code
//                        . ' Message: ' . $data->message . ' Detail: '
//                        . $data->error_description
//                    );
//                }
//                break;
//        }
        return $data;
    }

    protected function getHost($method, $params)
    {
        $host = \SendPulse\Mageia\Model\Config::SCHEME . '://'
            . \SendPulse\Mageia\Model\Config::ENDPOINT;

        if ($method) {
            $host .= "/$method";
        }

        if (is_array($params)) {
            foreach ($params as $pkey => $value) {
                if (is_numeric($pkey)) {
                    $host .= "/$value";
                } else {
                    $host .= "/$pkey/$value";
                }
            }
        }

        return trim($host);
    }

    public function info()
    {
        return $this->callServer('GET', 'user/info');
    }

    public function lists()
    {
        return $this->callServer('GET', 'addressbooks');
    }

    public function listCreateMember($listId, $members) {
        $url = sprintf('addressbooks/%u/emails', $listId);

        return $this->callServer('POST', $url, null, $members);
    }

    public function listDeleteMember($listId, $members)
    {
        $url = sprintf('addressbooks/%u/emails', $listId);

        return $this->callServer('DELETE', $url, null, $members);
    }
}