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

use Webkul\WMS\Api\Data\OrderInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Class OrderRepository warehouse
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class OrderRepository implements \Webkul\WMS\Api\OrderRepositoryInterface
{
    /**
     * OrderFactory
     *
     * @var OrderFactory
     */
    protected $orderFactory;

    /**
     * Order
     *
     * @var ResourceModel\Order
     */
    protected $resourceModel;

    /**
     * CollectionFactory
     *
     * @var ResourceModel\Order\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Array
     *
     * @var array
     */
    protected $instancesById = [];

    /**
     * Contructor of class OrderRepository
     *
     * @param OrderFactory                          $orderFactory      Order
     * @param ResourceModel\Order                   $resourceModel     Ordermodel
     * @param ResourceModel\Order\CollectionFactory $collectionFactory OrderCollection
     */
    public function __construct(
        OrderFactory $orderFactory,
        ResourceModel\Order $resourceModel,
        ResourceModel\Order\CollectionFactory $collectionFactory
    ) {
        $this->orderFactory = $orderFactory;
        $this->resourceModel = $resourceModel;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Save Order
     *
     * @param OrderInterface $order Order
     *
     * @return mixed
     */
    public function save(OrderInterface $order)
    {
        try {
            $this->resourceModel->save($order);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                $e->getMessage()
            );
        }
        unset($this->instancesById[$order->getId()]);
        return $this->getById($order->getId());
    }

    /**
     * Get Order
     *
     * @param integer $orderId orderId
     *
     * @return OrderInterface
     */
    public function getById($orderId)
    {
        $orderData = $this->orderFactory->create();
        $orderData->load($orderId);
        $this->instancesById[$orderId] = $orderData;
        return $this->instancesById[$orderId];
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
     * Delete Order
     *
     * @param OrderInterface $order order
     *
     * @return boolean
     */
    public function delete(OrderInterface $order)
    {
        $orderId = $order->getId();
        try {
            $this->resourceModel->delete($order);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\StateException(
                __("Unable to remove order with id %1", $orderId)
            );
        }
        unset($this->instancesById[$orderId]);
        return true;
    }

    /**
     * Delete order by id
     *
     * @param integer $orderId orderId
     *
     * @return boolean
     */
    public function deleteById($orderId)
    {
        $order = $this->getById($orderId);
        return $this->delete($order);
    }
}
