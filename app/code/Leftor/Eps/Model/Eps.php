<?php

namespace Leftor\Eps\Model;

use Magento\Quote\Model\Quote\Payment;

class Eps extends \Magento\Payment\Model\Method\AbstractMethod
{
	const PAYMENT_METHOD_EPS_CODE = 'eps';
    const TEST_URL = 'https://routing.eps.or.at/appl/epsSO-test/transinit/eps/v2_5';

    protected $_code = self::PAYMENT_METHOD_EPS_CODE;
    protected $_isGateway = false;
    protected $_isOffline = true;
    protected $_canCapture = false;
    protected $_canAuthorize = false;
    protected $_canRefund = false;

    protected $_remoteAddress;
    protected $_storeManager;

	protected $_formBlockType = 'Leftor\Eps\Block\Form\Instructions';
	
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        $this->_remoteAddress = $remoteAddress;
        $this->_storeManager = $storeManager;
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger
        );
    }

    /**
     * Instructions block path
     *
     * @var string
     */
    protected $_infoBlockType = 'Magento\Payment\Block\Info\Instructions';

     /**
     * Check method for processing with base currency
     *
     * @param string $currencyCode
     * @return bool
     */
    public function canUseForCurrency($currencyCode)
    {
        $baseCurrency = $currencyCode;
        $currencyCode = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
        
        if(!empty($currencyCode)) {
            if ($currencyCode != $this->getConfigData('currency')) {
                return false;
            } else {
                return true;
            }
        }
        if ($baseCurrency != $this->getConfigData('currency')) {
            return false;
        }
        return true;
    }

    /**
     * Get instructions text from config
     *
     * @return string
     */
    public function getInstructions()
    {
        return trim($this->getConfigData('instructions'));
    }

    protected function fullAddress($address){
        if($address != null && count($address)>1){
            return $address[0]." ".$address[1];
        }
        else
            return $address[0];
    }

    public function getIsTestMode() {
        return $this->getConfigData("test_mode");
    }

    public function getUserId() {
        if ($this->getIsTestMode()) {
            return $this->getConfigData("user_id_test");
        }
        return $this->getConfigData("user_id_production");
    }

    public function getPin() {
        if ($this->getIsTestMode()) {
            return $this->getConfigData("pin_test");
        }
        return $this->getConfigData("pin_production");
    }

    public function getBic() {
        if ($this->getIsTestMode()) {
            return $this->getConfigData("bic_test");
        }
        return $this->getConfigData("bic_production");
    }

    public function getIban() {
        if ($this->getIsTestMode()) {
            return $this->getConfigData("iban_test");
        }
        return $this->getConfigData("iban_production");
    }

    public function getOrderStatusSuccess() {
        return $this->getConfigData("order_status_on_success");
    }

    public function getOrderStatusFail() {
        return $this->getConfigData("order_status_on_fail");
    }

    public function getTargetUrl() {
        if($this->getIsTestMode()) {
            return self::TEST_URL;
        } else
            return null;
    }

}
