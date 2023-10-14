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

class Deletepayee extends AbstractWallet    {

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
                    $id            = $wholeData["id"]?? '';
                    $customerToken  = $wholeData["customerToken"] ?? '';
                    $customerId    = $this->_helper->getCustomerByToken($customerToken) ?? 0;
                    if ($customerId) {
                        if ($id != '') {
                            $this->deletePayee($id);
                            $returnArray["success"] = true;
                            $returnArray["message"]  = __("Payee is successfully deleted");
                        } else {
                            $returnArray["message"] = __("There is some error during executing this process, please try again later.");
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

    public function deletePayee($id)
    {
        $payeeModel = $this->walletPayee->create()->load($id);
        $payeeModel->delete();
    }

}