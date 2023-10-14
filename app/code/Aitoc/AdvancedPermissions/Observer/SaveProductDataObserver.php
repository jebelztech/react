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
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * @event eav_collection_abstract_load_before
 */
class SaveProductDataObserver implements ObserverInterface
{
    /**
     * @var Data
     */
    protected $helper;

    protected $attrGlobal;

    /**
     * EavCollection constructor.
     *
     * @param Data $helper
     */
    public function __construct(Data $helper)
    {
        $this->helper     = $helper;
        $this->attrGlobal = ['sku', 'price', 'qty', 'is_in_stock'];
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        if ($this->helper->isAdvancedPermissionEnabled()) {
            if (!$this->helper->getRole()->getManageGlobalAttribute()) {
                foreach ($this->attrGlobal as $value) {
                    $product->unsetData($value);
                }
            }
        }

        return;
    }
}
