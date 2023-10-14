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
interface OrderTotesInterface
{

    const ID = "id";
    const ORDER_ID = "order_id";
    const ASSIGNED_TOTE_ID = "assigned_tote_id";
    const ASSIGNED_TOTE_TITLE = "assigned_tote_title";

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
     * Function getAssignedToteId
     *
     * @return integer
     */
    public function getAssignedToteId();

    /**
     * Function setAssignedToteId
     *
     * @param integer $assigned_tote_id assigned_tote_id
     *
     * @return null
     */
    public function setAssignedToteId($assigned_tote_id);

    /**
     * Function getAssignedToteTitle
     *
     * @return string
     */
    public function getAssignedToteTitle();

    /**
     * Function setAssignedToteTitle
     *
     * @param string $assigned_tote_title assigned_tote_title
     *
     * @return null
     */
    public function setAssignedToteTitle($assigned_tote_title);
}
