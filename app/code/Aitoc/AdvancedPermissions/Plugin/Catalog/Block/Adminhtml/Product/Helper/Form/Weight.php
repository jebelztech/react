<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Plugin\Catalog\Block\Adminhtml\Product\Helper\Form;

use Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Weight as MagentoWight;

/**
 * Class Weight
 */
class Weight extends AbstractElement
{
    /**
     * Check if current admin can edit global product attributes, if don't - disable input fields
     *
     * @param MagentoWight $element
     */
    public function beforeGetElementHtml(MagentoWight $element)
    {
        if ($this->isNeedDisable()) {
            /** @noinspection PhpUndefinedMethodInspection */
            $element->setDisabled('disabled')->setReadonly(true);
        }
    }
}
