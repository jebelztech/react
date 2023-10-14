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

    class UpdateCart {

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
            $customerToken  = $wholeData["customerToken"] ?? '';
            $customerId     = $objectManager->get("Webkul\MobikulCore\Helper\Data")->getCustomerByToken($customerToken) ?? 0;
            if ($customerId != "") {
                $quoteCollection = $this->_quoteFactory->create()->getCollection();
                $quoteCollection->addFieldToFilter("customer_id", $customerId);
                $quoteCollection->addOrder("updated_at", "desc");
                $quote = $quoteCollection->getFirstItem();
                if ($quote->getEntityId()) {
                    $quoteId = $quote->getEntityId();
                }
            } else {
                $quoteId = $this->_request->getPost("quoteId");
            }
            if ($quoteId != "") {
                $quote = $this->_quoteFactory->create()->setStoreId($storeId)->load($quoteId);
            }
            if (!empty($quote)) {
                $returnArray            = [];
                $allItems = $quote->getAllItems();
                foreach ($allItems as $item) {
                    $productId = $item->getProductId();
                    if($productId == $walletProductId){
                        $item->setQty(1);
                        $item->save();
                        $returnArray["success"] = 0;
                        $returnArray["message"] = __("You can not update wallet product's quantity.");
                        echo $objectManager->create("Magento\Framework\Json\Helper\Data")->jsonEncode($returnArray);
                        die();
                    }
                }
            }
        }

    }