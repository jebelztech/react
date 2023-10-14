<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Plugin\Catalog\Block\Adminhtml\Product\Edit\Tab;

use Aitoc\AdvancedPermissions\Plugin\Catalog\Block\Adminhtml\Product\Helper\Form\AbstractElement;
use Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Inventory as InventoryTab;

class Inventory extends AbstractElement
{
    /**
     * @param InventoryTab $object
     * @param $result
     *
     * @return bool
     */
    public function afterIsReadonly(InventoryTab $object, $result)
    {
        return $this->isNeedDisable() ? true : $result;
    }
}
