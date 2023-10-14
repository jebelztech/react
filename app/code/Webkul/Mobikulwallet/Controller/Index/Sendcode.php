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

    use Webkul\Walletsystem\Model\Wallettransaction;
    
    class Sendcode extends AbstractWallet    {

        const SYMBOLS_COLLECTION = "0123456789";
        const DEFAULT_LENGTH = 6;

        public function execute()   {
            $returnArray                       = [];
            $returnArray["authKey"]            = "";
            $returnArray["message"]            = "";
            $returnArray["success"]            = false;
            $returnArray["base_amount"]        = 0;
            $returnArray["transferValidation"] = (bool)$this->_walletHelper->getTransferValidationEnable();
            try {
                $wholeData          = $this->getRequest()->getPostValue();
                if ($wholeData) {
                    $authKey  = $this->getRequest()->getHeader("authKey");
                    $authData = $this->_helper->isAuthorized($authKey);
                    if ($authData["code"] == 1) {
                        $amount         = $wholeData["amount"];
                        $walletNote     = $this->_walletHelper->validateScriptTag($wholeData["walletNote"]);
                        $recieverId     = $wholeData["receiverId"];
                        $customerToken  = $wholeData["customerToken"] ?? '';
                        $senderId       = $this->_helper->getCustomerByToken($customerToken) ?? 0;
                        $wholeData['senderId'] = $senderId;
                        if (!is_numeric($amount)) {
                            $returnArray["message"] = __("Invalid Amount.");
                            return $this->getJsonResponse($returnArray);
                        }
                        $toCurrency     = $this->_walletHelper->getBaseCurrencyCode();
                        $fromCurrency   = $this->_walletHelper->getCurrentCurrencyCode();
                        $transferAmount = $this->_walletHelper->getwkconvertCurrency($fromCurrency, $toCurrency, $amount);
                        if (!$this->_walletHelper->getTransferValidationEnable()) {
                            $wholeData["curr_code"] = $fromCurrency;
                            $totalAmount            = $this->_walletHelper->getWalletTotalAmount($senderId);
                            if ($transferAmount <= $totalAmount) {
                                $wholeData["base_amount"] = $transferAmount;
                                $wholeData["curr_amount"] = $amount;
                                $this->SendAmountToWallet($wholeData);
                                $this->DeductAmountFromWallet($wholeData);
                            } else {
                                $returnArray["message"] = __("You don't have enough amount in your wallet.");
                                return $this->getJsonResponse($returnArray);
                            }
                        }
                        $duration = $this->_walletHelper->getCodeValidationDuration();
                        if ($amount != 0 && $recieverId != 0 && $recieverId != "") {
                            $totalAmount = $this->_walletHelper->getWalletTotalAmount($senderId);
                            if ($transferAmount <= $totalAmount) {
                                $wholeData["base_amount"] = $transferAmount;
                                $data        = $this->sendEmailForCode($wholeData);
                                $sessionData = [
                                    "code"        => $this->createCodeHash($data["code"]),
                                    "amount"      => $wholeData["amount"],
                                    "sender_id"   => $data["customer_id"],
                                    "walletNote"  => $wholeData["walletNote"],
                                    "created_at"  => strtotime($this->_date->gmtDate()),
                                    "reciever_id" => $wholeData["receiverId"],
                                    "base_amount" => $transferAmount
                                ];
                                $serializedData = $this->_walletHelper->convertStringAccToVersion($sessionData, 'encode');
                                $this->_waletTransfer->setWalletTransferDataToSession($serializedData);
                                unset($sessionData["code"]);
                                $getParamData = urlencode(base64_encode(json_encode($sessionData)));
                                $paramsJson   = base64_decode(urldecode($getParamData));
                                if ($paramsJson)
                                    $params   = json_decode($paramsJson, true);
                                if (is_array($params) && count($params) && array_key_exists("sender_id", $params)) {
                                    if (!$this->_walletHelper->getTransferValidationEnable()){
                                        $returnArray["message"] = __("Amount has been transfered successfully.");
                                    }
                                    else{
                                        $returnArray["message"] = __("Code has been sent to your email id.");
                                    }
                                } else {
                                    $returnArray["message"] = __("Please try again later.");
                                    return $this->getJsonResponse($returnArray);
                                }
                            } else {
                                $returnArray["message"] = __("You don't have enough amount in your wallet.");
                                return $this->getJsonResponse($returnArray);
                            }
                        } else {
                            $returnArray["message"] = __("Try again with valid data.");
                            return $this->getJsonResponse($returnArray);
                        }
                        $returnArray["success"]     = true;
                        $returnArray["base_amount"] = $wholeData["base_amount"];
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

        public function sendEmailForCode($params)   {
            $data = [
                "code"        => $this->generateCode(),
                "amount"      => $params["amount"],
                "duration"    => $this->_walletHelper->getCodeValidationDuration(),
                "customer_id" => $params["senderId"],
                "base_amount" => $params["base_amount"]
            ];
            $this->_walletMail->sendTransferCode($data);
            return $data;
        }

        public function generateCode()  {
            $alphabet = self::SYMBOLS_COLLECTION;
            $length   = self::DEFAULT_LENGTH;
            $code     = "";
            for ($i = 0, $indexMax = strlen($alphabet) - 1; $i < $length; ++$i)
                $code .= substr($alphabet, mt_rand(0, $indexMax), 1);
            return $code;
        }

        protected function createCodeHash($code) {
            return $this->_encryptor->getHash($code, true);
        }

        public function updateSession() {
            $this->_waletTransfer->checkAndUpdateSession();
            $walletTransferData = $this->_waletTransfer->getWalletTransferDataToSession();
            if ($walletTransferData == "")
                return false;
            return true;
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
                "walletactiontype" => Wallettransaction::WALLET_ACTION_TYPE_CREDIT,
                'order_id' => 0,
                'status' => Wallettransaction::WALLET_TRANS_STATE_APPROVE,
                'increment_id' => ''
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
                "sender_type"      => Wallettransaction::CUSTOMER_TRANSFER_TYPE,
                "walletamount"     => $params["base_amount"],
                "walletactiontype" => Wallettransaction::WALLET_ACTION_TYPE_DEBIT,
                'order_id' => 0,
                'status' => Wallettransaction::WALLET_TRANS_STATE_APPROVE,
                'increment_id' => ''
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