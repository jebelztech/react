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

namespace Webkul\WMS\Api\Data;

/**
 * Class OrderInterface api
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
interface OrderInterface
{
    const ID = "id";
    const ORDER_ID = "order_id";
    const STAFF_ID = "staff_id";
    const PRODUCT_ID = "product_id";
    const WAREHOUSE_ID = "warehouse_id";
    const INCREMENT_ID = "increment_id";

    /**
     * Function getId
     *
     * @return integer
     */
    public function getId();

    /**
     * Function setId
     *
     * @param integer $id id
     *
     * @return null
     */
    public function setId($id);

    /**
     * Function getOrderId
     *
     * @return integer
     */
    public function getOrderId();

    /**
     * Function setOrderId
     *
     * @param integer $order_id order_id
     *
     * @return null
     */
    public function setOrderId($order_id);

    /**
     * Function getStaffId
     *
     * @return integer
     */
    public function getStaffId();

    /**
     * Function setStaffId
     *
     * @param integer $staff_id staff_id
     *
     * @return null
     */
    public function setStaffId($staff_id);

    /**
     * Function getProductId
     *
     * @return integer
     */
    public function getProductId();

    /**
     * Function setProductId
     *
     * @param integer $product_id product_id
     *
     * @return null
     */
    public function setProductId($product_id);

    /**
     * Function getWarehouseId
     *
     * @return integer
     */
    public function getWarehouseId();

    /**
     * Function setWarehouseId
     *
     * @param integer $warehouse_id warehouse_id
     *
     * @return null
     */
    public function setWarehouseId($warehouse_id);

    /**
     * Function getIncrementId
     *
     * @return string
     */
    public function getIncrementId();

    /**
     * Function setIncrementId
     *
     * @param string $increment_id increment_id
     *
     * @return null
     */
    public function setIncrementId($increment_id);
}
