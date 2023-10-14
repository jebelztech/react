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

    class Sendamount extends AbstractWallet    {

        public function execute()   {
            $returnArray            = [];
            $returnArray["authKey"] = "";
            $returnArray["message"] = "";
            $returnArray["success"] = false;
            $returnArray["transferValidation"] = (bool)$this->_walletHelper->getTransferValidationEnable();
            try {
                $wholeData          = $this->getRequest()->getPostValue();
                if ($wholeData) {
                    $authKey  = $this->getRequest()->getHeader("authKey");
                    $authData = $this->_helper->isAuthorized($authKey);
                    if ($authData["code"] == 1) {
                        $code           = $wholeData["code"];
                        $amount         = $wholeData["amount"];
                        $baseAmount     = $wholeData["baseAmount"];
                        $recieverId     = $wholeData["receiverId"];
                        $customerToken  = $wholeData["customerToken"] ?? '';
                        $senderId     = $this->_helper->getCustomerByToken($customerToken) ?? 0;
                        $walletNote     = $this->_walletHelper->validateScriptTag($wholeData["walletNote"]);
                        $toCurrency     = $this->_walletHelper->getBaseCurrencyCode();
                        $fromCurrency   = $this->_walletHelper->getCurrentCurrencyCode();
                        $transferAmount = $this->_walletHelper->getwkconvertCurrency($fromCurrency, $toCurrency, $amount);
                        if (!$this->_walletHelper->getTransferValidationEnable()) {
                            $totalAmount = $this->_walletHelper->getWalletTotalAmount($senderId);
                            if ($transferAmount <= $totalAmount && $senderId && $amount && $recieverId && $baseAmount) {
                                $wholeData["curr_code"]   = $this->_walletHelper->getCurrentCurrencyCode();
                                $wholeData["base_amount"] = $transferAmount;
                                $wholeData["curr_amount"] = $wholeData["amount"];
                                $this->SendAmountToWallet($wholeData);
                                $this->DeductAmountFromWallet($wholeData);
                                $returnArray["message"] = __("Amount Transfered successfully");
                            } else {
                                $returnArray["message"] = __("Something went wrong!");
                                return $this->getJsonResponse($returnArray);
                            }
                        } else {
                            $this->_waletTransfer->checkAndUpdateSession();
                            $walletTransferData = $this->_waletTransfer->getWalletTransferDataToSession();
                            if ($walletTransferData=='') {
                                $returnArray["message"] = __("Either code session is expired, or amount is already transferred.");
                                return $this->getJsonResponse($returnArray);
                            }
                            $walletCookieArray = $this->_walletHelper->convertStringAccToVersion($walletTransferData, 'decode');
                            if ($walletCookieArray['sender_id']==$senderId &&
                                $walletCookieArray['amount']==$amount &&
                                $walletCookieArray['reciever_id']==$recieverId) {
                                if (!$this->_encryptor->validateHash($code, $walletCookieArray['code'])) {
                                    $returnArray["message"] = __("Incorrect code.");
                                    return $this->getJsonResponse($returnArray);
                                }
                                $wholeData["base_amount"] = $transferAmount;
                                $wholeData["curr_code"]   = $this->_walletHelper->getCurrentCurrencyCode();
                                $wholeData["curr_amount"] = $wholeData["amount"];
                                $wholeData['walletnote'] = $walletNote;
                                $this->SendAmountToWallet($wholeData);
                                $this->DeductAmountFromWallet($wholeData);
                                $this->_waletTransfer->setWalletTransferDataToSession('');
                                $this->messageManager->addSuccess(__("Amount transferred successfully"));
                            } else {
                                $returnArray["message"] = __("Something went wrong!");
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

        public function SendAmountToWallet($params) {
            $customerModel = $this->_walletHelper->getCustomerByCustomerId($params["senderId"]);
            $senderName    = $customerModel->getName();
            if ($params["walletNote"] == "") {
                $params["walletNote"] = __("Transfer by %1", $senderName);
            }
            $transferAmountData = [
                "curr_code"        => $params["curr_code"],
                "sender_id"        => $params["senderId"],
                "customerid"       => $params["receiverId"],
                "walletnote"       => __($params["walletNote"]),
                "sender_type"      => 4,
                "curr_amount"      => $params["curr_amount"],
                "walletamount"     => $params["base_amount"],
                "walletactiontype" => "credit",
                "status"           => 1,
                'order_id'         => 0,
                'increment_id'     => ''
            ];
            $msg = __(
                "%1 amount %2ed by %3.  He added a note for the transaction: %4",
                $this->_walletHelper->getformattedPrice($transferAmountData["walletamount"]),
                $transferAmountData["walletactiontype"],
                $senderName,
                __($params["walletNote"])
            );
            $adminMsg = __(
                "'s account is updated with the %1 amount %2ed by %3. He added a note for the transaction: %4",
                $this->_walletHelper->getformattedPrice($transferAmountData["walletamount"]),
                $transferAmountData["walletactiontype"],
                $senderName,
                __($params["walletNote"])
            );
            $this->_walletUpdate->creditAmount($params["receiverId"], $transferAmountData, $msg, $adminMsg);
        }

        public function DeductAmountFromWallet($params)     {
            $customerModel = $this->_walletHelper->getCustomerByCustomerId($params["receiverId"]);
            $recieverName  = $customerModel->getName();
            if ($params["walletNote"] == "") {
                $params["walletNote"] = __("Transfer to %1", $recieverName);
            }
            $transferAmountData = [
                "curr_code"        => $params["curr_code"],
                "sender_id"        => $params["receiverId"],
                "customerid"       => $params["senderId"],
                "walletnote"       => __($params["walletNote"]),
                "curr_amount"      => $params["curr_amount"],
                "sender_type"      => 4,
                "walletamount"     => $params["base_amount"],
                "walletactiontype" => "debit",
                'order_id'         => 0,
                'status'           => 1,
                'increment_id'     => ''
            ];
            $msg = __(
                'You have transfered %1 amount to %2. you added a note on the transaction: %3',
                $this->_walletHelper->getformattedPrice($transferAmountData["walletamount"]),
                $recieverName,
                __($params["walletNote"])
            );
            $adminMsg = __(
                "'s account is updated with the %1 amount transferd to %2. He added a note for the transaction: %3",
                $this->_walletHelper->getformattedPrice($transferAmountData["walletamount"]),
                $recieverName,
                __($params["walletNote"])
            );
            $this->_walletUpdate->debitAmount($params["senderId"], $transferAmountData, $msg, $adminMsg);
        }

    }