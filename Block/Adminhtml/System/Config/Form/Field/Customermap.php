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

namespace SendPulse\Mageia\Block\Adminhtml\System\Config\Form\Field;

class Customermap extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    /**
     * @var \Magento\Framework\Data\Form\Element\Factory
     */
    protected $_elementFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Data\Form\Element\Factory $elementFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Data\Form\Element\Factory $elementFactory,
        array $data = []
    )
    {
        $this->_elementFactory  = $elementFactory;
        parent::__construct($context,$data);
    }
    protected function _construct()
    {
        $this->addColumn('magento', ['label' => __('Magento')]);
        $this->addColumn('sendpulse', ['label' => __('SendPulse')]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
        parent::_construct();
    }

}