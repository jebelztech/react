<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Block\Dashboard\Products;

use Aitoc\AdvancedPermissions\Helper\Data as ApHelper;
use Magento\Backend\Block\Dashboard\Grid;
use Magento\Backend\Block\Dashboard\Tab\Products\Ordered as BaseOrdered;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data as BackendHelper;
use Magento\Framework\Module\Manager;
use Magento\Sales\Model\ResourceModel\Report\Bestsellers\Collection;
use Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory;
use Aitoc\AdvancedPermissions\Helper\Dashboard\Customers as DashboardGridHelper;

/**
 * Class Ordered
 */
class Ordered extends BaseOrdered
{
    protected $_template = "Magento_Backend::dashboard/grid.phtml";

    /**
     * @var ApHelper
     */
    protected $helper;

    /**
     * @var DashboardGridHelper
     */
    protected $dashboardGridHelper;

    /**
     * Ordered constructor.
     *
     * @param Context $context
     * @param BackendHelper $backendHelper
     * @param Manager $moduleManager
     * @param CollectionFactory $collectionFactory
     * @param ApHelper $helper
     * @param DashboardGridHelper $dashboardGridHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        BackendHelper $backendHelper,
        Manager $moduleManager,
        CollectionFactory $collectionFactory,
        ApHelper $helper,
        DashboardGridHelper $dashboardGridHelper,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $backendHelper,
            $moduleManager,
            $collectionFactory,
            $data
        );

        $this->helper = $helper;
        $this->dashboardGridHelper = $dashboardGridHelper;
    }

    /**
     * Change collection with condition
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        if (!$this->isAdvancedPermissionsEnabled()) {
            return parent::_prepareCollection();
        }

        if (!$this->isMagentoSalesModuleEnabled()) {
            return $this;
        }

        $storeIds = $this->getStoreIds();

        $this->createAndSetProductCollection($storeIds);

        return Grid::_prepareCollection();
    }

    /**
     * @return bool
     */
    private function isAdvancedPermissionsEnabled()
    {
        return $this->helper->isAdvancedPermissionEnabled();
    }

    /**
     * @return bool
     */
    private function isMagentoSalesModuleEnabled()
    {
        return $this->_moduleManager->isEnabled('Magento_Sales');
    }

    /**
     * @return int[]
     */
    private function getStoreIds()
    {
        return $this->dashboardGridHelper->getStoreIds($this);
    }

    /**
     * @return int
     */
    private function getStoreIdInherited()
    {
        if ($websiteId = $this->getParam('website')) {
            $storeId = $this->getWebsiteStoreId($websiteId);
        } elseif ($groupId = $this->getParam('group')) {
            $storeId = $this->getGroupStoreId($groupId);
        } else {
            $storeId = (int)$this->getParam('store');
        }

        return $storeId;
    }

    /**
     * @param $websiteId
     * @return int
     */
    private function getWebsiteStoreId($websiteId)
    {
        $storeIds = $this->_storeManager->getWebsite($websiteId)->getStoreIds();
        return array_pop($storeIds);
    }

    /**
     * @param int $groupId
     * @return int
     */
    private function getGroupStoreId($groupId)
    {
        $storeIds = $this->_storeManager->getGroup($groupId)->getStoreIds();
        return array_pop($storeIds);
    }

    /**
     * @param int $inheritedStoreId
     * @return int null|null
     */
    private function fixStoreIdByAllowed($inheritedStoreId)
    {
        $allowedStoreIds = $this->getAllowedStoreViewIds();

        //q: maybe better return first allowed store?
        return in_array($inheritedStoreId, $allowedStoreIds) ? $inheritedStoreId : null;
    }

    /**
     * @return int[]
     */
    private function getAllowedStoreViewIds()
    {
        return $this->helper->getAllowedStoreViewIds();
    }

    /**
     * @param int[] $storeIds
     */
    private function createAndSetProductCollection($storeIds)
    {
        $collection = $this->createProductCollection($storeIds);
        $this->setCollection($collection);
    }

    /**
     * @param int[] $storeIds
     * @return Collection
     */
    private function createProductCollection($storeIds)
    {
        return $this->_collectionFactory
            ->create()
            ->setModel('Magento\Catalog\Model\Product')
            ->addStoreFilter($storeIds);
    }
}
