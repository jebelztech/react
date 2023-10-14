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

    namespace Webkul\Mobikulwallet\Controller\Index;

    class Add extends AbstractWallet    {

        public function execute()   {
            $returnArray                 = [];
            $returnArray["authKey"]      = "";
            $returnArray["message"]      = "";
            $returnArray["success"]      = false;
            $returnArray["responseCode"] = 0;
            try {
                $wholeData       = $this->getRequest()->getPostValue();
                if ($wholeData) {
                    $authKey  = $this->getRequest()->getHeader("authKey");
                    $authData = $this->_helper->isAuthorized($authKey);
                    if ($authData["code"] == 1) {
                        $qty           = $wholeData["qty"];
                        $price         = $wholeData["price"];
                        $storeId       = $wholeData["storeId"];
                        $productId     = $wholeData["productId"];
                        $customerToken = $wholeData["customerToken"] ?? '';
                        $currency      = $wholeData["currency"] ?? $this->store->getBaseCurrencyCode();
                        $this->store->setCurrentCurrencyCode($currency);
                        $customerId    = $this->_helper->getCustomerByToken($customerToken) ?? 0;
                        $quote         = new \Magento\Framework\DataObject();
                        $quoteCollection = $this->_quoteFactory->create()
                            ->getCollection()
                            ->addFieldToFilter("customer_id", $customerId)
                            ->addFieldToFilter("store_id", $storeId)
                            ->addFieldToFilter("is_active", 1)
                            ->addOrder("updated_at", "DESC");
                        $quote   = $quoteCollection->getFirstItem();
                        $quoteId = $quote->getId();
                        if ($quote->getId() < 0 || !$quoteId) {
                            $quote = $this->_quoteFactory->create()
                                ->setStoreId($storeId)
                                ->setIsActive(true)
                                ->setIsMultiShipping(false)
                                ->save();
                            $quoteId = (int) $quote->getId();
                            $customer = $this->_customerRepository->getById($customerId);
                            $quote->assignCustomer($customer);
                            $quote->setCustomer($customer);
                            $quote->getBillingAddress();
                            $quote->getShippingAddress()->setCollectShippingRates(true);
                            $quote->collectTotals()->save();
                        }
                        $cart      = $this->_cartFactory->create();
                        $cartItems = $quote->getAllVisibleItems();
                        $minimumAmount = 0;
                        $maximumAmount = 0;
                        $helper        = $this->_objectManager->get("Webkul\Walletsystem\Helper\Data");
                        $maximumAmount = $helper->getMaximumAmount();
                        $minimumAmount = $helper->getMinimumAmount();
                        if($minimumAmount > $maximumAmount){
                            $temp          = $maximumAmount;
                            $maximumAmount = $minimumAmount;
                            $minimumAmount = $temp;
                        }
                        $baseCurrenyCode = $helper->getBaseCurrencyCode();
                        $currencySymbol  = $helper->getCurrencySymbol(
                            $helper->getCurrentCurrencyCode()
                        );
                        $currentCurrenyCode = $helper->getCurrentCurrencyCode();
                        $adminConfigPrice   = $minimumAmount;
                        $minimumAmount = $helper->getwkconvertCurrency(
                                $baseCurrenyCode,
                                $currentCurrenyCode,
                                $adminConfigPrice
                            );
                        $walletProductId = $helper->getWalletProductId();
                        $product = $this->_productFactory->create()->setStoreId($storeId)->load($productId);
                        $returnArray["message"] = __("%1 is added to your shopping cart.", $this->_helperCatalog->stripTags($product->getName()));
                        $itemProId = 0;
                        foreach ($cartItems as $item)
                            $itemProId = $item->getProductId();
                        if(count($cartItems) > 1 || (count($cartItems) == 1 && $productId != $itemProId)) {
                            $returnArray["message"] = __("You can not add other products with wallet product, and vise versa.");
                            return $this->getJsonResponse($returnArray);
                        }
                        else{
                            if($price > $maximumAmount){
                                $returnArray["message"] = __("You can not add more than %1 amount to your wallet.", $this->stripTags($this->_priceFormat->currency($maximumAmount)));
                                return $this->getJsonResponse($returnArray);
                            }else if($price < $minimumAmount){
                                $returnArray["message"] = __("You can not add less than %1 amount to your wallet.", $currencySymbol.$minimumAmount);
                                return $this->getJsonResponse($returnArray);
                            }
                        }
                        if (!count($cartItems)) {
                            $params  = ["related_product"=>[], "options"=>[], "qty"=>$qty, "product"=>$productId];
                            $cart->setQuote($quote)->addProduct($product, $params)->save();
                        }
                        foreach ($cartItems as $item) {
                            $price = $item->getCustomPrice() + $price;
                            if($item->getProduct()->getId() == $walletProductId){
                                $item->setCustomPrice($price);
                                $item->setOriginalCustomPrice($price);
                                $item->setQty(1);
                                $item->getProduct()->setIsSuperMode(true);
                                $item->setRowTotal($price);
                                $item->save();
                            }
                        }
                        $returnArray["success"] = true;
                        $quote->collectTotals()->save();
                        $returnArray["cartCount"] = $quote->getItemsQty() * 1;
                        return $this->getJsonResponse($returnArray);
                    } else {
                        return $this->getJsonResponse($returnArray, 401, $authData["token"]);
                    }
                } else {
                    $returnArray["message"]      = __("Invalid Request");
                    $returnArray["responseCode"] = 0;
                    return $this->getJsonResponse($returnArray);
                }
            } catch(\Exception $e)   {
                $returnArray["message"] = $e->getMessage();
                return $this->getJsonResponse($returnArray);
            }
        }

    }