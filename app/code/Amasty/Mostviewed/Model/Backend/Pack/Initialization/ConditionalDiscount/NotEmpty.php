<?php

declare(strict_types=1);

namespace Amasty\Mostviewed\Model\Backend\Pack\Initialization\ConditionalDiscount;

use Magento\Framework\Exception\LocalizedException;

class NotEmpty implements ColumnValidatorInterface
{
    /**
     * @param string $columnName
     * @param string|null $value
     * @return void
     * @throws LocalizedException
     */
    public function validate(string $columnName, ?string $value): void
    {
        if ($value === null) {
            throw new LocalizedException(__(
                'The "%1" value doesn\'t exist. Enter the value and try again.',
                $columnName
            ));
        }
    }
}
