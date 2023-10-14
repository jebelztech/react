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
use Magento\CatalogInventory\Api\StockIndexInterface;
use Magento\CatalogInventory\Observer\SaveInventoryDataObserver;
use Magento\Framework\Event\ObserverInterface;

/**
 * @event eav_collection_abstract_load_before
 */
class InventoryObserver implements ObserverInterface
{
    /**
     * @var Data
     */
    protected $helper;
    
    /**
     * @var StockIndexInterface
     */
    protected $stockIndex;
    
    /**
     * @var SaveInventoryDataObserver
     */
    protected $saveInventory;

    /**
     * InventoryObserver constructor.
     *
     * @param StockIndexInterface $stockIndex
     * @param Data $helper
     * @param SaveInventoryDataObserver $saveInventory
     */
    public function __construct(
        StockIndexInterface $stockIndex,
        Data $helper,
        SaveInventoryDataObserver $saveInventory
    ) {
        $this->stockIndex = $stockIndex;
        $this->saveInventory = $saveInventory;
        $this->helper = $helper;
    }

    /**
     * @param EventObserver $observer
     *
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();

        if ($product->getStockData() === null) {
            if ($product->getIsChangedWebsites() || $product->dataHasChangedFor('status')) {
                $this->stockIndex->rebuild(
                    $product->getId(),
                    $product->getStore()->getWebsiteId()
                );
            }
        }
        
        if ($this->helper->isAdvancedPermissionEnabled() && !$this->helper->getRole()->getManageGlobalAttribute()) {
            return $this;
        }
        
        $this->saveInventory->execute($observer);
        return $this;
    }
}
