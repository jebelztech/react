<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */
 
namespace Aitoc\AdvancedPermissions\Plugin\Ui\DataProvider;
    
use Aitoc\AdvancedPermissions\Model\Permissions;

class ProductFormModifierCategories
{
    /**
     * @var Permissions
     */
    protected $permissions;
    
    /**
     * Product constructor.
     *
     * @param Permissions $$permissions
     */
    public function __construct(
        Permissions $permissions
    ) {
        $this->permissions = $permissions;
    }

    /**
     * Apply regular filters like collection filters
     *
     * @param AbstractDb $collection
     * @param Filter $filter
     *
     * @return void
     */
    public function afterModifyMeta(
        \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\Categories $object,
        $meta
    ) {
        $metaCategories = &$meta['product-details']['children']['container_category_ids']['children']['category_ids']['arguments']['data']['config']['options'];
        $metaCategories = $this->permissions->getAllowedCategoriesTree($metaCategories);
        return $meta;
    }
}
