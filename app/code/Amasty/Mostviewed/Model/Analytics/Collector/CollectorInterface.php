<?php

declare(strict_types=1);

namespace Amasty\Mostviewed\Model\Analytics\Collector;

interface CollectorInterface
{
    /**
     * Collect analytics action data
     */
    public function execute(): void;
}
