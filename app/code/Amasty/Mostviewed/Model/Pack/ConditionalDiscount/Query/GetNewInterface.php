<?php

declare(strict_types=1);

namespace Amasty\Mostviewed\Model\Pack\ConditionalDiscount\Query;

use Amasty\Mostviewed\Api\Data\ConditionalDiscountInterface;

interface GetNewInterface
{
    public function execute(): ConditionalDiscountInterface;
}
