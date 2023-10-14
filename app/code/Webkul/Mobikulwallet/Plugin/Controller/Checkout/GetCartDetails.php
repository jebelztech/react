<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Mobikulwallet
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Mobikulwallet\Plugin\Controller\Checkout;

use Magento\Framework\Controller\ResultFactory;

class GetCartDetails {

    protected $_helper;
    protected $_request;
    protected $_jsonHelper;
    protected $_walletHelper;
    protected $_resultFactory;
    protected $_customerSession;

    public function __construct(
        ResultFactory $resultFactory,
        \Webkul\MobikulCore\Helper\Data $helper,
        \Magento\Framework\App\Request\Http $request,
        \Webkul\Walletsystem\Helper\Data $walletHelper,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->_helper          = $helper;
        $this->_request         = $request;
        $this->_jsonHelper      = $jsonHelper;
        $this->_walletHelper    = $walletHelper;
        $this->_resultFactory   = $resultFactory;
        $this->_customerSession = $customerSession;
    }

    public function afterExecute(\Webkul\MobikulApi\Controller\Checkout\CartDetails $subject, $response)
    {
        if ($response->getRawData()) {
            $authKey      = $this->_request->getHeader("authKey");
            $authData     = $this->_helper->isAuthorized($authKey);
            $resultJson   = $this->_resultFactory->create(ResultFactory::TYPE_JSON);
            $responseData = json_decode($response->getRawData());
            $customerId   = $this->_helper->getCustomerByToken($this->_request->getParam("customerToken", "")) ?? 0;
            if ($authData["code"] == 1 && $customerId) {
                $this->_customerSession->setCustomerId($customerId);
                $walletData      = $this->_walletHelper->getWalletDetailsData();
                $walletProductId = $this->_walletHelper->getWalletProductId();
                $customerName = $walletData["customer_name"];
                $walletAmount = $walletData["currencySymbol"].$walletData["wallet_amount"];
                $allItems = [];
                foreach ($responseData->items as $item) {
                    if ($item->productId == $walletProductId) {
                        $item->options[] = [
                            "label" => __("Wallet Holder's Name"),
                            "value" => [$customerName]
                        ];
                        $item->options[] = [
                            "label" => __("Current Amount"),
                            "value" => [$walletAmount]
                        ];
                    }
                    $allItems[] = $item;
                }
                $responseData->items = $allItems;
                $this->_customerSession->unsCustomerId();
                $resultJson->setData($responseData);
                return $resultJson;
            }
            $resultJson->setData($responseData);
            return $resultJson;
        }
        return $response;        
    }
}
