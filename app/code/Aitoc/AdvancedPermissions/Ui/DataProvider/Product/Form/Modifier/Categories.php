<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Ui\DataProvider\Product\Form\Modifier;

use Aitoc\AdvancedPermissions\Model\Permissions;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Framework\DB\Helper as DbHelper;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\UrlInterface;

/**
 * Data provider for categories field of product page
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Categories extends \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\Categories
{
    /**
     * @var Permissions
     */
    protected $permissions;
    
    /**
     * Constructor
     *
     * @param LocatorInterface $locator
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param DbHelper $dbHelper
     * @param UrlInterface $urlBuilder
     * @param ArrayManager $arrayManager
     * @param Data $helper
     * @param StoreManagerInterface $storeManager
     * @param Permissions $permissions
     */
    public function __construct(
        LocatorInterface $locator,
        CategoryCollectionFactory $categoryCollectionFactory,
        DbHelper $dbHelper,
        UrlInterface $urlBuilder,
        ArrayManager $arrayManager,
        Permissions $permissions
    ) {
        parent::__construct($locator, $categoryCollectionFactory, $dbHelper, $urlBuilder, $arrayManager);
        $this->permissions = $permissions;
    }

    /**
     * Retrieve categories tree
     *
     * @param string|null $filter
     * @return array
     */
    protected function getCategoriesTree($filter = null)
    {
        return $this->permissions->getAllowedCategoriesTree(parent::getCategoriesTree($filter));
    }
}
