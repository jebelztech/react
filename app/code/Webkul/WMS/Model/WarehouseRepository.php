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

namespace Webkul\WMS\Model;

use Webkul\WMS\Api\Data\WarehouseInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Class WarehouseRepository warehouse
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class WarehouseRepository implements \Webkul\WMS\Api\WarehouseRepositoryInterface
{
    protected $resourceModel;
    protected $warehouseFactory;
    protected $collectionFactory;
    protected $instancesById = [];

    /**
     * Contructor of class WarehouseRepository
     *
     * @param WarehouseFactory                          $warehouseFactory  Warehouse
     * @param ResourceModel\Warehouse                   $resourceModel     Warehousemodel
     * @param ResourceModel\Warehouse\CollectionFactory $collectionFactory WarehouseCollection
     */
    public function __construct(
        WarehouseFactory $warehouseFactory,
        ResourceModel\Warehouse $resourceModel,
        ResourceModel\Warehouse\CollectionFactory $collectionFactory
    ) {
        $this->resourceModel = $resourceModel;
        $this->warehouseFactory = $warehouseFactory;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Save Warehouse
     *
     * @param WarehouseInterface $warehouse Warehouse
     *
     * @return warehouse
     */
    public function save(WarehouseInterface $warehouse)
    {
        try {
            $this->resourceModel->save($warehouse);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                $e->getMessage()
            );
        }
        unset($this->instancesById[$warehouse->getId()]);
        return $this->getById($warehouse->getId());
    }

    /**
     * Get Warehouse
     *
     * @param integer $warehouseId warehouseId
     *
     * @return WarehouseInterface
     */
    public function getById($warehouseId)
    {
        $warehouseData = $this->warehouseFactory->create();
        $warehouseData->load($warehouseId);
        $this->instancesById[$warehouseId] = $warehouseData;
        return $this->instancesById[$warehouseId];
    }

    /**
     * Get list of wahrehouse
     *
     * @param SearchCriteriaInterface $searchCriteria searchCriteria
     *
     * @return mixed
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->collectionFactory->create();
        $collection->load();
        return $collection;
    }

    /**
     * Delete Warehouse
     *
     * @param WarehouseInterface $warehouse warehouse
     *
     * @return boolean
     */
    public function delete(WarehouseInterface $warehouse)
    {
        $warehouseId = $warehouse->getId();
        try {
            $this->resourceModel->delete($warehouse);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\StateException(
                __("Unable to remove warehouse with id %1", $warehouseId)
            );
        }
        unset($this->instancesById[$warehouseId]);
        return true;
    }

    /**
     * Delete warehouse by id
     *
     * @param integer $warehouseId warehouseId
     *
     * @return boolean
     */
    public function deleteById($warehouseId)
    {
        $warehouse = $this->getById($warehouseId);
        return $this->delete($warehouse);
    }
}
