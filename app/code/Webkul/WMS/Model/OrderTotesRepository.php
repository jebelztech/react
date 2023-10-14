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

use Webkul\WMS\Api\Data\OrderTotesInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Class OrderTotesRepository warehouse
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class OrderTotesRepository implements \Webkul\WMS\Api\OrderTotesRepositoryInterface
{

    /**
     * OrderTotesFactory
     *
     * @var OrderTotesFactory
     */
    protected $orderTotesFactory;

    /**
     * OrderTotes
     *
     * @var ResourceModel\OrderTotes
     */
    protected $resourceModel;

    /**
     * CollectionFactory
     *
     * @var ResourceModel\OrderTotes\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Array
     *
     * @var array
     */
    protected $instancesById = [];

    /**
     * Contructor of class OrderTotesRepository
     *
     * @param OrderTotesFactory                          $orderTotesFactory orderTotesFactory
     * @param ResourceModel\OrderTotes                   $resourceModel     resourceModel
     * @param ResourceModel\OrderTotes\CollectionFactory $collectionFactory collectionFactory
     */
    public function __construct(
        OrderTotesFactory $orderTotesFactory,
        ResourceModel\OrderTotes $resourceModel,
        ResourceModel\OrderTotes\CollectionFactory $collectionFactory
    ) {
        $this->resourceModel = $resourceModel;
        $this->orderTotesFactory = $orderTotesFactory;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Save Order
     *
     * @param OrderTotesInterface $orderTotes orderTotes
     *
     * @return mixed
     */
    public function save(OrderTotesInterface $orderTotes)
    {
        try {
            $this->resourceModel->save($orderTotes);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                $e->getMessage()
            );
        }
        unset($this->instancesById[$orderTotes->getId()]);
        return $this->getById($orderTotes->getId());
    }

    /**
     * Get OrderTotes
     *
     * @param integer $orderTotesId orderTotesId
     *
     * @return OrderTotesInterface
     */
    public function getById($orderId)
    {
        $orderTotesData = $this->orderTotesFactory->create();
        $orderTotesData->load($orderTotesId);
        $this->instancesById[$orderTotesId] = $orderTotesData;
        return $this->instancesById[$orderTotesId];
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
     * Delete OrderTotes
     *
     * @param OrderTotesInterface $orderTotes orderTotes
     *
     * @return boolean
     */
    public function delete(OrderTotesInterface $orderTotes)
    {
        $orderTotesId = $orderTotes->getId();
        try {
            $this->resourceModel->delete($orderTotes);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\StateException(
                __("Unable to remove orderTotes with id %1", $orderTotesId)
            );
        }
        unset($this->instancesById[$orderTotesId]);
        return true;
    }

    /**
     * Delete orderTotes by id
     *
     * @param integer $orderTotesId orderTotesId
     *
     * @return boolean
     */
    public function deleteById($orderTotesId)
    {
        $orderTotes = $this->getById($orderTotesId);
        return $this->delete($orderTotes);
    }
}
