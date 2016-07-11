<?php
/**
 * Mageia Magento Component
 *
 * @category SendPulse
 * @package Mageia
 * @author SendPulse Team <info@sendpulse.com>
 * @copyright SendPulse (http://sendpulse.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @date: 3/11/16 3:30 PM
 * @file: MCADITest.php
 */
namespace SendPulse\Mageia\Test\Unit\Model;

class MCAPITest extends \PHPUnit_Framework_TestCase
{
    protected $_mcapi;

    public function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $curlMock = $this->getMockBuilder('Magento\Framework\HTTP\Adapter\Curl')
            ->disableOriginalConstructor()
            ->getMock();
        $curlMock->expects($this->any())
            ->method('addOption')
            ->willReturn(true);
        $curlMock->expects($this->any())
            ->method('connect')
            ->willReturn(true);
        $curlMock->expects($this->any())
            ->method('read')
            ->willReturn(true);
        $curlMock->expects($this->any())
            ->method('getInfo')
            ->willReturn(true);
        $curlMock->expects($this->any())
            ->method('close')
            ->willReturn(true);

        $helperMock = $this->getMockBuilder('SendPulse\Mageia\Helper\Data')
            ->disableOriginalConstructor()
            ->getMock();
        $helperMock->expects($this->any())
            ->method('getApiKey')
            ->willReturn('api-key');


        $this->_mcapi = $objectManager->getObject('SendPulse\Magemonkey\Model\MCAPI',
            [
                'helper' => $helperMock,
                'curl'  => $curlMock
            ]);
    }

    /**
     * @covers SendPulse\Mageia\Model\MCAPI::load
     * @covers SendPulse\Mageia\Model\MCAPI::getApiKey
     */
    public function testLoad()
    {
        $mcapi = $this->_mcapi->load('apikey');
        $this->assertEquals($mcapi->getApiKey(),'apikey');
    }
    /**
     * @covers SendPulse\Mageia\Model\MCAPI::setTimeout
     * @covers SendPulse\Mageia\Model\MCAPI::getTimeout
     */
    public function testTimeout()
    {
        $this->_mcapi->setTimeout(10);
        $this->assertEquals($this->_mcapi->getTimeout(),10);
    }
    /**
     * @covers SendPulse\Mageia\Model\MCAPI::info
     * @covers SendPulse\Mageia\Model\MCAPI::callServer
     */
    public function testInfo()
    {
        $this->_mcapi->info();
    }
    /**
     * @covers SendPulse\Mageia\Model\MCAPI::lists
     * @covers SendPulse\Mageia\Model\MCAPI::callServer
     */
    public function testLists()
    {
        $this->_mcapi->lists();
    }
    /**
     * @covers SendPulse\Mageia\Model\MCAPI::listMembers
     * @covers SendPulse\Mageia\Model\MCAPI::callServer
     */
    public function testListMembers()
    {
        $this->_mcapi->listMembers(1);
    }
    /**
     * @covers SendPulse\Mageia\Model\MCAPI::listCreateMember
     * @covers SendPulse\Mageia\Model\MCAPI::callServer
     */
    public function testListCreateMember()
    {
        $this->_mcapi->listCreateMember(1,['name'=>'name']);
    }
    /**
     * @covers SendPulse\Mageia\Model\MCAPI::listDeleteMember
     * @covers SendPulse\Mageia\Model\MCAPI::callServer
     * @covers SendPulse\Mageia\Model\MCAPI::getHost
     */
    public function testListDeleteMember()
    {
        $this->_mcapi->listDeleteMember(1,1);
    }
}
