<?php
/**
 * Webkul Software.
 *
 *
 * @category  Webkul
 * @package   Webkul_DeliveryBoy
 * @author    Webkul <support@webkul.com>
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html ASL Licence
 * @link      https://store.webkul.com/license.html
 */
namespace Webkul\DeliveryBoy\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Webkul\DeliveryBoy\Api\Data\VehicleTypeInterface;

class VehicleType extends AbstractModel implements VehicleTypeInterface, IdentityInterface
{
     /**
     * Tag to associate cache entries with
     *
     * @var string
     */
    const CACHE_TAG = "expressdelivery_vehicle_type";
    
    /**
     * Default Id for when id field value is null
     */
    const NOROUTE_ID = "no-route";

    /**
     * Tag to associate cache entries with
     *
     * @var string
     */
    protected $_cacheTag = "expressdelivery_vehicle_type";
    
    /**
     * Model event prefix
     *
     * @var string
     */
    protected $_eventPrefix = "expressdelivery_vehicle_type";

    /**
     * Initialize Model object
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Webkul\DeliveryBoy\Model\ResourceModel\VehicleType::class);
    }

    /**
     * @param int|null $id
     * @param int|null $field
     * @return self
     */
    public function load($id, $field = null)
    {
        if ($id === null) {
            return $this->noRouteOrder();
        }
        return parent::load($id, $field);
    }

    /**
     * Load object with noroute id data
     *
     * @return self
     */
    public function noRouteOrder()
    {
        return $this->load(self::NOROUTE_ID, $this->getIdFieldName());
    }

    /**
     * Return array of name of object in cache
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . "_" . $this->getId()];
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return parent::getData(self::ENTITY_ID);
    }

    /**
     * @param int $id
     * @return self
     */
    public function setId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }
    
    /**
     * @return string|null
     */
    public function getValue()
    {
        return parent::getData(self::VALUE);
    }

    /**
     * @param string $title
     * @return self
     */
    public function setValue($deliveryboyValue)
    {
        parent::setData(self::VALUE, $deliveryboyValue);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLabel()
    {
        return parent::getData(self::LABEL);
    }

    /**
     * @param string $status
     * @return self
     */
    public function setLabel($Label)
    {
        parent::setData(self::LABEL, $Label);
        return $this;
    }
}
