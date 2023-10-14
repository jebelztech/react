<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Plugin\Model\Store;

use Aitoc\AdvancedPermissions\Helper\Sales;
use Aitoc\AdvancedPermissions\Model\Store\StoreManager as ModuleStoreManager;
use Closure;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManager as CoreStoreManager;
use Magento\Store\Api\StoreRepositoryInterface;

/**
 * Class StoreManager
 */
class StoreManager
{
    /**
     * @var Sales
     */
    private $helper;

    /**
     * @var StoreManager
     */
    private $storeManager;

    /**
     * @var State
     */
    private $state;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var StoreRepositoryInterface
     */
    private $storeRepository;

    /**
     * @param Sales                    $helper
     * @param ModuleStoreManager       $storeManager
     * @param State                    $state
     * @param ObjectManagerInterface   $objectManager
     * @param StoreRepositoryInterface $storeRepository
     */
    public function __construct(
        Sales $helper,
        ModuleStoreManager $storeManager,
        State $state,
        ObjectManagerInterface $objectManager,
        StoreRepositoryInterface $storeRepository
    ) {
        $this->helper = $helper;
        $this->storeManager = $storeManager;
        $this->state = $state;
        $this->objectManager = $objectManager;
        $this->storeRepository = $storeRepository;
    }

    /**
     * @param CoreStoreManager $subject
     * @param Closure $work
     * @param bool $withDefault
     * @param bool $codeKey
     * @return array
     */
    public function aroundGetStores(
        CoreStoreManager $subject,
        Closure $work,
        $withDefault = false,
        $codeKey = false
    ) {
        if (!$this->isRestrictionsApplicableToStores()) {
            return $this->storeManager->getStoresAll($withDefault, $codeKey);
        }

        $oldStores = $work($withDefault, $codeKey);
        $AllowedStoreViewIds = $this->helper->getAllowedStoreViewIds();
        $AllowedStoreViewCodes = $this->helper->getAllowedStoreViewIds(true);
        $stores = [];

        foreach ($oldStores as $key => $value) {
            if ($codeKey && in_array($key, $AllowedStoreViewCodes)) {
                continue;
            }
            if (!$codeKey && in_array($key, $AllowedStoreViewIds)) {
                continue;
            }
            if (!in_array($key, $AllowedStoreViewCodes)) {
                unset($oldStores[$key]);
            }

            $value->setIsActive(false);
        }

        return $oldStores;
    }

    /**
     * @return bool
     */
    private function isRestrictionsApplicableToStores()
    {
        return $this->isSetAreaCode()
            && $this->getAuthSession()->getUser()
            && $this->helper->isAdvancedPermissionEnabled()
            && $this->helper->isAdvancedPermissionAllowed();
    }

    /**
     * @return bool
     */
    private function isSetAreaCode()
    {
        try {
            $this->state->getAreaCode();

            return true;
        } catch (LocalizedException $exception) {
            return false;
        }
    }

    /**
     * @return Session
     */
    private function getAuthSession()
    {
        return $this->objectManager->get(Session::class);
    }

    /**
     * @param CoreStoreManager $subject
     * @param $result
     *
     * @return bool
     */
    public function afterHasSingleStore(CoreStoreManager $subject, $result)
    {
        if ($result) {
            return count($subject->getStores(true)) < 2;
        }

        return $result;
    }

    /**
     * @param CoreStoreManager $subject
     * @param Closure $work
     * @param bool $withDefault
     * @param bool $codeKey
     *
     * @return array
     */
    public function aroundGetWebsites(
        CoreStoreManager $subject,
        Closure $work,
        $withDefault = false,
        $codeKey = false
    ) {
        $websitesOld = $work($withDefault, $codeKey);

        if ($this->isSetAreaCode() && $this->getAuthSession()->getUser()
            && $this->helper->isAdvancedPermissionEnabled()) {
            $websitesIds = $this->helper->getAllowedWebsiteIds();
            $websitesCode = $this->helper->getAllowedWebsiteIds(true);
            $websites = [];

            foreach ($websitesOld as $key => $value) {
                if ($codeKey && in_array($key, $websitesCode)) {
                    $websites[$key] = $value;
                }

                if (!$codeKey && in_array($key, $websitesIds)) {
                    $websites[$key] = $value;
                }
            }

            return $websites;
        }

        return $websitesOld;
    }

    public function aroundGetStore(
        CoreStoreManager $subject,
        Closure $proceed,
        $storeId = null
    ) {
        try {
            $result = $proceed($storeId);
        } catch (NoSuchEntityException $exception) {
            $allowedStoreIds = $this->helper->getAllowedStoreIds();
            $storeId = reset($allowedStoreIds);
            $result = $this->storeRepository->getActiveStoreById($storeId);
        }
        return $result;
    }
}
