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
 * Save Class for saving warehouses
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class Save extends \Webkul\WMS\Controller\Adminhtml\Warehouse
{
    /**
     * Execute function for class Save
     *
     * @return json
     */
    public function execute()
    {
        $returnToEdit = false;
        $originalRequestData = $this->getRequest()->getPostValue();
        $warehouseId = $originalRequestData["wms_warehouse"]["id"] ?? null;
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($originalRequestData) {
            try {
                $warehouseData = $originalRequestData["wms_warehouse"];
                $isExistingWarehouse = (bool)$warehouseId;
                $warehouse = $this->warehouseDataFactory->create();
                $existingWarehouse = $warehouse;
                if ($isExistingWarehouse) {
                    $existingWarehouse = $this->warehouseDataFactory
                        ->create()->load($warehouseId);
                    $warehouseData["id"] = $warehouseId;
                    $existingToteCount = $existingWarehouse->getToteCount();
                    $newToteCount = (int)$warehouseData['tote_count'];
                    $assignedProductQty = $this->getAssignedProductQty($warehouseData['id']);
                    if ($assignedProductQty > $newToteCount) {
                        $message =  __(
                            'Unable to save warehouse. Please unassigned a total of %1 product quantity and try again.',
                            $assignedProductQty - $newToteCount
                        );
                        return $this->redirectToEditPage($message, $originalRequestData, $warehouseData['id']);
                    }
                    if ($existingToteCount > $newToteCount) {
                        $toteStatus = $this->getToteStatus($warehouseData['id'], $existingToteCount, $newToteCount);
                        if (false === $toteStatus->getCanReduceToteCount()) {
                            $message =  __(
                                'Unable to save warehouse. Please free a total of %1 totes and try again.',
                                $toteStatus->getTotesToFree()
                            );
                            return $this->redirectToEditPage($message, $originalRequestData, $warehouseData['id']);
                        }
                    }
                }
                $warehouseData["updated_at"] = $this->date->gmtDate();
                if (!$isExistingWarehouse) {
                    $warehouseData["created_at"] = $this->date->gmtDate();
                }
                $warehouse->setData($warehouseData);
                // Save warehouse ////////////////////////////////////////////////
                if ($isExistingWarehouse) {
                    $this->warehouseRepository->save($warehouse);
                } else {
                    $warehouse = $this->warehouseRepository->save($warehouse);
                    $warehouseId = $warehouse->getId();
                }
                if ($warehouseId) {
                    // processing totes /////////////////////////////////////////////
                    $toteCollectionSize = $this->toteCollection->create()
                        ->addFieldToFilter("warehouse_id", $warehouseId)
                        ->getSize();
                    $this->_updateWarehouseTote(
                        $toteCollectionSize,
                        $warehouseData,
                        $warehouseId
                    );
                    // processing locations /////////////////////////////////////////
                    $this->_processLocation(
                        $warehouseData,
                        $existingWarehouse,
                        $warehouseId
                    );
                }
                $this->_getSession()->unsWarehouseFormData();
                // Done Saving warehouse, finish save action /////////////////////
                $this->coreRegistry->register("id", $warehouseId);
                $this->messageManager->addSuccess(__("You saved the warehouse."));
                $returnToEdit = (bool) $this->getRequest()->getParam("back", false);
            } catch (\Magento\Framework\Validator\Exception $exception) {
                $messages = $exception->getMessages();
                if (empty($messages)) {
                    $messages = $exception->getMessage();
                }
                $this->_addSessionErrorMessages($messages);
                $this->_getSession()->setWarehouseFormData($originalRequestData);
                $returnToEdit = true;
            } catch (\Exception $exception) {
                $this->messageManager->addException(
                    $exception,
                    __(
                        "Something went wrong while saving the warehouse. %1",
                        $exception->getMessage()
                    )
                );
                $this->_getSession()->setWarehouseFormData($originalRequestData);
                $returnToEdit = true;
            }
        }
        if ($returnToEdit) {
            if ($warehouseId) {
                $resultRedirect->setPath(
                    "wms/warehouse/edit",
                    [
                        "id" => $warehouseId,
                        "_current" => true
                    ]
                );
            } else {
                $resultRedirect->setPath("wms/warehouse/new", ["_current"=>true]);
            }
        } else {
            $resultRedirect->setPath("wms/warehouse/index");
        }
        return $resultRedirect;
    }

    public function redirectToEditPage($messageToDisplay, $originalRequestData, $warehouseId)
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $this->messageManager->addError(
            $messageToDisplay
        );
        $this->_getSession()->setWarehouseFormData($originalRequestData);
        $resultRedirect->setPath(
            "wms/warehouse/edit",
            [
                "id" => $warehouseId,
                "_current" => true
            ]
        );
        return $resultRedirect;
    }

    /**
     * Process locations
     *
     * @param array $warehouseData
     * @param object $existingWarehouse
     * @param int $warehouseId
     * @return void
     */
    private function _processLocation(
        $warehouseData,
        $existingWarehouse,
        $warehouseId
    ) {
        if (!(!isset($warehouseData["id"])
            || ((isset($warehouseData["row_count"])
            && isset($warehouseData["column_count"])
            && isset($warehouseData["racks_per_shelf"])
            && isset($warehouseData["shelves_per_cluster"]))
            && ($existingWarehouse->getRowCount() != $warehouseData["row_count"]
            || $existingWarehouse->getColumnCount() != $warehouseData["column_count"]
            || $existingWarehouse->getRacksPerShelf() != $warehouseData["racks_per_shelf"]
            || $existingWarehouse->getShelvesPerCluster()!= $warehouseData["shelves_per_cluster"]))
        )) {
            return ;
        }
        $productLocationsToPreserce = [];
        for ($row=1; $row <= $warehouseData["row_count"]; $row++) {
            for ($col=1; $col <= $warehouseData["column_count"]; $col++) {
                for ($shelf=65; $shelf < ($warehouseData["shelves_per_cluster"]+65); $shelf++) {
                    for ($rack=1; $rack <= $warehouseData["racks_per_shelf"]; $rack++) {
                        // phpcs:disable Magento2.Functions.DiscouragedFunction
                        $shelfChar = chr($shelf);
                        $productLocation = $this->productLocationFactory->create()->getCollection()
                            ->addFieldToFilter('row', $row)
                            ->addFieldToFilter('column', $col)
                            ->addFieldToFilter('shelf', $shelfChar)
                            ->addFieldToFilter('rack', $rack)
                            ->addFieldToFilter('warehouse_id', $warehouseId)
                            ->getFirstItem();
                        $productLocation->setRow($row)
                            ->setColumn($col)
                            ->setShelf($shelfChar) // there is not replacement found for this function so far
                            ->setRack($rack)
                            ->setWarehouseId($warehouseId)
                            ->save();
                        // phpcs:enable
                        $productLocationsToPreserce[] = $productLocation->getId();
                    }
                }
            }
        }

        $locationCollection = $this->locationCollection->create()
            ->addFieldToFilter("warehouse_id", $warehouseId)
            ->addFieldToFilter('id', ['nin' => $productLocationsToPreserce]);
        if ($locationCollection->getSize()) {
            foreach ($locationCollection as $eachLocation) {
                $eachLocation->delete();
            }
        }
    }

    public function getAssignedProductQty($warehouseId)
    {
        $productLocationColl = $this->productLocationFactory
            ->create()->getCollection()
            ->addFieldToFilter('warehouse_id', $warehouseId)
            ->addFieldToFilter('location_qty', ['gt' => 0]);
        $totalQty = 0;
        foreach ($productLocationColl as $productLocation) {
            $totalQty += $productLocation->getLocationQty();
        }
        return $totalQty;
    }

    /**
     * Update Warehouse totes
     *
     * @param int $toteCollectionSize
     * @param array $warehouseData
     * @param int $warehouseId
     * @return void
     */
    private function _updateWarehouseTote(
        $toteCollectionSize,
        $warehouseData,
        $warehouseId
    ) {
        if ($toteCollectionSize != $warehouseData["tote_count"]) {
            $targetDir = $this->directoryList->getPath("media");
            $collection = $this->toteCollection->create()->addFieldToFilter("warehouse_id", $warehouseId);
            $currentToteCount = $collection->getSize();
            $newToteCount = $warehouseData['tote_count'];
            if ($currentToteCount > $newToteCount) {
                $totesToBeDeleted = $currentToteCount - $newToteCount;
                $toteStatus = $this->getToteStatus($warehouseId, $currentToteCount, $newToteCount);
                $assignedToteIds = $toteStatus->getAssignedToteIds();
                if (count($assignedToteIds) > 0) {
                    $collection->addFieldToFilter('id', ['nin' => $assignedToteIds]);
                }
                $collection->setPageSize($totesToBeDeleted)
                    ->setCurPage(1)
                    ->load();
                foreach ($collection as $tote) {
                    $this->deleteTote($tote);
                }
            } else {
                $toteIndexStart = (int)$currentToteCount;
                $newTotesToBeAdded = $warehouseData["tote_count"] - $currentToteCount;
                $toteIndexEnd = $currentToteCount +
                        $newTotesToBeAdded;
                for ($i = $toteIndexStart; $i < $toteIndexEnd; $i++) {
                    $this->createTote($warehouseId, $i);
                }
            }

        }
    }

    private function createTote($warehouseId, $toteIndex)
    {
        $titleString = __("Tote-")->__toString().$warehouseId."-".($toteIndex + 1);
        $hashString = __("Tote-")->__toString().$warehouseId."-".($toteIndex + 1);
        $tote = $this->toteFactory->create()
            ->setTitle($titleString)
            ->setWarehouseId($warehouseId)
            ->save();
        $tote->setHash(
            $tote->getId()."-".$hashString."-".$warehouseId
        )->save();
    }

    private function deleteTote($tote)
    {
        $targetDir = $this->directoryList->getPath("media");
        $basePath = $targetDir."/"."wms"."/"."tote"."/".$tote->getId()."/";
        if ($this->driverInterface->isDirectory($basePath)) {
            $this->ioFile->rmdir($basePath, true);
        }
        $tote->delete();
    }

    private function getToteStatus($warehouseId, $newToteCount, $existingToteCount)
    {
        $noOfTotesToBeDeleted = $existingToteCount - $newToteCount;
        $toteColl = $this->toteCollection->create()->addFieldToFilter("warehouse_id", $warehouseId);
        $toteIds = [];
        foreach ($toteColl as $tote) {
            $toteIds[] = $tote->getId();
        }
        $orderTotesColl = $this->orderTotesF->create()->getCollection();
        $orderTotesColl->addFieldToFilter('assigned_tote_id', ['in' => $toteIds]);

        $orderStatusTable = $orderTotesColl->getTable('wms_order_status');
        $orderTotesColl->getSelect()->join(
            [
                "wms_order_status" => $orderStatusTable
            ],
            "wms_order_status.order_id = main_table.order_id && wms_order_status.status != 'packed'",
            []
        );
        $assignedToteIds = [];
        foreach ($orderTotesColl as $orderTote) {
            $assignedToteIds[] = $orderTote->getAssignedToteId();
        }
        $toteStatus = new \Magento\Framework\DataObject();
        $toteStatus->setExistingToteCount($existingToteCount);
        $toteStatus->setNewToteCount($newToteCount);
        $toteStatus->setAssignedToteIds($assignedToteIds);
        $toteStatus->setTotalAssignedTotes(count($assignedToteIds));
        $toteStatus->setTotalUnassignedTotes($existingToteCount - count($assignedToteIds));
        $toteStatus->setCanReduceToteCount($noOfTotesToBeDeleted <= $toteStatus->getTotalUnassignedTotes());
        $toteStatus->setTotesToFree($noOfTotesToBeDeleted - $toteStatus->getTotalUnassignedTotes());
        return $toteStatus;
    }
}
