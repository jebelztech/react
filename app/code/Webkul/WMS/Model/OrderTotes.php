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

use Magento\Framework\Model\AbstractModel;
use Webkul\WMS\Api\Data\OrderTotesInterface;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * Class OrderTotes warehouse
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class OrderTotes extends AbstractModel implements OrderTotesInterface, IdentityInterface
{

    const NOROUTE_ID = "no-route";
    const CACHE_TAG = "wms_order_totes";
    protected $_cacheTag = "wms_order_totes";
    protected $_eventPrefix = "wms_order_totes";

    /**
     * Contructor function
     *
     * @return null
     */
    protected function _construct()
    {
        $this->_init(\Webkul\WMS\Model\ResourceModel\OrderTotes::class);
    }

    /**
     * Function load
     *
     * @param integer $id    id
     * @param string  $field field
     *
     * @return null
     */
    public function load($id, $field = null)
    {
        if ($id === null) {
            return $this->noRouteOrder();
        }
        return parent::load($id, $field);
    }

    /**
     * Function noRouteOrder
     *
     * @return Order
     */
    public function noRouteOrder()
    {
        return $this->load(self::NOROUTE_ID, $this->getIdFieldName());
    }

    /**
     * Function getIdentities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . "_" . $this->getId()];
    }

    /**
     * Function getId
     *
     * @return integer
     */
    public function getId()
    {
        return parent::getData(self::ID);
    }

    /**
     * Function setId
     *
     * @param integer $id id
     *
     * @return boolean
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * Function getOrderId
     *
     * @return string
     */
    public function getOrderId()
    {
        return parent::getData(self::ORDER_ID);
    }

    /**
     * Function setOrderId
     *
     * @param string $order_id order_id
     *
     * @return boolean
     */
    public function setOrderId($order_id)
    {
        return $this->setData(self::ORDER_ID, $order_id);
    }

    /**
     * Function getAssignedToteId
     *
     * @return integer
     */
    public function getAssignedToteId()
    {
        return parent::getData(self::ASSIGNED_TOTE_ID);
    }

    /**
     * Function setAssignedToteId
     *
     * @param integer $assigned_tote_id assigned_tote_id
     *
     * @return null
     */
    public function setAssignedToteId($assigned_tote_id)
    {
        return $this->setData(self::ASSIGNED_TOTE_ID, $assigned_tote_id);
    }

    /**
     * Function getAssignedToteTitle
     *
     * @return string
     */
    public function getAssignedToteTitle()
    {
        return parent::getData(self::ASSIGNED_TOTE_TITLE);
    }

    /**
     * Function setAssignedToteTitle
     *
     * @param string $assigned_tote_title assigned_tote_title
     *
     * @return boolean
     */
    public function setAssignedToteTitle($assigned_tote_title)
    {
        return $this->setData(self::ASSIGNED_TOTE_TITLE, $assigned_tote_title);
    }
}
