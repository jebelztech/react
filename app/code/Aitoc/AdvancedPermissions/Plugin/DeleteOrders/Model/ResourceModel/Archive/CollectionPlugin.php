<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\AdvancedPermissions\Plugin\DeleteOrders\Model\ResourceModel\Archive;

use Aitoc\AdvancedPermissions\Helper\Data as ApiHelper;
use Aitoc\AdvancedPermissions\Helper\Sales as SalesHelper;
use Aitoc\DeleteOrders\Model\ResourceModel\Archive\Collection as ArchiveCollection;
use Magento\Framework\DB\Select;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class CollectionPlugin
 */
class CollectionPlugin
{
    /**
     * @var ApiHelper
     */
    private $apiHelper;

    /**
     * @var SalesHelper
     */
    private $salesHelper;

    /**
     * CollectionPlugin constructor.
     * @param ApiHelper $apiHelper
     * @param SalesHelper $salesHelper
     */
    public function __construct(ApiHelper $apiHelper, SalesHelper $salesHelper)
    {
        $this->apiHelper = $apiHelper;
        $this->salesHelper = $salesHelper;
    }

    /**
     * @param ArchiveCollection $subject
     * @return null
     */
    public function beforeLoad(ArchiveCollection $subject, $printQuery = false, $logQuery = false)
    {
        if (!$this->isAdvancedPermissionEnabled() || $subject->isLoaded()) {
            return null;
        }

        $this->addAllowedProductFilter($subject);

        return null;
    }

    /**
     * @return bool
     */
    private function isAdvancedPermissionEnabled()
    {
        return $this->apiHelper->isAdvancedPermissionEnabled();
    }

    /**
     * @return array
     */
    private function getAllowedStoreIds()
    {
        return $this->apiHelper->getAllowedStoreIds();
    }

    /**
     * @param ArchiveCollection $subject
     * @param Select $countSelect
     * @return Select
     */
    public function afterGetSelectCountSql(ArchiveCollection $subject, Select $countSelect)
    {
        if (!$this->isAdvancedPermissionEnabled()) {
           return $countSelect;
        }

        $allowedStoreIds = $this->getAllowedStoreIds();
        $countSelect->where('order_grid_table.store_id IN (?)', $allowedStoreIds);

        return $countSelect;
    }

    /**
     * @param ArchiveCollection $collection
     * @param Select|null $select
     * @return AbstractCollection
     */
    public function addAllowedProductFilter(ArchiveCollection $collection, Select $select = null)
    {
        if (!$select) {
            $select = $collection->getSelect();
        }

        return $this->salesHelper->addAllowedProductFilterForOrderArchiveCollection($collection, $select);
    }
}
