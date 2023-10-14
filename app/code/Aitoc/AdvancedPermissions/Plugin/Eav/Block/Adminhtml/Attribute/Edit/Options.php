<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Plugin\Eav\Block\Adminhtml\Attribute\Edit;

use Aitoc\AdvancedPermissions\Model\Store\StoreManager;
use Magento\Eav\Block\Adminhtml\Attribute\Edit\Options\Options as AttrOptions;

class Options
{
    /**
     * @var StoreManager
     */
    protected $storeManager;

    /**
     * @param StoreManager $storeManager
     */
    public function __construct(
        StoreManager $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * Retrieve stores collection with default store
     *
     * @return array
     */
    public function aroundGetStores(AttrOptions $object, \Closure $closure)
    {
        return $this->storeManager->getStoresAll(true);
    }
}
