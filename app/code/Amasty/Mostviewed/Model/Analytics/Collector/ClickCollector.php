<?php

declare(strict_types=1);

namespace Amasty\Mostviewed\Model\Analytics\Collector;

use Amasty\Mostviewed\Model\Analytics\Collector\Click\Block;
use Amasty\Mostviewed\Model\Analytics\Collector\Click\Cart;

class ClickCollector implements CollectorInterface
{
    /**
     * @var Block
     */
    private $blockCollector;

    /**
     * @var Cart
     */
    private $cartCollector;

    public function __construct(
        Block $blockCollector,
        Cart $cartCollector
    ) {
        $this->blockCollector = $blockCollector;
        $this->cartCollector = $cartCollector;
    }

    /**
     * Collect click analytics actions
     */
    public function execute(): void
    {
        $this->blockCollector->execute();
        $this->cartCollector->execute();
    }
}
