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

namespace Webkul\WMS\Controller\Adminhtml\Warehouse;

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Warehouse Edit Class to change the Warhouse Details
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class Edit extends \Webkul\WMS\Controller\Adminhtml\Warehouse
{
    /**
     * Warehouse edit execute
     *
     * @return \Magento\Framework\Controller\Result\RedirectFactory
     */
    public function execute()
    {
        $warehouseId = $this->initCurrentWarehouse();
        $isExistingWarehouse = (bool)$warehouseId;
        if ($isExistingWarehouse) {
            try {
                $warehouseData = [];
                $warehouseData["wms_warehouse"] = [];
                $warehouse = null;
                $warehouse = $this->warehouseRepository->getById($warehouseId);
                $result = $warehouse->getData();
                if (count($result)) {
                    $warehouseData["wms_warehouse"] = $result;
                    $warehouseData["wms_warehouse"]["id"] = $warehouseId;
                    $this->_getSession()->setWarehouseFormData($warehouseData);
                } else {
                    $this->messageManager->addError(
                        __("Requested warehouse doesn't exist")
                    );
                    $resultRedirect = $this->resultRedirectFactory->create();
                    $resultRedirect->setPath("wms/warehouse/index");
                    return $resultRedirect;
                }
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addException(
                    $e,
                    __("Something went wrong while editing the warehouse.")
                );
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath("wms/warehouse/index");
                return $resultRedirect;
            }
        }
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu("Webkul_WMS::warehouse");
        if ($isExistingWarehouse) {
            $resultPage->getConfig()->getTitle()
                ->prepend(
                    __(
                        "Edit Warehouse \"%1\"",
                        $warehouseData["wms_warehouse"]["title"]
                    )
                );
        } else {
            $resultPage->getConfig()->getTitle()->prepend(__("New Warehouse"));
        }
        return $resultPage;
    }

    /**
     * Function to Initialize current Warehouse
     *
     * @return Int
     */
    protected function initCurrentWarehouse()
    {
        $warehouseId = (int)$this->getRequest()->getParam("id");
        if ($warehouseId) {
            $this->coreRegistry->register("id", $warehouseId);
        }
        return $warehouseId;
    }
}
