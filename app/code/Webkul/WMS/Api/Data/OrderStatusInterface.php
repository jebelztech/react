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
 * Class OrderStatusInterface  api
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
interface OrderStatusInterface
{

    const ID = "id";
    const STATUS = "status";
    const ORDER_ID = "order_id";

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
     * Function getStatus
     *
     * @return integer
     */
    public function getStatus();

    /**
     * Function setStatus
     *
     * @param integer $status status
     *
     * @return null
     */
    public function setStatus($status);

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
}
