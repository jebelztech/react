<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\AdvancedPermissions\Model\Store\System;

use Aitoc\AdvancedPermissions\Model\Store\StoreManager;

class Store extends \Magento\Store\Model\System\Store
{
    /**
     * @var StoreManager
     */
    protected $storeManager;

    /**
     * Store constructor.
     *
     * @param StoreManager $storeManager
     */
    public function __construct(
        StoreManager $storeManager
    ) {
        parent::__construct($storeManager);
    }

    /**
     * Retrieve store values for form
     *
     * @param bool $empty
     * @param bool $all
     *
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getStoreValuesAllForForm($empty = false, $all = false)
    {
        $options = [];
        if ($empty) {
            $options[] = ['label' => '', 'value' => ''];
        }
        if ($all) {
            $options[] = ['label' => __('All Store Views'), 'value' => 0];
        }

        foreach ($this->_storeManager->getWebsitesAll() as $website) {
            $stores = [];
            foreach ($this->loadAllGroupCollection() as $group) {
                if ($website->getId() != $group->getWebsiteId()) {
                    continue;
                }
                $groupShow = false;
                foreach ($this->_storeManager->getStoresAll() as $store) {
                    if ($group->getId() != $store->getGroupId()) {
                        continue;
                    }

                    if (!$groupShow) {
                        $groupShow = true;
                        $values    = [];
                    }
                    $values[] = [
                        'label'  => $store->getName(),
                        'value'  => $store->getId(),
                        'type'   => 'storeview',
                        'parent' => $group->getId()
                    ];
                }
                if ($groupShow) {
                    $stores[] = [
                        'label'  => $group->getName(),
                        'value'  => $group->getId(),
                        'scopes' => $values,
                        'type'   => 'store'
                    ];
                }
            }
            $options[] = [
                'label'  => $website->getName(),
                'scopes' => $stores,
                'value'  => $website->getId(),
                'type'   => 'website'
            ];
        }
        return $options;
    }

    /**
     * Get all websites
     *
     * @param bool $empty
     * @param bool $all
     *
     * @return array
     */
    public function getWebsiteValuesAllForForm($empty = false, $all = false)
    {
        foreach ($this->_storeManager->getWebsitesAll() as $website) {
            $options[] = ['label' => $website->getName(), 'value' => $website->getId()];
        }

        return $options;
    }

    /**
     * Load/Reload Group collection
     *
     * @return $this
     */
    protected function loadAllGroupCollection()
    {
        $groupCollection = [];
        foreach ($this->_storeManager->getWebsitesAll() as $website) {
            foreach ($website->getGroups() as $group) {
                $groupCollection[$group->getId()] = $group;
            }
        }

        return $groupCollection;
    }
}
