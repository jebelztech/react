<?php

declare(strict_types=1);

namespace Amasty\Mostviewed\Model\Backend\Pack\Initialization\ConditionalDiscount;

use Magento\Framework\Exception\LocalizedException;

interface ValidatorInterface
{
    /**
     * Validate all conditional discounts data.
     *
     * @param array $discountsData
     * @return void
     * @throws LocalizedException
     */
    public function validate(array $discountsData): void;
}
