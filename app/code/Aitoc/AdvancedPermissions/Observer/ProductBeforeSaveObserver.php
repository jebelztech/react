<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Observer;

use Aitoc\AdvancedPermissions\Helper\Data;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class ProductBeforeSaveObserver implements ObserverInterface
{
    /**
     * @var Data
     */
    protected $helper;
    
    /**
     * @var ProductFactory
     */
    protected $productloader;

    /**
     * InventoryObserver constructor.
     *
     * @param Data $helper
     * @param ProductFactory $productloader
     */
    public function __construct(
        Data $helper,
        ProductFactory $productloader
    ) {
        $this->helper = $helper;
        $this->productloader = $productloader;
    }

    /**
     * @param EventObserver $observer
     *
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        if ($this->helper->isAdvancedPermissionEnabled()) {
            $product = $observer->getEvent()->getProduct();
            $entity  = $this->productloader->create()->load($product->getId());
            // fix: append non-manageable websites while sub-admin save product
            $disallowed = array_diff($entity->getWebsiteIds(), $this->helper->getAllowedWebsiteIds());
            $product->setWebsiteIds(array_unique(array_merge($disallowed, $product->getWebsiteIds())));
        }
        return $this;
    }
}
