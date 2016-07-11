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

class Api
{
    /**
     * @var SPAPI|null
     */
    protected $_spapi = null;
    /**
     * @var \SendPulse\Mageia\Helper\Data|null
     */
    protected $_helper = null;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface|null
     */
    protected $_storeManager = null;

    /**
     * Api constructor.
     *
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \SendPulse\Mageia\Helper\Data              $helper
     * @param SPAPI                                      $spapi
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \SendPulse\Mageia\Helper\Data $helper,
        \SendPulse\Mageia\Model\SPAPI $spapi
    ) {
        $this->_helper = $helper;
        $this->_spapi = $spapi;
        $this->_storeManager = $storeManager;

    }

    public function __call($method, $args = null)
    {
        return $this->call($method, $args);
    }

    public function call($command, $args)
    {
        $result = null;
        if ($args) {
            if (is_callable(array($this->_spapi, $command))) {
                $reflectionMethod = new \ReflectionMethod(
                    $this->_spapi, $command
                );
                $result = $reflectionMethod->invokeArgs($this->_spapi, $args);
            }
        } else {
            if (is_callable(array($this->_spapi, $command))) {
                $result = $this->_spapi->{$command}();
            }
        }
        return $result;
    }
}