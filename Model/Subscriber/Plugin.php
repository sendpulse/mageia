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

namespace SendPulse\Mageia\Model\Subscriber;


class Plugin
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
        \SendPulse\Mageia\Model\Api $api,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_helper = $helper;
        $this->_customer = $customer;
        $this->_customerSession = $customerSession;
        $this->_api = $api;
        $this->_storeManager = $storeManager;
    }

    public function beforeUnsubscribeCustomerById(
        $subscriber, $customerId
    ) {
        $subscriber->loadByCustomerId($customerId);
        if ($subscriber->getMageiaId()) {
            $emails = [$subscriber->getMageiaId()];

            $data = ['emails' => json_encode($emails)];
            $listId = $this->_helper->getDefaultList();
            $return = $this->_api->listDeleteMember($listId, $data);

            if (isset($return->result) && $return->result) {
                $subscriber->setMageiaId('');
                $subscriber->save();
            }
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

            /*
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
            */

            $email = ['email' => $customer->getEmail()];
            if ($mergeVars) {
                $email['variables'] = $mergeVars;
            }
            $data = ['emails' => json_encode([$email])];
            $listId = $this->_helper->getDefaultList();
            $return = $api->listCreateMember($listId, $data);

            if (isset($return->result) && $return->result) {
                $subscriber->setMageiaId($customer->getEmail());
                $subscriber->save();
            }
        }
        return [$customerId];
    }

    public function beforeSubscribe($subscriber, $email)
    {
        $isSubscribeOwnEmail = $this->_customerSession->isLoggedIn()
            && $this->_customerSession->getCustomerDataObject()->getEmail()
            == $subscriber->getSubscriberEmail();
        if ($isSubscribeOwnEmail) {
            $subscriber->loadByEmail($email);
        }
        $storeId = $this->_storeManager->getStore()->getId();

        if ($this->_helper->isMageiaEnabled($storeId)) {
            $api = $this->_api;

            $data = ['emails' => json_encode([['email' => $email]])];
            $listId = $this->_helper->getDefaultList();
            $return = $api->listCreateMember($listId, $data);

            if (isset($return->result) && $return->result
            ) {
                $subscriber->setMageiaId($email);
            }
        }
    }

    public function beforeUnsubscribe($subscriber)
    {
        $subscriber->loadByEmail($subscriber->getEmail());

        if ($subscriber->getMageiaId()) {
            $emails = [$subscriber->getMageiaId()];

            $data = ['emails' => json_encode($emails)];
            $listId = $this->_helper->getDefaultList();
            $return = $this->_api->listDeleteMember($listId, $data);

            if (isset($return->result) && $return->result) {
                $subscriber->setMageiaId('');
                $subscriber->save();
            }
        }
    }
}