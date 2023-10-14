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

use Webkul\WMS\Api\Data\OrderInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Class OrderRepositoryInterface api
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
interface OrderRepositoryInterface
{
    /**
     * Function getById
     *
     * @param integer $id id
     *
     * @return mixed
     */
    public function getById($id);

    /**
     * Function deleteById
     *
     * @param integer $id id
     *
     * @return null
     */
    public function deleteById($id);

    /**
     * Function save
     *
     * @param OrderInterface $order order
     *
     * @return null
     */
    public function save(OrderInterface $order);

    /**
     * Function delete
     *
     * @param OrderInterface $order order
     *
     * @return null
     */
    public function delete(OrderInterface $order);

    /**
     * Function getList
     *
     * @param SearchCriteriaInterface $searchCriteria searchCriteria
     *
     * @return null
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
