<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Plugin\CatalogInventory\Block\Adminhtml\Form\Field;

use Aitoc\AdvancedPermissions\Plugin\Catalog\Block\Adminhtml\Product\Helper\Form\AbstractElement;
use Magento\CatalogInventory\Block\Adminhtml\Form\Field\Stock as MagentoStockField;

class Stock extends AbstractElement
{
    /**
     * Check if current admin can edit global product attributes, if don't - disable input fields
     *
     * @param MagentoStockField $element
     */
    public function beforeGetElementHtml(MagentoStockField $element)
    {
        if ($this->isNeedDisable()) {
            $element->setDisabled('disabled')->setReadonly(true)->lock();
        }
    }
}
