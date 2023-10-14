<?php
/**
 * Webkul Software.
 *
 *
 * @category  Webkul
 * @package   Webkul_DeliveryBoy
 * @author    Webkul <support@webkul.com>
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html ASL Licence
 * @link      https://store.webkul.com/license.html
 */
namespace Webkul\DeliveryBoy\Api;

use Webkul\DeliveryBoy\Api\Data\OrderTransactionInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface OrderTransactionRepositoryInterface
{
    /**
     * @param int $id
     * @return OrderTransactionInterface
     */
    public function get($id);

    /**
     * @param int $id
     * @return OrderTransactionInterface
     * @throws \Magento\Framework\Exception\StateException
     */
    public function deleteById($id);

    /**
     * @param OrderTransactionInterface $deliveryboy
     * @return OrderTransactionInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(OrderTransactionInterface $deliveryboy);

    /**
     * @param OrderTransactionInterface $deliveryboy
     * @return bool
     * @throws \Magento\Framework\Exception\StateException
     */
    public function delete(OrderTransactionInterface $deliveryboy);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Webkul\DeliveryBoy\Model\ResourceModel\OrderTransaction\Collection
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
