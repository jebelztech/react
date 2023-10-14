<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutDeliveryDate
*/

declare(strict_types=1);

namespace Amasty\CheckoutDeliveryDate\Api\Data;

interface DeliveryInterface
{
    const ENTITY_ID = 'id';
    const ORDER_ID = 'order_id';
    const QUOTE_ID = 'quote_id';
    const DATE = 'date';
    const TIME = 'time';
    const COMMENT = 'comment';

    /**
     * @return string|int|null
     */
    public function getId();

    /**
     * @return int|null
     */
    public function getOrderId(): ?int;

    /**
     * @param int $id
     * @return void
     */
    public function setOrderId(int $id): void;

    /**
     * @return int|null
     */
    public function getQuoteId(): ?int;

    /**
     * @param int $id
     * @return void
     */
    public function setQuoteId(int $id): void;

    /**
     * @return string|int|null
     */
    public function getDate();

    /**
     * @param string|int|null $date
     * @return void
     */
    public function setDate($date): void;

    /**
     * @return string|int|null
     */
    public function getTime();

    /**
     * @param string|int|null $time
     * @return void
     */
    public function setTime($time): void;

    /**
     * @return string|null
     */
    public function getComment(): ?string;

    /**
     * @param string $comment
     * @return void
     */
    public function setComment(string $comment): void;
}
