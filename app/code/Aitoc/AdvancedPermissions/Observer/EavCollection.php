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
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * @event eav_collection_abstract_load_before
 */
class EavCollection implements ObserverInterface
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * EavCollection constructor.
     *
     * @param Data $helper
     */
    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * Add additional filters to a collection for restrict store view.
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $collection = $observer->getEvent()->getCollection();

        if (!$collection instanceof ProductCollection) {
            return;
        }

        if (!$this->helper->isAdvancedPermissionEnabled()) {
            return;
        }

        $allowedWebsites = $this->helper->getAllowedWebsiteIds();

        if (!count($allowedWebsites)) {
            return;
        }

        /**
         * Filtering Catalog Product Collection by allowed website ids
         */
        $limitationFilters = $collection->getLimitationFilters();

        if (isset($limitationFilters['store_id'])) {
            return;
        }

        if (isset($limitationFilters['website_ids'])) {
            return;
        }

        $collection->addWebsiteFilter($allowedWebsites);

        return;
    }
}
