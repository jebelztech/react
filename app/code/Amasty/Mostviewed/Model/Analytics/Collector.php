<?php

namespace Amasty\Mostviewed\Model\Analytics;

use Amasty\Mostviewed\Model\Analytics\Collector\CollectorInterface;

class Collector
{
    /**
     * @var CollectorInterface[]
     */
    private $collectors;

    public function __construct(
        array $collectors = []
    ) {
        $this->collectors = $collectors;
    }

    /**
     * Collect analytics action data
     */
    public function execute(): void
    {
        foreach ($this->collectors as $collector) {
            if ($collector instanceof CollectorInterface) {
                $collector->execute();
            }
        }
    }
}
