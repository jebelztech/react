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

use \Webkul\Walletsystem\Model\Wallettransaction;
    class View extends AbstractWallet    {

        public function execute()   {
            $returnArray                    = [];
            $returnArray["authKey"]         = "";
            $returnArray["message"]         = "";
            $returnArray["success"]         = false;
            $returnArray["transactionData"] = [];
            try {
                $wholeData         = $this->getRequest()->getPostValue();
                if ($wholeData) {
                    $authKey  = $this->getRequest()->getHeader("authKey");
                    $authData = $this->_helper->isAuthorized($authKey);
                    if ($authData["code"] == 1) {
                        $transactionId  = $wholeData["transactionId"];
                        $customerToken  = $wholeData["customerToken"] ?? '';
                        $currency      = $wholeData["currency"] ?? $this->store->getBaseCurrencyCode();
                        $this->store->setCurrentCurrencyCode($currency);
                        $customerId     = $this->_helper->getCustomerByToken($customerToken) ?? 0;
                        $helper         = $this->_objectManager->get("Webkul\Walletsystem\Helper\Data");
                        $walletTransactionData = $this->_walletTransaction->create()->load($transactionId);
                        $amount       = $walletTransactionData->getCurrAmount();
                        $currencyCode = $walletTransactionData->getCurrencyCode();
                        $precision    = 2;
                        if ($customerId == $walletTransactionData->getCustomerId()) {
                            $transactionData["amount"] = [
                                "label" => __("Amount"),
                                "value" => $this->stripTags($this->_priceCurrency->format($amount, $includeContainer = true, $precision, $scope = null, $currencyCode))
                            ];
                            $transactionData["action"] = [
                                "label" => __("Action"),
                                "value" => $walletTransactionData->getAction()
                            ];
                            $transactionData["type"] = [
                                "label" => __("Type"),
                                "value" => $helper->getTransactionPrefix($walletTransactionData->getSenderType(), $walletTransactionData->getAction())
                            ];
                            $transactionData["date"] = [
                                "label" => __("Transaction At"),
                                "value" => $this->_localeDate->date(new \DateTime($walletTransactionData->getTransactionAt()))->format("g:ia \o\\n l jS F Y")
                            ];
                            $transactionData["note"] = [
                                "label" => __("Transaction note"),
                                "value" => $walletTransactionData->getTransactionNote()
                            ];
                            $transactionLabel = __('Approved');
                            if ($walletTransactionData->getStatus()==Wallettransaction::WALLET_TRANS_STATE_PENDING) {
                                $transactionLabel =  __('Pending');
                            }
                            else if ($walletTransactionData->getStatus()==Wallettransaction::WALLET_TRANS_STATE_CANCEL) {
                                $transactionLabel =  __('Cancelled');
                            }
                            $transactionData["status"] = [
                                "label" => __("Transaction Status"),
                                "value" => $transactionLabel
                            ];
                            $transactionData["bankDetails"] = new \stdClass();
                            
                            $transactionData["order"]  = new \stdClass();
                            $transactionData["sender"] = new \stdClass();
                            $incrementid = "";

                            $orderDetailsActions = [
                                Wallettransaction::ORDER_PLACE_TYPE,
                                Wallettransaction::REFUND_TYPE
                            ];
                            if ($walletTransactionData->getOrderId()) {
                                $order = $this->_order->load($walletTransactionData->getOrderId());
                                $incrementid = $order->getIncrementId();
                            }
                            if ($walletTransactionData->getSenderType() == 0) {
                                $transactionData["order"] = [
                                    "label" => __("Reference"),
                                    "value" => $incrementid
                                ];
                            } elseif ($walletTransactionData->getSenderType() == 1) {
                                if ($walletTransactionData->getAction() == "credit") {
                                    $transactionData["order"] = [
                                        "label" => __("Reference"),
                                        "value" => $incrementid
                                    ];
                                }
                            } elseif ($walletTransactionData->getSenderType() == 2) {
                                $transactionData["order"] = [
                                    "label" => __("Reference"),
                                    "value" => $incrementid
                                ];
                            } elseif ($walletTransactionData->getSenderType() == 3) {

                            } elseif ($walletTransactionData->getSenderType() == 4) {
                                if ($walletTransactionData->getAction() == "credit") {
                                    $senderData = $this->_customerFactory->create()->load($walletTransactionData->getSenderId());
                                    $transactionData["sender"] = [
                                        "label" => __("Sender"),
                                        "value" => $senderData->getName()
                                    ];
                                } else {
                                    $recieverData = $this->_customerFactory->create()->load($walletTransactionData->getSenderId());
                                    $transactionData["sender"] = [
                                        "label" => __("Receiver"),
                                        "value" => $recieverData->getName()
                                    ];
                                }
                            }
                            else
                            if( !in_array(
                                $walletTransactionData->getSenderType(),
                                $orderDetailsActions
                            ) && $walletTransactionData->getSenderType()!=Wallettransaction::CASH_BACK_TYPE &&
                                $walletTransactionData->getSenderType()!=Wallettransaction::CUSTOMER_TRANSFER_TYPE &&
                                $walletTransactionData->getSenderType()==Wallettransaction::CUSTOMER_TRANSFER_BANK_TYPE) {
                            //    echo Wallettransaction::CUSTOMER_TRANSFER_BANK_TYPE;die;
                                $accountData = $this->accountDetails->load($walletTransactionData->getBankDetails());
                                if ($accountData->getId()) {
                                    $helper->getbankDetails(nl2br($walletTransactionData->getBankDetails()));
                                    // $transactionData["bankDetails"] = [
                                    //     [
                                    //         "label" => __('A/c Holder Name'),
                                    //         "value" => $accountData->getBankname()
                                    //     ], [
                                    //         "label" => __('Bank Name'),
                                    //         "value" => $accountData->getBankname()
                                    //     ], [
                                    //         "label" => __('Bank Code'),
                                    //         "value" => $accountData->getBankcode()
                                    //     ], [
                                    //         "label" => __('Additional Information'),
                                    //         "value" => $accountData->getAdditional()
                                    //     ]
                                    // ];
                                    $transactionData["bankDetails"] = [
                                        'label' => __("Bank Details"),
                                        'value' => __('A/c Holder Name')."\n".$accountData->getHoldername()."\n\n".
                                        __('Bank Name')."\n".$accountData->getBankname()."\n\n".
                                        __('Bank Code')."\n".$accountData->getBankcode()."\n\n".
                                        __('Additional Information')."\n".$accountData->getAdditional()
                                    ];
                                }
                            }
                            $returnArray["success"]         = true;
                            $returnArray["transactionData"] = $transactionData;
                        } else {
                            $returnArray["message"] = __("You are not authorized to access this transaction!");
                            return $this->getJsonResponse($returnArray);
                        }
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