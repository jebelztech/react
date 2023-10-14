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
 * Class MassDisable warehouse
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class MassDisable extends \Webkul\WMS\Controller\Adminhtml\Warehouse
{
    /**
     * Execure function for mass disable class
     *
     * @return page
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $collection = $this->filter->getCollection(
            $this->collectionFactory->create()
        );
        $warehousesUpdated = 0;
        $coditionArr = [];
        foreach ($collection->getAllIds() as $warehouseId) {
            $currentWarehouse = $this->warehouseRepository->getById($warehouseId);
            $warehouseData = $currentWarehouse->getData();
            if (count($warehouseData)) {
                $condition = "`id`=".$warehouseId;
                array_push($coditionArr, $condition);
                $warehousesUpdated++;
            }
        }
        $coditionData = implode(" OR ", $coditionArr);
        $collection->setWarehouseData($coditionData, ["status" => 0]);
        if ($warehousesUpdated) {
            $this->messageManager->addSuccess(
                __(
                    "A total of %1 warehouse(s) were disabled.",
                    $warehousesUpdated
                )
            );
        }
        return $resultRedirect->setPath("wms/warehouse/index");
    }
}
