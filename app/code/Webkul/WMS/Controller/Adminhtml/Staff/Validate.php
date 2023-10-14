<?php
/**
 * Webkul Software.
 *
 * PHP version 7.0+
 *
 * @category  Webkul
 * @package   Webkul_WMS
 * @author    Webkul <support@webkul.com>
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html ASL Licence
 * @link      https://store.webkul.com/license.html
 */

namespace Webkul\WMS\Controller\Adminhtml\Staff;

/**
 * Validate Class for staff
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class Validate extends \Webkul\WMS\Controller\Adminhtml\Staff
{
    /**
     * ValidateStaff function for class Upload
     *
     * @param \Magento\Framework\DataObject $response response
     *
     * @return staff
     */
    protected function validateStaff($response)
    {
        $banner = null;
        $errors = [];
        try {
            $banner = $this->staffDataFactory->create();
            $data = $this->getRequest()->getParams();
            $dataResult = $data["wms_staff"];
            $errors = [];
            if (isset($dataResult["password"])) {
                if ($dataResult["password"] != $dataResult["confpassword"]) {
                    $errors[] = __("Password should match confirmation.");
                }
            } else {
                $errors[] = __("Password can not be blank.");
            }
            if (!isset($dataResult["name"])) {
                $errors[] = __("Name is mandatory field.");
            }
            if (!isset($dataResult["email"])) {
                $errors[] = __("Email is mandatory field.");
            }
            if (!isset($dataResult["contact_no"])) {
                $errors[] = __("Contact Number is mandatory field.");
            }
            if (!isset($dataResult["dob"])) {
                $errors[] = __("Date of birth is mandatory field.");
            }
            if (!isset($dataResult["gender"])) {
                $errors[] = __("Gender is mandatory field.");
            }
            if (!isset($dataResult["address"])) {
                $errors[] = __("Address is mandatory field.");
            }
            $staff = $this->collectionFactory
                ->create()
                ->addFieldToFilter(
                    "id",
                    [
                        "neq" => $dataResult["id"] ?? 0
                    ]
                )
                ->addFieldToFilter("email", $dataResult["email"])
                ->getFirstItem();
            if ($staff->getId()) {
                $errors[] = __("There is already a staff exist with this Email.");
            }
        } catch (\Magento\Framework\Validator\Exception $exception) {
            $exceptionMsg = $exception->getMessages(
                \Magento\Framework\Message\MessageInterface::TYPE_ERROR
            );
            foreach ($exceptionMsg as $error) {
                $errors[] = $error->getText();
            }
        }
        if ($errors) {
            $messages = $response->hasMessages() ? $response->getMessages() : [];
            foreach ($errors as $error) {
                $messages[] = $error;
            }
            $response->setMessages($messages);
            $response->setError(1);
        }
        return $banner;
    }

    /**
     * Execute function for class Upload
     *
     * @return json
     */
    public function execute()
    {
        $response = new \Magento\Framework\DataObject();
        $response->setError(0);
        $this->validateStaff($response);
        $resultJson = $this->resultJsonFactory->create();
        if ($response->getError()) {
            $response->setError(true);
            $response->setMessages($response->getMessages());
        }
        $resultJson->setData($response);
        return $resultJson;
    }
}
