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

namespace SendPulse\Mageia\Block\Adminhtml\System\Config;

class OauthWizard extends \Magento\Config\Block\System\Config\Form\Field
{
    protected $_template    = 'system/config/oauth_wizard.phtml';

    protected $_authorizeUri     = "https://login.sendpulse.com/oauth2/authorize";
    protected $_accessTokenUri   = "https://login.sendpulse.com/oauth2/token";
    protected $_redirectUri      = "http://sendpulse.com/magento/sendpulse/oauth2/complete_header_M2.php";
    protected $_clientId         = 976537930266;

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element) {
        $originalData = $element->getOriginalData();

        $label = $originalData['button_label'];

        $this->addData(array(
            'button_label' => __($label),
            'button_url'   => $this->authorizeRequestUrl(),
            'html_id' => $element->getHtmlId(),
        ));
        return $this->_toHtml();
    }
    public function authorizeRequestUrl() {

        $url = $this->_authorizeUri;
        $redirectUri = urlencode($this->_redirectUri);

        return "{$url}?redirect_uri={$redirectUri}&response_type=code&client_id={$this->_clientId}";
    }

}