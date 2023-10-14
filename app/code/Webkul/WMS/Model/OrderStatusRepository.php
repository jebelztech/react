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

use Webkul\WMS\Api\Data\OrderStatusInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Class OrderStatusRepository warehouse
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class OrderStatusRepository implements \Webkul\WMS\Api\OrderStatusRepositoryInterface
{
    /**
     * OrderStatusFactory
     *
     * @var OrderStatusFactory
     */
    protected $orderStatusFactory;

    /**
     * OrderStatus
     *
     * @var ResourceModel\OrderStatus
     */
    protected $resourceModel;

    /**
     * CollectionFactory
     *
     * @var ResourceModel\OrderStatus\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Array
     *
     * @var array
     */
    protected $instancesById = [];

    /**
     * Contructor of class OrderStatusRepository
     *
     * @param OrderStatusFactory                          $orderStatusFactory orderStatusFactory
     * @param ResourceModel\OrderStatus                   $resourceModel      resourceModel
     * @param ResourceModel\OrderStatus\CollectionFactory $collectionFactory  collectionFactory
     */
    public function __construct(
        OrderStatusFactory $orderStatusFactory,
        ResourceModel\OrderStatus $resourceModel,
        ResourceModel\OrderStatus\CollectionFactory $collectionFactory
    ) {
        $this->resourceModel = $resourceModel;
        $this->collectionFactory = $collectionFactory;
        $this->orderStatusFactory = $orderStatusFactory;
    }

    /**
     * Save OrderStatus
     *
     * @param OrderStatusInterface $orderStatus orderStatus
     *
     * @return mixed
     */
    public function save(OrderStatusInterface $orderStatus)
    {
        try {
            $this->resourceModel->save($orderStatus);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                $e->getMessage()
            );
        }
        unset($this->instancesById[$orderStatus->getId()]);
        return $this->getById($orderStatus->getId());
    }

    /**
     * Get OrderStatus
     *
     * @param integer $orderStatusId orderStatusId
     *
     * @return OrderStatusInterface
     */
    public function getById($orderStatusId)
    {
        $orderStatusData = $this->orderStatusFactory->create();
        $orderStatusData->load($orderStatusId);
        $this->instancesById[$orderStatusId] = $orderStatusData;
        return $this->instancesById[$orderStatusId];
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
     * Delete OrderStatus
     *
     * @param OrderStatusInterface $orderStatus orderStatus
     *
     * @return boolean
     */
    public function delete(OrderStatusInterface $orderStatus)
    {
        $orderStatusId = $orderStatus->getId();
        try {
            $this->resourceModel->delete($orderStatus);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\StateException(
                __("Unable to remove orderStatus with id %1", $orderStatusId)
            );
        }
        unset($this->instancesById[$orderStatusId]);
        return true;
    }

    /**
     * Delete orderStatus by id
     *
     * @param integer $orderStatusId orderStatusId
     *
     * @return boolean
     */
    public function deleteById($orderStatusId)
    {
        $orderStatus = $this->getById($orderStatusId);
        return $this->delete($orderStatus);
    }
}
