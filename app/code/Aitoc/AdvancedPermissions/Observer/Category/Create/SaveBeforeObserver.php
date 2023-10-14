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
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class SaveBeforeObserver implements ObserverInterface
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
     * Constructor
     *
     * @param Data $helper
     * @param Session $authStorage
     */
    public function __construct(
        Data $helper,
        Session $authStorage
    ) {
        $this->helper = $helper;
        $this->authStorage = $authStorage;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        if ($this->helper->isAdvancedPermissionEnabled()) {
            $category = $observer->getEvent()->getCategory();
            
            // existing category
            if ($category->getId()) {
                return;
            }
            
            if ($assignStoreId = $this->getConfigAllowedCategoryStore($category)) {
                $this->authStorage->setAitUpdateRoleAllowedCategories($assignStoreId);
            }
        }
    }
    
    /**
     * @param mixed $category
     */
    public function getConfigAllowedCategoryStore($category)
    {
        $categoriesByStore = $this->helper->getTree(Data::ADVANCED_CATEGORIES, true);
        $parentStoreIds    = $category->getParentCategory()->getStoreIds();

        $stores = [];
        // collect stores that linked with new category
        foreach ($this->helper->getAllowedStoreIds() as $id) {
            // get stores with shared categories and has allowed store
            if (in_array($id, $parentStoreIds) && !empty($categoriesByStore[$id])) {
                $stores[] = $id;
            }
        }

        return $stores;
    }
}
