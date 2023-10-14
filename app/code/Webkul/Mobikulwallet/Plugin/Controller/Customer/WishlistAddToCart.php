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

    namespace Webkul\Mobikulwallet\Plugin\Controller\Customer;

    class WishlistAddToCart {

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
            $wholeData = $this->_request->getParams();
            $customerToken  = $wholeData["customerToken"] ?? '';
            $customerId     = $objectManager->get("Webkul\MobikulCore\Helper\Data")->getCustomerByToken($customerToken) ?? 0;
            if ($customerId != "") {
                $quoteCollection = $this->_quoteFactory->create()->getCollection();
                $quoteCollection->addFieldToFilter("customer_id", $customerId);
                $quoteCollection->addOrder("updated_at", "desc");
                $quote = $quoteCollection->getFirstItem();
            }
            $cartItems       = $quote->getAllVisibleItems();
            $walletProductId = $objectManager->get("Webkul\Walletsystem\Helper\Data")->getWalletProductId();
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