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

use Webkul\DeliveryBoy\Api\Data\VehicleTypeInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface VehicleTypeRepositoryInterface
{
    /**
     * @param int $id
     * @return VehicleTypeInterface
     */
    public function get($id);

    /**
     * @param int $id
     * @return VehicleTypeInterface
     * @throws \Magento\Framework\Exception\StateException
     */
    public function deleteById($id);

    /**
     * @param VehicleTypeInterface $deliveryboy
     * @return VehicleTypeInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(VehicleTypeInterface $deliveryboy);

    /**
     * @param VehicleTypeInterface $deliveryboy
     * @return bool
     * @throws \Magento\Framework\Exception\StateException
     */
    public function delete(VehicleTypeInterface $deliveryboy);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Webkul\DeliveryBoy\Model\ResourceModel\VehicleType\Collection
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
