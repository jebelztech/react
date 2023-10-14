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

/**
 * Class to Delete warehouse
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class Delete extends \Webkul\WMS\Controller\Adminhtml\Warehouse
{
    /**
     * Execute function for delete Class
     *
     * @return page
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $formKeyIsValid = $this->formKeyValidator->validate($this->getRequest());
        $isPost = $this->getRequest()->isPost();
        if (!$formKeyIsValid || !$isPost) {
            $this->messageManager->addError(__("Warehouse could not be deleted."));
            return $resultRedirect->setPath("wms/warehouse/index");
        }
        $warehouseId = $this->initCurrentWarehouse();
        $locationCollection = $this->locationCollection->create()
            ->addFieldToFilter("warehouse_id", $warehouseId);
        foreach ($locationCollection as $eachLocation) {
            $this->locationRepository->deleteById($eachLocation->getId());
        }
        if (!empty($warehouseId)) {
            try {
                $this->warehouseRepository->deleteById($warehouseId);
                $this->messageManager->addSuccess(__("Warehouse has been deleted."));
            } catch (\Exception $exception) {
                $this->messageManager->addError($exception->getMessage());
            }
        }
        return $resultRedirect->setPath("wms/warehouse/index");
    }

    /**
     * Function to intialise current Warehouse
     *
     * @return int
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
