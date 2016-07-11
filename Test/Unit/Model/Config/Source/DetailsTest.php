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

namespace SendPulse\Mageia\Test\Unit\Model\Config\Soruce;

class DetailsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \SendPulse\Mageia\Model\Config\Source\Details
     */
    protected $_collection;
    protected $_collectionEmpty;
    /**
     * @var \|\PHPUnit_Framework_MockObject_MockObject|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_apiMock;

    protected function setUp()
    {
//        $apiMock = $this->getMockBuilder('SendPulse\Mageia\Model\Api')
//            ->disableOriginalConstructor()
//            ->getMock();
        $helperMock = $this->getMockBuilder('SendPulse\Mageia\Helper\Data')
            ->disableOriginalConstructor()
            ->getMock();
        $mcapiMock = $this->getMockBuilder('SendPulse\Mageia\Model\MCAPI')
            ->disableOriginalConstructor()
            ->getMock();

        $helperMock->expects($this->any())
            ->method('getApiKey')
            ->willReturn('apikey');

        $options = array('account_name'=>'sendpulse','total_subscribers'=>5,'contact'=>(object)array('company'=>'sendpulse'));
        $mcapiMock->expects($this->any())
            ->method('info')
            ->willReturn((object)$options);

//        $apiMock->expects($this->any())
//            ->method('loadByStore')
//            ->willReturn($mcapiMock);

        $storeManagerMock = $this->getMockBuilder('Magento\Store\Model\StoreManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $storeMock = $this->getMockBuilder('Magento\Store\Model\Store')
            ->disableOriginalConstructor()
            ->getMock();
        $storeMock->expects($this->any())
            ->method('getId')
            ->willReturn(1);
        $storeManagerMock->expects($this->any())
            ->method('getStore')
            ->willReturn($storeMock);

        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $apiMock = $objectManager->getObject('SendPulse\Magemonkey\Model\Api',
            [
                'helper' => $helperMock,
                'mcapi'  => $mcapiMock,
                'storeManager' => $storeManagerMock
            ]
        );

        $mcapiEmptyMock = $this->getMockBuilder('SendPulse\Mageia\Model\MCAPI')
            ->disableOriginalConstructor()
            ->getMock();
        $optionsEmpty = (object)array('nolists'=>(object)array((object)array()));
        $mcapiEmptyMock->expects($this->any())
            ->method('info')
            ->willReturn($optionsEmpty);

        $apiEmptyMock = $objectManager->getObject('SendPulse\Magemonkey\Model\Api',
            [
                'helper' => $helperMock,
                'mcapi'  => $mcapiEmptyMock,
                'storeManager' => $storeManagerMock
            ]
        );
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->_collection = $objectManager->getObject('SendPulse\Mageia\Model\Config\Source\Details',
            [
                'helper' => $helperMock,
                'api' => $apiMock
            ]
        );
        $this->_collectionEmpty = $objectManager->getObject('SendPulse\Mageia\Model\Config\Source\Details',
            [
                'api' => $apiEmptyMock,
                'helper' => $helperMock
            ]
        );

    }
    public function testToOptionArray()
    {
        $this->_collectionEmpty->toOptionArray();
        $this->assertNotEmpty($this->_collection->toOptionArray());

        foreach ($this->_collection->toOptionArray() as $item) {
            $this->assertArrayHasKey('value', $item);
            $this->assertArrayHasKey('label', $item);
        }
    }

}