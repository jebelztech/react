<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */


namespace Aitoc\AdvancedPermissions\Block\Role\Editor\Render;

class Radio extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Radio
{
    /**
     * @return string
     */
    public function renderHeader()
    {
        $checked = '';
        if ($filter = $this->getColumn()->getFilter()) {
            $checked = $filter->getValue() ? ' checked="checked"' : '';
        }

        $disabled = '';
        if ($this->getColumn()->getDisabled()) {
            $disabled = ' disabled="disabled"';
        }

        if ($this->getColumn()->getName() == 'is_ait_allow') {
            $out = '<th class="data-grid-th data-grid-actions-cell">';
        } else {
            $out = '<th class="data-grid-th data-grid-actions-cell">';
        }

        $out .= '<input type="radio" ';
        $out .= 'name="' . $this->getColumn()->getHtmlName() . '" ';
        $out .= 'onclick="' . $this->getColumn()->getGrid()->getJsObjectName() . '.checkCheckboxes(this)" ';
        $out .= 'class="admin__control-radio"' . $checked . $disabled . ' ';
        $out .= 'value="' . $this->getColumn()->getRadioValue() . '" ';
        $out .= 'title="' . __('Select All') . '"/><label></label>';

        $out .= '<span>' .
            $this->getColumn()->getHeader() .
            '</span></th>';

        return $out;
    }

    /**
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $values = $this->getColumn()->getValues();
        $value  = $row->getData($this->getColumn()->getIndex());

        if ($this->getColumn()->getRadioValue() == '') {
            $checked = !in_array($row->getId(), $values) ? ' checked="checked"' : '';
        } elseif (is_array($values)) {
            $checked = in_array($row->getId(), $values) ? ' checked="checked"' : '';
        } else {
            $checked = ($row->getId() === $this->getColumn()->getValue()) ? ' checked="checked"' : '';
        }
        $disabled = '';
        if ($this->getColumn()->getDisabled()) {
            $disabled = ' disabled="disabled"';
        }

        $html = '<label><input type="radio" id="' . $this->getColumn()->getFieldName() .
            '" name="' . $this->getColumn()->getHtmlName() . '[' . $row->getId() . ']" ';
        $html .= 'value="' . $this->getColumn()->getRadioValue()
            . '" class="radio"' . $checked . $disabled . ' rawid='
            . $row->getId() . ' />';

        $options      = $this->getColumn()->getOptions();
        $optionsScope = $this->getColumn()->getOptionsScope();
        if (!empty($options) && is_array($options)) {
            if (isset($options[$value])) {
                $html .= $this->escapeHtml($options[$value]);
            }
        }
        $html .= '</label>';

        return $html;
    }
}