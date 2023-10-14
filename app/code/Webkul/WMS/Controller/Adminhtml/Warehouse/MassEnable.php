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
 * Class MassEnable warehouse
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class MassEnable extends \Webkul\WMS\Controller\Adminhtml\Warehouse
{
    /**
     * Execure function for mass enable class
     *
     * @return page
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $collection = $this->filter->getCollection(
            $this->collectionFactory->create()
        );
        $bannersUpdated = 0;
        $coditionArr = [];
        foreach ($collection->getAllIds() as $key => $warehouseId) {
            $currentWarehouse = $this->warehouseRepository->getById($warehouseId);
            $warehouseData = $currentWarehouse->getData();
            if (count($warehouseData)) {
                $condition = "`id`=".$warehouseId;
                array_push($coditionArr, $condition);
                $bannersUpdated++;
            }
        }
        $coditionData = implode(" OR ", $coditionArr);
        $collection->setWarehouseData($coditionData, ["status"=>1]);
        if ($bannersUpdated) {
            $this->messageManager->addSuccess(
                __(
                    "A total of %1 warehouse(s) were enabled.",
                    $bannersUpdated
                )
            );
        }
        return $resultRedirect->setPath("wms/warehouse/index");
    }
}
