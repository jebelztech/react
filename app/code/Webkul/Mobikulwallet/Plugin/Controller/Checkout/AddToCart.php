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

    class AddToCart {

        protected $_request;
        protected $_quoteFactory;

        public function __construct(
            \Magento\Quote\Model\QuoteFactory $quoteFactory,
            \Magento\Framework\App\RequestInterface $request
        ) {
            $this->_request       = $request;
            $this->_quoteFactory  = $quoteFactory;
        }

        public function beforeExecute() {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $walletProductId = $objectManager->get("Webkul\Walletsystem\Helper\Data")->getWalletProductId();
            $storeId = $this->_request->getPost("storeId");
            $customerToken  = $this->_request->getPost("customerToken", "");
            $customerId     = $objectManager->get("Webkul\MobikulCore\Helper\Data")->getCustomerByToken($customerToken) ?? 0;
            $quoteId = 0;
            if ($customerId != "") {
                $quoteCollection = $this->_quoteFactory->create()->getCollection();
                $quoteCollection->addFieldToFilter("customer_id", $customerId);
                $quoteCollection->addFieldToFilter("store_id", $storeId);
                $quoteCollection->addFieldToFilter("is_active", 1);
                $quoteCollection->addOrder("updated_at", "desc");
                $quote = $quoteCollection->getFirstItem();
                if ($quote->getEntityId()) {
                    $quoteId = $quote->getEntityId();
                }
            } else {
                $quoteId = $this->_request->getPost("quoteId");
            }
            $objectManager->get("Webkul\MobikulCore\Helper\Data")->printLog('sdsadasd');
            $objectManager->get("Webkul\MobikulCore\Helper\Data")->printLog($quoteId);
            if ($quoteId != "") {
                $quote = $this->_quoteFactory->create()->setStoreId($storeId)->load($quoteId);
                $returnArray = [];
                $cartItems   = $quote->getAllVisibleItems();
                foreach ($cartItems as $item) {
                    if($item->getProduct()->getId() == $walletProductId){
                        $returnArray["success"] = false;
                        $returnArray["message"] = __("You can not add other products with wallet product, and vise versa.");
                        echo $objectManager->create("Magento\Framework\Json\Helper\Data")->jsonEncode($returnArray);
                        die();
                    }
                }
            }
        }

    }