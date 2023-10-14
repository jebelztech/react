<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Observer\Category\Create;

use Aitoc\AdvancedPermissions\Helper\Data;
use Aitoc\AdvancedPermissions\Model\Stores;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class SaveAfterObserver implements ObserverInterface
{
    /**
     * @var Data
     */
    protected $helper;
    
    /**
     * @var Session
     */
    protected $authStorage;
    
    /**
     * @var Stores $stores
     */
    protected $stores;
    
    /**
     * Constructor
     *
     * @param Data $helper
     * @param Session $authStorage
     * @param Stores $stores
     */
    public function __construct(
        Data $helper,
        Session $authStorage,
        Stores $stores
    ) {
        $this->helper = $helper;
        $this->authStorage = $authStorage;
        $this->stores = $stores;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        if ($this->helper->isAdvancedPermissionEnabled()) {
            $category = $observer->getEvent()->getDataObject();

            foreach ($this->authStorage->getAitUpdateRoleAllowedCategories() as $categoryStoreId) {
                $roleStore = $this->stores->getCollection()
                    ->addFieldToFilter('store_id', $categoryStoreId)
                    ->addFieldToFilter('advanced_role_id', $this->helper->getRole()->getId())
                    ->getFirstItem();

                if ($roleStore->getId()) {
                    $categoryIds = $roleStore->getCategoryIds();
                    $roleStore->setCategoryIds(implode(',', [$categoryIds, $category->getId()]));
                    $roleStore->save();
                }
            }
            $this->authStorage->setAitUpdateRoleAllowedCategories(null);
        }
    }
}
