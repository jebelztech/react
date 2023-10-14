<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/

declare(strict_types=1);

namespace Amasty\CheckoutCore\Observer\CustomerAttribute;

use Amasty\CheckoutCore\Model\Field\Form\GetMaxSortOrder;
use Amasty\CheckoutCore\Model\Field\Form\Processor\CustomerAttributes;
use Magento\Customer\Model\Attribute;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;

class UpdateSortOrder implements ObserverInterface
{
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
     * Event: amasty_customer_attributes_before_save
     *
     * @param Observer $observer
     * @return void
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        /** @var Attribute $attribute */
        $attribute = $observer->getEvent()->getData('object');

        if ($this->canSetSortOrder($attribute)) {
            $sortOrder = $this->getMaxSortOrder->execute() + self::SORT_ORDER_STEP;
            $attribute->setData('sorting_order', $sortOrder);
            $attribute->setData('sort_order', $sortOrder + CustomerAttributes::SORT_ORDER_OFFSET);
        }
    }

    private function canSetSortOrder(Attribute $attribute): bool
    {
        $isEnabled = (bool) $attribute->getData('used_in_product_listing');

        if ($attribute->isObjectNew()
            && (int) $attribute->getData('sorting_order') === 0
            && $isEnabled
        ) {
            return true;
        }

        return (int) $attribute->getOrigData('used_in_product_listing') === 0 && $isEnabled;
    }
}
