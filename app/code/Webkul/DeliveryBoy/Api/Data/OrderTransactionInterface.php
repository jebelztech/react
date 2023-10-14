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

interface OrderTransactionInterface
{
    /**
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const ID = "entity_id";
    const DELIVERYBOY_ORDER_ID = "deliveryboy_order_id";
    const AMOUNT = "amount";
    const TRANSACTION_ID = "transaction_id";
    const IS_CLOSED = "is_closed";
    const CREATED_AT = "created_at";
    const UPDATED_AT = "updated_at";

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
    public function getDeliveryboyOrderId();

    /**
     * @param string $title
     * @return self
     */
    public function setDeliveryboyOrderId($deliveryboyOrderId);

    /**
     * @return string|null
     */
    public function getAmount();

    /**
     * @param string $status
     * @return self
     */
    public function setAmount($amount);

    /**
     * @return float|null
     */
    public function getTransactionId();

    /**
     * @param float $rating
     * @return self
     */
    public function setTransactionId($transactionId);

    /**
     * @return float|null
     */
    public function getIsClosed();

    /**
     * @param float $rating
     * @return self
     */
    public function setIsClosed($isClosed);

    /**
     * @return float|null
     */
    public function getCreatedAt();

    /**
     * @param float $rating
     * @return self
     */
    public function setCreatedAt($createdAt);
    /**
     * @return float|null
     */
    public function getUpdatedAt();

    /**
     * @param float $rating
     * @return self
     */
    public function setUpdatedAt($updatedAt);
}
