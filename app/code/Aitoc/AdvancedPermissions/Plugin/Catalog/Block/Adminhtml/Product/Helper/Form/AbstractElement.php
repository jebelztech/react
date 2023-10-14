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

use Aitoc\AdvancedPermissions\Helper\Data;

class AbstractElement
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @param Data $helper
     */
    public function __construct(
        Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Check if current admin can edit global product attributes, if don't - disable input fields
     *
     * @param $element
     */
    protected function globalAttributeCheck($element)
    {
        if (is_object($element) && $this->isNeedDisable()) {
            $element->setDisabled(true)->setReadonly(true);
        }
    }

    /**
     * Check if current admin can edit global product attributes, if don't - disable input fields
     *
     * @return bool
     */
    public function isNeedDisable()
    {
        return ($this->helper->isAdvancedPermissionEnabled()
            && !$this->helper->getRole()->getManageGlobalAttribute());
    }
}
