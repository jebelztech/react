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

class ProductNewObserver implements ObserverInterface
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
     * Observer constructor.
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
            $ids = $this->helper->getAllowedWebsiteIds();
            if (count($ids)) {
                $product->setWebsiteIds([$ids[0]]);
            }
        }
    }
}
