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
 * Class Mass delete to delete warehouse in massaction
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class MassDelete extends \Webkul\WMS\Controller\Adminhtml\Warehouse
{
    /**
     * Execute function for MassDelete class
     *
     * @return page
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $collection = $this->filter->getCollection(
            $this->collectionFactory->create()
        );
        $warehousesDeleted = 0;
        foreach ($collection->getAllIds() as $warehouseId) {
            $locationCollection = $this->locationCollection->create()
                ->addFieldToFilter("warehouse_id", $warehouseId);
            foreach ($locationCollection as $eachLocation) {
                $this->locationRepository->deleteById($eachLocation->getId());
            }
            if (!empty($warehouseId)) {
                try {
                    $this->warehouseRepository->deleteById($warehouseId);
                    $warehousesDeleted++;
                } catch (\Exception $exception) {
                    $this->messageManager->addError($exception->getMessage());
                }
            }
        }
        if ($warehousesDeleted) {
            $this->messageManager->addSuccess(
                __(
                    "A total of %1 warehouse(s) were deleted.",
                    $warehousesDeleted
                )
            );
        }
        return $resultRedirect->setPath("wms/warehouse/index");
    }
}
