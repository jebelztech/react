<?php

declare(strict_types=1);

namespace Amasty\Mostviewed\Model\Backend\Pack\Initialization\ConditionalDiscount;

use Magento\Framework\Exception\LocalizedException;

class GreaterThanOne implements ColumnValidatorInterface
{
    public function validate(string $columnName, ?string $value): void
    {
        $value = (int) $value;
        if ($value < 2) {
            throw new LocalizedException(__('Please set "%1" higher than 1', $columnName));
        }
    }
}
