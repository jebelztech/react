<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\AdvancedPermissions\Model\Store;

use Aitoc\AdvancedPermissions\Helper\Data;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Cache\FrontendInterface;
use Magento\Store\Api\GroupRepositoryInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Api\StoreResolverInterface;
use Magento\Store\Api\WebsiteRepositoryInterface;

class StoreManager extends \Magento\Store\Model\StoreManager
{

    /**
     * @var Data
     */
    protected $helper;

    /**
     * StoreManager constructor.
     *
     * @param StoreRepositoryInterface $storeRepository
     * @param GroupRepositoryInterface $groupRepository
     * @param WebsiteRepositoryInterface $websiteRepository
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreResolverInterface $storeResolver
     * @param FrontendInterface $cache
     * @param Data $helper
     */
    public function __construct(
        StoreRepositoryInterface $storeRepository,
        GroupRepositoryInterface $groupRepository,
        WebsiteRepositoryInterface $websiteRepository,
        ScopeConfigInterface $scopeConfig,
        StoreResolverInterface $storeResolver,
        FrontendInterface $cache,
        Data $helper
    ) {
        parent::__construct(
            $storeRepository,
            $groupRepository,
            $websiteRepository,
            $scopeConfig,
            $storeResolver,
            $cache,
            true
        );
        $this->helper = $helper;
    }

    /**
     * Get all stores
     *
     * @param bool $withDefault
     * @param bool $codeKey
     *
     * @return array
     */
    public function getStoresAll($withDefault = false, $codeKey = false)
    {
        $stores = [];
        $this->storeRepository->clean();
        foreach ($this->storeRepository->getList() as $store) {
            if (!$withDefault && $store->getId() == 0) {
                continue;
            }
            if ($codeKey) {
                $stores[$store->getCode()] = $store;
            } else {
                $stores[$store->getId()] = $store;
            }
        }

        return $stores;
    }

    /**
     * Get all websites
     *
     * @param bool $withDefault
     * @param bool $codeKey
     *
     * @return array
     */
    public function getWebsitesAll($withDefault = false, $codeKey = false)
    {
        $websites = [];
        foreach ($this->websiteRepository->getList() as $website) {
            if (!$withDefault && $website->getId() == 0) {
                continue;
            }
            if ($codeKey) {
                $websites[$website->getCode()] = $website;
            } else {
                $websites[$website->getId()] = $website;
            }
        }

        return $websites;
    }
}
