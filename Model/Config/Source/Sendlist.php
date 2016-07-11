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

class Sendlist implements \Magento\Framework\Option\ArrayInterface
{
    protected $_api     = null;
    protected $_options = null;
    protected $_helper  = null;
    /**
     * @param \SendPulse\Mageia\Helper\Data $helper
     */
    public function __construct(
        \SendPulse\Mageia\Helper\Data $helper,
        \SendPulse\Mageia\Model\Api $api
    )
    {

        $this->_helper = $helper;
        $this->_api = $api;
        if ($helper->getApiKey() && $helper->getApiSecret()) {
            $this->_options = $this->_api->lists();
        }
    }
    public function toOptionArray()
    {
        if(isset($this->_options)) {
            $rc = array();
            foreach($this->_options as $list)
            {
                if(isset($list->id) && isset($list->name)) {
                    $rc[] = array('value' => $list->id, 'label' => $list->name);
                }
            }
            return $rc;
        }else{
            return array(array('value' => 0, 'label' => __('---No Data---')));
        }
    }

    public function toArray()
    {
        $rc = array();
        foreach($this->_options->lists as $list)
        {
            $rc[$list->id] = $list->name;
        }
        return $rc;
    }
}