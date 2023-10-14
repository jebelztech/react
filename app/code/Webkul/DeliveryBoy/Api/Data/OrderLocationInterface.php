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
namespace Webkul\DeliveryBoy\Api\Data;

interface OrderLocationInterface
{
    /**
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const ID = "entity_id";
    const ORDER_ID = "order_id";
    const LATITUDE = "latitude";
    const LONGITUDE = "longitude";

    /**
     * @return int|null
     */
    public function getId();

    /**
     * @param int $id
     * @return self
     */
    public function setId($id);

    /**
     * @return string|null
     */
    public function getOrderId();

    /**
     * @param string $title
     * @return self
     */
    public function setOrderId($deliveryboyOrdeId);

    /**
     * @return string|null
     */
    public function getLatitude();

    /**
     * @param string $status
     * @return self
     */
    public function setLatitude($latitude);

    /**
     * @return float|null
     */
    public function getLongitude();

    /**
     * @param float $rating
     * @return self
     */
    public function setLongitude($longitude);
}
