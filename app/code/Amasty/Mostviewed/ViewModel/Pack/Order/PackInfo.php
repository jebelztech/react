<?php

declare(strict_types=1);

namespace Amasty\Mostviewed\ViewModel\Pack\Order;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class PackInfo implements ArgumentInterface
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    public function __construct(UrlInterface $urlBuilder)
    {
        $this->urlBuilder = $urlBuilder;
    }

    public function getPackUrl(int $packId): string
    {
        return $this->urlBuilder->getUrl('amasty_mostviewed/pack/edit', ['id' => $packId]);
    }
}
