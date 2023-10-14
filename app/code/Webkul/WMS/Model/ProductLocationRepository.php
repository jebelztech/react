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

use Webkul\WMS\Api\Data\ProductLocationInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Class ProductLocationRepository warehouse
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class ProductLocationRepository implements \Webkul\WMS\Api\ProductLocationRepositoryInterface
{
    /**
     * ProductLocationFactory
     *
     * @var ProductLocationFactory
     */
    protected $productLocationFactory;

    /**
     * ProductLocation
     *
     * @var ResourceModel\ProductLocation
     */
    protected $resourceModel;

    /**
     * CollectionFactory
     *
     * @var ResourceModel\ProductLocation\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Array
     *
     * @var array
     */
    protected $instancesById = [];

    /**
     * Contructor of class ProductLocationRepository
     *
     * @param ResourceModel\ProductLocation                   $resourceModel          resourceModel
     * @param ProductLocationFactory                          $productLocationFactory productLocationFactory
     * @param ResourceModel\ProductLocation\CollectionFactory $collectionFactory      collectionFactory
     */
    public function __construct(
        ResourceModel\ProductLocation $resourceModel,
        ProductLocationFactory $productLocationFactory,
        ResourceModel\ProductLocation\CollectionFactory $collectionFactory
    ) {
        $this->resourceModel = $resourceModel;
        $this->collectionFactory = $collectionFactory;
        $this->productLocationFactory = $productLocationFactory;
    }

    /**
     * Save ProductLocation
     *
     * @param ProductLocationInterface $productLocation productLocation
     *
     * @return mixed
     */
    public function save(ProductLocationInterface $productLocation)
    {
        try {
            $this->resourceModel->save($productLocation);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                $e->getMessage()
            );
        }
        unset($this->instancesById[$productLocation->getId()]);
        return $this->getById($productLocation->getId());
    }

    /**
     * Get ProductLocation
     *
     * @param integer $productLocationId productLocationId
     *
     * @return ProductLocationInterface
     */
    public function getById($productLocationId)
    {
        $productLocationData = $this->productLocationFactory->create();
        $productLocationData->load($productLocationId);
        $this->instancesById[$productLocationId] = $productLocationData;
        return $this->instancesById[$productLocationId];
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
     * Delete ProductLocation
     *
     * @param ProductLocationInterface $productLocation productLocation
     *
     * @return boolean
     */
    public function delete(ProductLocationInterface $productLocation)
    {
        $productLocationId = $productLocation->getId();
        try {
            $this->resourceModel->delete($productLocation);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\StateException(
                __("Unable to remove productLocation with id %1", $productLocationId)
            );
        }
        unset($this->instancesById[$productLocationId]);
        return true;
    }

    /**
     * Delete productLocation by id
     *
     * @param integer $productLocationId productLocationId
     *
     * @return boolean
     */
    public function deleteById($productLocationId)
    {
        $productLocation = $this->getById($productLocationId);
        return $this->delete($productLocation);
    }
}
