<?php
/**
* Webkul Software.
*
* @category  Webkul
* @package   Webkul_Mobikulwallet
* @author    Webkul
* @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
* @license   https://store.webkul.com/license.html
*/
namespace Webkul\Mobikulwallet\Controller\Index;

use Webkul\Walletsystem\Model\Wallettransaction;

class Bankaccount extends AbstractWallet {

    public function execute()   {
        $returnArray            = [];
        $returnArray["authKey"] = "";
        $returnArray["message"] = "";
        $returnArray["success"] = false;
        try {
            $wholeData          = $this->getRequest()->getPostValue();
            if ($wholeData) {
                $authKey  = $this->getRequest()->getHeader("authKey");
                $authData = $this->_helper->isAuthorized($authKey);
                if ($authData["code"] == 1) {
                    $amount         = $wholeData["amount"] ?? 0;
                    $bankDetails    = $wholeData["bankDetails"] ?? '';
                    $walletNote     = $wholeData["walletNote"] ?? '';
                    $customerToken  = $wholeData["customerToken"] ?? '';
                    $customerId     = $this->_helper->getCustomerByToken($customerToken) ?? 0;
                    
                    if ($customerId) {
                        if ($amount && $bankDetails != ''
                        && !preg_match('#<script(.*?)>(.*?)</script>#is', $walletNote)
                        && !preg_match('#<script(.*?)>(.*?)</script>#is', $bankDetails)
                        ) {
                            $walletNote = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $walletNote);
                            $bankDetails = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $bankDetails);
                            $baseCurrencyCode = $this->_walletHelper->getBaseCurrencyCode();
                            $currencycode = $this->_walletHelper->getCurrentCurrencyCode();
                            // $amount = $params['amount'];
                            $baseAmount = $this->_walletHelper->getwkconvertCurrency(
                                $currencycode,
                                $baseCurrencyCode,
                                $amount
                            );

                            $params['customer_id'] = $customerId;
                            $params['amount'] = $amount;
                            $params['bank_details'] = $bankDetails;
                            $params['walletnote'] = $walletNote;
                            $params['curr_code'] = $currencycode;
                            $params['curr_amount'] = $amount;
                            $params['order_id'] = 0;
                            $params['status'] = Wallettransaction::WALLET_TRANS_STATE_PENDING;
                            $params['increment_id'] = '';
                            $params['walletamount'] = $baseAmount;
                            $params['walletactiontype'] = 'debit';
                            $params['sender_id'] = 0;
                            $params['sender_type'] = Wallettransaction::CUSTOMER_TRANSFER_BANK_TYPE;
                            $params['transfer_to_bank'] = 1;
                            if ($walletNote=='') {
                                $walletNote = __('%1, Amount is transferred by customer to bank account', $amount);
                            }
                            
                            $result = $this->_walletUpdate->debitAmount($customerId, $params);
                           
                            if (is_array($result) && array_key_exists('success', $result)) {
                                $this->setNotificationMessageForAdmin();
                                $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
                                if ($this->scopeConfig->getValue('walletsystem/message_after_request/show_message', $storeScope)) {
                                    $message = $this->scopeConfig->getValue('walletsystem/message_after_request/message_content', $storeScope);
                                    $returnArray["message"]      = $message;
                                } else {
                                    $returnArray["message"]      = __("Amount transfer request has been sent!");
                                }
                            } else {
                                $returnArray["success"]     = false;
                                $returnArray["message"]      = __("Respective amount is not available your wallet");
                                return $this->getJsonResponse($returnArray);
                            }
                        } else {
                            $returnArray["message"]      = __("Something went wrong, please try again");
                            $returnArray["success"]     = false;
                            return $this->getJsonResponse($returnArray);
                        }
                    }

                    $returnArray["success"]     = true;
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

    public function setNotificationMessageForAdmin()
    {
        $notificationModel = $this->_walletNotification->getCollection();
        if (!$notificationModel->getSize()) {
            $this->_walletNotification->setBanktransferCounter(1);
            $this->_walletNotification->save();
        } else {
            foreach ($notificationModel->getItems() as $notification) {
                $notification->setBanktransferCounter($notification->getBanktransferCounter()+1);
            }
        }
        $notificationModel->save();
    }
}