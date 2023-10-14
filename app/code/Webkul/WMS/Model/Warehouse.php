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
use Webkul\WMS\Api\Data\WarehouseInterface;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * Class Warehouse warehouse
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class Warehouse extends AbstractModel implements WarehouseInterface, IdentityInterface
{

    const NOROUTE_ID = "no-route";
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
    const CACHE_TAG = "wms_warehouse";
    protected $_cacheTag = "wms_warehouse";
    protected $_eventPrefix = "wms_warehouse";

    /**
     * Contructor function
     *
     * @return null
     */
    protected function _construct()
    {
        $this->_init(\Webkul\WMS\Model\ResourceModel\Warehouse::class);
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
            return $this->noRouteWarehouse();
        }
        return parent::load($id, $field);
    }

    /**
     * Function noRouteWarehouse
     *
     * @return Warehouse
     */
    public function noRouteWarehouse()
    {
        return $this->load(self::NOROUTE_ID, $this->getIdFieldName());
    }

    /**
     * Function getAvailableStatuses
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [
            self::STATUS_ENABLED  => __("Enabled"),
            self::STATUS_DISABLED => __("Disabled")
        ];
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
     * Function getTitle
     *
     * @return string
     */
    public function getTitle()
    {
        return parent::getData(self::TITLE);
    }

    /**
     * Function setTitle
     *
     * @param string $title title
     *
     * @return boolean
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * Function getStatus
     *
     * @return integer
     */
    public function getStatus()
    {
        return parent::getData(self::STATUS);
    }

    /**
     * Function setStatus
     *
     * @param integer $status status
     *
     * @return boolean
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Function getSource
     *
     * @return string
     */
    public function getSource()
    {
        return parent::getData(self::SOURCE);
    }

    /**
     * Function setSource
     *
     * @param string $source source
     *
     * @return boolean
     */
    public function setSource($source)
    {
        return $this->setData(self::SOURCE, $source);
    }

    /**
     * Function getRowCount
     *
     * @return integer
     */
    public function getRowCount()
    {
        return parent::getData(self::ROW_COUNT);
    }

    /**
     * Function setRowCount
     *
     * @param integer $row_count row_count
     *
     * @return boolean
     */
    public function setRowCount($row_count)
    {
        return $this->setData(self::ROW_COUNT, $row_count);
    }

    /**
     * Function getToteCount
     *
     * @return string
     */
    public function getToteCount()
    {
        return parent::getData(self::TOTE_COUNT);
    }

    /**
     * Function setToteCount
     *
     * @param string $tote_count tote_count
     *
     * @return null
     */
    public function setToteCount($tote_count)
    {
        return $this->setData(self::TOTE_COUNT, $tote_count);
    }

    /**
     * Function getCreatedAt
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return parent::getData(self::CREATED_AT);
    }

    /**
     * Function setCreatedAt
     *
     * @param string $created_at created_at
     *
     * @return boolean
     */
    public function setCreatedAt($created_at)
    {
        return $this->setData(self::CREATED_AT, $created_at);
    }

    /**
     * Function getUpdatedAt
     *
     * @return string
     */
    public function getUpdatedAt()
    {
        return parent::getData(self::UPDATED_AT);
    }

    /**
     * Function setUpdatedAt
     *
     * @param string $updated_at updated_at
     *
     * @return boolean
     */
    public function setUpdatedAt($updated_at)
    {
        return $this->setData(self::UPDATED_AT, $updated_at);
    }

    /**
     * Function getColumnCount
     *
     * @return integer
     */
    public function getColumnCount()
    {
        return parent::getData(self::COLUMN_COUNT);
    }

    /**
     * Function setColumnCount
     *
     * @param integer $column_count column_count
     *
     * @return boolean
     */
    public function setColumnCount($column_count)
    {
        return $this->setData(self::COLUMN_COUNT, $column_count);
    }

    /**
     * Function getRacksPerShelf
     *
     * @return integer
     */
    public function getRacksPerShelf()
    {
        return parent::getData(self::RACKS_PER_SHELF);
    }

    /**
     * Function setRacksPerShelf
     *
     * @param integer $racks_per_shelf racks_per_shelf
     *
     * @return null
     */
    public function setRacksPerShelf($racks_per_shelf)
    {
        return $this->setData(self::RACKS_PER_SHELF, $racks_per_shelf);
    }

    /**
     * Function getShelvesPerCluster
     *
     * @return integer
     */
    public function getShelvesPerCluster()
    {
        return parent::getData(self::SHELVES_PER_CLUSTER);
    }

    /**
     * Function setShelvesPerCluster
     *
     * @param integer $shelves_per_cluster shelves_per_cluster
     *
     * @return null
     */
    public function setShelvesPerCluster($shelves_per_cluster)
    {
        return $this->setData(self::SHELVES_PER_CLUSTER, $shelves_per_cluster);
    }
}
