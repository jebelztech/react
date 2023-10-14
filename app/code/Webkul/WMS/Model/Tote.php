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

use Webkul\WMS\Api\Data\ToteInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * Class Tote warehouse
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class Tote extends AbstractModel implements ToteInterface, IdentityInterface
{
    const NOROUTE_ID = "no-route";
    const CACHE_TAG = "wms_tote";
    protected $_cacheTag = "wms_tote";
    protected $_eventPrefix = "wms_tote";

    /**
     * Contructor function
     *
     * @return null
     */
    protected function _construct()
    {
        $this->_init(\Webkul\WMS\Model\ResourceModel\Tote::class);
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
            return $this->noRouteTote();
        }
        return parent::load($id, $field);
    }

    /**
     * Function noRouteTote
     *
     * @return Tote
     */
    public function noRouteTote()
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
     * Function getWarehouseId
     *
     * @return string
     */
    public function getWarehouseId()
    {
        return parent::getData(self::WAREHOUSE_ID);
    }

    /**
     * Function setWarehouseId
     *
     * @param string $warehouse_id warehouse_id
     *
     * @return null
     */
    public function setWarehouseId($warehouse_id)
    {
        return $this->setData(self::WAREHOUSE_ID, $warehouse_id);
    }
}
