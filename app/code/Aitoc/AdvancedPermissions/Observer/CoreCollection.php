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
use Aitoc\AdvancedPermissions\Helper\Sales;
use Magento\Customer\Model\ResourceModel\Customer;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\ResourceModel\Order\Creditmemo;
use Magento\Framework\Model\ResourceModel\AbstractResource;

/**
 * @event core_collection_abstract_load_before
 */
class CoreCollection implements ObserverInterface
{
    /**
     * @var Sales
     */
    protected $salesHelper;
    
    /**
     * @var Data
     */
    protected $helper;
    
    /**
     * CoreCollection constructor.
     *
     * @param Sales $salesHelper
     * @param Data $helper
     */
    public function __construct(
        Sales $salesHelper,
        Data $helper
    ) {
        $this->salesHelper = $salesHelper;
        $this->helper      = $helper;
    }

    /**
     * Add additional filters to a collection for restrict store view.
     *
     * @event core_collection_abstract_load_before
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $collection = $observer->getEvent()->getCollection();
        $collectionResource = $collection->getResource();

        if ($collectionResource instanceof Customer) {
            $showAll = $this->helper->getRole()->getShowAllCustomers();

            if (!$showAll) {
                $customerEntityTableName = $collectionResource->getTable('customer_entity');

                $collection->getSelect()
                    ->joinLeft(
                        ['ce_t' => $customerEntityTableName],
                        "ce_t.entity_id = main_table.entity_id",
                        ["store_id" => "ce_t.store_id"]
                    );
            }
        } elseif ($collectionResource instanceof AbstractResource
            && $this->salesHelper->isSalesResource($collectionResource)
            && $this->salesHelper->isAdvancedPermissionEnabled()
        ) {
            $this->salesHelper->addAllowedProductFilterIfRequired($collection);
        } elseif ($collectionResource instanceof Creditmemo) {
            if (!$this->helper->isAdvancedPermissionEnabled()) {
                return $this;
            }

            $allowedWebsites = $this->helper->getAllowedStoreIds();

            if (!count($allowedWebsites)) {
                return $this;
            }

            $collection->addFieldToFilter(
                'store_id',
                ['in' => $allowedWebsites]
            );
        }
    }
}
