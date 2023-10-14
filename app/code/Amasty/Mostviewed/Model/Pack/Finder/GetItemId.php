<?php

declare(strict_types=1);

namespace Amasty\Mostviewed\Model\Pack\Finder;

class GetItemId
{
    /**
     * @var int
     */
    private $lastId = -100;

    public function execute(): int
    {
        return ++$this->lastId;
    }
}
