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

class Addpayee extends AbstractWallet    {

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
                    $storeId       = $wholeData["storeId"]?? 1;
                    $nickName      = $wholeData["nickName"]?? '';
                    $customerEmail = $wholeData["customerEmail"]?? '';
                    $customerToken = $wholeData["customerToken"] ?? '';
                    // $confirmEmail  = $wholeData["confirmEmail"]?? '';
                    $customerId    = $this->_helper->getCustomerByToken($customerToken) ?? 0;
                    if ($customerId) {
                        if ($nickName && $customerEmail
                        && !preg_match('#<script(.*?)>(.*?)</script>#is', $nickName)
                        ) {
                            $customer = $this->_customerFactory->create();
                            $websiteId = $this->storeManager->getStore()->getWebsiteId();
                            if (isset($websiteId)) {
                                $customer->setWebsiteId($websiteId);
                            }
                            $returnArray["customerId"] = $customerId;
                            $wholeData['customer_id'] = $customerId;
                            $wholeData['nickname'] = $nickName;
                            $customer->loadByEmail($customerEmail);
                            if ($customer && $customer->getId()) {
                                if ($customer->getId() == $customerId) {
                                    $result['error_msg'] = __("You can not add yourself in your payee list.");
                                    $result['error'] = 1;
                                } elseif ($this->alreadyAddedInPayee($wholeData, $customer)) {
                                    $result['error_msg'] = __("Customer with %1 email address id already present in payee list", $customerEmail);
                                    $result['error'] = 1;
                                } else {
                                    $result = $this->addPayeeToCustomer($wholeData, $customer);
                                }
                            } else {
                                $result['error_msg'] = __(
                                    "No customer found with email address %1",
                                    $customerEmail
                                );
                                $result['error'] = 1;
                            }
                            if ($result['error'] == 1) {
                                $returnArray["message"]      = $result['error_msg'];
                                $returnArray["success"]      = false;
                                return $this->getJsonResponse($returnArray);
                            } else {
                                $returnArray["success"] = true;
                                $returnArray["message"] = __('Payee is added in your list');
                            }
                        }
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

    public function addPayeeToCustomer($params, $customer)
    {
        $message = '';
        $payeeModel = $this->walletPayee->create();
        $configStatus = $this->_walletHelper->getPayeeStatus();
        if (!$configStatus) {
            $status = $payeeModel::PAYEE_STATUS_ENABLE;
        } else {
            $status = $payeeModel::PAYEE_STATUS_DISABLE;
        }
        $payeeModel->setCustomerId($params['customer_id'])
            ->setNickName($params['nickname'])
            ->setPayeeCustomerId($customer->getEntityId())
            ->setStatus($status)
            ->setWebsiteId($customer->getWebsiteId())
            ->save();

        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $payeeApprovalRequired = $this->scopeConfig->getValue('walletsystem/transfer_settings/payeestatus', $storeScope);
        if ($payeeApprovalRequired) {
            $this->setNotificationMessageForAdmin();
        }
        if ($payeeApprovalRequired) {
            $displayCustomMessage = $this->scopeConfig->getValue('walletsystem/transfer_settings/show_payee_message', $storeScope);
            if ($displayCustomMessage) {
                $message = __($this->scopeConfig->getValue('walletsystem/transfer_settings/show_payee_message_content', $storeScope));
            }
        }
        $result = [
            'error' => 0,
            'success_msg' => ($message) ? $message : __('Payee is added in your list')
        ];
        return $result;
    }
    public function alreadyAddedInPayee($params, $customer)
    {
        $payeeModel = $this->walletPayee->create()->getCollection()
            ->addFieldToFilter('customer_id', $params['customer_id'])
            ->addFieldToFilter('payee_customer_id', $customer->getEntityId())
            ->addFieldToFilter('website_id', $customer->getWebsiteId());
        if ($payeeModel->getSize()) {
            return true;
        }
        return false;
    }

    public function setNotificationMessageForAdmin()
    {
        $notificationModel = $this->_walletNotification->getCollection();
        if (!$notificationModel->getSize()) {
            $this->_walletNotification->setPayeeCounter(1);
            $this->_walletNotification->save();
        } else {
            foreach ($notificationModel->getItems() as $notification) {
                $notification->setPayeeCounter($notification->getPayeeCounter()+1);
            }
        }
        $notificationModel->save();
    }

}