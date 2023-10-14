<?php

namespace Amasty\Mostviewed\Model\Observer\Backend;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Amasty\Mostviewed\Model\Indexer\ProductProcessor;

class CatalogProductSaveAfterObserver implements ObserverInterface
{
    /**
     * @var ProductProcessor
     */
    private $productProcessor;

    public function __construct(ProductProcessor $productProcessor)
    {
        $this->productProcessor = $productProcessor;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();

        if ($product) {
            $this->productProcessor->reindexRow($product->getId());
        }
    }
}
