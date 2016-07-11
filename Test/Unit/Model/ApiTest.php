<?php
/**
 * Mageia Magento Component
 *
 * @category SendPulse
 * @package Mageia
 * @author SendPulse Team <info@sendpulse.com>
 * @copyright SendPulse (http://sendpulse.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @date: 3/7/16 3:49 PM
 * @file: ApiTest.php
 */
namespace SendPulse\Mageia\Test\Unit\Model;

class ApiTest  extends \PHPUnit_Framework_TestCase
{
    protected $api;
    public function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $helperMock = $this->getMockBuilder('SendPulse\Mageia\Helper\Data')
            ->disableOriginalConstructor()
            ->getMock();
        $helperMock->expects($this->any())
            ->method('getApiKey')
            ->willReturn('api-key');

        $mcapiMock = $this->getMockBuilder('SendPulse\Mageia\Model\MCAPI')
            ->disableOriginalConstructor()
            ->getMock();
        $mcapiMock->expects($this->once())
            ->method('info')
            ->willReturn(true);
        $mcapiMock->expects($this->once())
            ->method('listMembers')
            ->willReturn(true);
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

        $this->api = $objectManager->getObject('SendPulse\Mageia\Model\Api',
            [
                'storeManager' => $storeManagerMock,
                'helper' => $helperMock,
                'mcapi'  => $mcapiMock
            ]);
    }

    /**
     * @covers SendPulse\Mageia\Model\Api::call
     */
    public function testCall()
    {
        $this->api->info();
        $this->api->listMembers(1);
    }
}