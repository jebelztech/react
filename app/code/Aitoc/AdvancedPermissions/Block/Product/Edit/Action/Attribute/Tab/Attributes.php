<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Block\Product\Edit\Action\Attribute\Tab;

use Aitoc\AdvancedPermissions\Helper\Data;
use Magento\Catalog\Block\Adminhtml\Product\Edit\Action\Attribute\Tab\Attributes as BaseAttributes;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Attributes extends BaseAttributes
{
    /**
     * @param AbstractElement $element
     *
     * @return string
     */
    protected function _getAdditionalElementHtml($element)
    {
        $helper = $this->getData('helper');
        $attribute = $this->getData('attribute');
        $attrEav = $this->getData('attr');
        if ($helper->getRole()->getManageGlobalAttribute()) {
            $allowGlobal[] = 1;
        }
        if ($helper->getRole()->getScope() == Data::SCOPE_STORE) {
            $allowGlobal[] = 0;
        }
        if ($helper->getRole()->getScope() == Data::SCOPE_WEBSITE) {
            $allowGlobal[] = 0;
            $allowGlobal[] = 2;
        }

        $attr = $attribute->load($element->getId(), "attribute_code");
        $cav  = $attrEav->load($attr->getId());
        // Add name attribute to checkboxes that correspond to multiselect elements
        $nameAttributeHtml = $element->getExtType() === 'multiple' ? 'name="' . $element->getId() . '_checkbox"' : '';
        $elementId         = $element->getId();
        $dataAttribute     = "data-disable='{$elementId}'";
        $dataCheckboxName  = 'toggle_' . $elementId;
        $checkboxLabel     = __('Change');

        if ($helper->isAdvancedPermissionEnabled()
            && $attr->getEntityType()->getId() == 4
            && !in_array(
                $cav->getIsGlobal(),
                $allowGlobal
            )
        ) {
            return '';
        }

        $html = <<<HTML
<span class="attribute-change-checkbox">
    <input type="checkbox" id="$dataCheckboxName" name="$dataCheckboxName" class="checkbox" $nameAttributeHtml onclick="toogleFieldEditMode(this, '{$elementId}')" $dataAttribute />
    <label class="label" for="$dataCheckboxName">
        {$checkboxLabel}
    </label>
</span>
HTML;
        if ($elementId === 'weight') {
            $html .= <<<HTML
<script>require(['Magento_Catalog/js/product/weight-handler'], function (weightHandle) {
    weightHandle.hideWeightSwitcher();
});</script>
HTML;
        }

        return $html;
    }
}
