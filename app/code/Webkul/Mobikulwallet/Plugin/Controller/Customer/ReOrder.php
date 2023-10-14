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

class ReOrder {

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
        $incrementId   = $this->_request->getPost("incrementId");
        $customerToken = $this->_request->getPost("customerToken");
        $customerId    = $objectManager->get("Webkul\MobikulCore\Helper\Data")->getCustomerByToken($customerToken) ?? 0;
        $storeId       = $this->_request->getPost("storeId");
        if ($customerId != "") {
            $quoteCollection = $this->_quoteFactory->create()->getCollection();
            $quoteCollection->addFieldToFilter("customer_id", $customerId);
            $quoteCollection->addFieldToFilter("store_id", $storeId);
            $quoteCollection->addFieldToFilter("is_active", 1);
            $quoteCollection->addOrder("updated_at", "desc");
            $quote = $quoteCollection->getFirstItem();
        }
        $order           = $objectManager->create("\Magento\Sales\Model\Order")->loadByIncrementId($incrementId);
        $cartItems       = $quote->getAllVisibleItems();
        $orderItems      = $order->getAllVisibleItems();
        $returnArray     = [];
        $walletProductId = $objectManager->get("Webkul\Walletsystem\Helper\Data")->getWalletProductId();
        $price = 0;
        foreach ($order->getItemsCollection() as $item) {
            if ($item->getProduct()->getId() == $walletProductId) {
                $price = $item->getPrice();
            }
        }
        $otherItems = false;
        $walletItem = false;
        $updated = false;
        foreach($cartItems as $item) {
            if ($item->getProduct()->getId() == $walletProductId) {
                $walletItem = true;
                $price = $item->getCustomPrice() + $price;
                $item->setCustomPrice($price);
                $item->setOriginalCustomPrice($price);
                $item->setQty(1);
                $item->getProduct()->setIsSuperMode(true);
                $item->setRowTotal($price);
                $item->save();
                $updated = true; 
            } else {
                $otherItems = true;
            }
        }
        foreach ($order->getItemsCollection() as $item) {
            if ($item->getProduct()->getId() != $walletProductId && $walletItem) {
                $returnArray["success"] = false;
                $returnArray["message"] = __('You can not add other product with wallet product');
                $returnArray["cartCount"] = $quote->getItemsQty() * 1;
                echo $objectManager->create("Magento\Framework\Json\Helper\Data")->jsonEncode($returnArray);
                die();
            } elseif ($item->getProduct()->getId() == $walletProductId && $otherItems) {
            $objectManager->create("Webkul\MobikulCore\Helper\Data")->printLog($this->_request->getParams());
                $returnArray["success"] = false;
                $returnArray["message"] = __('You can not add wallet product with other product');
                $returnArray["cartCount"] = $quote->getItemsQty() * 1;
                echo $objectManager->create("Magento\Framework\Json\Helper\Data")->jsonEncode($returnArray);
                die();
            }
        }
        if (count($cartItems) && $updated) {
            $quote->collectTotals()->save();
            $returnArray["success"] = true;
            $returnArray["message"] = __("Product(s) has been added to cart.");
            $returnArray["cartCount"] = $quote->getItemsQty() * 1;
            echo $objectManager->create("Magento\Framework\Json\Helper\Data")->jsonEncode($returnArray);
            die();
        }
    }

}