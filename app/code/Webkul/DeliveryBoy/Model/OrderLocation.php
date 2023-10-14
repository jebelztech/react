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
use Webkul\DeliveryBoy\Api\Data\OrderLocationInterface;

class OrderLocation extends AbstractModel implements OrderLocationInterface, IdentityInterface
{
     /**
     * Tag to associate cache entries with
     *
     * @var string
     */
    const CACHE_TAG = "expressdelivery_order_location";
    
    /**
     * Default Id for when id field value is null
     */
    const NOROUTE_ID = "no-route";

    /**
     * Tag to associate cache entries with
     *
     * @var string
     */
    protected $_cacheTag = "expressdelivery_order_location";
    
    /**
     * Model event prefix
     *
     * @var string
     */
    protected $_eventPrefix = "expressdelivery_order_location";

    /**
     * Initialize Model object
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Webkul\DeliveryBoy\Model\ResourceModel\OrderLocation::class);
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
        return parent::getData(self::ID);
    }

    /**
     * @param int $id
     * @return self
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }
    
    /**
     * @return string|null
     */
    public function getOrderId()
    {
        return parent::getData(self::ORDER_ID);
    }

    /**
     * @param string $title
     * @return self
     */
    public function setOrderId($deliveryboyOrderId)
    {
        parent::setData(self::ORDER_ID, $deliveryboyOrderId);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLatitude()
    {
        return parent::getData(self::LATITUDE);
    }

    /**
     * @param string $status
     * @return self
     */
    public function setLatitude($latitude)
    {
        parent::setData(self::LATITUDE, $latitude);
        return $this;
    }

    /**
     * @return float|null
     */
    public function getLongitude()
    {
        return parent::getData(self::LONGITUDE);
    }

    /**
     * @param float $rating
     * @return self
     */
    public function setLongitude($longitude)
    {
        parent::setData(self::LONGITUDE, $longitude);
        return $this;
    }
}
