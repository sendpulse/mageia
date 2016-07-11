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


namespace SendPulse\Mageia\Model\Config\Source;

class Details implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \SendPulse\Mageia\Model\Api|null
     */
    protected $_api = null;
    /**
     * @var null
     */
    protected $_options = null;
    /**
     * @var \SendPulse\Mageia\Helper\Data|null
     */
    protected $_helper = null;

    /**
     * @param \SendPulse\Mageia\Helper\Data $helper
     */
    public function __construct(
        \SendPulse\Mageia\Helper\Data $helper,
        \SendPulse\Mageia\Model\Api $api
    ) {
        $this->_helper = $helper;
        $this->_api = $api;

        if ($helper->getApiKey() && $helper->getApiSecret()) {
            $this->_options = $this->_api->info();
        }
    }

    public function toOptionArray()
    {
        if (isset($this->_options->email)) {
            return [
                ['value' => 'Name', 'label' => $this->_options->name],
                ['value' => 'Email', 'label' => $this->_options->email],
                ['value' => 'Phone', 'label' => $this->_options->phone],
            ];
        } else {
            return [
                ['value' => 'Error', 'label' => __('Invalid API Key')]
            ];
        }
    }
//    public function toArray()
//    {
//        return array(
//            'Account Name' => $this->_options->account_name
//        );
//
//    }
}
