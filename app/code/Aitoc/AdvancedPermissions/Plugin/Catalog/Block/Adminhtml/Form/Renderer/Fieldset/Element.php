<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Plugin\Catalog\Block\Adminhtml\Form\Renderer\Fieldset;

use Aitoc\AdvancedPermissions\Helper\Data;
use Magento\Catalog\Block\Adminhtml\Form\Renderer\Fieldset\Element as FieldsetElementRenderer;

class Element
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * Element constructor.
     *
     * @param Data $helper
     */
    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param FieldsetElementRenderer $object
     */
    public function beforeGetElementHtml(FieldsetElementRenderer $object)
    {
        if (!$this->helper->isAdvancedPermissionEnabled()) {
            return;
        }

        $element   = $object->getElement();
        $attribute = $element->getEntityAttribute();

        if ($attribute && $attribute->isScopeGlobal() && !$this->helper->getRole()->getManageGlobalAttribute()) {
            $element->setDisabled(true);
        }
    }
}
