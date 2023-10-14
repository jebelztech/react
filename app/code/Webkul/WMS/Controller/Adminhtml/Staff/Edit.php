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

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Staff Edit Class
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class Edit extends \Webkul\WMS\Controller\Adminhtml\Staff
{
    /**
     * Staff edit execute
     *
     * @return \Magento\Framework\Controller\Result\RedirectFactory
     */
    public function execute()
    {
        $staffId = $this->initCurrentStaff();
        $isExistingStaff = (bool)$staffId;
        if ($isExistingStaff) {
            try {
                $staffData = [];
                $staffData["wms_staff"] = [];
                $staff = null;
                $staff = $this->staffRepository->getById($staffId);
                $result = $staff->getData();
                $baseTmpPath = "";
                $target = $this->storeManager->getStore()
                    ->getBaseUrl(
                        \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                    ).$baseTmpPath;
                if (count($result)) {
                    $staffData["wms_staff"] = $result;
                    $staffData["wms_staff"]["filename"] = [];
                    $staffData["wms_staff"]["filename"][0] = [];
                    $staffData["wms_staff"]["filename"][0]["name"] = $result["filename"];
                    $staffData["wms_staff"]["filename"][0]["url"] = $target.$result["filename"];
                    $filePath = $this->mediaDirectory->getAbsolutePath($baseTmpPath).$result["filename"];
                    if ($this->driverInterface->isFile($filePath)) {
                        $staffData["wms_staff"]["filename"][0]["size"] = $this->fileHelper->getFileSize($filePath);
                    } else {
                        $staffData["wms_staff"]["filename"][0]["size"] = 0;
                    }
                    $staffData["wms_staff"]["id"] = $staffId;
                    $this->_getSession()->setStaffFormData($staffData);
                } else {
                    $this->messageManager->addError(
                        __("Requested staff doesn't exist")
                    );
                    $resultRedirect = $this->resultRedirectFactory->create();
                    $resultRedirect->setPath("wms/staff/index");
                    return $resultRedirect;
                }
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addException(
                    $e,
                    __("Something went wrong while editing the staff.")
                );
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath("wms/staff/index");
                return $resultRedirect;
            }
        }
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu("Webkul_WMS::staff");
        if ($isExistingStaff) {
            $resultPage->getConfig()->getTitle()
                ->prepend(
                    __(
                        "Edit Staff \"%1\"",
                        $staffData["wms_staff"]["name"]
                    )
                );
        } else {
            $resultPage->getConfig()->getTitle()->prepend(__("New Staff"));
        }
        return $resultPage;
    }

    /**
     * Function to Initialize current Staff
     *
     * @return Int
     */
    protected function initCurrentStaff()
    {
        $staffId = (int)$this->getRequest()->getParam("id");
        if ($staffId) {
            $this->coreRegistry->register("id", $staffId);
        }
        return $staffId;
    }
}
