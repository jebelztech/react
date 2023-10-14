<?php

declare(strict_types=1);

namespace Amasty\Mostviewed\Model\Pack\ConditionalDiscount\Command;

use Magento\Framework\Exception\CouldNotDeleteException;

interface RemoveByPackIdInterface
{
    /**
     * @param int $packId
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function execute(int $packId): bool;
}
