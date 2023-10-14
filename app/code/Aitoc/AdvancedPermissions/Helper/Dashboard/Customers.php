<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\AdvancedPermissions\Helper\Dashboard;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Api\Data\WebsiteInterface;
use Magento\Store\Api\Data\GroupInterface;
use Aitoc\AdvancedPermissions\Helper\Data as Helper;
use Magento\Backend\Block\Dashboard\Grid;
use Magento\Reports\Model\ResourceModel\Order\Collection as OrderCollection;
use Magento\Reports\Model\ResourceModel\Customer\Collection as CustomerCollection;

/**
 * Class Customers
 */
class Customers
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Helper
     */
    private $helper;

    /**
     * Customers constructor.
     * @param StoreManagerInterface $storeManager
     * @param Helper $helper
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        Helper $helper
    ) {
        $this->storeManager = $storeManager;
        $this->helper = $helper;
    }

    /**
     * @param Grid $grid
     * @param OrderCollection|CustomerCollection $collection
     */
    public function setStoreIdsFilter(Grid $grid, $collection)
    {
        $storeIds = $this->getStoreIds($grid);
        $collection->addAttributeToFilter('store_id', ['in' => $storeIds]);
    }


    /**
     * @param Grid $grid
     * @return int[]
     */
    public function getStoreIds(Grid $grid)
    {
        $storeId = $grid->getParam('store');
        $websiteId = $grid->getParam('website');
        $groupId = $grid->getParam('group');

        $inheritedStoreId = $this->getStoreIdsInherited($storeId, $websiteId, $groupId);

        return $this->filterStoreIdsByRestrictions($inheritedStoreId);
    }

    /**
     * @param int $storeId
     * @param int $websiteId
     * @param int $groupId
     * @return int[]
     */
    private function getStoreIdsInherited($storeId, $websiteId, $groupId)
    {
        $storeIds = [];

        if ($storeId) {
            $storeIds = [$storeId];
        } elseif($websiteId) {
            $storeIds = $this->getWebsiteStoreIds($websiteId);
        } elseif ($groupId) {
            $storeIds = $this->getGroupStoreIds($groupId);
        }

        return $storeIds;
    }

    /**
     * @param int $websiteId
     * @return int[]
     */
    private function getWebsiteStoreIds($websiteId)
    {
        $website = $this->getWebsiteById($websiteId);

        return $website->getStoreIds();
    }

    /**
     * @param int $websiteId
     * @return WebsiteInterface
     */
    private function getWebsiteById($websiteId)
    {
        return $this->storeManager->getWebsite($websiteId);
    }

    /**
     * @param int $groupId
     * @return GroupInterface
     */
    private function getGroupStoreIds($groupId)
    {
        $group = $this->getGroupById($groupId);

        return $group->getStoreIds();
    }

    /**
     * @param int $groupId
     * @return GroupInterface
     */
    private function getGroupById($groupId)
    {
        return $this->storeManager->getGroup($groupId);
    }

    /**
     * @param int[] $inheritedStoreId
     * @return int[]
     */
    private function filterStoreIdsByRestrictions($inheritedStoreId)
    {
        $allowedStoreIds = $this->getAllowedStoreIds();

        if (!$inheritedStoreId) {
            return $allowedStoreIds;
        }

        $intersectedStoreIds = array_intersect($inheritedStoreId, $allowedStoreIds);

        return $intersectedStoreIds
            ? $intersectedStoreIds
            : $allowedStoreIds;
    }

    /**
     * @return int[]
     */
    private function getAllowedStoreIds()
    {
        return $this->helper->getAllowedStoreIds();
    }

    /**
     * @param Grid $grid
     * @return int
     */
    public function getStoreFilter(Grid $grid)
    {
        return $grid->getParam('store') ? 1 : 0;
    }

}