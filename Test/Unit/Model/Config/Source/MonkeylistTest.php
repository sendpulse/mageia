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

namespace SendPulse\Mageia\Test\Unit\Model\Config\Source;

class SendlistTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var
     */
    protected $_options;

    protected function setUp()
    {
        $helperMock = $this->getMockBuilder('SendPulse\Mageia\Helper\Data')
            ->disableOriginalConstructor()
            ->getMock();
        $mcapiMock = $this->getMockBuilder('SendPulse\Mageia\Model\MCAPI')
            ->disableOriginalConstructor()
            ->getMock();
        $apiEmptyMock = $this->getMockBuilder('SendPulse\Mageia\Model\Api')
            ->disableOriginalConstructor()
            ->getMock();
        $mcapiEmptyMock = $this->getMockBuilder('SendPulse\Mageia\Model\MCAPI')
            ->disableOriginalConstructor()
            ->getMock();

        $helperMock->expects($this->any())
            ->method('getApiKey')
            ->willReturn('apikey');

        $options = (object)array('lists'=>(object)array((object)array('id'=>1,'name'=>'list1'),(object)array('id'=>2,'name'=>'list2')));
        $optionsEmpty = (object)array('nolists'=>(object)array((object)array()));

        $mcapiMock->expects($this->any())
            ->method('lists')
            ->willReturn($options);

        $mcapiEmptyMock->expects($this->any())
            ->method('lists')
            ->willReturn($optionsEmpty);

        $apiEmptyMock->expects($this->any())
            ->method('loadByStore')
            ->willReturn($apiEmptyMock);
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

        $this->_options = $objectManager->getObject('SendPulse\Mageia\Model\Config\Source\Sendlist',
            [
                'api' => $apiMock,
                'helper' => $helperMock
            ]
        );
        $this->_optionsEmpty = $objectManager->getObject('SendPulse\Mageia\Model\Config\Source\Sendlist',
            [
                'api' => $apiEmptyMock,
                'helper' => $helperMock
            ]
        );
        $this->api = $apiMock;
    }

    public function testToOptionArray()
    {
        //$this->assertNotEmpty($this->_options->lists);
        $this->api->loadByStore(1);
        $this->assertNotEmpty($this->_options->toOptionArray());
        foreach ($this->_options->toOptionArray() as $item) {
            $this->assertArrayHasKey('value', $item);
            $this->assertArrayHasKey('label', $item);
        }
        $this->assertNotEmpty($this->_optionsEmpty->toOptionArray());
    }

    public function testToArray()
    {
        $this->assertNotEmpty($this->_options->toArray());
        foreach ($this->_options->toOptionArray() as $item) {
            $this->assertArrayHasKey('value', $item);
            $this->assertArrayHasKey('label', $item);
        }
    }

}