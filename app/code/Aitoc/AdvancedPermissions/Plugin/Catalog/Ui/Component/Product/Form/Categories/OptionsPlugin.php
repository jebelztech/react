<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Plugin\Catalog\Ui\Component\Product\Form\Categories;

use Aitoc\AdvancedPermissions\Helper\Data;
use Aitoc\AdvancedPermissions\Model\Permissions;

class OptionsPlugin
{
    /**
     * @var Permissions
     */
    protected $permissions;
    
    /**
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param RequestInterface $request
     * @param Data $helper
     */
    public function __construct(
        Permissions $permissions
    ) {
        $this->permissions = $permissions;
    }

    /**
     * {@inheritdoc}
     */
    public function afterToOptionArray(
        \Magento\Catalog\Ui\Component\Product\Form\Categories\Options $subject,
        $options
    ) {
        return $this->permissions->getAllowedCategoriesTree($options);
    }
}
