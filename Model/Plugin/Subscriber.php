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

namespace SendPulse\Mageia\Model\Plugin;


class Subscriber
{
    /**
     * @var \SendPulse\Mageia\Helper\Data
     */
    protected $_helper;
    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $_customer;
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;
    /**
     * @param \SendPulse\Mageia\Helper\Data    $helper
     * @param \Magento\Customer\Model\Customer $customer
     * @param \Magento\Customer\Model\Session  $customerSession
     */
    protected $_api = null;

    public function __construct(
        \SendPulse\Mageia\Helper\Data $helper,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Customer\Model\Session $customerSession,
        \SendPulse\Mageia\Model\Api $api

    ) {
        $this->_helper = $helper;
        $this->_customer = $customer;
        $this->_customerSession = $customerSession;
        $this->_api = $api;
    }

    public function beforeUnsubscribeCustomerById(
        $subscriber, $customerId
    ) {
        $subscriber->loadByCustomerId($customerId);
        if ($subscriber->getMagemonkeyId()) {
            //$api = $this->_api->loadByStore($subscriber->getStoreId());
            $this->_api->listDeleteMember(
                $this->_helper->getDefaultList(), $subscriber->getMagemonkeyId()
            );
            $subscriber->setMagemonkeyId('');
        }
    }

    public function beforeSubscribeCustomerById(
        $subscriber, $customerId
    ) {
        $subscriber->loadByCustomerId($customerId);
        $storeId = $subscriber->getStoreId();
        if ($this->_helper->isMageiaEnabled($storeId)) {
            $customer = $this->_customer->load($customerId);
            $mergeVars = $this->_helper->getMergeVars($customer);
            $api = $this->_api;
            $isSubscribeOwnEmail = $this->_customerSession->isLoggedIn()
                && $this->_customerSession->getCustomerDataObject()->getEmail()
                == $subscriber->getSubscriberEmail();
            if ($this->_helper->isDoubleOptInEnabled($storeId)
                && !$isSubscribeOwnEmail
            ) {
                $status = 'pending';
            } else {
                $status = 'subscribed';
            }
            if ($mergeVars) {
                $data = array('list_id'       => $this->_helper->getDefaultList(
                ), 'email_address'            => $customer->getEmail(),
                              'email_type'    => 'html', 'status' => $status,
                              'merge_fields'  => $mergeVars);
            } else {
                $data = array('list_id'       => $this->_helper->getDefaultList(
                ), 'email_address'            => $customer->getEmail(),
                              'email_type'    => 'html', 'status' => $status);
            }
            $return = $api->listCreateMember(
                $this->_helper->getDefaultList(), json_encode($data)
            );
            if (isset($return->id)) {
                $subscriber->setMageiaId($return->id);
            }
        }
        return [$customerId];
    }
}