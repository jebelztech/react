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

    class Applypaymentamount extends AbstractWallet    {

        public function execute()   {
            $returnArray               = [];
            $returnArray["authKey"]    = "";
            $returnArray["message"]    = "";
            $returnArray["success"]    = false;
            $returnArray["walletData"] = [];
            try {
                $wholeData           = $this->getRequest()->getPostValue();
                if ($wholeData) {
                    $authKey  = $this->getRequest()->getHeader("authKey");
                    $authData = $this->_helper->isAuthorized($authKey);
                    if ($authData["code"] == 1) {
                        $storeId         = $wholeData["storeId"];
                        $wallet          = $wholeData["wallet"];
                        $customerToken   = $wholeData["customerToken"] ?? '';
                        $customerId      = $this->_helper->getCustomerByToken($customerToken) ?? 0;
                        // unset($this->_checkoutSession);
                        // $grandtotal      = $wholeData["grandtotal"];
                        // print_r($wholeData);die();
                        $helper          = $this->_objectManager->get("Webkul\Walletsystem\Helper\Data");
                        $environment     = $this->_emulate->startEnvironmentEmulation($storeId);
                        $quoteCollection = $this->_objectManager->create("\Magento\Quote\Model\Quote")
                            ->getCollection()
                            ->addFieldToFilter("customer_id", $customerId)
                            ->addFieldToFilter("store_id", $storeId)
                            ->addFieldToFilter("is_active", 1)
                            ->addOrder("updated_at", "DESC");
                        $quote              = $quoteCollection->getFirstItem();
                        
                        $this->_checkoutSession->setId($quote->getId());
                        $this->_customerSession->setCustomerId($customerId);
                        $subtotal           = $quote->getSubtotal();
                        $shippingAmount     = $quote->getShippingAddress()->getShippingAmount();
                        $cartDiscountamount = $quote->getShippingAddress()->getDiscountAmount();
                        if ($cartDiscountamount == null || $cartDiscountamount == 0) {
                            $cartDiscountamount = $quote->getBillingAddress()->getDiscountAmount();
                        }
                        $totals = $quote->getTotals();
                        $taxAmount = 0;
                        if (array_key_exists("tax", $totals)) {
                            $taxAmount = $totals["tax"]->getValue();
                        }
                        if ($taxAmount == 0) {
                            foreach ($quote->getAllItems() as $item) {
                                $taxAmount = $taxAmount + $item->getTaxAmount();
                            }
                        }
                        $grandtotal = $subtotal + $shippingAmount + $taxAmount;
                        if ($cartDiscountamount != null) {
                            $grandtotal = $grandtotal + $cartDiscountamount;
                        }
                        $grandtotal       = (float) $grandtotal;
                        $grandtotal       = round($grandtotal, 4);
                        $amount           = $helper->getWalletTotalAmount($customerId);
                        $store            = $helper->getStore();
                        $converttedAmount = $helper->currentCurrencyAmount($amount, $store);
                        $leftAmountToPay  = 0;
                        if ($wallet == "set") {
                            if ($converttedAmount >= $grandtotal) {
                                $discountAmount = $grandtotal;
                            } else {
                                $discountAmount  = $converttedAmount;
                                $leftAmountToPay = $grandtotal - $converttedAmount;
                            }
                            $left           = $converttedAmount - $discountAmount;
                            $baseLeftAmount = $helper->baseCurrencyAmount($left);
                            $leftinWallet   = $helper->getformattedPrice($baseLeftAmount > 0 ? $baseLeftAmount : 0);
                            $walletValue    = [
                                "flag"         => 1,
                                "type"         => $wallet,
                                "amount"       => $discountAmount,
                                "grand_total"  => $grandtotal,
                                "leftinWallet" => $leftinWallet
                            ];
                            $walletData = [
                                "formattedLeftInWallet"      => $leftinWallet,
                                "formattedPaymentToMade"     => $helper->getformattedPrice($grandtotal),
                                "unformattedLeftInWallet"    => $baseLeftAmount,
                                "formattedAmountInWallet"    => $helper->getformattedPrice($amount),
                                "unformattedPaymentToMade"   => $grandtotal,
                                "formattedLeftAmountToPay"   => $helper->getformattedPrice($leftAmountToPay),
                                "unformattedAmountInWallet"  => $amount,
                                "unformattedLeftAmountToPay" => $leftAmountToPay
                            ];
                            $this->_checkoutSession->setWalletDiscount($walletValue);
                        } else {
                            $leftinWallet = $helper->getformattedPrice($amount);
                            $walletValue = [
                                "flag"         => 0,
                                "type"         => $wallet,
                                "amount"       => 0,
                                "grand_total"  => $grandtotal,
                                "leftinWallet" => $leftinWallet
                            ];
                            $walletData = [
                                "formattedLeftInWallet"      => $leftinWallet,
                                "formattedPaymentToMade"     => $helper->getformattedPrice($grandtotal),
                                "unformattedLeftInWallet"    => $amount,
                                "formattedAmountInWallet"    => $helper->getformattedPrice($amount),
                                "unformattedPaymentToMade"   => $grandtotal,
                                "formattedLeftAmountToPay"   => $helper->getformattedPrice($leftAmountToPay),
                                "unformattedAmountInWallet"  => $amount,
                                "unformattedLeftAmountToPay" => $leftAmountToPay
                            ];
                            $this->_checkoutSession->setWalletDiscount($walletValue);
                        }
                    

                        $returnArray["success"]    = true;
                        $returnArray["walletData"] = $walletData;
                        $this->_emulate->stopEnvironmentEmulation($environment);
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