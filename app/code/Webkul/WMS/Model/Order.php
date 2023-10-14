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
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * Class Order warehouse
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class Order extends AbstractModel implements OrderInterface, IdentityInterface
{
    const NOROUTE_ID = "no-route";
    const CACHE_TAG = "wms_order";
    protected $_cacheTag = "wms_order";
    protected $_eventPrefix = "wms_order";

    /**
     * Contructor function
     *
     * @return null
     */
    protected function _construct()
    {
        $this->_init(\Webkul\WMS\Model\ResourceModel\Order::class);
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
     * Function getStaffId
     *
     * @return integer
     */
    public function getStaffId()
    {
        return parent::getData(self::STAFF_ID);
    }

    /**
     * Function setStaffId
     *
     * @param integer $staff_id staff_id
     *
     * @return boolean
     */
    public function setStaffId($staff_id)
    {
        return $this->setData(self::STAFF_ID, $staff_id);
    }

    /**
     * Function getProductId
     *
     * @return integer
     */
    public function getProductId()
    {
        return parent::getData(self::PRODUCT_ID);
    }

    /**
     * Function setProductId
     *
     * @param integer $product_id product_id
     *
     * @return null
     */
    public function setProductId($product_id)
    {
        return $this->setData(self::PRODUCT_ID, $product_id);
    }

    /**
     * Function getWarehouseId
     *
     * @return integer
     */
    public function getWarehouseId()
    {
        return parent::getData(self::WAREHOUSE_ID);
    }

    /**
     * Function setWarehouseId
     *
     * @param integer $warehouse_id warehouse_id
     *
     * @return null
     */
    public function setWarehouseId($warehouse_id)
    {
        return $this->setData(self::WAREHOUSE_ID, $warehouse_id);
    }

    /**
     * Function getIncrementId
     *
     * @return string
     */
    public function getIncrementId()
    {
        return parent::getData(self::INCREMENT_ID);
    }

    /**
     * Function setIncrementId
     *
     * @param string $increment_id increment_id
     *
     * @return boolean
     */
    public function setIncrementId($increment_id)
    {
        return $this->setData(self::INCREMENT_ID, $increment_id);
    }
}
