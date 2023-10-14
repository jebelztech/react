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
use Webkul\WMS\Api\Data\ProductLocationInterface;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * Class ProductLocation warehouse
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class ProductLocation extends AbstractModel implements ProductLocationInterface, IdentityInterface
{
    const NOROUTE_ID = "no-route";
    const CACHE_TAG = "wms_product_location";
    protected $_cacheTag = "wms_product_location";
    protected $_eventPrefix = "wms_product_location";

    /**
     * Contructor function
     *
     * @return null
     */
    protected function _construct()
    {
        $this->_init(\Webkul\WMS\Model\ResourceModel\ProductLocation::class);
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
            return $this->noRouteProductLocation();
        }
        return parent::load($id, $field);
    }

    /**
     * Function noRouteProductLocation
     *
     * @return ProductLocation
     */
    public function noRouteProductLocation()
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
     * Function getRow
     *
     * @return integer
     */
    public function getRow()
    {
        return parent::getData(self::ROW);
    }

    /**
     * Function setRow
     *
     * @param integer $row row
     *
     * @return null
     */
    public function setRow($row)
    {
        return $this->setData(self::ROW, $row);
    }

    /**
     * Function getRack
     *
     * @return integer
     */
    public function getRack()
    {
        return parent::getData(self::RACK);
    }

    /**
     * Function setRack
     *
     * @param integer $rack rack
     *
     * @return null
     */
    public function setRack($rack)
    {
        return $this->setData(self::RACK, $rack);
    }

    /**
     * Function getShelf
     *
     * @return string
     */
    public function getShelf()
    {
        return parent::getData(self::SHELF);
    }

    /**
     * Function setShelf
     *
     * @param string $shelf shelf
     *
     * @return null
     */
    public function setShelf($shelf)
    {
        return $this->setData(self::SHELF, $shelf);
    }

    /**
     * Function getColumn
     *
     * @return integer
     */
    public function getColumn()
    {
        return parent::getData(self::COLUMN);
    }

    /**
     * Function setColumn
     *
     * @param integer $column column
     *
     * @return null
     */
    public function setColumn($column)
    {
        return $this->setData(self::COLUMN, $column);
    }

    /**
     * Function getIncrementId
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
}
