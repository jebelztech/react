<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Plugin\Sales\Order;

use Aitoc\AdvancedPermissions\Helper\Data;
use Magento\Catalog\Model\CategoryFactory;

class Grid
{
    /**
     * @var Data
     */
    private $helper;
    
    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;

    private $collection;

    /**
     * Constructor.
     *
     * @param Data $helper
     * @param CategoryFactory $categoryFactory
     */
    public function __construct(
        Data $helper,
        CategoryFactory $categoryFactory
    ) {
        $this->helper = $helper;
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * Sales Order Grid Collection Update
     */
    public function afterSearch($intercepter, $collection)
    {
        $this->collection = $collection;
        
        if ($collection->getMainTable() === $collection->getConnection()->getTableName('sales_order_grid')) {
            $this->applyPermissionFilter();
        }
        
        return $this->collection;
    }
    
    /**
     * Check and apply corresponding filter
     */
    public function applyPermissionFilter()
    {
        if (!$this->helper->isAdvancedPermissionEnabled()) {
            return;
        }
        
        // filter by store categories
        if (is_array($this->helper->getCategoryIds()) &&
            count($this->helper->getCategoryIds()) > 0 &&
            $this->helper->getScope() == Data::SCOPE_STORE
        ) {
            $categories = $this->getFullCategories($this->helper->getCategoryIds());
            $this->filterByCategories($categories);
        }
    }
    
    /**
     * Modify SQL Request
     *
     * @param array $categories
     */
    public function filterByCategories($categories)
    {
        $categorySelect = $this->collection->getConnection()->select()->from(
            ['soi' => $this->collection->getTable('sales_order_item')],
            'soi.order_id'
        )->joinLeft(
            ['ccp' => $this->collection->getTable('catalog_category_product')],
            'soi.product_id = ccp.product_id',
            ['']
        )->where(
            $this->collection->getConnection()->prepareSqlCondition('ccp.category_id', ['in' => $categories])
        )->where(
            $this->collection->getConnection()->prepareSqlCondition('soi.parent_item_id', ['null' => true])
        )->distinct(true);
        
        $ordersSubselect = $this->collection->getConnection()->fetchCol($categorySelect);
        
        $this->collection->getSelect()->where(
            $this->collection->getConnection()->prepareSqlCondition(
                'main_table.entity_id',
                ['in' => $ordersSubselect]
            )
        );
    }
    
    /**
     * Get All Categories
     *
     * @param $elements
     *
     * @return array
     */
    public function getFullCategories($elements)
    {
        $categories = [];
        foreach ($elements as $element) {
            $category = $this->categoryFactory->create()->load($element);
            $children = $category->getChildren();
            $childs = explode(',', $children);
            $categories = array_merge($categories, array_diff($childs, $categories));
        }
        if (count($categories)) {
            $elements = array_merge($elements, array_diff($categories, $elements));
        }
        foreach ($elements as $key => $value) {
            if (!$value) {
                unset($elements[$key]);
            }
        }

        return $elements;
    }
}
