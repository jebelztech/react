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

namespace Webkul\WMS\Api;

use Webkul\WMS\Api\Data\WarehouseInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Class WarehouseRepositoryInterface api
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
interface WarehouseRepositoryInterface
{
    /**
     * Function getById
     *
     * @param integer $warehouseId warehouseId
     *
     * @return mixed
     */
    public function getById($warehouseId);

    /**
     * Function deleteById
     *
     * @param integer $warehouseId warehouseId
     *
     * @return null
     */
    public function deleteById($warehouseId);

    /**
     * Function save
     *
     * @param WarehouseInterface $warehouse warehouse
     *
     * @return null
     */
    public function save(WarehouseInterface $warehouse);

    /**
     * Function delete
     *
     * @param WarehouseInterface $warehouse warehouse
     *
     * @return null
     */
    public function delete(WarehouseInterface $warehouse);

    /**
     * Function getList
     *
     * @param SearchCriteriaInterface $searchCriteria searchCriteria
     *
     * @return null
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
