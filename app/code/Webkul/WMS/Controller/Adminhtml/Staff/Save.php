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
 * Save Class staff
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class Save extends \Webkul\WMS\Controller\Adminhtml\Staff
{
    /**
     * Execute function for class Save
     *
     * @return json
     */
    public function execute()
    {
        $returnToEdit = false;
        $requestData = $this->getRequest()->getPostValue();
        $staffId = $requestData["wms_staff"]["id"] ?? null;
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($requestData) {
            try {
                $staffData = $requestData["wms_staff"];
                $imageName = $this->getStafImageName($staffData);
                if (strpos($imageName, "wms/profileimages/") !== false) {
                    $staffData["filename"] = $imageName;
                } else {
                    $staffData["filename"] = "wms/profileimages/".$imageName;
                }
                $isExistingStaff = (bool)$staffId;
                $staff = $this->staffDataFactory->create();
                if ($isExistingStaff) {
                    $staffData["id"] = $staffId;
                }
                $staffData["updated_at"] = $this->date->gmtDate();
                if (!$isExistingStaff) {
                    $staffData["created_at"] = $this->date->gmtDate();
                }
                $rawPassword = null;
                if ($staffData["password"] == $staffData["confpassword"] && $staffData["password"] != "") {
                    $rawPassword = $staffData["password"];
                    $staffData["password"] = $this->createPasswordHash($staffData["password"]);
                } else {
                    unset($staffData["password"]);
                }
                $staff->setData($staffData);
                if (isset($staffData['dob']) && false === strptime($staffData['dob'], "%m/%d/%Y")) {
                    $this->messageManager->addError(
                        __(
                            "Please enter a valid date of birth"
                        )->__toString()
                    );
                    if ($staffId) {
                        $resultRedirect->setPath(
                            "wms/staff/edit",
                            [
                                "id"=>$staffId,
                                "_current"=>true
                            ]
                        );
                    } else {
                        $resultRedirect->setPath("wms/staff/new", ["_current"=>true]);
                    }
                    return $resultRedirect;
                }

                // Save staff ////////////////////////////////////////////////
                if ($isExistingStaff) {
                    $this->staffRepository->save($staff);
                } else {
                    $savedStaff = $this->staffRepository->save($staff);
                    $staffId = $savedStaff->getId();
                }
                $this->inlineTranslation->suspend();
                try {
                    $emailParams = [];
                    $emailParams["userName"] = $staffData["email"];
                    $emailParams["password"] = $rawPassword ?? __("Not changed.");
                    $emailParams["staffName"] = $staffData["name"];
                    $sender = [
                        "name" => $this->helper->getConfigData("trans_email/ident_general/name"),
                        "email" => $this->helper->getConfigData("trans_email/ident_general/email")
                    ];
                    $transport = $this->transportBuilder
                        ->setTemplateIdentifier("wms_staff_credentials_email")
                        ->setTemplateOptions(
                            [
                                "area"  => \Magento\Framework\App\Area::AREA_ADMINHTML,
                                "store" => $this->storeManager->getStore()->getId(),
                            ]
                        )
                        ->setTemplateVars($emailParams)
                        ->setFrom($sender)
                        ->addTo($staffData["email"], $staffData["name"])
                        ->getTransport();
                    $transport->sendMessage();
                    $this->inlineTranslation->resume();
                } catch (\Exception $e) {
                    $this->inlineTranslation->resume();
                }
                $this->_getSession()->unsStaffFormData();
                // Done Saving staff, finish save action /////////////////////
                $this->coreRegistry->register("id", $staffId);
                $this->messageManager->addSuccess(__("You saved the staff."));
                $returnToEdit = (bool) $this->getRequest()->getParam("back", false);
            } catch (\Magento\Framework\Validator\Exception $exception) {
                $messages = $exception->getMessages();
                if (empty($messages)) {
                    $messages = $exception->getMessage();
                }
                $this->_addSessionErrorMessages($messages);
                $this->_getSession()->setStaffFormData($requestData);
                $returnToEdit = true;
            } catch (\Exception $exception) {
                $this->messageManager->addException(
                    $exception,
                    __(
                        "Something went wrong while saving the staff. %1",
                        $exception->getMessage()
                    )
                );
                $this->_getSession()->setStaffFormData($requestData);
                $returnToEdit = true;
            }
        }
        if ($returnToEdit) {
            if ($staffId) {
                $resultRedirect->setPath(
                    "wms/staff/edit",
                    [
                        "id"=>$staffId,
                        "_current"=>true
                    ]
                );
            } else {
                $resultRedirect->setPath("wms/staff/new", ["_current"=>true]);
            }
        } else {
            $resultRedirect->setPath("wms/staff/index");
        }
        return $resultRedirect;
    }

    /**
     * Funciton to get staff Image name
     *
     * @param array $staffData staffData
     *
     * @return page
     */
    protected function getStafImageName($staffData)
    {
        if (isset($staffData["filename"][0]["name"])) {
            if (isset($staffData["filename"][0]["name"])) {
                return $staffData["filename"] = $staffData["filename"][0]["name"];
            } else {
                throw new \Magento\Framework\Exception\LocalizedException(__("Please upload profile image."));
            }
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(__("Please upload profile image."));
        }
    }
}
