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

    class Transfer extends AbstractWallet    {

        public function execute()   {
            $returnArray                            = [];
            $returnArray["logo"]                    = "";
            $returnArray["authKey"]                 = "";
            $returnArray["message"]                 = "";
            $returnArray["success"]                 = false;
            $returnArray["responseCode"]            = 0;
            $returnArray["currencyCode"]            = "";
            $returnArray["walletAmount"]            = "";
            $returnArray["walletSummaryHeading"]    = "";
            $returnArray["walletSummarySubHeading"] = "";
            try {
                $wholeData         = $this->getRequest()->getPostValue();
                if ($wholeData) {
                    $authKey  = $this->getRequest()->getHeader("authKey");
                    $authData = $this->_helper->isAuthorized($authKey);
                    if ($authData["code"] == 1) {
                        $mFactor       = $wholeData["mFactor"]?? 1;
                        $customerToken = $wholeData["customerToken"] ?? '';

                        $currency      = $wholeData["currency"] ?? $this->store->getBaseCurrencyCode();
                        $this->store->setCurrentCurrencyCode($currency);
                        
                        $customerId    = $this->_helper->getCustomerByToken($customerToken) ?? 0;
                        $helper        = $this->_objectManager->get("Webkul\Walletsystem\Helper\Data");
                        if($helper->getWalletenabled()){
                            $Iconheight = $IconWidth = 144 * $mFactor;
                            $newUrl     = "";
                            $basePath   = $this->_moduleReader->getModuleDir(\Magento\Framework\Module\Dir::MODULE_VIEW_DIR, "Webkul_Walletsystem");
                            $basePath  .= "/frontend/web/images/wallet.png";
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
                            
                            $walletPayeeCollection = $this->walletPayee->create()
                            ->getCollection()
                            ->addFieldToFilter('customer_id', $customerId);

                            if ($walletPayeeCollection->getSize()) {
                                foreach ($walletPayeeCollection as $model) {
                                    
                                    $eachModel                  = [];
                                    $customerModel = $this->_customerFactory->create()->load($model->getData("payee_customer_id"));
                               
                                    $eachModel["id"]            = $model->getId();
                                    $eachModel["name"]          = $model->getNickName();
                                    $eachModel["email"]         = $customerModel->getEmail();
                                    $eachModel["status"]        = $model->getStatus()?__('Applied'):__('Pending');
                                    $returnArray["payeeList"][] = $eachModel;
                                }
                            }
                            $returnArray["success"] = true;
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