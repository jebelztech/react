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

    class Index extends AbstractWallet    {

        public function execute()   {
            $returnArray                            = [];
            $returnArray["logo"]                    = "";
            $returnArray["authKey"]                 = "";
            $returnArray["message"]                 = "";
            $returnArray["success"]                 = false;
            $returnArray["subHeading"]              = [];
            $returnArray["buttonLabel"]             = "";
            $returnArray["mainHeading"]             = "";
            $returnArray["responseCode"]            = 0;
            $returnArray["currencyCode"]            = "";
            $returnArray["walletAmount"]            = "";
            $returnArray["maximumAmount"]           = 0;
            $returnArray["minimumAmount"]           = 0;
            $returnArray["walletProductId"]         = 0;
            $returnArray["transactionList"]         = [];
            $returnArray["accountDetails"]          = [];
            $returnArray["rechargeFieldLabel"]      = "";
            $returnArray["walletSummaryHeading"]    = "";
            $returnArray["walletSummarySubHeading"] = "";
            try {
                $wholeData       = $this->getRequest()->getParams();
                if ($wholeData) {
                    $authKey  = $this->getRequest()->getHeader("authKey");
                    $authData = $this->_helper->isAuthorized($authKey);
                    if ($authData["code"] == 1) {
                        $mFactor       = $wholeData["mFactor"];
                        $customerToken = $wholeData["customerToken"] ?? '';
                        $customerId    = $this->_helper->getCustomerByToken($customerToken) ?? 0;
                        $minimumAmount = 0;
                        $maximumAmount = 0;
                        $helper        = $this->_objectManager->get("Webkul\Walletsystem\Helper\Data");
                        if($helper->getWalletenabled()){
                            $maximumAmount = $helper->getMaximumAmount();
                            $minimumAmount = $helper->getMinimumAmount();
                            if($minimumAmount > $maximumAmount){
                                $temp          = $maximumAmount;
                                $maximumAmount = $minimumAmount;
                                $minimumAmount = $temp;
                            }
                            $returnArray["maximumAmount"] = $maximumAmount;
                            $returnArray["minimumAmount"] = $minimumAmount;
                            $Iconheight = $IconWidth = 144 * $mFactor;
                            $newUrl = "";
                            $basePath = $this->_moduleReader->getModuleDir(\Magento\Framework\Module\Dir::MODULE_VIEW_DIR, "Webkul_Walletsystem");
                            $basePath .= "/frontend/web/images/wallet.png";
                            if (is_file($basePath)) {
                                $newPath = $this->_baseDir."/"."mobikulresized"."/".$IconWidth."x".$Iconheight."/"."wallet.png";
                                $this->resizeNCache($basePath, $newPath, $IconWidth, $Iconheight);
                                $newUrl = $this->_helper->getUrl("media")."mobikulresized"."/".$IconWidth."x".$Iconheight."/"."wallet.png";
                            }
                            $returnArray["logo"] = $newUrl;
                            $returnArray["walletSummaryHeading"] = __("Wallet Details");
                            $returnArray["currencyCode"] = $helper->getCurrentCurrencyCode();
                            $remainingAmount = 0;
                            $walletRecordCollection = $this->_walletrecordModel->create()->addFieldToFilter("customer_id", $customerId);
                            if(count($walletRecordCollection)){
                                foreach ($walletRecordCollection as $record) {
                                    $remainingAmount = $record->getRemainingAmount();
                                }
                            }
                            $returnArray["walletAmount"] = $this->stripTags($this->_priceFormat->currency($remainingAmount));
                            $returnArray["walletSummarySubHeading"] = __("Your wallet Balance");
                            $returnArray["rechargeFieldLabel"] = __("Enter Amount to be Added in wallet");
                            $returnArray["walletProductId"] = $helper->getWalletProductId();
                            $returnArray["buttonLabel"] = __("Add Money To Wallet");
                            $returnArray["mainHeading"]  = __("Last Transactions");
                            $returnArray["subHeading"][] = __("Description");
                            $returnArray["subHeading"][] = __("Debit");
                            $returnArray["subHeading"][] = __("Credit");
                            $returnArray["subHeading"][] = __("Status");
                            $walletCollection = $this->_wallettransactionModel->create()
                                ->addFieldToFilter("customer_id", $customerId)
                                ->setOrder("transaction_at", "DESC");
                            $transactionList = [];
                            if(count($walletCollection)){
                                foreach ($walletCollection as $record) {
                                    $eachTransaction = [];
                                    $prefix = $helper->getTransactionPrefix($record->getSenderType(), $record->getAction());
                                    $eachTransaction["viewId"]      = 0;
                                    $eachTransaction["incrementId"] = "";
                                    $eachTransaction["viewId"]      = $record->getEntityId();
                                    $eachTransaction["description"] = $prefix." #".$record->getEntityId();
                                    $eachTransaction["debit"]       = "";
                                    $eachTransaction["credit"]      = "";
                                    if($record->getAction() == "debit")
                                        $eachTransaction["debit"]   = $this->stripTags($this->_priceFormat->currency($record->getCurrAmount()));
                                    else
                                        $eachTransaction["credit"]  = $this->stripTags($this->_priceFormat->currency($record->getCurrAmount()));
                                    $eachTransaction["status"]      = __("Pending");
                                    if($record->getStatus())
                                    $eachTransaction["status"]      = __("Applied");
                                    $transactionList[]              = $eachTransaction;
                                }
                                $returnArray["transactionList"]     = $transactionList;
                            }
                            else{
                                $returnArray["message"] = __("No records found!");
                            }
                            
                            $accountDetails = $this->accountDetails->getCollection()
                                ->addFieldToFilter("customer_id", ["eq"=> $customerId]);
                            if ($accountDetails->getSize()) {
                                foreach ($accountDetails as $model) {
                                    $eachDetails                        = [];
                                    if ($model->getBankname() != "") {
                                        $eachDetails["id"]                  = $model->getId();
                                        $eachDetails["bankName"]            = $model->getBankname();
                                        $returnArray["accountDetails"][]    = $eachDetails;
                                    }
                                }
                            }
                            else{
                                $returnArray["messageForAccountDetails"] = __("No Record Found!");
                            }

                            $returnArray["success"] = true;
                            $returnArray["idd"] = $customerId;
                        }
                        else{
                            $returnArray["message"] = __("Payment method is not enabled.");
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