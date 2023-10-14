<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/

declare(strict_types=1);

namespace Amasty\CheckoutCore\Observer\OrderAttribute;

use Amasty\CheckoutCore\Model\Field\Form\GetMaxSortOrder;
use Amasty\Orderattr\Model\Attribute\Attribute;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;

class UpdateSortOrder implements ObserverInterface
{
    public const FLAG_NO_UPDATE = 'no_update';
    public const SORT_ORDER_STEP = 10;

    /**
     * @var GetMaxSortOrder
     */
    private $getMaxSortOrder;

    public function __construct(GetMaxSortOrder $getMaxSortOrder)
    {
        $this->getMaxSortOrder = $getMaxSortOrder;
    }

    /**
     * Event: amasty_orderattr_entity_attribute_save_before
     *
     * @param Observer $observer
     * @return void
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        /** @var Attribute $attribute */
        $attribute = $observer->getEvent()->getData('attribute');

        if ($this->canSetSortOrder($attribute)) {
            $attribute->setSortingOrder($this->getMaxSortOrder->execute() + self::SORT_ORDER_STEP);
        }
    }

    private function canSetSortOrder(Attribute $attribute): bool
    {
        if ($attribute->hasData(self::FLAG_NO_UPDATE)) {
            return false;
        }

        $isEnabled = (bool) $attribute->getIsVisibleOnFront();

        if ($attribute->isObjectNew()
            && (int) $attribute->getSortingOrder() === 0
            && $isEnabled
        ) {
            return true;
        }

        return (int) $attribute->getOrigData('is_visible_on_front') === 0 && $isEnabled;
    }
}
