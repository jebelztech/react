<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Plugin\Block;

use Aitoc\AdvancedPermissions\Helper\Data;
use Closure;
use Magento\Backend\Block\Store\Switcher as MagentoSwitcher;
use Magento\Catalog\Model\ResourceModel\Product;
use Magento\Store\Model\ResourceModel\Store\Collection;
use Magento\Store\Model\WebsiteFactory;
use Magento\Sales\Block\Adminhtml\Order\Create\Store\Select as OrderStoreSelect;

class Switcher
{

    /**
     * @var Data
     */
    private $helper;

    /**
     * @var WebsiteFactory
     */
    protected $websiteFactory;

    /**
     * Switcher constructor.
     *
     * @param Data $helper
     * @param WebsiteFactory $websiteFactory
     */
    public function __construct(
        Data $helper,
        WebsiteFactory $websiteFactory
    ) {
        $this->helper         = $helper;
        $this->websiteFactory = $websiteFactory;
    }


    /**
     * @param Product $object
     * @param Closure $work
     * @param $product
     *
     * @return mixed
     */
    public function aroundGetWebsiteCollection(MagentoSwitcher $object, Closure $work)
    {
        $collection = $this->websiteFactory->create()->getResourceCollection();

        $websiteIds = $this->helper->getAllowedWebsiteIds();

        if ($websiteIds !== null) {
            $collection->addIdFilter($websiteIds);
        }

        return $collection->load();
    }

    /**
     * @param $object
     * @param $stores
     *
     * @return mixed
     */
    public function afterGetStores($object, $stores)
    {
        if ($storeIds = $this->helper->getAllowedStoreViewIds()) {
            foreach (array_keys($stores) as $storeId) {
                if (!in_array($storeId, $storeIds)) {
                    unset($stores[$storeId]);
                }
            }
        }

        return $stores;
    }

    /**
     * @param $object
     * @param $result
     * @return string
     */
    public function afterGetTemplate($object, $result)
    {

        if ((!($object instanceof OrderStoreSelect))
            && $this->helper->isAdvancedPermissionEnabled()
            && $this->helper->isAdvancedPermissionAllowed()
        ) {
            return 'Aitoc_AdvancedPermissions::store/switcher.phtml';
        }

        return $result;
    }

    /**
     * @param $object
     * @param $result
     * @return bool
     */
    public function afterHasDefaultOption($object, $result)
    {
        if ($this->helper->isHideAllStoreViews()) {
            return false;
        }

        return $result;
    }

    /**
     * @param MagentoSwitcher $object
     * @param Closure $work
     * @param $group
     *
     * @return Collection
     */
    public function aroundGetStoreCollection(MagentoSwitcher $object, Closure $work, $group)
    {
        if (!$group instanceof \Magento\Store\Model\Group) {
            $group = $object->_storeGroupFactory->create()->load($group);//q: wtf? _storeGroupFactory member has protected access
        }

        $stores    = $group->getStoreCollection();
        $_storeIds = $this->helper->getAllowedStoreViewIds();

        if (!empty($_storeIds)) {
            $stores->addIdFilter($_storeIds);
        }

        return $stores;
    }
}
