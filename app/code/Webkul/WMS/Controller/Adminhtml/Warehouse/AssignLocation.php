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

use Magento\Framework\Controller\ResultFactory;

/**
 * Assign Location in warehouse
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class AssignLocation extends \Webkul\WMS\Controller\Adminhtml\Warehouse
{
    /**
     * Execute function for class AssignLocation
     *
     * @return json
     */
    public function execute()
    {
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        try {
            $request = $this->getRequest();
            $data = $this->getRequest()->getParam("data");
            $dataQty = $this->getRequest()->getParam("dataQty");
            $warehouseId = $this->getRequest()->getParam("id");
            $assignedProductQty = $this->getRequest()->getParam("assignedProductQty");
            $warehouseSourceCode = $request->getParam('warehouseSourceCode');
            $productSku = $request->getParam('productSku');
            $productId = $this->productF->create()->getCollection()
                ->addFieldToFilter('sku', $productSku)
                ->getFirstItem()
                ->getId();
            $productStockQty = $this->getSalableQtyBySkuSource
                ->execute($productSku, $warehouseSourceCode);
            $totalAssignmentQty = 0;
            $productLocations = [];
            if ($data) {
                $locationsExcludingCurrentShelf = $this->locationCollection->create()
                    ->addFieldToFilter('id', ['nin' => array_keys($dataQty)])
                    ->addFieldToFilter('product_id', $productId)
                    ->addFieldToFilter('warehouse_id', $warehouseId);
                foreach ($locationsExcludingCurrentShelf as $location) {
                    $totalAssignmentQty += $location->getLocationQty();
                }
                foreach ($dataQty as $locationId => $locationQty) {
                    $location = $this->locationRepository->getById($locationId);
                    if (isset($data[$locationId]) && $data[$locationId] == 0) {
                        $oldQty = $location->getLocationQty();
                        $location->setProductId(0)->setLocationQty(0)->save();
                        $totalAssignmentQty -= $oldQty;
                    } elseif (isset($data[$locationId]) && $data[$locationId] == $productId) {
                        $location->setProductId($productId);
                        $location->setLocationQty($locationQty);
                        $productLocations[] = $location;
                    }
                    $totalAssignmentQty += $locationQty;
                }
                if ($totalAssignmentQty > $productStockQty) {
                    $resultJson->setData(
                        [
                            "success" => false,
                            "message" => __(
                                "Unable to perform the requested operation.".
                                "Product assignment qty exceeded total available product qty: %1",
                                $productStockQty
                            )
                        ]
                    );
                } else {
                    foreach ($productLocations as $location) {
                        $this->locationRepository->save($location);
                    }
                    $resultJson->setData(
                        [
                            "success" => true,
                            "message" => __("Process completed Successfully")
                        ]
                    );
                    $this->messageManager->addSuccess(__("Process completed Successfully"));
                }
            } else {
                $resultJson->setData(
                    [
                        "success" => true,
                        "message" => __("Process completed Successfully")
                    ]
                );
            }
            return $resultJson;
        } catch (\Exception $e) {
            $this->messageManager->addException(
                $e,
                __(
                    "Something went wrong while saving the location. %1",
                    $e->getMessage()
                )
            );
            $resultJson->setData(
                [
                    "success" => false,
                    "message" => __($e->getMessage())
                ]
            );
            return $resultJson;
        }
    }
}
