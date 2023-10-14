<?php

namespace Amasty\Mostviewed\Model\ResourceModel\Pack\Analytic\Sales\OrderFilters;

use Magento\Sales\Model\ResourceModel\Order\Grid\Collection;

interface OrderFilterInterface
{
    public function execute(Collection $collection, string $value): void;
}
