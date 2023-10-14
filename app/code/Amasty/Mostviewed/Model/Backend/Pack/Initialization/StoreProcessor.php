<?php

declare(strict_types=1);

namespace Amasty\Mostviewed\Model\Backend\Pack\Initialization;

use Amasty\Mostviewed\Api\Data\PackInterface;

class StoreProcessor implements ProcessorInterface
{
    public function execute(PackInterface $pack, array $inputPackData): void
    {
        $pack->getExtensionAttributes()->setStores($inputPackData['stores'] ?? []);
    }
}
